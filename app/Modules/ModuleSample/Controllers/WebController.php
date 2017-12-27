<?php namespace App\Modules\ModuleSample\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Models\Activities;
use App\Http\Models\Restaurants;
use Illuminate\Support\Facades\Input;
use App\Modules\ModuleSample\Models\UsersWeb;

class WebController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function testWebAction(){

        echo "Hell Yeah";
        $data['users'] = UsersWeb::all();

        $data['restaurants'] = Restaurants::all();;
        //dd($data);
        $data['errors'] = 'Review not found';
        return view('ModuleSample::reviewweb',$data);
    }

   
} // End of Class

?>