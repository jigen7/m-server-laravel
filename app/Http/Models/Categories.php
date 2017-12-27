<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\ModelFormatter;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model {

	protected $table = 'categories';

    public static function getCuisineByName($name)
    {
        return self::where('name', $name)
            ->where('type', CONSTANTS::CATEGORY_CUISINE)
            ->first();
    }

    public static function getAllTags()
    {
        return self::where('type', CONSTANTS::CATEGORY_TAG)
            ->orderBy('id')
            ->get();
    }

    /**
     * Returns array of categories based on restaurant ID
     *
     * @param $restaurant_id
     *
     * @return array
     */
    public static function getFormattedRestaurantCategories($restaurant_id)
    {
        $restaurant_categories = self::join('restaurants_category', 'categories.id', '=', 'restaurants_category.category_id')
            ->where('restaurants_category.restaurant_id', $restaurant_id)
            ->select('categories.id as id', 'categories.type as type', 'categories.name')
            ->get();

        $category_data = array();
        foreach($restaurant_categories as $category) {
            $category_data[] = ModelFormatter::categoryFormat($category);
        }
        return $category_data;
    }
/*********************************** START ACCESSOR METHODS ************************************/


/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/



/*************************** END MUTATORS SETTER METHODS ************************************/


}



