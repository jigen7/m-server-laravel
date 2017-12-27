<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Models\Cms\PhotosCms;
use App\Http\Models\Cms\ReportedCms;
use App\Http\Models\Cms\RestaurantsCms;
use Illuminate\Support\Facades\Request;

class ReportedCmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * View and search for reported restaurants
     *
     * @return View
     */
    public function indexRestaurantsAction()
    {
        $request = Request::all();
        $request = array(
            'from' => isset($request['from']) ? $request['from'] : '',
            'to' => isset($request['to']) ? $request['to'] : ''
        );

        $data = $request;
        $data += array(
            'page_title' => 'Reported Restaurants',
            'restaurants' => ReportedCms::getRestaurants($request['from'], $request['to']),
            'reported_approved' => CONSTANTS::REPORTED_APPROVED,
            'reported_rejected' => CONSTANTS::REPORTED_REJECTED,
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'reported'
            ),
        );

        return view('cms.reported.restaurants.index', $data);
    }

    /**
     * Return the details of the reported restaurant
     * Used for the View popup
     *
     * @param $restaurant_id
     * @return View
     */
    public function viewRestaurantAction($restaurant_id)
    {
        $data = array();
        $restaurant = RestaurantsCms::find($restaurant_id)->toArray();

        if ($restaurant) {
            $boolean_columns = array(
                'can_deliver',
                'can_dinein',
                'can_dineout',
                'is_24hours',
                'smoking',
                'credit_card',
                'status_close',
            );

            foreach ($restaurant as $key => $value) {
                if (in_array($key, $boolean_columns)) {
                    if ($value === CONSTANTS::YES) {
                        $restaurant[$key] = 'Yes';
                    } elseif ($value === CONSTANTS::NO) {
                        $restaurant[$key] = 'No';
                    }
                }

                if ($key == 'status_verify') {
                    if ($value === CONSTANTS::STATUS_ACTIVE) {
                        $restaurant[$key] = 'Active';
                    } else {
                        $restaurant[$key] = 'Inactive';
                    }
                }
            }

            $data['restaurant'] = $restaurant;
        }

        return view('cms.reported.restaurants.view', $data);
    }

    /**
     * View and search for reported photos
     *
     * @return View
     */
    public function indexPhotosAction()
    {
        $request = Request::all();
        $request = array(
            'from' => isset($request['from']) ? $request['from'] : '',
            'to' => isset($request['to']) ? $request['to'] : ''
        );

        $data = $request;
        $data += array(
            'page_title' => 'Reported Photos',
            'photos' => ReportedCms::getPhotos($request['from'], $request['to']),
            'reported_approved' => CONSTANTS::REPORTED_APPROVED,
            'reported_rejected' => CONSTANTS::REPORTED_REJECTED,
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'reported'
            ),
        );

        return view('cms.reported.photos.index', $data);
    }

    /**
     * Return the details of the reported photo
     * Used for the View popup
     *
     * @param $photo_id
     * @return View
     */
    public function viewPhotoAction($photo_id)
    {
        $data = array();
        $photo = PhotosCms::find($photo_id)->toArray();

        if ($photo) {
            foreach ($photo as $key => $value) {
                if ($key == 'type') {
                    if ($value === CONSTANTS::REVIEW) {
                        $photo[$key] = 'Review';
                    } elseif ($value === CONSTANTS::CHECKIN) {
                        $photo[$key] = 'Checkin';
                    } elseif ($value === CONSTANTS::BOOKMARK) {
                        $photo[$key] = 'Bookmark';
                    } elseif ($value === CONSTANTS::COMMENT) {
                        $photo[$key] = 'Comment';
                    } elseif ($value === CONSTANTS::PHOTO) {
                        $photo[$key] = 'Photo';
                    } elseif ($value === CONSTANTS::RESTAURANT) {
                        $photo[$key] = 'Restaurant';
                    }
                }

                if ($key == 'status') {
                    if ($value === CONSTANTS::STATUS_ACTIVE) {
                        $photo[$key] = 'Active';
                    } else {
                        $photo[$key] = 'Inactive';
                    }
                }
            }

            $data['photo'] = $photo;
        }

        return view('cms.reported.photos.view', $data);
    }

    /**
     * Change report status into approved or rejected
     *
     * @return View
     */
    public function changeReportStatusAction()
    {
        $request = REQUEST::all();

        $page_name = $request['page_name'];
        $reported_id = $request['reported_id'];
        $report_status = $request['report_status'];
        ReportedCms::updateReportStatus($reported_id, $report_status);

        return redirect('cms/reported/'.$page_name.'/index');
    }
} // End of Class

?>
