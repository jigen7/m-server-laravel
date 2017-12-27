<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Activities;
use App\Http\Models\Bookmarks;
use App\Http\Models\Categories;
use App\Http\Models\LogRecentlyViewed;
use App\Http\Models\Photos;
use App\Http\Models\Restaurants;
use App\Http\Models\RestaurantsCategory;
use App\Http\Models\RestaurantsSuggest;
use App\Http\Models\Users;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Helpers\Utilities\SearchUtilities;
use App\Http\Helpers\NgWord;


class RestaurantController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Displays individual restaurant information
     * route: /restaurants/{restaurant_id}/{viewer_id?}
     *
     * @param id - restaurant ID
     * @param viewer_id - user ID of the viewer (Optional)
     * @return Response
     */
    public function viewAction($id)
    {
        $viewer_id = Input::get('viewer_id', null);
        $restaurant = Restaurants::find($id);
        $data = array();

        if ($restaurant) {
            $restaurant->view_count++;
            $restaurant->save();
            $restaurant_categories = RestaurantsCategory::getByRestaurantId($id);
            $photos = Photos::getByRestaurantId($id);
            $is_bookmarked = Bookmarks::isBookmarked($viewer_id, $restaurant->id);
            $data[KeyParser::restaurant] = ModelFormatter::restaurantViewFormat($restaurant, $is_bookmarked);

            foreach ($photos as $photo) {
                $data[KeyParser::photos][] = ModelFormatter::photosFormat($photo);
            }

            $data[KeyParser::categories] = Categories::getFormattedRestaurantCategories($restaurant->id);
            Categories::getFormattedRestaurantCategories($restaurant->id);
            $latest_activity = Activities::getLatestRestaurantActivity($id);

            if ($latest_activity) {
                $data[KeyParser::activity] = ModelFormatter::activityRestaurantViewFormat($latest_activity);
                $data[KeyParser::user] = Users::getStatistics($id);
                $data += Activities::getActivityType($latest_activity->type, $latest_activity->type_id);
            }
        }

        $recently_viewed = array();

        if ($viewer_id) {
            $where = array(
                'user_id' => $viewer_id,
                'restaurant_id' => $id
            );
            $rv = LogRecentlyViewed::where($where)->get()->first();

            if ($rv) {
                $rv->date_modified = date('Y-m-d H:i:s');
                $rv->save();
            }

            $rv = new LogRecentlyViewed();
            $rv->addNewLog($viewer_id, $id);
        }

        $json_return[KeyParser::data] = $data;
        return response()->json($json_return);
    }

    /**
     * Displays list of nearby restaurants
     * route: /restaurants/near/{longitude}/{latitude}/{distance}
     * Optional URL params: category (sort), search_key, page, and max_results
     *
     * @param $longitude
     * @param $latitude
     * @param $distance
     * @return Response
     */
    public function nearAction($longitude, $latitude, $distance)
    {
        $sort = Input::get('category', null);
        $search_key = Input::get('search_key', null);
        $current_page = Input::get('page', CONSTANTS::FIRST_PAGE);
        $max_results = Input::get('max_results', CONSTANTS::RESTAURANTS_MAX_RESULTS_PAGINATION_LIMIT);
        $restaurants = Restaurants::getNearbyRestaurants(
            $longitude,
            $latitude,
            $distance,
            $max_results,
            $current_page,
            $search_key,
            null,
            $sort
        );
        $data = array();

        foreach ($restaurants as $restaurant) {
            $data[] = array(
                KeyParser::restaurant => ModelFormatter::nearRestaurantFormat($restaurant),
                KeyParser::categories => Categories::getFormattedRestaurantCategories($restaurant->id)
            );
        }

        $page = array(
            KeyParser::current => $restaurants->currentPage(),
            KeyParser::number => $restaurants->lastPage()
        );
        $json_return = array(
            KeyParser::data => $data,
            KeyParser::page => $page
        );

        return response()->json($json_return);
    }

    /**
     * Restaurant Search function
     * route: /restaurants/search
     * Optional URL params: all (search_key to be used), name, rating, cuisine, orderby
     *
     * @return Response
     */
    public function searchAction()
    {
        $params = Input::get();

        $search_results = SearchUtilities::restaurantSearch($params);

        return response()->json($search_results);
    }

    /**
     * Displays list of cuisines based on nearby restaurants
     * route: /restaurants/nearby-cuisines/{longitude}/{latitude}/{distance}
     *
     * @param $longitude
     * @param $latitude
     * @param $distance
     * @return Response
     */
    public function nearbyCuisineAction($longitude, $latitude, $distance)
    {
        $cuisines = Restaurants::getNearbyCuisines($longitude, $latitude, $distance);
        $data = array();

        foreach ($cuisines as $cuisine) {
            $data[][KeyParser::category] = ModelFormatter::categoryWithPhotoFormat($cuisine, CONSTANTS::CATEGORY_CUISINE);
        }

        $json_return[KeyParser::data] = $data;
        return response()->json($json_return);
    }

    /**
     * Displays a list of nearby restaurants based on a specific cuisine
     * route: /restaurants/nearby-restaurant-cuisines/{longitude}/{latitude}/{distance}/{cuisine}
     *
     * @param $longitude
     * @param $latitude
     * @param $distance
     * @param $cuisine
     * @return Response
     */
    public function nearbyRestaurantsCuisineAction($longitude, $latitude, $distance, $cuisine)
    {
        $current_page = Input::get('page', CONSTANTS::FIRST_PAGE);
        $category = Categories::getCuisineByName($cuisine);
        $restaurants = Restaurants::getNearbyRestaurants(
            $longitude,
            $latitude,
            $distance,
            CONSTANTS::RESTAURANTS_GET_NEARBY_PAGINATION_LIMIT,
            $current_page,
            null,
            $category->id,
            null
        );
        $data = array();

        foreach ($restaurants as $restaurant) {
            $data[] = array(
                KeyParser::restaurant => ModelFormatter::nearRestaurantFormat($restaurant),
                KeyParser::categories => Categories::getFormattedRestaurantCategories($restaurant->id)
            );
        }

        $page = array(
            KeyParser::current => $restaurants->currentPage(),
            KeyParser::number => $restaurants->lastPage()
        );
        $json_return = array(
            KeyParser::data => $data,
            KeyParser::page => $page
        );
        return response()->json($json_return);
    }

    /** Displays list of restaurants within user's recent activity list
     * route: /restaurants/recent-activity/{user_id}/{search_key?}
     *
     * @param $user_id
     * @param $search_key (optional) for restaurant name searches
     * @return Response
     */
    public function recentActivitySearchAction($user_id, $search_key = null)
    {
        $restaurants = Restaurants::getRecentActivityRestaurants($user_id, $search_key);
        $data = array();

        foreach ($restaurants as $restaurant) {
            $categories = Categories::getFormattedRestaurantCategories($restaurant->id);
            $data[] = array(
                KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                KeyParser::categories => $categories
            );
        }

        $page = array(
            KeyParser::current => $restaurants->currentPage(),
            KeyParser::number => $restaurants->lastPage()
        );
        $json_return = array(
            KeyParser::data => $data,
            KeyParser::page => $page
        );
        return response()->json($json_return);
    }

    /**
     * Returns list of restaurant names for use in auto-complete
     * route: /restaurants/name_search/{search_key}
     *
     * @param $search_key
     * @return Response
     */
    public function restaurantsAutoCompleteAction($search_key)
    {
        $json_return[KeyParser::data] = Restaurants::getRestaurantNames($search_key);
        return response()->json($json_return);
    }

    /**
     * Add new restaurant suggest and return JSON data
     * route: /restaurants/suggest
     *
     * @param Request $request
     * @return Response
     */
    public function suggestAction(Request $request)
    {
        $data = $request->json()->get('restaurant');

        if (!isset($data['name']) ||
            !isset($data['address']) ||
            !isset($data['latitude']) ||
            !isset($data['longitude']) ||
            !isset($data['user_id'])
        ) {
            $message = "Format should be: {'restaurant': {'name': <string>, 'address': <string>, 'latitude': <double>, 'longitude': <double>, 'user_id': <int>}}";
            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check Ng Words
        $ng_words = NgWord::ngword_filter(
            $data['name'] . ' ' .
            $data['telephone'] . ' ' .
            $data['address'] . ' ' .
            $data['cuisines'] . ' ' .
            $data['other_details']
        );

        if ($ng_words) {
            $message = "Bad words found: " . implode(', ', $ng_words);
            return showErrorResponse($message, HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BADWORDS_FOUND);
        }

        try {
            $restaurant_suggest = new RestaurantsSuggest();
            $restaurant_suggest->addRestaurantSuggest($data);

            $json_return[KeyParser::data] = ModelFormatter::restaurantSuggestFormat($restaurant_suggest);

        } catch (\Exception $e ) {
            return showErrorResponse($e->getMessage());
        }

        return response()->json($json_return);
    }

    public function getTagListAction ()
    {
        $tags = Categories::getAllTags();
        $data[][KeyParser::category] = array(
            KeyParser::id => 0,
            KeyParser::type => CONSTANTS::CATEGORY_TAG,
            KeyParser::name => CONSTANTS::ALL_TAG_NAME,
            KeyParser::photo => CONSTANTS::ALL_TAG_PHOTO
        );
        foreach ($tags as $tag) {
            $data[][KeyParser::category] = ModelFormatter::categoryWithPhotoFormat($tag, CONSTANTS::CATEGORY_TAG);
        }

        $json_return[KeyParser::data] = $data;
        return response()->json($json_return);
    }
}
