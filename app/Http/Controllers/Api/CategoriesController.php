<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\KeyParser;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /* Return categories cuisines with photos
     *
     * @return Response
     *
     */
    public function cuisineListAction(){

        $cuisines = DB::table('categories')
            ->join('category_photos', 'categories.id', '=', 'category_photos.category_id')
            ->select('categories.name', 'category_photos.photo_url as photo')
            ->get();


        $json_return[KeyParser::data] = $cuisines;

        return response()->json($json_return);




    } // end of cuisineListAction

} // End of Class

?>