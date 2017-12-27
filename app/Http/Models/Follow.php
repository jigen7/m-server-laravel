<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Follow extends Model {

	protected $table = 'follow';

    public $timestamps = false;

/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Returns list of all followers per user_id
     * use in Add Review API
     *
     * @param $user_id
     * @return mixed
     */
    public static function getFollowerUsersAll($user_id)
    {
        return self::where('followed_user_id', $user_id)
            ->get();
    } // end getFollowerUsers

    /**
     * Returns list of followers per user_id
     *
     * @param $user_id
     * @return mixed
     */
    public static function getFollowerUsers($user_id)
    {
        return self::leftJoin('users', 'users.id', '=', 'follow.follower_user_id')
            ->where('followed_user_id', $user_id)
            ->orderBy('follow.date_created', CONSTANTS::ORDER_DESC)
            ->orderBy('follow.id')
            ->paginate(CONSTANTS::FOLLOW_GET_FOLLOWS_PAGINATION_LIMIT);
    } // end getFollowerUsers

    /**
     * Returns list of followed users per user_id
     *
     * @param $user_id
     * @return mixed
     */
    public static function getFollowedUsers($user_id)
    {
        return self::leftJoin('users', 'users.id', '=', 'follow.followed_user_id')
            ->where('follower_user_id', $user_id)
            ->orderBy('follow.date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::FOLLOW_GET_FOLLOWS_PAGINATION_LIMIT);
    } // end getFollowedUsers

    /**
     * Returns an array of followed user IDs
     *
     * @param $user_id
     * @return mixed
     */
    public static function getFollowedUserIds($user_id)
    {
        return self::where('follower_user_id', $user_id)
            ->lists('followed_user_id');
    }

/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Add new follow data
     *
     * @param $follower_id
     * @param $followed_id
     * @throws \Exception
     */
    public function addFollow($follower_id, $followed_id)
    {
        try {
            DB::beginTransaction();

            $this->follower_user_id = $follower_id;
            $this->followed_user_id = $followed_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();

            $notification_data = new Notification();
            $notification_data->addNotificationNewFollower($follower_id, $followed_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Unfollow a user
     *
     * @param $follower_id
     * @param $followed_id
     * @throws \Exception
     */
    public function unfollowUser($follower_id, $followed_id)
    {
        try {
            $follow = self::where('follower_user_id', $follower_id)
                ->where('followed_user_id', $followed_id);

            if ($follow->count()) {
                $follow->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

/*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Returns 1 if the user is being followed. If not, return 0.
     *
     * @param $follower_user_id
     * @param $followed_user_id
     * @return mixed
     */
    public static function isFollowed($follower_user_id, $followed_user_id)
    {
        // Check if the user have followers 1 - Followed by a Uer 0 - Does'nt Have Followers
        $follow = self::where('follower_user_id', $follower_user_id)
            ->where('followed_user_id', $followed_user_id)
            ->get();

        if ($follow->count()) {
            return CONSTANTS::FOLLOW_IS_FOLLOWED;
        }

        return CONSTANTS::FOLLOW_IS_NOT_FOLLOWED;
    }

    /**
     * Returns the number of followers or followed users per user_id
     *
     * @param $user_id
     * @param string $follow_type
     * @return mixed
     */
    public static function getCountByUserId($user_id, $follow_type = CONSTANTS::FOLLOW_FOLLOWED)
    {
        if ($follow_type == CONSTANTS::FOLLOW_FOLLOWER) {
            $follow_count = self::where('followed_user_id', $user_id)->count();      // This will count how many follower the user_id has
        } else {
            $follow_count = self::where('follower_user_id', $user_id)->count();      // This will count how many the user_id is following
        }

        return $follow_count;
    } // end getCountByUserId

}