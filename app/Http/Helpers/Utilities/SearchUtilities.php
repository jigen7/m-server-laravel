<?php
namespace App\Http\Helpers\Utilities;

use App\Http\Models\Restaurants;
use App\Http\Models\Categories;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\ModelFormatter;

class SearchUtilities
{

    public static function restaurantSearch ($params)
    {
        $longitude = (isset($params['long'])) ? $params['long'] : 0;
        $latitude = (isset($params['lat'])) ? $params['lat'] : 0;
        $review_count_query = "(SELECT COUNT(id) FROM reviews WHERE reviews.restaurant_id = restaurants.id)";
        $checkin_count_query = "(SELECT COUNT(id) FROM check_ins WHERE check_ins.restaurant_id = restaurants.id)";
        $restaurants = Restaurants::select('restaurants.*', DB::raw("$review_count_query AS review_count"), DB::raw("$checkin_count_query AS checkin_count"));

        if (isset($params['name'])) {
            $restaurants->where('restaurants.name', 'LIKE', '%'. strtolower(substr($params['name'], 0, 30)). '%');
        }

        if (isset($params['address'])) {
            $restaurants->where('restaurants.address', 'LIKE', '%'. strtolower(substr($params['address'], 0, 30)). '%');
        }

        if (isset($params['tag']) && $params['tag'] != '') {
            $tag = $params['tag'];
            $restaurants->leftJoin('restaurants_category', 'restaurants.id', '=', 'restaurants_category.restaurant_id')
                ->where('restaurants_category.category_id', $tag);
        }

        $search_results = $restaurants->get();
        $distances = array();

        $data = array();
        foreach ($search_results as $count => $restaurant) {

            if($restaurant['id'] == NULL){
                continue;
            }

            if ($longitude > 0 && $latitude > 0) {
                $restaurant->distance = 3956 * 2 * asin(sqrt(pow(sin(($latitude - $restaurant->latitude) * pi() / 180 / 2), 2) + cos($latitude * pi() / 180) * cos($restaurant->latitude * pi() / 180) * pow(sin(($longitude - $restaurant->longitude) * pi() / 180 / 2), 2)));
            } else {
                $restaurant->distance = 0;
            }
            $data[$count]['restaurant'] = ModelFormatter::restaurantSearchFormat($restaurant);
            $distances[$count] = $restaurant->distance;
            $data[$count][KeyParser::categories] = Categories::getFormattedRestaurantCategories($restaurant->id);
        }

        array_multisort($distances, SORT_ASC, $data);
        $search_results_count = count($data);
        $current_page = $params['page'];
        $data = array_splice($data, ($current_page - 1) * CONSTANTS::RESTAURANTS_PARTIAL_SEARCH_PAGINATION_LIMIT, CONSTANTS::RESTAURANTS_PARTIAL_SEARCH_PAGINATION_LIMIT);
        $page = array(
            KeyParser::current => $current_page,
            KeyParser::number => ceil($search_results_count / CONSTANTS::RESTAURANTS_PARTIAL_SEARCH_PAGINATION_LIMIT)
        );

        return array(
            KeyParser::data => $data,
            KeyParser::page => $page
        );

    }
}

?>