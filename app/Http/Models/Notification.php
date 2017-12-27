<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\ModelFormatter;
use Illuminate\Database\Eloquent\Model;
use PhpSpec\Exception\Exception;
use DateTime;
use Illuminate\Support\Facades\Config;
use App\Http\Helpers\KeyParser;
use Illuminate\Pagination\LengthAwarePaginator;

class Notification extends Model {

    protected $table = 'notification';

    public $timestamps = false;

    /*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Returns all notification data per user_id and status
     *
     * @param $user_to
     * @param $status
     * @param $count
     * @param $order
     *
     */
    public static function getNotificationByUserTo($user_to = false, $status = CONSTANTS::NOTIFICATION_STATUS_DELETED, $count = false, $order = CONSTANTS::ORDER_DESC)
    {
        $notifications = self::where('status', '<=', $status);

        if($user_to) {
            $notifications->where('user_id_to', $user_to);
        }

        if($count) {
            return $notifications->count();
        }

        return($notifications->orderBy('date_created', $order)
            ->paginate(CONSTANTS::NOTIFICATIONS_VIEW_PAGINATION_LIMIT));
    }

    /**
     * Returns all notification data per user_id and status using lengthAwarePagination
     *
     * @param int $status
     * @param string $order
     * @param bool $user_id_to
     * @param int current_page
     * @param boolean $count_only
     * @return LengthAwarePaginator
     */
    public static function getNotificationByUserToCustomPaginate($status = CONSTANTS::NOTIFICATION_STATUS_DELETED, $order = CONSTANTS::ORDER_DESC, $user_id_to = false, $current_page = CONSTANTS::FIRST_PAGE, $count_only = false)
    {
        $notifications = self::where('status', '<=', $status);

        if ($user_id_to) {
            $notifications->where('user_id_to', $user_id_to);
        }

        $notifications = $notifications->orderBy('date_created', $order)
            ->get();

        $data = array();
        $duplicate_activities = array();
        $notif_count = 0;

        foreach ($notifications as $notification) {
            $type_id = $notification->type_id;
            $type = $notification->type;
            $restaurant_id = $notification->restaurant_id;

            if ($restaurant_id != 0 && !Restaurants::isExists($restaurant_id)) {
                continue;
            }

            $from_user = Users::find($notification->user_id_from);

            if (!$count_only) {
                if($notification->status == CONSTANTS::NOTIFICATION_STATUS_NEW) {
                    $notification->updateStatus(CONSTANTS::NOTIFICATION_STATUS_UNREAD);
                }
            }

            if ($from_user && isset($duplicate_activities[$type][$type_id])) {
                $date_diff = strtotime($duplicate_activities[$type][$type_id][KeyParser::date_created]) - strtotime($notification->date_created);

                if($date_diff < CONSTANTS::DAY_SECOND_VALUE && $date_diff >= 0) {
                    $notif_index = $duplicate_activities[$type][$type_id][KeyParser::index];
                    $user_fullname = $from_user->getUserFullName();

                    if(!array_key_exists(KeyParser::usernames_from, $data[$notif_index][KeyParser::notification]) || !in_array($user_fullname, $data[$notif_index][KeyParser::notification][KeyParser::usernames_from])) {
                        $data[$notif_index][KeyParser::notification][KeyParser::usernames_from][] = $user_fullname;
                        $data[$notif_index][KeyParser::notification][KeyParser::users_from][] = $from_user->toArray();
                    }

                    continue;
                }
            }

            $data[$notif_count][KeyParser::notification] = ModelFormatter::notificationFormat($notification);
            $to_user = Users::find($notification->user_id_to);

            if ($to_user) {
                $data[$notif_count][KeyParser::notification] += array(
                    KeyParser::facebook_id_to => $to_user->facebook_id
                );
            }

            if ($from_user) {
                $data[$notif_count][KeyParser::notification] += array(
                    KeyParser::facebook_id_from => $from_user->facebook_id,
                    KeyParser::usernames_from => array($from_user->getUserFullName()),
                    KeyParser::users_from => array($from_user->toArray())
                );
            }

            $duplicate_activities[$type][$type_id] = array(
                KeyParser::date_created => $notification->date_created,
                KeyParser::index => $notif_count
            );

            $restaurant = Restaurants::find($notification->restaurant_id);

            if ($restaurant) {
                $data[$notif_count][KeyParser::notification] += array(
                    KeyParser::restaurant_name => $restaurant->name
                );
            }

            $notif_count++;
        }

        if($count_only) {
            return count($data);
        }

        $max_results = CONSTANTS::NOTIFICATIONS_VIEW_PAGINATION_LIMIT;
        $offset = ($current_page * $max_results) - $max_results;
        $notification_data = array_slice($data, $offset, $max_results);
        $paginated_data = new LengthAwarePaginator($notification_data, count($data), $max_results);

        return $paginated_data;
    }

    /*********************************** END ACCESSOR METHODS ************************************/



    /*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Add new notification data for Like
     *
     * @param $liker_id
     * @param $owner_id
     * @param $like_type
     * @param $type_id
     * @param $restaurant_id
     * @return Notification
     * @throws \Exception
     */
    public function addLikeNotification($liker_id, $owner_id, $like_type, $type_id, $restaurant_id)
    {
        try {

            $this->user_id_from = $liker_id;
            $this->user_id_to = $owner_id;
            $this->type = $like_type;
            $this->type_id = $type_id;
            $this->restaurant_id = $restaurant_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();
            $this->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    } // end addLikeNotification

    /**
     * Add new notification data for comments
     *
     * @param $user_id_from
     * @param $user_id_to
     * @param $type
     * @param $type_id
     * @param $restaurant_id
     * @return $this
     * @throws \Exception
     */
    public function addCommentNotification($user_id_from, $user_id_to, $type, $type_id, $restaurant_id)
    {
        try {
            $this->user_id_from = $user_id_from;
            $this->user_id_to = $user_id_to;
            $this->type = $type;
            $this->type_id = $type_id;
            $this->restaurant_id = $restaurant_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();
            $this->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Add new notification data
     * New Follower
     *
     * @param $follower_id
     * @param $followed_id
     * @throws \Exception
     * @return $this
     */
    public function addNotificationNewFollower($follower_id, $followed_id)
    {
        try {
            $this->user_id_from = $follower_id;
            $this->user_id_to = $followed_id;
            $this->type = CONSTANTS::NOTIFICATION_TYPE_NEW_FOLLOWER;
            $this->type_id = $follower_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();
            $this->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }
           return $this;
       } // end addNotification

     /** Add new notification data for Review
     * @param $user_id_from
     * @param $user_id_to
     * @param $type_id
     * @param $restaurant_id
     * @throws \Exception
     *
     * @return Notification object
     */
    public function addNotificationNewReview($user_id_from, $user_id_to, $type_id, $restaurant_id)
    {
        try {
            $this->user_id_from = $user_id_from;
            $this->user_id_to = $user_id_to;
            $this->type = CONSTANTS::NOTIFICATION_TYPE_FOLLOWING_REVIEW;
            $this->type_id = $type_id;
            $this->restaurant_id = $restaurant_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();
            $this->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }
    } // end addNotificationNewReview

    /** Add new notification data for Checkin
     *
     * @param $user_id_from
     * @param $user_id_to
     * @param $type_id
     * @param $restaurant_id
     * @throws \Exception
     */
    public function addNotificationNewCheckin($user_id_from, $user_id_to, $type_id, $restaurant_id)
    {
        try {
            $notification_data = new Notification();
            $notification_data->user_id_from = $user_id_from;
            $notification_data->user_id_to = $user_id_to;
            $notification_data->type = CONSTANTS::NOTIFICATION_TYPE_FOLLOWING_CHECKIN;
            $notification_data->type_id = $type_id;
            $notification_data->restaurant_id = $restaurant_id;
            $notification_data->date_created = date('Y-m-d H:i:s');
            $notification_data->save();
            $notification_data->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }
    } // end addNotificationNewCheckin

    /** Add new notification data for Photo
     *
     * @param $user_id_from
     * @param $user_id_to
     * @param $type_id
     * @param $restaurant_id
     * @throws \Exception
     */
    public function addNotificationNewPhoto($user_id_from, $user_id_to, $type_id, $restaurant_id)
    {
        try {
            $notification_data = new Notification();
            $notification_data->user_id_from = $user_id_from;
            $notification_data->user_id_to = $user_id_to;
            $notification_data->type = CONSTANTS::NOTIFICATION_TYPE_UPLOADED_PHOTO;
            $notification_data->type_id = $type_id;
            $notification_data->restaurant_id = $restaurant_id;
            $notification_data->date_created = date('Y-m-d H:i:s');
            $notification_data->save();
            $notification_data->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add general notification data
     *
     * @param Array $params
     * @throws \Exception
     *
     * @return Notification object
     *
     */
    public function addGeneralNotification($params) {
        try {
            $this->user_id_from = $params[CONSTANTS::KEY_USER_ID_FROM];
            $this->user_id_to = $params[CONSTANTS::KEY_USER_ID_TO];
            $this->type = $params[CONSTANTS::KEY_TYPE];
            $this->type_id = $params[CONSTANTS::KEY_TYPE_ID];
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();
            $this->sendPush();
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }


    /**
     * Build message for notification payload
     *
     * @return string
     */
    public function buildMessage()
    {
        $type = $this->type;
        $user_from = Users::find($this->user_id_from);
        $username_from = $user_from->getUserFullName();
        $restaurant_name = '';

        if ($this->restaurant_id != 0) {
            $restaurant = Restaurants::find($this->restaurant_id);
            $restaurant_name = $restaurant->name;
        }

        switch($type) {
            case CONSTANTS::NOTIFICATION_TYPE_NEW_FOLLOWER:
                $message = "$username_from is now following you!";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_LIKE_REVIEW:
                $message = "$username_from liked your review.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_LIKE_CHECKIN:
                $message = "$username_from liked your check-in at $restaurant_name.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_LIKE_PHOTO:
                $message = "$username_from liked your photo";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_FRIEND_JOIN:
                $message = "Your facebook friend, $username_from started using Masarap!";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_CHECKIN:
                $message = "$username_from commented on your check-in at $restaurant_name.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_PHOTO:
                $message = "$username_from commented on your photo.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_REVIEW:
                $message = "$username_from commented on your review.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_FOLLOWING_CHECKIN:
                $message = "$username_from checked in at $restaurant_name.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_FOLLOWING_REVIEW:
                $message = "$username_from posted a review about $restaurant_name.";
                break;
            case CONSTANTS::NOTIFICATION_TYPE_UPLOADED_PHOTO:
                $message = "$username_from added a new photo for $restaurant_name.";
                break;
            default:
                $message = "You have new updates from Masarap.";
        }

        return $message;
    }

    /**
     * Update notification status
     *
     * @params $status
     * @throws \Exception
     * @return Notification object
     */
    public function updateStatus($status)
    {
        try {
            $this->status = $status;

            if ($status == CONSTANTS::NOTIFICATION_STATUS_READ) {
                $this->date_read = date('Y-m-d H:i:s');
            }

            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }
    /********************** Start of Send Push Notification Functions *************************/

    /**
     * Retry sendPush for notifications not older than 5 minutes ago
     *
     * @return mixed
     */
    public static function retrySendPush()
    {
        $date = new DateTime;
        $date->modify('-5 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $notifications = self::where('pushed' , 0)
            ->where('date_created', '>=', $formatted_date)
            ->get();

        foreach ($notifications as $notification) {
            $notification->sendPush();
        }
    }

    /**
     * Send Push Notification using either APNS(Apple) or GCM(Android)
     *
     * @throws \Exception
     * @return result
     */
    private function sendPush()
    {
        $user_to = Users::find($this->user_id_to);
        if(!$user_to) {
            throw new Exception('User for notification does not exist');
        }
        $registration_ids = array($user_to->device_id);

        $is_enabled = $user_to->notification;
        if($is_enabled == 0) {
            return false;
        }

        switch ($user_to->device_type) {
            case CONSTANTS::DEVICE_ANDROID:
                $result = $this->sendToGCM($registration_ids);
                break;
            case CONSTANTS::DEVICE_IOS:
                $result = $this->sendToAPNS();
                break;
            default:
                throw new Exception("Unknown device");
        }


        $this->pushed = 1;
        $this->save();
        return $result;
    }

    /**
     * Send push notification through GCM server
     *
     * @param Array $registration_ids = device_ids
     * @return result
     *
     */
    private function sendToGCM ($registration_ids)
    {
        $user_notification_count = $this->getNotificationByUserTo($this->user_id_to, CONSTANTS::NOTIFICATION_STATUS_NEW, true);
        $type = $this->type;

        $activities = array();
        $user_count = 0;
        $username_array = array();
        $activity_type = 0;

        //Time Interval for notification groupings = 1 Day (86400 seconds)
        $date_range = array(
            KeyParser::date_from => date('Y-m-d H:i:s',time()-CONSTANTS::DAY_SECOND_VALUE),
            KeyParser::date_to => date('Y-m-d H:i:s')
        );

        //Build usernames_from array()
        if(strpos($type, 'photo')) {
            $activity_type = 5;
        } elseif(strpos($type, 'checkin')) {
            $activity_type = 2;
        } elseif(strpos($type, 'review')) {
            $activity_type = 1;
        }

        if($type == CONSTANTS::NOTIFICATION_TYPE_LIKE_CHECKIN || $type == CONSTANTS::NOTIFICATION_TYPE_LIKE_REVIEW || $type == CONSTANTS::NOTIFICATION_TYPE_LIKE_PHOTO) {
            $activities = Like::getLikerList($activity_type, $this->type_id, false, $date_range);
        } else if($type == CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_CHECKIN || $type == CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_REVIEW || $type == CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_PHOTO) {
            $activities = Comments::getByType($activity_type, $this->type_id, $date_range);
        } else {
            $user_from = Users::find($this->user_id_from);
            $username_array[] = $user_from->getUserFullName();
        }

        foreach($activities as $activity) {
            if($activity->user_id == $this->user_id_to) {
                continue;
            }
            $user_count++;
            $user_fullname = Users::getFullNameById($activity->user_id);
            if($user_count <= CONSTANTS::NOTIFICATION_USER_GROUP_LIMIT && !in_array($user_fullname, $username_array)) {
                $username_array[] = $user_fullname;
            }
        }

        // Build payload
        $payload = array(
            KeyParser::id => $this->id,
            KeyParser::usernames_from => $username_array,
            KeyParser::count => $user_notification_count,
            KeyParser::type => $this->type,
            KeyParser::type_id => $this->type_id,
            KeyParser::restaurant_id => $this->restaurant_id,
            KeyParser::user_id_from => $this->user_id_from,
            KeyParser::user_id_to => $this->user_id_to,
            //Push notification indices for 1.1.1 and below
            'typeid' => '0',
            'useridfrom' => '0',
            'useridto' => '0',
            'restaurantid' => '0',
            'message' => $this->buildMessage()
        );

        $restaurant_name = Restaurants::getRestaurantNameById($this->restaurant_id);
        if($restaurant_name) {
            $payload[KeyParser::restaurant_name] = $restaurant_name;
        }

        $fields = array
        (
            KeyParser::registration_ids => $registration_ids,
            KeyParser::data              => $payload
        );

        $headers = array
        (
            'Authorization: key=' . GCM_API_KEY,
            'Content-Type: application/json'
        );

        //Prepare cURL
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, GCM_API_URL );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        return $result;
    }

    public function sendToAPNS()
    {
        $user_to = Users::find($this->user_id_to);
        $device_id = $user_to->device_id;

        // Build payload
        $aps = array(
            KeyParser::alert => $this->buildMessage(),
            KeyParser::sound => CONSTANTS::APNS_SOUND_DEFAULT,
            KeyParser::badge => $this->getNotificationByUserTo($this->user_id_to, CONSTANTS::NOTIFICATION_STATUS_NEW, true)
        );

        $custom = array(
            KeyParser::id => $this->id,
            KeyParser::type => $this->type,
            KeyParser::type_id => $this->type_id,
            KeyParser::restaurant_id => $this->restaurant_id,
            KeyParser::user_id_from => $this->user_id_from,
            KeyParser::user_id_to => $this->user_id_to
        );

        $body = array('aps' => $aps, 'custom' => $custom);
        $payload = json_encode($body);
        $json_size = strlen($payload);

        $pem_file = Config::get('apns.apns_pem_file');
        $host = Config::get('apns.apns_host');
        $passphrase = Config::get('apns.apns_passphrase');

        // Create socket
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_file);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        try
        {
            $fp = stream_socket_client($host, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        } catch (\Exception $e)
        {
            return array(
                KeyParser::status => CONSTANTS::FAIL,
                KeyParser::message => CONSTANTS::NOTIFICATION_SERVER_FAILURE,
                KeyParser::host => $host,
                KeyParser::pem_file => $pem_file,
                KeyParser::error => $e->getMessage()
            );
        }
        if (!$fp)
        {
            return array(
                KeyParser::status => CONSTANTS::FAIL,
                KeyParser::message => CONSTANTS::NOTIFICATION_SERVER_FAILURE,
                KeyParser::host => $host,
                KeyParser::pem_file => $pem_file,
                KeyParser::error =>  CONSTANTS::NOTIFICATION_SERVER_FAILURE);
        }

        // Build send data
        try
        {
            $send_data = chr(0) .chr(0).chr(32). pack('H*', $device_id) .chr(0).chr($json_size) . $payload;
        } catch (\ErrorException $e)
        {
            return array(KeyParser::status => CONSTANTS::FAIL, KeyParser::message => CONSTANTS::NOTIFICATION_INVALID_DEVICE_ID);
        }

        // Send
        $result = fwrite($fp, $send_data);

        if ($result !== false)
            $result = $body;

        fclose($fp);
        return $result;
    }
}