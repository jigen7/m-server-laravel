<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\LogRecentlyViewed;
use App\Http\Models\Restaurants;

class LogRecentlyViewedController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Returns restaurants recently viewed by a user
     *
     * @param $user_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction($user_id)
    {
        $json_return = array();

        if (!$user_id) {
            return showErrorResponse('Invalid user_id');
        }

        $recently_viewed_data = LogRecentlyViewed::getByUserId($user_id);
        $recently_viewed_data = $recently_viewed_data->toArray();
        $page_data = $recently_viewed_data;
        unset($page_data['data']);
        $recently_viewed_data = $recently_viewed_data['data'];
        $recently_viewed = array();
        $restaurant = null;

        foreach ($recently_viewed_data as $rvd) {
            $restaurant = Restaurants::find($rvd['restaurant_id']);

            if ($restaurant) {
                $recently_viewed[] = ModelFormatter::restaurantFormat($restaurant);
            }

            $restaurant = null;
        }

        $json_return = array(
            KeyParser::data => $recently_viewed,
            KeyParser::page => array(
                KeyParser::current => $page_data['current_page'],
                KeyParser::number => $page_data['last_page']
            )
        );
        return response()->json($json_return);
    }
}