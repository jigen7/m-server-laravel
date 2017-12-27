<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ModelFormatter;
use App\Http\Helpers\CONSTANTS;
use App\Http\Models\Follow;
use App\Http\Models\Reviews;
use App\Http\Models\Users;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Illuminate\Http\Request;
use App\Http\Helpers\KeyParser;
use Illuminate\Support\Facades\Config;

class FollowController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Follow another user
     *
     * @param Request $request
     * @return Response
     */
    public function followAction(Request $request)
    {
        $data = $request->json()->get('Follow');

        if (!validateParameters($data)) {
            return showErrorResponse("Format should be: {'Follow': {'follower_id': <int>, 'following_id: <int>}}", HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($data['follower_id'])) {
            $follower_id = $data['follower_id'];
        } else {
            $follower_id = convertFbIdToId($data['follower_fb_id']);
        }

        if (isset($data['following_id'])) {
            $following_id = $data['following_id'];
        } else {
            $following_id = convertFbIdToId($data['following_fb_id']);
        }

        if ($follower_id === false || $following_id === false) {
            return showErrorResponse('No such user');
        }

        try {
            $follow_data = new Follow();
            $follow_data->addFollow($follower_id, $following_id);
        } catch (\Exception $e) {
            if ($e->getCode() == INTEGRITY_CONSTRAINT_VIOLATION) {
                return showErrorResponse('You are trying to follow yourself or already following the user', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_USER_ALREADY_FOLLOWED);
            } else {
                return showErrorResponse($e->getMessage());
            }
        }

        $json_return[KeyParser::data] = array(
            KeyParser::user => Users::getStatistics($following_id),
            KeyParser::message => 'Success'
        );

        return response()->json($json_return);
    }

    /**
     * Follow multiple users
     * Used in registration when a user logs in to FB for the first time
     *
     * @param Request $request
     * @return Response
     */
    public function followManyAction(Request $request)
    {
        $json_return[KeyParser::data] = array(
            KeyParser::users        => array(),
            KeyParser::failed_users => array()
        );

        $data = $request->json()->get('Follow');

        if ( (!isset($data['follower_id']) && !isset($data['follower_fb_id'])) ||
            (!isset($data['following_ids']) && !isset($data['following_fb_ids'])) ) {
            return showErrorResponse("Format should be: {'Follow': {'follower_fb_id': <int>, 'following_fb_ids: <array:int>}}", HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($data['follower_id'])) {
            $follower_id = $data['follower_id'];
        } else {
            $follower_id = convertFbIdToId($data['follower_fb_id']);
        }

        if (isset($data['following_ids'])) {
            $following_ids = $data['following_ids'];

            if (!is_array($following_ids)) {
                $following_ids = array($following_ids);
            }
        } else {
            $following_fb_ids = $data['following_fb_ids'];

            if (!is_array($following_fb_ids)) {
                $following_fb_ids = array($following_fb_ids);
            }

            $following_ids = array();

            foreach ($following_fb_ids as $following_fb_id) {
                $following_id = convertFbIdToId($following_fb_id);

                if ($following_id) {
                    $following_ids[] = $following_id;
                } else {
                    $json_return[KeyParser::data][KeyParser::failed_users][] = $following_fb_id;
                }
            }
        }

        foreach ($following_ids as $following_id) {
            if (isSameUser($follower_id, $following_id)) {
                $json_return[KeyParser::data][KeyParser::failed_users][] = $following_id;
                continue;
            }

            try {
                $follow_data = new Follow();
                $follow_data->addFollow($follower_id, $following_id);
            } catch (\Exception $e) {
                if ($e->getCode() == INTEGRITY_CONSTRAINT_VIOLATION) {
                    $json_return[KeyParser::data][KeyParser::failed_users][] = $following_id;
                    continue;
                } else {
                    return showErrorResponse($e->getMessage());
                }
            }

            $user_data = Users::find($following_id);

            $json_return[KeyParser::data][KeyParser::users][] = array(
                KeyParser::id => $following_id,
                KeyParser::facebook_id => $user_data->facebook_id,
                KeyParser::follower_count => Follow::getCountByUserId($user_data->id, CONSTANTS::FOLLOW_FOLLOWER),
                KeyParser::following_count => Follow::getCountByUserId($user_data->id, CONSTANTS::FOLLOW_FOLLOWED),
            );
        }

        return response()->json($json_return);
    }

    /**
     * Unfollow a user
     *
     * @param Request $request
     * @return Response
     */
    public function unfollowAction (Request $request)
    {
        $data = $request->json()->get('Follow');

        if (!validateParameters($data)) {
            return showErrorResponse("Format should be: {'Follow': {'follower_id': <int>, 'following_id: <int>}}", HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($data['follower_id'])) {
            $follower_id = $data['follower_id'];
        } else {
            $follower_id = convertFbIdToId($data['follower_fb_id']);
        }

        if (isset($data['following_id'])) {
            $following_id = $data['following_id'];
        } else {
            $following_id = convertFbIdToId($data['following_fb_id']);
        }

        if ($follower_id === false || $following_id === false) {
            return showErrorResponse('No such user', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        try {
            $follow_data = new Follow();
            $follow_data->unfollowUser($follower_id, $following_id);

            // TODO: Add Notification::deleteByFromToUserIds() here
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        }

        $json_return[KeyParser::data] = array(
            KeyParser::user => Users::getStatistics($following_id),
            KeyParser::message => 'Success'
        );

        return response()->json($json_return);
    }


    /**
     * Get the target user's follower list
     * Displays if the viewer is following the users in the target's follower list
     *
     * @param Request $request
     * @return Response
     */
    public function followersAction(Request $request)
    {
        $json_return[KeyParser::data] = array(
            KeyParser::users => array()
        );

        $user_id = $request->user_id;
        $viewer_id = $request->viewer_id;

        if (!$user_id) {
            return showErrorResponse('Incorrect request parameters', HTTP_UNPROCESSABLE_ENTITY);
        }

        $follower_users = Follow::getFollowerUsers($user_id);

        if ($follower_users) {
            foreach ($follower_users as $key => $follower_user) {
                $follower_user_id = $follower_user->id;

                $json_return[KeyParser::data][KeyParser::users][$key] = ModelFormatter::userLongFormat($follower_user);
                $json_return[KeyParser::data][KeyParser::users][$key] += array (
                    KeyParser::is_followed_by_user   => Follow::isFollowed($user_id, $follower_user_id),
                    KeyParser::follower_count        => Follow::getCountByUserId($follower_user_id, CONSTANTS::FOLLOW_FOLLOWER),
                    KeyParser::review_count          => Reviews::getCountByUserId($follower_user_id),
                    KeyParser::is_followed_by_viewer => Follow::isFollowed($viewer_id, $follower_user_id)
                );
            }
        }

        $json_return[KeyParser::page] = array(
            KeyParser::current => $follower_users->currentPage(),
            KeyParser::number =>  $follower_users->lastPage()
        );

        return response()->json($json_return);
    }

    /**
     * Get the target user's following list
     * Displays if the viewer is also following the users in the target's following list
     *
     * @param Request $request
     * @return Response
     */
    public function followingAction(Request $request)
    {
        $json_return[KeyParser::data] = array(
            KeyParser::users => array()
        );

        $user_id = $request->user_id;
        $viewer_id = $request->viewer_id;

        if (!$user_id) {
            return showErrorResponse('Incorrect request parameters', HTTP_UNPROCESSABLE_ENTITY);
        }

        $followed_users = Follow::getFollowedUsers($user_id);

        if ($followed_users) {
            foreach ($followed_users as $key => $followed_user) {
                $followed_user_id = $followed_user->id;

                $json_return[KeyParser::data][KeyParser::users][$key] = ModelFormatter::userLongFormat($followed_user);
                $json_return[KeyParser::data][KeyParser::users][$key] += array (
                    KeyParser::is_followed_by_user   => CONSTANTS::FOLLOW_IS_FOLLOWED,
                    KeyParser::follower_count        => Follow::getCountByUserId($followed_user_id, CONSTANTS::FOLLOW_FOLLOWER),
                    KeyParser::review_count          => Reviews::getCountByUserId($followed_user_id),
                    KeyParser::is_followed_by_viewer => Follow::isFollowed($viewer_id, $followed_user_id)
                );
            }
        }

        $json_return[KeyParser::page] = array(
            KeyParser::current => $followed_users->currentPage(),
            KeyParser::number =>  $followed_users->lastPage()
        );

        return response()->json($json_return);
    }

    /**
     * Returns a list of Facebook friends who are using Masarap which you have not yet followed
     *
     * @param Request $request
     * @return Response
     * @throws FacebookRequestException
     */
    public function followFBUsersAction(Request $request)
    {
        $json_return[KeyParser::data] = array(
            KeyParser::users => array()
        );

        $data = $request->json()->get('User');

        if (!$data) {
            return showErrorResponse('Incorrect request parameters', HTTP_UNPROCESSABLE_ENTITY);
        }

        $user_id = $data[CONSTANTS::KEY_ID];
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
        $facebook_response = (new FacebookRequest($facebook_session, 'GET', '/me/friends?limit=5000'))->execute();
        $friend_list = $facebook_response->getResponse();
        $facebook_friends = array();
        $followed_users = array();
        $friend_count = 0;

        foreach ($friend_list->data as $friend) {
            $friend_user = Users::getByFbId($friend->id);

            if (!$friend_user) {
                continue;
            }

            $is_followed = Follow::isFollowed($user_id, $friend_user->id);
            $follower_count = Follow::getCountByUserId($friend_user->id, CONSTANTS::FOLLOW_FOLLOWER);
            $review_count = Reviews::getCountByUserId($friend_user->id);

            if (!$is_followed && $friend_user->id != $user_id) {
                $facebook_friends[$friend_count] = ModelFormatter::userFormat($friend_user);
                $facebook_friends[$friend_count] += array(
                    KeyParser::follower_count => $follower_count,
                    KeyParser::review_count => $review_count,
                    KeyParser::is_followed_by_viewer => $is_followed
                );
            }  elseif ($is_followed && $friend_user->id != $user_id) {
                $followed_users[$friend_count] = ModelFormatter::userFormat($friend_user);
                $followed_users[$friend_count] += array(
                    KeyParser::follower_count => $follower_count,
                    KeyParser::review_count => $review_count,
                    KeyParser::is_followed_by_viewer => $is_followed
                );
            }

            $friend_count++;
        }

        $facebook_friends = array_merge($facebook_friends, $followed_users);
        $json_return[KeyParser::data][KeyParser::users] = $facebook_friends;

        return response()->json($json_return);
    }

    /**
     * Returns a list of Twitter friends which you have not yet followed
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function followTwitterUsersAction(Request $request)
    {
        $json_return[KeyParser::data] = array(
            KeyParser::users => array(),
            Keyparser::is_private => CONSTANTS::TWITTER_PUBLIC
        );

        $user_id = $request->json()->get('User')['user_id'];
        $twitter_id = $request->json()->get('User')['twitter_id'];

        if (!$twitter_id || !$user_id) {
            return showErrorResponse('Failed to access Twitter account');
        }

        $settings = array(
            'oauth_access_token' => Config::get('services.twitter.oauth_access_token'),
            'oauth_access_token_secret' => Config::get('services.twitter.oauth_access_token_secret'),
            'consumer_key' => Config::get('services.twitter.consumer_key'),
            'consumer_secret' => Config::get('services.twitter.consumer_secret'),
        );

        $url = 'https://api.twitter.com/1.1/friends/ids.json';
        $getfield = "?user_id=$twitter_id";
        $requestMethod = 'GET';

        $twitter = new \TwitterAPIExchange($settings);

        $response = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $friends = json_decode($response);
        $twitter_friends = array();
        $followed_users = array();
        $friend_count = 0;

        if (isset($friends->error)) {
            $json_return[KeyParser::data][Keyparser::is_private] = CONSTANTS::TWITTER_PRIVATE;
        } else {
            foreach ($friends->ids as $friend_id) {
                $friend_user = Users::getByTwitterId($friend_id);

                if (!$friend_user) {
                    continue;
                }

                $is_followed = Follow::isFollowed($user_id, $friend_user->id);
                $follower_count = Follow::getCountByUserId($friend_user->id, CONSTANTS::FOLLOW_FOLLOWER);
                $review_count = Reviews::getCountByUserId($friend_user->id);

                if (!$is_followed && $friend_user->id != $user_id) {
                    $twitter_friends[$friend_count] = ModelFormatter::userFormat($friend_user);
                    $twitter_friends[$friend_count] += array(
                        KeyParser::follower_count => $follower_count,
                        KeyParser::review_count => $review_count,
                        KeyParser::is_followed_by_viewer => $is_followed
                    );
                } elseif ($is_followed && $friend_user->id != $user_id) {
                    $followed_users[$friend_count] = ModelFormatter::userFormat($friend_user);
                    $followed_users[$friend_count] += array(
                        KeyParser::follower_count => $follower_count,
                        KeyParser::review_count => $review_count,
                        KeyParser::is_followed_by_viewer => $is_followed
                    );
                }

                $friend_count++;
            }

            $twitter_friends = array_merge($twitter_friends, $followed_users);
            $json_return[KeyParser::data][KeyParser::users] = $twitter_friends;
        }

        return response()->json($json_return);
    }

} // End of Class

?>