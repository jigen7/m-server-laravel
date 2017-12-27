<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class RestaurantsCategoryCms extends Model
{
    protected $table = 'restaurants_category';
    public $timestamps = false;

    /**
     * Get data by restaurant_id and category_id
     *
     * @param $restaurant_id
     * @param $category_id
     * @return mixed
     * @throws \Exception
     */
    public static function getByRestaurantCatId($restaurant_id, $category_id)
    {
        if (!$restaurant_id) {
            throw new \Exception('Invalid restaurant ID for restaurant category');
        }

        if (!$category_id) {
            throw new \Exception('Invalid category ID for restaurant category');
        }

        $where = array(
            'restaurant_id' => $restaurant_id,
            'category_id' => $category_id
        );
        return self::where($where)->count();
    }

    /**
     * Adds new restaurant category data
     *
     * @param $restaurant_id
     * @param $category_id
     * @throws \Exception
     */
    public function addRestaurantCategory($restaurant_id, $category_id)
    {
        if (!$restaurant_id) {
            throw new \Exception('Invalid restaurant ID for restaurant category');
        }

        if (!$category_id) {
            throw new \Exception('Invalid category ID for restaurant category');
        }

        try {
            $this->restaurant_id = $restaurant_id;
            $this->category_id = $category_id;
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}