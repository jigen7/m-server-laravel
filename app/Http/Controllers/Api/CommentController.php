<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Models\CheckIns;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Photos;
use App\Http\Helpers\NgWord;
use App\Http\Models\Comments;
use App\Http\Models\Restaurants;
use App\Http\Models\Reviews;
use App\Http\Models\Users;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Output JSON output of comments in a review
     *
     * @param $id
     * @return Response
     */
    public function viewByReviewIdAction($id)
    {
        $review = Reviews::find($id);

        if (!$review) {
            return showErrorResponse('Review not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_REVIEW_MISSING);
        }

        if (!Restaurants::isExists($review->restaurant_id)) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        $json_return = array(
            KeyParser::data => array(),
            KeyParser::comment_count => 0,
            KeyParser::page => array()
        );

        $comments = Comments::getByTypePaginate(CONSTANTS::REVIEW, $id);

        if ($comments->count()) {
            foreach ($comments as $comment) {
                $json_return[KeyParser::data][] = array(
                    KeyParser::comment => ModelFormatter::commentFormat($comment),
                    KeyParser::user => Users::find($comment->user_id)
                );
            }

            $json_return[KeyParser::comment_count] = Comments::getCountByType(CONSTANTS::REVIEW, $id);
            $json_return[KeyParser::page] = array(
                KeyParser::current => $comments->currentPage(),
                KeyParser::number => $comments->lastPage()
            );
        }

        return response()->json($json_return);
    }

    /**
     * Output JSON output of comments in a checkin
     *
     * @param $id
     * @return Response
     */
    public function viewByCheckinAction($id)
    {
        $checkin = CheckIns::find($id);

        if (!$checkin){
            return showErrorResponse('Checkin not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_CHECKIN_MISSING);
        }

        if (!Restaurants::isExists($checkin->restaurant_id)) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        $json_return = array(
            KeyParser::data => array(),
            KeyParser::comment_count => 0,
            KeyParser::page => array()
        );

        $comments = Comments::getByTypePaginate(CONSTANTS::CHECKIN, $id);

        if ($comments->count()) {
            foreach ($comments as $comment) {
                $json_return[KeyParser::data][] = array(
                    KeyParser::comment => ModelFormatter::commentFormat($comment),
                    KeyParser::user => Users::find($comment->user_id)
                );
            }

            $json_return[KeyParser::comment_count] = Comments::getCountByType(CONSTANTS::CHECKIN, $id);
            $json_return[KeyParser::page] = array(
                KeyParser::current => $comments->currentPage(),
                KeyParser::number => $comments->lastPage()
            );
        }

        return response()->json($json_return);
    }

    /**
     * Output JSON output of comments in a photo
     *
     * @param $id
     * @return Response
     */
    public function viewByPhotoIdAction($id)
    {
        $photo = Photos::find($id);

        if (!$photo) {
            return showErrorResponse('Photo not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_PHOTO_MISSING);
        }

        if (!Restaurants::isExists($photo->restaurant_id)) {
            return showErrorResponse('Restaurant data not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_GENERAL);
        }

        $json_return = array(
            KeyParser::data => array(),
            KeyParser::comment_count => 0,
            KeyParser::page => array()
        );

        $comments = Comments::getByTypePaginate(CONSTANTS::PHOTO, $id);

        if ($comments->count()) {
            foreach ($comments as $comment) {
                $json_return[KeyParser::data][] = array(
                    KeyParser::comment => ModelFormatter::commentFormat($comment),
                    KeyParser::user => Users::find($comment->user_id)
                );
            }

            $json_return[KeyParser::comment_count] = Comments::getCountByType(CONSTANTS::PHOTO, $id);
            $json_return[KeyParser::page] = array(
                KeyParser::current => $comments->currentPage(),
                KeyParser::number => $comments->lastPage()
            );
        }

        return response()->json($json_return);
    }

    /**
     * Add new comment and include new comment data to JSON output
     *
     * @param Request $request
     * @return Response
     */
    public function addCommentAction(Request $request)
    {
        $data = $request->json()->get('Comment');

        if (!isset($data['text']) || !isset($data['type']) || !isset($data['type_id']) || !isset($data['user_id'])) {
            $message = "Format should be: {'Comment': {'text': <string>, 'type' : <int>, 'type_id': <int>, 'user_id': <int>}}";
            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check Ng Words
        $ng_words = NgWord::ngword_filter($data['text']);

        if ($ng_words) {
            $message = "Bad word(s) found: " . implode(',', $ng_words);
            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        } // check of Ng Words

        try {
            $comment = new Comments();
            $comment->addComment($data['text'], $data['type'], $data['type_id'], $data['user_id']);
            $user = Users::find($data['user_id']);
            $user = ModelFormatter::userLongFormat($user);

            $json_return[KeyParser::data] = array(
                KeyParser::comment => ModelFormatter::commentFormat($comment),
                KeyParser::user => $user,
            );
        } catch (\Exception $e) {
            switch ($e->getMessage()) {
                case CONSTANTS::ERROR_CODE_REVIEW_MISSING :
                    $append = "Missing Review";
                    break;
                case CONSTANTS::ERROR_CODE_CHECKIN_MISSING :
                    $append = "Missing Checkin";
                    break;
                case CONSTANTS::ERROR_CODE_PHOTO_MISSING :
                    $append = "Missing Photo";
                    break;
                default :
                    $append = $e->getMessage();
            }

            return showErrorResponse('Error Adding Comment: ' . $append, HTTP_ACCEPTED, (int)$e->getMessage());
        }

        $comments = Comments::getByTypePaginate($data['type'], $data['type_id']);

        foreach ($comments as $comment) {
            $user = Users::find($comment->user_id);
            $json_return[KeyParser::comments][] = array(
                KeyParser::comment => ModelFormatter::commentFormat($comment),
                KeyParser::user => ModelFormatter::userFormat($user)
            );
        }

        $json_return[KeyParser::comment_count] = Comments::getCountByType($data['type'], $data['type_id']);

        $json_return[KeyParser::page] = array(
            KeyParser::current => $comments->currentPage(),
            KeyParser::number =>  $comments->lastPage()
        );

        return response()->json($json_return);
    }

    /**
     * Delete comment
     *
     * @param $id
     * @return Response
     */
    public function deleteCommentAction($id)
    {
        $comment = Comments::find($id);

        if(!$comment) {
            return showErrorResponse('Comment not found');
        }

        $comment_type = $comment->type;
        $comment_type_id = $comment->type_id;

        try {
            $comment->deleteComment();
            $json_return[KeyParser::data] = array(
                KeyParser::id => $id,
                KeyParser::is_success => CONSTANTS::DELETE_SUCCESS,
            );
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        }

        $comments = Comments::getByTypePaginate($comment_type, $comment_type_id);

        foreach ($comments as $comment) {
            $user = Users::find($comment->user_id);
            $json_return[KeyParser::comments][] = array(
                KeyParser::comment => ModelFormatter::commentFormat($comment),
                KeyParser::user => ModelFormatter::userFormat($user)
            );
        }

        $json_return[KeyParser::comment_count] = Comments::getCountByType($comment_type, $comment_type_id);

        $json_return[KeyParser::page] = array(
            KeyParser::current => $comments->currentPage(),
            KeyParser::number =>  $comments->lastPage()
        );

        return response()->json($json_return);
    }

    /**
     * Update comment
     *
     * @param Request $request
     * @return Response
     */
    public function editCommentAction(Request $request)
    {
        $data = $request->json()->get('Comment');

        if (!isset($data['id']) || !isset($data['text']) ||  !isset($data['user_id'])) {
            $message = "Format should be: {'Comment': {'id': <int>, 'text': <string>, 'user_id': <int>}}";
            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check Ng Words
        $ng_words = NgWord::ngword_filter($data['text']);

        if ($ng_words) {
            $message = "Bad word(s) found: " . implode(',', $ng_words);
            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        } // check of Ng Words

        try {
            $comment = new Comments();
            $comment = $comment->editComment($data['id'], $data['user_id'], $data['text']);
            $comment = ModelFormatter::commentFormat($comment);
            $user = Users::find($data['user_id']);
            $user = ModelFormatter::userLongFormat($user);
            $json_return[KeyParser::data] = array(
                KeyParser::comment => $comment,
                KeyParser::user => $user,
            );
        } catch (\Exception $e) {
            return showErrorResponse('Error Editing Comment');
        }

        return response()->json($json_return);
    }
} // End of Class