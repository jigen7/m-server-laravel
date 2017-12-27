<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Comments;
use App\Http\Models\Follow;
use App\Http\Models\Like;
use App\Http\Models\Notification;
use App\Http\Models\Photos;
use App\Http\Models\Restaurants;
use App\Http\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Activities;
use App\Http\Helpers\NgWord;
use App\Http\Helpers\Utilities\FileUtilities;

class PhotosController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * View Photos By Type
     * type: restaurant, review, checkin, user, photo
     * route: /photos/{type}/{type_id}
     *
     * @param int $type
     * @param int $type_id
     * @return Response
     */
    public function viewPhotosByTypeAction($type, $type_id)
    {
        $json_return = array('data' => array());

        $photos = Photos::getByTypePagination($type, $type_id);

        if ($photos->count()) {
            foreach($photos as $photo){
                $photo_object = array(
                    KeyParser::photo => ModelFormatter::photosFormat($photo),
                    KeyParser::user => Users::find($photo->user_id),
                    KeyParser::restaurant => Restaurants::find($photo->restaurant_id)
                );

                if ($type == 'photo') {
                    $photo_object += array(
                        KeyParser::like_count => Like::getCount(CONSTANTS::PHOTO, $photo->id)
                    );
                }

                $json_return[KeyParser::data][] = $photo_object;
                unset($photo_object);
            } // end foreach
        } elseif ($type == 'photo') {
            return showErrorResponse('Photo not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_PHOTO_MISSING);
        }

        $json_return[KeyParser::page] = array(
            KeyParser::current => $photos->currentPage(),
            KeyParser::number => $photos->lastPage()
        );

        return response()->json($json_return);
    } // end of viewPhotos

    /**
     * Upload Photo Routine for Restaurant
     * route: photos/upload/restaurant
     *
     * @param Request $request
     * @return Mixed
     */
    public function photoUploadRestaurantAction(Request $request)
    {
        $data = $request->only('json','fileField');
        $data_photos = $data['fileField'];
        $data_json = json_decode(file_get_contents($data['json']), true);

        try {
            DB::beginTransaction();

            foreach($data_json['Photos'] as $data_json_photo) {

                foreach ($data_photos as $key => $data_photo) {
                    $photo_text = "";
                    if (isset($data_json_photo['Photo']['text'])) {
                        $photo_text = $data_json_photo['Photo']['text'];

                        // Check Ng Words
                        $ng_words = NgWord::ngword_filter($photo_text);
                        if ($ng_words) {
                            $message = "Bad word(s) found: " . implode(' ',$ng_words);
                            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
                        } // check of Ng Words
                    }
                    $data_photos[$key]->text = $photo_text;
                }
            }

            $restaurant_id = $data_json['Restaurant']['restaurant_id'];
            $user_id = $data_json['Restaurant']['user_id'];

            // Save and Upload Photo
            $photos_upload = new Photos();
            $success_photo = $photos_upload->saveUploadedPhotos($data_photos, $data_json['Restaurant'],CONSTANTS::RESTAURANT,  $restaurant_id);

            $followers = Follow::getFollowerUsersAll($user_id);
            $notification = new Notification();

            //Add Activity
            foreach($success_photo as $photo) {
                $activity = new Activities();
                $activity->addActivity(CONSTANTS::PHOTO_UPLOAD_RESTAURANT, $photo->id, $user_id, $restaurant_id);

                foreach ($followers as $follower) {
                    $notification->addNotificationNewPhoto($user_id, $follower->follower_user_id, $photo->id, $restaurant_id);
                }
            }

            $photos_array = Photos::convertPhotosToArray($success_photo);
            $restaurant = Restaurants::find($restaurant_id);
            $user = Users::find($user_id);

            $json_return[KeyParser::data] = array(
                KeyParser::activity => ModelFormatter::activityFormat($activity),
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::user => ModelFormatter::userLongFormat($user),
                KeyParser::photos => $photos_array
            );

            DB::commit();

            return response()->json($json_return);
        } catch (\Exception $e) {
            if(FileUtilities::fileSizeExceedCheck($_SERVER['CONTENT_LENGTH'])) {
                return showErrorResponse($e->getMessage(), HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_FILE_SIZE_EXCEED);
            }
            return showErrorResponse($e->getMessage());
        } // end catch
    } // end of photoUploadRestaurantAction

    /**
     * Delete a Photo
     * route: /photos/delete{id}
     *
     * @param Request $request
     * @return Response
     */
    public function photoDeleteAction(Request $request)
    {
        $path = API_UPLOAD_DIR.'/';
        $data = $request->json()->get('Photo');

        $failed_ids = array();
        $succeeded_ids = array();

        foreach($data['id'] as $photo_id) {
            $photo = Photos::find($photo_id);

            if(!$photo || $photo->user_id != $data['user_id']) {
                $failed_ids[] = $photo_id;
                continue;
            }

            $filename = $photo->url;
            $fullpath = $path.$filename;

            try {
                DB::beginTransaction();

                if(FILE::exists($fullpath)){
                    // Delete an array of files
                    //$files = array($file1, $file2);

                    FILE::delete($fullpath);
                } // end if Exists

                $comment = new Comments();
                $comment->deleteCommentByType(CONSTANTS::PHOTO, $photo->id);

                $like = new Like();
                $like->deleteLikes(CONSTANTS::PHOTO, $photo->id);

                $activity = new Activities();
                $activity->deleteActivity(CONSTANTS::PHOTO_UPLOAD_RESTAURANT, $photo->id);

                $photo->delete();

                DB::commit();
                $succeeded_ids[] = $photo_id;
            } catch (\Exception $e) {
                DB::rollback();

                return showErrorResponse('Error deleting photo');
            }

        }

        $json_return[KeyParser::data] = array(
            KeyParser::success => $succeeded_ids,
            KeyParser::failed => $failed_ids
        );

        return response()->json($json_return);
    } // end of photoDeleteAction

} // End of Class

?>