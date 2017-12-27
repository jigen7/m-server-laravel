<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use Illuminate\Database\Eloquent\Model;

class Like extends Model {

    protected $table = 'like';

    public $timestamps = false;

/*********************************** START ACCESSOR METHODS ************************************/

    /** Get the liker list of an activity or photo
     *
     * @param string type
     * @param int type_id
    */
    public static function getLikerList($type, $type_id, $paginated = true, $date_range = false)
    {
        $likers =  self::where('type', $type)
            ->where('type_id', $type_id);

        if(isset($date_range[KeyParser::date_from]) && isset($date_range[KeyParser::date_to])) {
            return $likers->whereBetween('date_created', array($date_range[KeyParser::date_from], $date_range[KeyParser::date_to]))
                ->orderBy('date_created', CONSTANTS::ORDER_DESC)
                ->get();
        }

        if($paginated) {
            return $likers->paginate(CONSTANTS::LIKE_GET_LIST_PAGINATION_LIMIT);
        }

        return $likers->get();
    }

    /**
     * Get like count per type_id
     *
     * @param $type_id
     * @param $type
     * @return mixed
     */
    public static function getCount ($type, $type_id)
    {
        return self::where('type', $type)
            ->where('type_id', $type_id)
            ->get()
            ->count();
    }

    /*********************************** END ACCESSOR METHODS ************************************/


    /*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Add like data
     *
     * @param $type_id
     * @param $type
     * @param $user_id
     * @param $like_object
     * @param $like_type
     * @return $this
     * @throws \Exception
     */
    public function addLike($type_id, $type, $user_id, $like_object, $like_type)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();

            $this->type_id = $type_id;
            $this->type = $type;
            $this->user_id = $user_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();

            $restaurant_id = $like_object['restaurant_id'];
            $owner_id = $like_object['user_id'];

            if ($user_id != $owner_id) {
                $notification_data = new Notification();
                $notification_data->addLikeNotification($user_id, $owner_id, $like_type, $type_id, $restaurant_id);
            }

            $connection->commit();

            return $this;
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end addLike

    /**
     * Delete like for review, checkin, or photo
     *
     * @param $type
     * @param $type_id
     * @param $user_id
     * @throws \Exception
     */
    public function deleteLike($type, $type_id, $user_id)
    {
        try {
            $like = self::where('type', $type)
                ->where('type_id', $type_id)
                ->where('user_id', $user_id);

            if ($like) {
                $like->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    } // end deleteLike

    /**
     * Delete likes
     *
     * @param $type
     * @param $type_ids
     * @return bool
     * @throws \Exception
     */
    public function deleteLikes($type, $type_id)
    {
        if (!in_array($type, array(CONSTANTS::REVIEW, CONSTANTS::CHECKIN, CONSTANTS::RESTAURANT, CONSTANTS::COMMENT, CONSTANTS::PHOTO))) {
            throw new \Exception('Type is unrecognized');
        }

        $likes = self::where('type', $type)
            ->where('id', $type_id);

        if ($likes) {
            $likes->delete();

            return true;
        }

        return false;
    } // end deleteLikes

    /*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Get like Status
     * 1 - like 0 - not like
     *
     * @param $user_id
     * @param $type
     * @param $type_id
     * @return bool
     */
    public static function isLiked($user_id, $type, $type_id)
    {
        $like = self::where('user_id', $user_id)
            ->where('type', $type)
            ->where('type_id', $type_id)
            ->get();

        if ($like->count()) {
            return 1;
        }

        return 0;
    } // end getStatus

}