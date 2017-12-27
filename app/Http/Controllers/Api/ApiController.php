<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\File;

class ApiController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function symfonyAction()
    {
        $ua = Request::header('User-Agent');
        //Redirect to static Masarap page if no User Agent HTTP header was found
        if(!preg_match("@Masarap/@",$ua)) {
            return File::get(public_path() . '/masarap-symfony/index.html');
        } // end if no Masarap User Agent

        $status_code = FORCE_UPDATE_STATUS_CODE;
        $data = array(
            'status_code' => $status_code,
            'message' => "[$status_code] Force Update.",
            'app_version' => APP_VERSION,
            'app_store' => APP_STORE_LINK,
            'google_play' => PLAY_STORE_LINK);
        $response = new Response(json_encode($data));
        $response->setStatusCode($status_code, "Force Update");
        $response->headers->set('Content-Type', 'application/json');
        $response->send();
        exit();
    }
} // End of Class

?>