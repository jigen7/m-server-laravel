<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\ModelFormatter;
use App\Http\Helpers\KeyParser;
use Illuminate\Support\Facades\Input;


class CheckIns extends Model {

	protected $table = 'check_ins';
    public $timestamps = false;

/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Get all Checkins by Restaurant ID
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getByRestaurantId($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->get();
    } // end getByRestaurantId

    public static function getByRestaurantIdPaginated($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::CHECKINS_GET_BY_RESTAURANT_ID_PAGINATION_LIMIT);
    }

    /**
     * Get all Checkins by user ID
     *
     * @param $user_id
     * @return mixed
     */
    public static function getByUserIdPaginated($user_id)
    {
        return self::where('user_id', $user_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::CHECKINS_GET_BY_USER_ID_PAGINATION_LIMIT);
    }


/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS GETTER METHODS ************************************/

    /**
     * @param $data
     * @return $this
     * @throws \Exception
     */
    public function addCheckin($data)
    {
        $lat = Input::get('lat', 0);
        $long = Input::get('long', 0);
        try {
            $this->restaurant_id = $data['restaurant_id'];
            $this->message = $data['message'];
            $this->user_id = $data['user_id'];
            $this->latitude = $lat;
            $this->longitude = $long;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();

            return $this;
        } catch (\Exception $e) {
            throw new \Exception ('Failed to save checkin');
        }
    }// end of addReview

    /**
     * @param $id
     * @param $data
     * @return Checkin $checkin
     * @throws $e
     */
    public function editCheckin($id, $data)
    {
        try {
            $checkin = self::find($id);

            if (!$checkin) {
                throw new \Exception('Checkin not found');
            }

            if ($checkin->user_id != $data['user_id']) {
                throw new \Exception('User is not the original owner of the checkin');
            }

            $checkin->message = $data['message'];
            $checkin->save();

            return $checkin;
        } catch (\Exception $e) {
            throw $e;
        }
    }// end of editReview

    /**
     * Delete Checkin
     * @param $id
     * @throws $e
     */
    public function deleteCheckin($id)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $checkin = CheckIns::find($id);

            if ($checkin) {
                //delete checkin
                $checkin->delete();

                //delete activities
                $activities = new Activities();
                $activities->deleteActivity(CONSTANTS::CHECKIN,$id);

                //delete comments
                $comments = new Comments();
                $comments->deleteCommentByType(CONSTANTS::CHECKIN,$id);

                //delete likes
                $like = new Like();
                $like->deleteLikes(CONSTANTS::CHECKIN, $id);
            } else {
                throw new \Exception('No checkin found');
            }

            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end of deleteReview

/*************************** END MUTATORS GETTER METHODS ************************************/

    /**
     * Get checkin count per user
     *
     * @param $user_id
     * @return mixed
     */
    public static function getCountByUserId ($user_id)
    {
        return self::leftJoin('restaurants', 'check_ins.restaurant_id', '=', 'restaurants.id')
            ->where('check_ins.user_id', $user_id)
            ->whereNull('restaurants.deleted_at')
            ->count();
    } // end getCountByUserId

    /**
     * Construct the needed Array for Checkins User and Restaurant
     * action:
     *
     * @param $checkins
     * @return mixed
     */
    public static function checkinsQueries($checkins)
    {
        $checkins_array = array();

        if (!$checkins){
            return $checkins_array;
        }

        foreach ($checkins as $checkin) {
            $restaurant = Restaurants::find($checkin->restaurant_id);
            if (!$restaurant) {
                continue;
            }
            $user = Users::find($checkin->user_id);

            $photos = Photos::getByType(CONSTANTS::CHECKIN, $checkin->id);
            $photos_array = Photos::convertPhotosToArray($photos);

            $checkins_array[] = array(
                KeyParser::checkin => ModelFormatter::checkinFormat($checkin),
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::user   => ModelFormatter::userLongFormat($user),
                KeyParser::photos => $photos_array,
            );

            unset($restaurant);
            unset($user);
            unset($photos);
            unset($photos_array);
        } //end foreach

        return $checkins_array;
    } // end of checkinsQueries

}



