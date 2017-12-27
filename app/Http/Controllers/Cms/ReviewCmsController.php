<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Models\Cms\CommentsCms;
use App\Http\Models\Cms\ReviewsCms;
use Illuminate\Support\Facades\Request;

class ReviewCmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * View and search for reviews
     *
     * @return View
     */
    public function indexAction()
    {
        $request = Request::all();
        $request = array(
            'from' => isset($request['from']) ? $request['from'] : '',
            'to' => isset($request['to']) ? $request['to'] : ''
        );

        $data = $request;
        $data += array(
            'page_title' => 'Reviews',
            'reviews' => ReviewsCms::getReviews($request['from'], $request['to']),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'review'
            ),
        );

        return view('cms.review.index', $data);
    }

    /**
     * Return the details of the review including the comments and photos
     * Used for the View popup
     *
     * @param $review_id
     * @return View
     */
    public function viewAction($review_id)
    {
        $data = array(
            'review' => ReviewsCms::getReviewDetails($review_id),
            'comments' => CommentsCms::getByReviewId($review_id)
            // TODO: Add photos per review
        );

        return view('cms.review.view', $data);
    }

} // End of Class

?>