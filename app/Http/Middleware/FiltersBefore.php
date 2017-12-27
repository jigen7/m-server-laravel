<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class FiltersBefore implements Middleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //check Server Status
        if(SERVER_STATUS === 1){

            $response = new Response();
            $status_code = SERVER_MAINTENANCE_STATUS_CODE;
            $response->setStatusCode($status_code, "Under Maintenance");
            $response->send();
            exit();

        }//end of SERVER STATUS CHECK

        $ua = $request->headers;
        $accept = $request->headers->get('Accept');
        $bypass = $request->query("bypass");


        if(!$bypass && BYPASS_USER_AGENT_CHECK == 0 ){

            //Redirect to static Masarap page if no User Agent HTTP header was found
            if(!preg_match("@Masarap/@",$ua)) {
                return File::get(public_path() . '/masarap-symfony/index.html');
            } // end if no Masarap User Agent

             //Check the version in the User Agent for FORCE UPDATE
             //Use in Version 1.1 and Up
             if  (!preg_match("@Masarap/" . APP_VERSION . "@", $ua) && !preg_match("@version=" . SERVER_VERSION . "@", $accept) ) {
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
            } // end of pregmatch
        } // end of bypass if

        return $next($request);
    }

}