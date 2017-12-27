<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Helpers\NgWord;
use App\Http\Models\Activities;
use App\Http\Models\CheckIns;
use App\Http\Models\Comments;
use App\Http\Models\Follow;
use App\Http\Models\Notification;
use App\Http\Models\Photos;
use App\Http\Models\Restaurants;
use App\Http\Models\Users;
use Illuminate\Http\Request;
use DB;
use App\Http\Helpers\Utilities\FileUtilities;

class CheckinController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }
    /**
     * View Checkin by Id
     * route: /checkins/view/{id}
     *
     * @param @id
     * @return Response
     */
    public function viewAction($id)
    {
        $json_return = array();
        $checkin = CheckIns::find($id);

        if (!$checkin){
            return showErrorResponse('Checkin not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_CHECKIN_MISSING);
        }

        $restaurant = Restaurants::find($checkin->restaurant_id);
        if(!$restaurant) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        $user = Users::find($checkin->user_id);

        $photos = Photos::getByType(CONSTANTS::CHECKIN, $checkin->id);
        $photos_array = Photos::convertPhotosToArray($photos);

        $comments = Comments::getByType(CONSTANTS::REVIEW, $checkin->id);
        $comments_array = array();

        if ($comments){
            foreach ($comments as $comment){
                $comments_array[] = ModelFormatter::commentFormat($comment);
            }
        }

        $json_return[KeyParser::data] = array(
            KeyParser::checkin => ModelFormatter::checkinFormat($checkin),
            KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
            KeyParser::user   => ModelFormatter::userLongFormat($user),
            KeyParser::photos => $photos_array,
        );

        return response()->json($json_return);
    } // end of viewAction


    /**
     * Get aAll Checkins By User Id
     * route: /checkins/user/{id}
     *
     * @param $id
     * @return Response
     */
    public function userAction($id)
    {
        $checkins = CheckIns::getByUserIdPaginated($id);
        $checkins_array = CheckIns::checkinsQueries($checkins);

        $page = array(
            KeyParser::current => $checkins->currentPage(),
            KeyParser::number => $checkins->lastPage()
        );

        $json_return = array(
            KeyParser::data => $checkins_array,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    } // end of userAction

    /**
     * Review Add
     * route: checkins/add
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function addAction(Request $request)
    {
        $data = $request->only('json','fileField');
        $data_photos = $data['fileField'];
        $data_json = json_decode(file_get_contents($data['json']), true);

        if (!isset($data_json['CheckIn']) ||
            !isset($data_json['CheckIn']['user_id']) ||
            !isset($data_json['CheckIn']['restaurant_id']) ||
            !isset($data_json['CheckIn']['message'])) {
            $message = "Format should be: {'CheckIn': {'user_id': <int>, 'restaurant_id': <int>, 'message': <string>}, 'Photos': []}";

            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);
        } // check for valid data


        //Check if restaurant ID is existing
        $restaurant = Restaurants::find($data_json['CheckIn']['restaurant_id']);
        if(!$restaurant) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        // Check Ng Words
        $ng_words = NgWord::ngword_filter($data_json['CheckIn']['message']);

        if ($ng_words) {
            $message = "Bad words found: " . implode(', ', $ng_words);

            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        } // check of Ng Words

        try {
            DB::beginTransaction();

            // Save Checkin Data
            $checkin = new CheckIns();
            $new_checkin = $checkin->addCheckin($data_json['CheckIn']);

            // Save and Upload Photo
            $photos_upload = new Photos();
            $photos_upload->saveUploadedPhotos($data_photos, $data_json['CheckIn'],CONSTANTS::CHECKIN,  $new_checkin->id);

            // Send Notification to Followers about the new Checkin
            $followers = Follow::getFollowerUsersAll($new_checkin->user_id);
            $notification = new Notification();

            foreach ($followers as $follower) {
                $notification->addNotificationNewCheckin($new_checkin->user_id, $follower->follower_user_id, $new_checkin->id, $new_checkin->restaurant_id);
            }

            //Add Activity
            $activity = new Activities();
            $new_activity = $activity->addActivity(CONSTANTS::CHECKIN, $new_checkin->id, $new_checkin->user_id, $new_checkin->restaurant_id);

            $photos = Photos::getByType(CONSTANTS::CHECKIN, $new_checkin->id);
            $photos_array = Photos::convertPhotosToArray($photos);
            $user = Users::find($new_checkin->user_id);

            $json_return[KeyParser::data] = array(
                KeyParser::activity => ModelFormatter::activityFormat($new_activity),
                KeyParser::user => ModelFormatter::userLongFormat($user),                
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::checkin => ModelFormatter::checkinFormat($new_checkin),
                KeyParser::photos => $photos_array,
            );

            //DB Commit
            DB::commit();

            return response()->json($json_return);
        } catch (\Exception $e) {
            DB::rollback();
            if(FileUtilities::fileSizeExceedCheck($_SERVER['CONTENT_LENGTH'])) {
                return showErrorResponse($e->getMessage(), HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_FILE_SIZE_EXCEED);
            }
            return showErrorResponse($e->getMessage());
        } // end catch
    } // end of addAction

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws Exception
     */
    public function editAction(Request $request, $id)
    {

        /* Multipart Procedure
        $data = $request->only('json');
        $data_json = json_decode(file_get_contents($data['json']), true);
        */

        $data_json['CheckIn'] = $request->json()->get('CheckIn');

        if (!isset($data_json['CheckIn']) ||
            !isset($data_json['CheckIn']['message'])) {
            $message = "Format should be: {'CheckIn': 'message': <string>}}";
            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);

        } // check fo valid data

        // Check Ng Words
        $ng_words = NgWord::ngword_filter($data_json['CheckIn']['message']);

        if ($ng_words) {
            $message = "Bad word(s) found: " . implode(',', $ng_words);

            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        } // check of Ng Words

        try {
            // Edit Review Data
            $checkin = new Checkins();
            $edit_checkin = $checkin->editCheckin($id, $data_json['CheckIn']);

            $photos = Photos::getByType(CONSTANTS::CHECKIN, $edit_checkin->id);
            $photos_array = Photos::convertPhotosToArray($photos);

            $restaurant = Restaurants::find($edit_checkin->restaurant_id);
            if (!$restaurant) {
                return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
            }

            $user = Users::find($edit_checkin->user_id);

            $json_return[KeyParser::data] = array(
                KeyParser::checkin => ModelFormatter::checkinFormat($edit_checkin),
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::user => ModelFormatter::userLongFormat($user),
                KeyParser::photos => $photos_array,
            );

            return response()->json($json_return);
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        } // end catch
    } // end editAction

    /**
     * Review Delete
     * route: checkin/delete/{id}
     *
     * @param $id
     * @return request
     */
    public function deleteAction($id)
    {
        try {
            $checkin = new CheckIns();
            $checkin->deleteCheckin($id);

            $json_return[KeyParser::data] = array(
                KeyParser::id => $id,
                KeyParser::is_success => CONSTANTS::DELETE_SUCCESS,
            );
        } catch (\Exception $e) {
            $message = "Failed to delete checkin with ID $id";

            return showErrorResponse($message);
        }

        return response()->json($json_return);
    } // end of deleteAction

} // End of Class

?>