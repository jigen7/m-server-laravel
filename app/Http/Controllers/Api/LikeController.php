<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Bookmarks;
use App\Http\Models\CheckIns;
use App\Http\Models\Comments;
use App\Http\Models\Follow;
use App\Http\Models\Like;
use App\Http\Models\Photos;
use App\Http\Models\Restaurants;
use App\Http\Models\Reviews;
use App\Http\Models\Users;
use Illuminate\Http\Request;

class LikeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Add like on review, checkin or photo
     *
     * @param $request
     * @return mixed
     */
    public function addAction(Request $request)
    {
        $data = $request->json()->get('Like');

        $type = $data['type'];
        $type_id = $data['type_id'];
        $user_id = $data['user_id'];

        $user_data = Users::find($user_id);

        if (is_null($user_data)) {
            return showErrorResponse('Invalid user');
        }

        $is_liked = Like::isLiked($user_id, $type, $type_id);

        if ($is_liked) {
            $like_count = Like::getCount($type, $type_id);

            $json_return[KeyParser::data] = array(
                KeyParser::type => $type,
                KeyParser::type_id => $type_id,
                KeyParser::user_id => $user_id,
                KeyParser::is_existing => CONSTANTS::LIKE_IS_EXISTING,
                KeyParser::like_count => $like_count
            );

            return response()->json($json_return);
        }

        switch ($type) {
            case CONSTANTS::REVIEW:
                $like_object = Reviews::find($type_id);
                $like_type = CONSTANTS::NOTIFICATION_TYPE_LIKE_REVIEW;

                if (!$like_object) {
                    $status_code = CONSTANTS::ERROR_CODE_REVIEW_MISSING;
                }
                break;
            case CONSTANTS::CHECKIN:
                $like_object = CheckIns::find($type_id);
                $like_type = CONSTANTS::NOTIFICATION_TYPE_LIKE_CHECKIN;

                if (!$like_object) {
                    $status_code = CONSTANTS::ERROR_CODE_CHECKIN_MISSING;
                }
                break;
            case CONSTANTS::PHOTO:
                $like_object = Photos::find($type_id);
                $like_type = CONSTANTS::NOTIFICATION_TYPE_LIKE_PHOTO;

                if (!$like_object) {
                    $status_code = CONSTANTS::ERROR_CODE_PHOTO_MISSING;
                }
                break;
            default:
                return showErrorResponse('Invalid type', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_INVALID_TYPE);
        }

        if (!isset($like_object)) {
            return showErrorResponse('Invalid type id', HTTP_ACCEPTED, $status_code);
        }

        if (!Restaurants::isExists($like_object->restaurant_id)) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        try {
            $like_data = new Like();
            $query_response = $like_data->addLike($type_id, $type, $user_id, $like_object, $like_type);
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        }

        $json_return[KeyParser::data] = array(
            KeyParser::type => $query_response->type,
            KeyParser::type_id => $query_response->type_id,
            KeyParser::user_id => $query_response->user_id,
            KeyParser::is_existing => CONSTANTS::LIKE_IS_NOT_EXISTING,
            KeyParser::like_count => Like::getCount($type, $type_id)
        );

        return response()->json($json_return);
    }

    /**
     * Delete like on review, checkin or photo
     *
     * @param $request
     * @return mixed
     */
    public function deleteAction(Request $request)
    {
        $type = $request->type;
        $type_id = $request->type_id;
        $user_id = $request->user_id;

        $error_msg = checkTypeId($type, $type_id);

        if ($error_msg) {
            return $error_msg;
        }

        $is_liked = Like::isLiked($user_id, $type, $type_id);

        if ($is_liked) {
            try {
                $like = new Like();
                $like->deleteLike($type, $type_id, $user_id);
            } catch (\Exception $e) {
                return showErrorResponse($e->getMessage());
            }
        }

        $json_return[KeyParser::data] = array(
            KeyParser::type => $type,
            KeyParser::type_id => $type_id,
            KeyParser::user_id => $user_id,
            KeyParser::like_count => Like::getCount($type, $type_id)
        );

        return response()->json($json_return);
    }


    /**
     * Get the list of users that likes the activity
     *
     * @param request $request
     * @return Response
     */
    public function likerListAction(Request $request)
    {
        $viewer_id = $request->viewer_id;
        $type = $request->type;
        $type_id = $request->type_id;

        $error_msg = checkTypeId($type, $type_id);

        if ($error_msg) {
            return $error_msg;
        }

        $liker_list = Like::getLikerList($type, $type_id);
        $json_return[KeyParser::data] = array();

        if ($liker_list) {
            foreach ($liker_list as $index => $liker) {
                $user_id = $liker->user_id;
                $user_data = Users::find($user_id);
                if($user_data){

                    $array = ModelFormatter::userLongFormat($user_data);
                    $array += array(
                        KeyParser::follower_count => Follow::getCountByUserId($user_id, CONSTANTS::FOLLOW_FOLLOWER),
                        KeyParser::review_count => Reviews::getCountByUserId($user_id),
                        KeyParser::is_followed_by_viewer => Follow::isFollowed($viewer_id, $user_id)
                    );
                    $json_return[KeyParser::data][] = $array;
                    unset($array);
                } // end of check $user_date
            }

            $json_return[KeyParser::like_count] = Like::getCount($type, $type_id);
        }

        $json_return[KeyParser::page] = array(
            KeyParser::current => $liker_list->currentPage(),
            KeyParser::number => $liker_list->lastPage()
        );

        return response()->json($json_return);
    }

}

?>