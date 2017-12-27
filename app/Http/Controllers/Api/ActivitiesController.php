<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Models\Activities;
use App\Http\Models\Restaurants;
use Illuminate\Support\Facades\Input;

class ActivitiesController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Get the user Activities by user id
     * route: /activities/user/{id}
     *
     * @param int $id
     * @return Response
     */
    public function getUserActivitiesAction($id)
    {
        $activities = Activities::select('activities.*')
            ->leftJoin('restaurants', 'activities.restaurant_id', '=', 'restaurants.id')
            ->where('activities.user_id',$id)
            ->whereNull('restaurants.deleted_at')
            ->orderBy('activities.date_created',CONSTANTS::ORDER_DESC)
            ->take(3)
            ->get();

        $activitiesArray = Activities::activitiesQueries($activities);

        $json_return = array(
             KeyParser::data => $activitiesArray,
        );

        return response()->json($json_return);
    } // end getUserActivitiesAction

    /**
     * Get All Restaurant Activities by Restaurant id
     * route: /activities/restaurant/{id}
     *
     * @param int $id
     * @return Response
     */
    public function getRestaurantActivitiesAction($id)
    {
        $res_activities = Activities::getRestaurantActivities($id);
        $activities = Activities::activitiesQueries($res_activities);

        $page[KeyParser::current] = $res_activities->currentPage();
        $page[KeyParser::number] = $res_activities->lastPage();

        $json_return = array(
            KeyParser::data => $activities,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    } // end getRestaurantActivitiesAction

    /**
     * Get Followed Activities
     * route: activities/followed/{id}
     *
     * @param int $id
     * @return Response
     */
    public function getFollowedActivitiesAction($id)
    {
        $res_id = Input::get('restaurant_id');

        $res_activities = Activities::getFollowedActivities($id, $res_id);
        $activities = Activities::activitiesQueries($res_activities);

        $page[KeyParser::current] = $res_activities->currentPage();
        $page[KeyParser::number] = $res_activities->lastPage();

        $json_return = array(
            KeyParser::data => $activities,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    }// end getFollowActivitiesAction


    /**
     * Returns the nearby restaurant activities of the user base on location
     * route: /activities/restaurant/near/{longitude}/{latitude}/{distance}
     * get user_id parameter to exclude current user data to show in teh activities
     *
     * @param $longitude
     * @param $latitude
     * @param $distance
     * @return response
     */
    public function getNearRestaurantActivitiesAction($longitude, $latitude, $distance)
    {
        $viewer_id = Input::get('viewer_id', 0);
        $restaurant_ids = array();

        $near_restaurant = Restaurants::getNearbyRestaurants($longitude, $latitude, $distance, CONSTANTS::RESTAURANTS_GET_NEARBY_PAGINATION_LIMIT);

        // get restaurant ids
        foreach ($near_restaurant as $restaurant) {
            $restaurant_ids[] = $restaurant->id;
        }

        $restaurant_activities = Activities::getRestaurantsActivities($restaurant_ids, $viewer_id);
        $activitiesArray = Activities::activitiesQueries($restaurant_activities);

        $page[KeyParser::current] = $restaurant_activities->currentPage();
        $page[KeyParser::number] = $restaurant_activities->lastPage();

        $json_return = array(
            KeyParser::data => $activitiesArray,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    } // end getNearRestaurantActivitiesAction

} // End of Class

?>