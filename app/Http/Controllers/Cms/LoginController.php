<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Models\Cms\CmsUser;
use Illuminate\Http\Request;
use Artdarek\OAuth\OAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(OAuth $oauth)
    {
        $oauth->setHttpClient('CurlClient');
        $this->oauth = $oauth;
    }

    public function loginWithGoogle(Request $request)
    {
        $code = $request->get('code');

        $googleService = $this->oauth->consumer('Google');

        if (!is_null($code)) {
            $googleService->requestAccessToken($code);

            $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
            $cms_user = CmsUser::validateCmsUser($result['email']);

            if (!$cms_user) {
                dd("Not a valid google account");
            }

            Auth::login($cms_user);
            if (Auth::check()) {
                return Redirect::intended('cms/restaurant/index');
            }

        } else {
            $url = $googleService->getAuthorizationUri();
            return redirect((string)$url);
        }
    }

    public function login()
    {
        $data = array(
            'page_title' => 'Log-in'
        );
        return view('cms.main.login', $data);
    }

    public function logout()
    {
        Auth::logout();
        $data = array(
            'page_title' => 'Log-in'
        );
        return view('cms.main.login', $data);
    }

}

?>