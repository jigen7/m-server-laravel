<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantsCategory extends Model {

	protected $table = 'restaurants_category';

/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Get restaurantCategories based on restaurant ID
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getByRestaurantId($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->get();
    }

/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/



/*************************** END MUTATORS SETTER METHODS ************************************/


}



