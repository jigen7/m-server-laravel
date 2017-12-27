<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\ModelFormatter;
use App\Http\Helpers\KeyParser;

class Activities extends Model {

	protected $table = 'activities';

    public $timestamps = false;

/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Get the latest activity of a restaurant
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getLatestRestaurantActivity($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->orderBy('date_created',CONSTANTS::ORDER_DESC)
            ->first();
    }

    /**
     * Get the activities of a restaurant
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getRestaurantActivities($restaurant_id)
    {
        return self::where('restaurant_id',$restaurant_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::RESTAURANTS_GET_ACTIVITIES_PAGINATION_LIMIT);
    }

    /**
     * Get the activities of multiple restaurants from the newest
     *
     * @param $restaurant_ids
     * @param $viewer_id
     * @return mixed
     */
    public static function getRestaurantsActivities($restaurant_ids, $viewer_id)
    {
        return self::whereIn('restaurant_id', $restaurant_ids)
            ->where('user_id', '!=', $viewer_id )
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::RESTAURANTS_GET_ACTIVITIES_PAGINATION_LIMIT);
    }

    /**
     * Query the users, restaurant, bookmarks, checkins, reviews
     *
     * @param $follower_id
     * @param int $restaurant_id
     * @return mixed
     */
    public static function getFollowedActivities($follower_id, $restaurant_id){
        $following_ids = Follow::getFollowedUserIds($follower_id);

        $res_activities = self::whereIn('user_id', $following_ids)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC);

        if ($restaurant_id) {
            $res_activities->where('restaurant_id',$restaurant_id);
        }

        return $res_activities->paginate(CONSTANTS::RESTAURANTS_GET_ACTIVITIES_PAGINATION_LIMIT);
    }// end of getAllFollowingByFollowerId

/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Add Activity function
     *
     * @param $type
     * @param $type_id
     * @param $user_id
     * @param $restaurant_id
     * @throws \Exception
     */
    public function addActivity($type, $type_id, $user_id, $restaurant_id)
    {
        try {
            $this->type = $type;
            $this->type_id = $type_id;
            $this->user_id = $user_id;
            $this->restaurant_id = $restaurant_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();
            return $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete activities
     *
     * @param $type
     * @param $type_id
     * @return bool
     * @throws \Exception
     */
    public function deleteActivity($type, $type_id)
    {
        $activity = self::where('type', $type)
            ->where('type_id', $type_id)
            ->get()
            ->first();

        if ($activity) {
            $activity->delete();
            return true;
        }

        return false;
    }

/*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Query the users, restaurant, bookmarks, checkins, reviews
     *
     * @param int activities
     * @return mixed
     */
    public static function activitiesQueries($activities)
    {
        $activities_array = array();

        if($activities->count()){
            foreach ($activities as $activity) {
                $data = array();
                $data[KeyParser::activity] = ModelFormatter::activityFormat($activity);

                //GetUserInfo
                $user = Users::find($activity->user_id);
                if ($user) {
                    $data[KeyParser::user] = ModelFormatter::userFormat($user);
                } else {
                    $data[KeyParser::user][KeyParser::error] = "No Information";
                }//end check user

                //GetRestaurantInfo
                $restaurant = Restaurants::where('status_verify', CONSTANTS::STATUS_VERIFIED)
                    ->find($activity->restaurant_id);

                if ($restaurant) {
                    $data[KeyParser::restaurant] = ModelFormatter::restaurantFormat($restaurant);
                } else {
                    $data[KeyParser::restaurant][KeyParser::error] = "No Information";
                } // end check restaurant

                $data += self::getActivityType($activity->type, $activity->type_id);

                $activities_array[] = $data;
                unset($data);
            }//end foreach
        }//end count

        return $activities_array;
    }//end of activitiesQuery

    /**
     * Returns either review, checkin, or bookmark activity based on $type parameter.
     *
     * @param $type - activity type. Either 'checkin', 'review', or 'bookmark'
     * @param $type_id - ID for checkin/review/bookmark activity
     * @return mixed
     */
    public static function getActivityType($type, $type_id)
    {
        $arr = array();

        switch($type) {

            case CONSTANTS::CHECKIN:
                $check_in = CheckIns::find($type_id);
                if($check_in) {
                    $arr[KeyParser::checkin] = ModelFormatter::checkinFormat($check_in);
                } else {
                    $arr[KeyParser::checkin] = array();
                }
                $photos = Photos::getByType(CONSTANTS::CHECKIN, $type_id);
                $arr[KeyParser::photos] = Photos::convertPhotosToArray($photos);
                break;

            case CONSTANTS::REVIEW:
                $review = Reviews::find($type_id);
                if($review) {
                    $arr[KeyParser::review] = ModelFormatter::reviewFormat($review);
                } else {
                    $arr[KeyParser::review] = array();
                }

                $photos = Photos::getByType(CONSTANTS::REVIEW, $type_id);

                $arr[KeyParser::photos] = Photos::convertPhotosToArray($photos);
                break;

            case CONSTANTS::PHOTO_UPLOAD_RESTAURANT:
                    $photos = Photos::where('id',$type_id)->get();
                    $arr[KeyParser::photos] = Photos::convertPhotosToArray($photos);
                break;
        }
        unset($photos_arr);
        return $arr;
    } // end getActivityType

}//end of class