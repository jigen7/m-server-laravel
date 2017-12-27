<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;

class MainCmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Displays index page
     *
     * @return \Illuminate\View\View
     */
    public function indexAction()
    {
        $data = array(
            'page_title' => 'Index'
        );
        return view('cms.main.index', $data);
    }
}