<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Helpers\NgWord;
use App\Http\Models\Activities;
use App\Http\Models\Comments;
use App\Http\Models\Follow;
use App\Http\Models\Notification;
use App\Http\Models\Photos;
use App\Http\Models\Restaurants;
use App\Http\Models\Reviews;
use App\Http\Models\Users;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Input;
use App\Http\Helpers\Utilities\FileUtilities;

class ReviewController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Review Add
     * route: reviews/add
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function addAction(Request $request)
    {
        $json = Input::get('json', '');
        $data = $request->only('json', 'fileField');
        $data_photos = $data['fileField'];

        if ($json) {
            $data_json['Review'] = $request->json()->get('Review');
        } else {
            $data_json = json_decode(file_get_contents($data['json']), true);
        }

        if (!isset($data_json['Review']) ||
            !isset($data_json['Review']['user_id']) ||
            !isset($data_json['Review']['restaurant_id']) ||
            !isset($data_json['Review']['title']) ||
            !isset($data_json['Review']['text']) ||
            !isset($data_json['Review']['rating'])
        ) {
            $message = "Format {'Review': {'user_id': <int>, 'restaurant_id': <int>, 'title': <string>, 'text': <string>, 'rating': <double>}, 'Photos': []}";

            return showErrorResponse($message);
        } // check fo valid data

        if(!Restaurants::isExists($data_json['Review']['restaurant_id'])) {
            return showErrorResponse("Restaurant data not found", HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        if (!in_array($data_json['Review']['rating'], array('0.5', '1.0', '1', '1.5', '2.0', '2', '2.5', '3.0', '3', '3.5', '4.0', '4', '4.5', '5.0', '5'))) {
            $message = "Rating must not be 0. Any of 0.5, 1.0, 1.5, ..., 5.0";

            return showErrorResponse($message);
        } // check for rating value

        // Check Ng Words
        $ng_words = NgWord::ngword_filter($data_json['Review']['title'] . ' ' . $data_json['Review']['text']);

        if ($ng_words) {

            $message = "Bad word(s) found: " . implode(' ', $ng_words);

            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        } // check of Ng Words

        try {
            DB::beginTransaction();
            // Save Review Data
            $review = new Reviews();
            $new_review = $review->addReview($data_json['Review']);

            // Save and Upload Photo
            $photos_upload = new Photos();
            $photos_upload->saveUploadedPhotos($data_photos, $data_json['Review'], CONSTANTS::REVIEW, $new_review->id);

            //@TODO Restaurant Rating Implementation create a cron job for Rating Implementation

            // Send Notification to Followers about the new Review
            $followers = Follow::getFollowerUsersAll($new_review->user_id);

            foreach ($followers as $follower) {
                $notification = new Notification();
                $notification->addNotificationNewReview($new_review->user_id, $follower->follower_user_id, $new_review->id, $new_review->restaurant_id);
            }

            //Add Activity
            $activity = new Activities();
            $new_activity = $activity->addActivity(CONSTANTS::REVIEW, $new_review->id, $new_review->user_id, $new_review->restaurant_id);

            $photos = Photos::getByType(CONSTANTS::REVIEW, $new_review->id);
            $photos_array = Photos::convertPhotosToArray($photos);
            $restaurant = Restaurants::find($new_review->restaurant_id);
            $user = Users::find($new_review->user_id);

            $json_return[KeyParser::data] = array(
                KeyParser::activity => ModelFormatter::activityFormat($new_activity),
                KeyParser::user => ModelFormatter::userLongFormat($user),
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::review => ModelFormatter::reviewFormat($new_review),
                KeyParser::photos => $photos_array,
            );

            DB::commit();

            return response()->json($json_return);
        } catch (\Exception $e) {
            DB::rollback();
            if(FileUtilities::fileSizeExceedCheck($_SERVER['CONTENT_LENGTH'])) {
                return showErrorResponse($e->getMessage(), HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_FILE_SIZE_EXCEED);
            }
            return showErrorResponse($e->getMessage());
        } // end catch
    } // end addAction

    /**
     * Review Edit
     * route: reviews/edit
     *
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

        $data_json['Review'] = $request->json()->get('Review');

        if (!isset($data_json['Review']) ||
            !isset($data_json['Review']['user_id']) ||
            !isset($data_json['Review']['restaurant_id']) ||
            !isset($data_json['Review']['title']) ||
            !isset($data_json['Review']['text']) ||
            !isset($data_json['Review']['rating'])
        ) {
            $message = "Format {'Review': {'user_id': <int>, 'title': <string>, 'text': <string>, 'price': <int>, 'date_visited': <string>, 'rating': <double>, 'restaurant_id': <int>}}";
            return showErrorResponse($message);
        } // check fo valid data

        if(!Restaurants::isExists($data_json['Review']['restaurant_id'])) {
            return showErrorResponse("Restaurant data not found", HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        if (!in_array($data_json['Review']['rating'], array('0.5', '1.0', '1', '1.5', '2.0', '2', '2.5', '3.0', '3', '3.5', '4.0', '4', '4.5', '5.0', '5'))) {
            $message = "Rating must not be 0. Any of 0.5, 1.0, 1.5, ..., 5.0";

            return showErrorResponse($message);
        } // check for rating value

        // Check Ng Words
        $ng_words = NgWord::ngword_filter($data_json['Review']['text']);

        if ($ng_words) {
            $message = "Bad word(s) found: " . implode(',', $ng_words);

            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        } // check of Ng Words

        try {
            // Edit Review Data
            $review = new Reviews();
            $edit_review = $review->editReview($id, $data_json['Review']);

            $photos = Photos::getByType(CONSTANTS::REVIEW, $edit_review->id);
            $photos_array = Photos::convertPhotosToArray($photos);
            $restaurant = Restaurants::find($edit_review->restaurant_id);
            $user = Users::find($edit_review->user_id);

            $json_return[KeyParser::data] = array(
                KeyParser::review => ModelFormatter::reviewFormat($edit_review),
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::user => ModelFormatter::userLongFormat($user),
                KeyParser::photos => $photos_array,
            );

            return response()->json($json_return);
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        } // end catch
    } // end of editAction

    /**
     * Review Delete
     * route: reviews/delete/{id}
     *
     * @param $id
     * @return request
     */
    public function deleteAction($id)
    {
        try {
            $review = new Reviews();
            $review->deleteReview($id);

            $json_return[KeyParser::data] = array(
                KeyParser::id => $id,
                KeyParser::is_success => CONSTANTS::DELETE_SUCCESS,
            );
        } catch (\Exception $e) {
            return showErrorResponse('Error deleting review');
        }

        return response()->json($json_return);
    }// end of deleteAction

    /**
     * Review View
     * route: reviews/view{id}
     *
     * @param $id
     * @optional ?viewer_id
     * @return Response
     */
    public function viewAction($id)
    {
        $json_return = array();
        $review = Reviews::find($id);

        if (!$review) {
            return showErrorResponse('Review not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_REVIEW_MISSING);
        }

        $restaurant = Restaurants::find($review->restaurant_id);

        if (!$restaurant) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        $user = Users::find($review->user_id);

        $photos = Photos::getByType(CONSTANTS::REVIEW, $review->id);
        $photos_array = Photos::convertPhotosToArray($photos);

        $comments = Comments::getByType(CONSTANTS::REVIEW, $review->id);
        $comments_array = array();

        if ($comments) {
            foreach ($comments as $comment) {
                $comments_array[] = ModelFormatter::commentFormat($comment);
            }
        }

        $json_return[KeyParser::data] = array(
            KeyParser::review => ModelFormatter::reviewFormat($review),
            KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
            KeyParser::user => ModelFormatter::userLongFormat($user),
            KeyParser::photos => $photos_array,
            KeyParser::comments => $comments_array,
        );

        return response()->json($json_return);
    } // end of Action

    /**
     * Review User
     * route: reviews/user/{id}
     *
     * @param $id
     * @optional ?page ?viewer_id
     * @return Response
     */
    public function userAction($id)
    {
        $reviews = Reviews::getByUserIdPaginated($id);
        $reviews_array = Reviews::reviewsQueries($reviews);

        $page[KeyParser::current] = $reviews->currentPage();
        $page[KeyParser::number] = $reviews->lastPage();

        $json_return = array(
            KeyParser::data => $reviews_array,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    } // end of userAction

    /**
     * Review Restaurant
     * route: reviews/restaurant/{id}
     *
     * @param $id
     * @optional ?page ?viewer_id
     * @return Response
     */
    public function restaurantAction($id)
    {
        $reviews = Reviews::getByRestaurantIdPaginated($id);
        $reviews_array = Reviews::reviewsQueries($reviews);

        $page[KeyParser::current] = $reviews->currentPage();
        $page[KeyParser::number] = $reviews->lastPage();

        $json_return = array(
            KeyParser::data => $reviews_array,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    } // end restaurantAction

} // End of Class

?>