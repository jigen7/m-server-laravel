<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Follow;
use App\Http\Models\Notification;
use App\Http\Models\Users;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Displays individual user information
     * route: users/view?user_id=<user_id>&fb_id=<facebook_id>
     *
     * @param Request $request
     * @return Response
     */
    public function viewAction(Request $request)
    {
        $user_id = $request->user_id;
        $fb_id = $request->fb_id;

        if (!$user_id && !$fb_id) {
            return showErrorResponse('Request data must include either user ID or Facebook ID', HTTP_UNPROCESSABLE_ENTITY);
        }

        if($user_id) {
            $user = Users::find($user_id);
        } elseif ($fb_id) {
            $user = Users::getByFbId($fb_id);
        }

        if (!$user) {
            $data[KeyParser::data][KeyParser::user] = array();
            return response()->json($data);
        }

        $data[KeyParser::user] = ModelFormatter::userLongFormat($user);
        $json_return[KeyParser::data] = $data;

        return response()->json($json_return);
    }

    /**
     * Create a new user
     *
     * @param Request $request
     * @return Response
     * @throws FacebookRequestException
     */
    public function addAction(Request $request)
    {
        $data = $request->json()->get('User');

        if(!$data) {
            return showErrorResponse('Incorrect request parameters', HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $new_user = new Users();
            $new_user->addUser($data);
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        }

        //Send push notifications to all Facebook friends who are using Masarap
        $fb_access_token = $data[CONSTANTS::KEY_FB_ACCESS_TOKEN];

        if(!$fb_access_token) {
            return showErrorResponse('Failed to access Facebook account');
        }

        FacebookSession::setDefaultApplication(
            Config::get('services.facebook.client_id'),
            Config::get('services.facebook.client_secret')
        );

        FacebookSession::enableAppSecretProof(false);

        $facebook_session = new FacebookSession($fb_access_token);
        $facebook_response = (new FacebookRequest($facebook_session, 'GET', '/me/friends/'))->execute();
        $friend_list = $facebook_response->getResponse();

        $failed_notifications = array();

        foreach($friend_list->data as $friend) {
            $friend_user = Users::getByFbId($friend->id);

            if (!$friend_user) {
                continue;
            }

            $params = array(
                CONSTANTS::KEY_USER_ID_FROM => $new_user->id,
                CONSTANTS::KEY_USER_ID_TO => $friend_user->id,
                CONSTANTS::KEY_TYPE => CONSTANTS::NOTIFICATION_TYPE_FRIEND_JOIN,
                CONSTANTS::KEY_TYPE_ID => $new_user->id
            );

            try {
                $notification = new Notification();
                $notification->addGeneralNotification($params);
            } catch (\Exception $e) {
                $failed_notifications[] = $friend_user->id;
            }
        }

        $json_return[KeyParser::data] = array(
            KeyParser::user => ModelFormatter::userLongFormat($new_user),
            KeyParser::message => 'User successfully registered and push notifications are sent to Facebook friends',
            KeyParser::unsent_notifications => $failed_notifications
        );

        return response()->json($json_return);
    }


    /**
     * Edit existing user information
     *
     * @param Request $request
     * @param $user_id
     * @return Response
     */
    public function editAction(Request $request, $user_id)
    {
        $data = $request->json()->get('User');

        if (!$data) {
            return showErrorResponse('Incorrect request parameters', HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Users::find($user_id);

        if (!$user) {
            return showErrorResponse('User not found');
        };

        $user->editUser($data);

        $json_return[KeyParser::data][KeyParser::user] = $user;

        return response()->json($json_return);
    }

    /**
     * Search users
     *
     * @return Response
     */
    public function searchAction()
    {
        $search_key = Input::get('key');
        $viewer_id = Input::get('viewer_id', false);

        $users = Users::userSearch($search_key, $viewer_id);
        $data[KeyParser::users] = array();

        foreach ($users as $index => $user) {
            $data[KeyParser::users][$index] = Users::getStatistics($user->id, $viewer_id);
        }

        $page = array(
            KeyParser::current => $users->currentPage(),
            KeyParser::number => $users->lastPage()
        );

        $json_return = array(
            KeyParser::data => $data,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    }

    /**
     * Enable user's notification
     *
     * @param Request $request
     * @return response
     */
    public function enableNotificationAction(Request $request)
    {
        $data = $request->json()->get('User');

        if (!isset($data[CONSTANTS::KEY_USER_ID]) ||
            !isset($data[CONSTANTS::KEY_DEVICE_ID]) ||
            !isset($data[CONSTANTS::KEY_DEVICE_TYPE])) {
            $message = "Format should be: {'User': {'user_id': <int>, 'device_id': <string>, 'device_type': <string>}}";

            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Users::find($data[CONSTANTS::KEY_USER_ID]);


        if (!$user) {
            return showErrorResponse('User not found');
        }

        $user->enableNotification($data[CONSTANTS::KEY_USER_ID], $data[CONSTANTS::KEY_DEVICE_ID], $data[CONSTANTS::KEY_DEVICE_TYPE]);

        $json_return[KeyParser::data][KeyParser::user] = Users::getStatistics($user->id);

        return response()->json($json_return);
    }

    /**
     * Disable user's notification
     *
     * @param Request $request
     * @return response
     */
    public function disableNotificationAction(Request $request)
    {
        $data = $request->json()->get('User');

        $user = Users::find($data[CONSTANTS::KEY_USER_ID]);

        if (!$user) {
            return showErrorResponse('User not found');
        }

        $old_device_id = $user->device_id;
        $new_device_id = $data[CONSTANTS::KEY_DEVICE_ID];

        if (!$old_device_id && $old_device_id != $new_device_id) {
            return showErrorResponse('The user is registered on an old device', HTTP_ACCEPTED);
        }

        $user->disableNotification();

        $json_return[KeyParser::data][KeyParser::user] = Users::getStatistics($user->id);

        return response()->json($json_return);
    }

    /**
     * Displays individual user information
     * route: /users/viewstats/<user_id>?viewer_id=<viewer_id>
     *
     * @param $user_id
     * @return Response
     */
    public function viewStatisticsAction($user_id)
    {
        $viewer_id = Input::get('viewer_id', false);
        $user = Users::getStatistics($user_id, $viewer_id);

        if (!$user) {
            return showErrorResponse('User not found');
        };

        $json_return[KeyParser::data][KeyParser::user] = $user;

        return response()->json($json_return);
    }

    /**
     * Get list of users with the most number of activities
     * Prioritize users that are not being followed yet
     *
     * @param $user_id
     * @return Response
     */
    public function viewFeaturedUsersAction($user_id)
    {
        $followed_users = array();
        $featured_users = array();
        $json_return[KeyParser::data] = array();

        $users = Users::getUsersWithMostActivities()->toArray();
        $all_followed_users = Follow::getFollowedUserIds($user_id);

        foreach ($users as $user) {
            $is_followed = in_array($user['id'], $all_followed_users);

            if ($user['id'] != $user_id) {
                if ($is_followed) {
                    $followed_users[] = $user['id'];
                } else {
                    $featured_users[] = $user['id'];
                }
            }
        }

        $featured_users = array_merge($featured_users, $followed_users);
        $featured_users = array_slice($featured_users, 0, 20);

        foreach($featured_users as $index => $featured_user) {
            $json_return[KeyParser::data][KeyParser::users][$index] = Users::getStatistics($featured_user, $user_id);
        }

        return response()->json($json_return);
    }

} // End of Class

?>