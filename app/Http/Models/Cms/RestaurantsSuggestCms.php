<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Schema;
use PhpSpec\Exception\Exception;

class RestaurantsSuggestCms extends Model
{
    protected $table = 'restaurants_suggest';
    public $timestamps = false;

    /**
     * Update restaurant data
     *
     * @param $id
     * @param $name
     * @param $address
     * @param $telephone
     * @param $budget
     * @param $latitude
     * @param $longitude
     * @param $operating_time
     * @param $credit_card
     * @param $smoking
     * @param $is_24hours
     * @param $can_dinein
     * @param $can_dineout
     * @param $can_deliver
     * @param $cuisines
     * @param $other_details
     * @return $this
     * @throws Exception
     */
    public function editRestaurantSuggest($id, $name, $address, $telephone, $budget, $latitude, $longitude, $operating_time, $credit_card, $smoking, $is_24hours, $can_dinein, $can_dineout, $can_deliver, $cuisines, $other_details)
    {
        try {
            $restaurant = self::find($id);

            if (!$restaurant) {
                throw new Exception('Restaurant not found');
            }

            $restaurant->name = $name;
            $restaurant->address = $address;
            $restaurant->telephone = $telephone;
            $restaurant->budget = $budget;
            $restaurant->latitude = $latitude;
            $restaurant->longitude = $longitude;
            $restaurant->operating_time = $operating_time;
            $restaurant->credit_card = $credit_card;
            $restaurant->smoking = $smoking;
            $restaurant->is_24hours = $is_24hours;
            $restaurant->can_dinein = $can_dinein;
            $restaurant->can_dineout = $can_dineout;
            $restaurant->can_deliver = $can_deliver;
            $restaurant->cuisines = $cuisines;
            $restaurant->other_details = $other_details;
            $restaurant->save();

            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
