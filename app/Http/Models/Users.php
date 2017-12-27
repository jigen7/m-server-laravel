<?php
namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Users extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    /**
     * Get user data based on Facebook ID
     *
     * @param $fb_id
     * @return array
     */
    public static function getByFbId($fb_id)
    {
        return self::where('facebook_id', $fb_id)->first();
    }

    /**
     * Get user data based on Twitter ID
     *
     * @param $twitter_id
     * @return array
     */
    public static function getByTwitterId($twitter_id)
    {
        return self::where('twitter_id', $twitter_id)->first();
    }

    /**
     * Get user's full name
     *
     * @return string
     */
    public function getUserFullName()
    {
        return $this->firstname . " " . $this->lastname;
    }

    /**
     * Get user's full name by ID
     *
     * @param $user_id
     * @return bool|string
     */
    public static function getFullNameById($user_id)
    {
        $user = self::find($user_id);
        if ($user) {
            return $user->firstname . " " . $user->lastname;
        }
        return false;
    }


    /**
     * Get users based on search key
     *
     * @param $search_key
     * @param $exclude_id
     * @return mixed
     */
    public static function userSearch($search_key, $exclude_id = false)
    {
        if (strpos($search_key, '@')) {
            $users = self::where('email', 'LIKE', "%$search_key%");
        } else {
            $users = self::where(function ($query) use ($search_key) {
                $escaped_key = escapeKey($search_key);

                $query->orWhere('email', 'LIKE', "$escaped_key@%")
                    ->orWhere(DB::raw('CONCAT(firstname, " ", lastname)'), 'LIKE', "%$escaped_key%");
            });
        }

        if ($exclude_id) {
            $users->where('id', '!=', $exclude_id);
        }

        return $users->orderBy('firstname', CONSTANTS::ORDER_ASC)->paginate(CONSTANTS::USER_SEARCH_PAGINATION_LIMIT);
    }

    /**
     * Add user information to the database
     *
     * @param $data
     * @throws \Exception
     * @return Users $this
     *
     */
    public function addUser($data)
    {
        $this->uuid = getUuid();
        $this->firstname = default_key_value(CONSTANTS::KEY_FIRSTNAME, $data, CONSTANTS::EMPTY_VALUE);
        $this->lastname = default_key_value(CONSTANTS::KEY_LASTNAME, $data, CONSTANTS::EMPTY_VALUE);
        $this->gender = default_key_value(CONSTANTS::KEY_GENDER, $data, CONSTANTS::EMPTY_VALUE);
        $this->age = default_key_value(CONSTANTS::KEY_AGE, $data, 0);
        $this->email = default_key_value(CONSTANTS::KEY_EMAIL, $data, CONSTANTS::EMPTY_VALUE);
        $this->income = default_key_value(CONSTANTS::KEY_INCOME, $data, 0);
        $this->nationality = default_key_value(CONSTANTS::KEY_NATIONALITY, $data, CONSTANTS::EMPTY_VALUE);
        $this->facebook_id = default_key_value(CONSTANTS::KEY_FACEBOOK_ID, $data, CONSTANTS::EMPTY_VALUE);
        $this->device_id = default_key_value(CONSTANTS::KEY_DEVICE_ID, $data, CONSTANTS::EMPTY_VALUE);
        $this->device_type = default_key_value(CONSTANTS::KEY_DEVICE_TYPE, $data, CONSTANTS::DEVICE_UNKNOWN);
        $current_date = date('Y-m-d H:i:s');
        $this->date_modified = $current_date;
        $this->date_created = $current_date;

        if ($this->device_type == CONSTANTS::DEVICE_UNKNOWN) {
            $this->notification = 0;
        } else {
            $this->disableSameDeviceId($this->device_id);
            $this->notification = 1;
        }

        try {
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Edit user information and save to database
     *
     * @param $data
     * @throws \Exception
     * @return Users $this
     *
     */
    public function editUser($data)
    {
        $this->firstname = default_key_value(CONSTANTS::KEY_FIRSTNAME, $data, $this->firstname);
        $this->lastname = default_key_value(CONSTANTS::KEY_LASTNAME, $data, $this->lastname);
        $this->gender = default_key_value(CONSTANTS::KEY_GENDER, $data, $this->gender);
        $this->age = default_key_value(CONSTANTS::KEY_AGE, $data, $this->age);
        $this->email = default_key_value(CONSTANTS::KEY_EMAIL, $data, $this->email);
        $this->income = default_key_value(CONSTANTS::KEY_INCOME, $data, $this->income);
        $this->nationality = default_key_value(CONSTANTS::KEY_NATIONALITY, $data, $this->nationality);
        $this->facebook_id = default_key_value(CONSTANTS::KEY_FACEBOOK_ID, $data, $this->facebook_id);
        $this->twitter_id = default_key_value(CONSTANTS::KEY_TWITTER_ID, $data, $this->twitter_id);
        $this->twitter_auth_token = default_key_value(CONSTANTS::KEY_TWITTER_AUTH_TOKEN, $data, $this->twitter_auth_token);
        $this->twitter_auth_secret = default_key_value(CONSTANTS::KEY_TWITTER_AUTH_SECRET, $data, $this->twitter_auth_secret);
        $this->device_id = default_key_value(CONSTANTS::KEY_DEVICE_ID, $data, $this->device_id);
        $this->device_type = default_key_value(CONSTANTS::KEY_DEVICE_TYPE, $data, $this->device_type);
        $this->date_modified = date('Y-m-d H:i:s');

        try {
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Enable user notification
     *
     * @param $user_id
     * @param $device_id
     * @param $device_type
     * @return $this|bool
     * @throws \Exception
     */
    public function enableNotification($user_id, $device_id, $device_type)
    {
        $this->device_id = $device_id;
        $this->device_type = checkDeviceType($device_type);

        if ($this->device_type === 'Unknown') {
            $this->notification = 0;
            return false;
        } else {
            $this->disableSameDeviceId($device_id, $user_id);
            $this->notification = 1;
        }

        try {
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Disable user notification
     *
     * @return $this
     * @throws \Exception
     */
    public function disableNotification()
    {
        $this->notification = 0;

        try {
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Disables notification for users with same device ID
     *
     * @param $device_id
     * @param bool $user_id
     */
    public function disableSameDeviceId($device_id, $user_id = false)
    {
        //For users who logged in on the same device
        $same_device_ids = New Users();

        if ($user_id) {
            $same_device_ids = $same_device_ids->where('id', '<>', $user_id);
        }

        $same_device_ids = $same_device_ids->where('device_id', $device_id)
            ->get();

        foreach ($same_device_ids as $same_device_user) {
            $same_device_user->notification = 0;
            $same_device_user->save();
        }
    }

    /**
     * Get the review, checkin, bookmark, follow, photos, comments, and notification data of a user
     *
     * @param $id
     * @param $viewer_id
     * @return array
     */
    public static function getStatistics($id, $viewer_id = false)
    {
        $user = self::find($id);

        if (!$user) {
            return array();
        }

        $user_array = ModelFormatter::userLongFormat($user);
        $user_array[KeyParser::review_count] = Reviews::getCountByUserId($id);
        $user_array[KeyParser::checkin_count] = CheckIns::getCountByUserId($id);
        $user_array[KeyParser::bookmark_count] = Bookmarks::getCountByUserId($id);
        $user_array[KeyParser::following_count] = Follow::getCountByUserId($id, CONSTANTS::FOLLOW_FOLLOWED);
        $user_array[KeyParser::follower_count] = Follow::getCountByUserId($id, CONSTANTS::FOLLOW_FOLLOWER);
        $user_array[KeyParser::photo_count] = Photos::getCountByUserId($id);
        $user_array[KeyParser::comment_count] = Comments::getCountByUserId($id);
        $user_array[KeyParser::unread_notification_count] = Notification::getNotificationByUserToCustomPaginate(
            CONSTANTS::NOTIFICATION_STATUS_UNREAD,
            CONSTANTS::ORDER_DESC,
            $id,
            null,
            true
        );
        $user_array[KeyParser::new_notification_count] = Notification::getNotificationByUserToCustomPaginate(
            CONSTANTS::NOTIFICATION_STATUS_NEW,
            CONSTANTS::ORDER_DESC,
            $id,
            null,
            true
        );

        if ($viewer_id) {
            $user_array[KeyParser::is_followed_by_viewer] = Follow::isFollowed($viewer_id, $id);
        }

        return $user_array;
    }

    /**
     * Return user IDs of all users with the most number of activities to the least
     *
     * @return mixed
     */
    public static function getUsersWithMostActivities()
    {
        return self::leftJoin('activities', function($join)
            {
                $join->on('activities.user_id', '=', 'users.id');
            })
            ->groupBy('users.id')
            ->orderBy(DB::raw('count(activities.id)'), CONSTANTS::ORDER_DESC)
            ->orderBy('user_id')
            ->get(['users.id']);
    }
}