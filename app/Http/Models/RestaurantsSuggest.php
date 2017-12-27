<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;

class RestaurantsSuggest extends Model {

    protected $table = 'restaurants_suggest';
    public $timestamps = false;

    /*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Adds a new restaurant suggest and returns inserted data upon success
     *
     * @param $data
     * @return RestaurantsSuggest
     * @throws \Exception
     */
    public function addRestaurantSuggest($data)
    {
        try {
            $this->name = $data['name'];
            $this->address = $data['address'];
            $this->telephone = $data['telephone'];
            $this->latitude = $data['latitude'];
            $this->longitude = $data['longitude'];
            $this->user_id = $data['user_id'];
            $this->cuisines = $data['cuisines'];
            $this->other_details = $data['other_details'];
            $this->save();

            return $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*************************** END MUTATORS SETTER METHODS ************************************/
}
