<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Models\Cms\PhotosCms;
use Illuminate\Support\Facades\Request;

class PhotosCmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Displays list of photos
     *
     * @return \Illuminate\View\View
     */
    public function indexAction()
    {
        $data = array();
        $from_date = urldecode(Request::input('fd', ''));
        $to_date = urldecode(Request::input('td', ''));

        if ($from_date && $to_date) {
            $is_date_valid = date_parse($from_date);

            if ($is_date_valid['error_count']) {
                $errors[] = 'Invalid From Date format';
                $data['errors'] = $errors;
                return view('cms.photos.index', $data);
            }

            $is_date_valid = date_parse($to_date);

            if ($is_date_valid['error_count']) {
                $errors[] = 'Invalid To Date format';
                $data['errors'] = $errors;
                return view('cms.photos.index', $data);
            }

            if (strtotime($from_date) > strtotime($to_date)) {
                $errors[] = 'To Date should be later than From Date';
                $data['errors'] = $errors;
                return view('cms.photos.index', $data);
            }

            $formatted_from_date = date('Y-m-d', strtotime($from_date));
            $formatted_from_date .= ' 00:00:00';
            $formatted_to_date = date('Y-m-d', strtotime($to_date));
            $formatted_to_date .= ' 23:59:59';
            $photos = PhotosCms::whereBetween(
                'date_uploaded',
                array(
                    $formatted_from_date,
                    $formatted_to_date
                )
            )->orderBy('date_uploaded', 'DESC')->paginate(20);
            $photos->appends(
                array(
                    'fd' => $from_date,
                    'td' => $to_date
                )
            );
        } else {
            $photos = PhotosCms::orderBy('id', 'ASC')->paginate(20);
        }

        foreach ($photos as $key => $value) {
            switch ($value->type) {
                case 1:
                    $photos[$key]->type = 'Review';
                    break;
                case 2:
                    $photos[$key]->type = 'Check-In';
                    break;
                case 6:
                    $photos[$key]->type = 'Restaurant Photo';
                    break;
                default:
                    break;
            }

            $photos[$key]->url = env('WEB_HOST') . 'uploads/default/' . $photos[$key]->url;

            switch ($value->status) {
                case 0:
                    $photos[$key]->status = 'Deactivated';
                    break;
                case 1:
                    $photos[$key]->status = 'Active';
                    break;
                default:
                    break;
            }

            $photos[$key]->date_uploaded = date('M d, Y H:i:s', strtotime($photos[$key]->date_uploaded));
        }

        $data = array(
            'photos' => ($photos->count()) ? $photos : array(),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'photos'
            ),
            'page_title' => 'Photos'
        );
        return view('cms.photos.index', $data);
    }

    /**
     * Displays photo info
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function viewAction($id)
    {
        $data = array();
        $errors = array();

        if (!$id) {
            $errors[] = 'Invalid photo ID';
            $data['errors'] = $errors;
            return view('cms.photos.view', $data);
        }

        $photo = PhotosCms::find($id);

        if (!$photo) {
            $errors[] = 'Photo not found';
            $data['errors'] = $errors;
            return view('cms.photos.view', $data);
        }

        $photo = $photo->toArray();

        switch ($photo['type']) {
            case 1:
                $photo['type'] = 'Review';
                break;
            case 2:
                $photo['type'] = 'Check-In';
                break;
            case 6:
                $photo['type'] = 'Restaurant Photo';
                break;
            default:
                break;
        }

        $photo['url'] = env('WEB_HOST') . 'uploads/default/' . $photo['url'];

        switch ($photo['status']) {
            case 0:
                $photo['status'] = 'Deactivated';
                break;
            case 1:
                $photo['status'] = 'Active';
                break;
            default:
                break;
        }

        $photo['date_uploaded'] = date('M d, Y H:i:s', strtotime($photo['date_uploaded']));
        $data['photo'] = $photo;
        return view('cms.photos.view', $data);
    }
}