<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\Comments;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Models\Like;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Notification;
use App\Http\Models\Restaurants;
use App\Http\Models\Users;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class NotificationsController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Lists all undeleted notifications per user and updates status to UNREAD on NEW notifications
     * route: notifications/view
     *
     * @return Response
     */
    public static function viewAction()
    {
        $status = Input::get('status', CONSTANTS::NOTIFICATION_STATUS_READ);
        $user_id_to = Input::get('user_id_to', null);
        $current_page = Input::get('page', CONSTANTS::FIRST_PAGE);
        $order = Input::get('orderby', CONSTANTS::ORDER_DESC);
        $data = array();

        if ($order != CONSTANTS::ORDER_DESC) {
            $order = CONSTANTS::ORDER_ASC;
        }

        $notifications = Notification::getNotificationByUserToCustomPaginate($status, $order, $user_id_to, $current_page);

        foreach ($notifications as $notification) {
            $data[] = $notification;
        }

        $page = array(
            KeyParser::current => $notifications->currentPage(),
            KeyParser::number => $notifications->lastPage()
        );

        $json_return = array(
            KeyParser::data => $data,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    }

    public function readAction(Request $request)
    {
        $data = $request->json()->get('Notification');

        if (!$data) {
            return showErrorResponse('Incorrect request parameters', HTTP_UNPROCESSABLE_ENTITY);
        }

        $notification = Notification::find($data[CONSTANTS::KEY_ID]);

        if($notification) {
            $notification = ModelFormatter::notificationFormat($notification->updateStatus(CONSTANTS::NOTIFICATION_STATUS_READ));
        }

        $json_return[KeyParser::data][KeyParser::notification] = $notification;

        return response()->json($json_return);
    }
} // End of Class






?>