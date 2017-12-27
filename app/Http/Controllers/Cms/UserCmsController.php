<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Models\Cms\UsersCms;
use Illuminate\Support\Facades\Request;

class UserCmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * View and search for users
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
            'page_title' => 'Users',
            'users' => UsersCms::getUsers($request['from'], $request['to']),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'user'
            ),
        );

        return view('cms.user.index', $data);
    }

    /**
     * Return the details of a user including the activities
     * Used for the View popup
     *
     * @param $user_id
     * @return View
     */
    public function viewAction($user_id)
    {
        // TODO: get user activities

        return view('cms.user.view');
    }

}