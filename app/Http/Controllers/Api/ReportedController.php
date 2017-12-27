<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Photos;
use App\Http\Models\Reported;
use Illuminate\Http\Request;

class ReportedController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Report a comment, photo, restaurant, or review
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $json_return = array();
        $data = $request->json()->get('Report');

        if ($data['type'] == CONSTANTS::PHOTO) {
            $photo = Photos::find($data['type_id']);

            if (!$photo) {
                $message = 'Photo not found or already deleted';
                return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_PHOTO_MISSING);
            }

            $is_existing_photo_report = Reported::isExistingPhotoReport($data['type_id'], $data['user_id']);

            if ($is_existing_photo_report) {
                $message = 'You have already reported this photo';
                return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_REPORTED_ALREADY);
            }
        }

        try {
            $report = new Reported();
            $report_object = $report->addReport($data);

            $json_return[KeyParser::data] = ModelFormatter::reportFormat($report_object);
        } catch (\Exception $e) {
            return showErrorResponse($e->getMessage());
        }

        return response()->json($json_return);
    }

} // End of Class

?>