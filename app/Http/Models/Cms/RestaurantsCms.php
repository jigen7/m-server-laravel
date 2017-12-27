<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Schema;
use PhpSpec\Exception\Exception;

class RestaurantsCms extends Model
{
    protected $table = 'restaurants';
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
     * @return \Illuminate\Support\Collection|null|static
     * @throws Exception
     * @throws \Exception
     */
    public function editRestaurant($id, $name, $address, $telephone, $budget, $latitude, $longitude)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
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
            $restaurant->save();
            $connection->commit();
            return $restaurant;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * Add new restaurants data
     *
     * @param $data
     * @return RestaurantsCms|\Illuminate\Support\Collection|null|static
     * @throws \Exception
     */
    public function addRestaurant($data)
    {
        try {
            $restaurant = $this->find($data['id']);

            if (!$restaurant) {
                $restaurant = new RestaurantsCms();
                $restaurant->id = $data['id'];
                $restaurant->rating = 0;
                $restaurant->view_count = 0;
                $restaurant->status_close = 0;
                $restaurant->status_verify = 1;
            }

            $restaurant->name = $data['name'];
            $restaurant->slug_name = '';
            $restaurant->address = $data['address'];
            $restaurant->telephone = $data['telephone'];
            $restaurant->budget = $data['budget'];
            $restaurant->operating_time = $data['operating_time'];
            $restaurant->latitude = $data['latitude'];
            $restaurant->longitude = $data['longitude'];
            $restaurant->thumbnail = $data['thumbnail'];
            $restaurant->credit_card = $data['credit_card'];
            $restaurant->smoking = (!empty($data['smoking'])) ? $data['smoking'] : 0;
            $restaurant->is_24hours = (!empty($data['is_24hours'])) ? $data['is_24hours'] : 0;
            $restaurant->can_dinein = (!empty($data['can_dinein'])) ? $data['can_dinein'] : 1;
            $restaurant->can_dineout = (!empty($data['can_dineout'])) ? $data['can_dineout'] : 1;
            $restaurant->can_deliver = (!empty($data['can_deliver'])) ? $data['can_deliver'] : 0;
            $restaurant->user_id = (!empty($data['user_id'])) ? $data['user_id'] : 0;
            $restaurant->save();
            return $restaurant;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Updates slug_name column of restaurants
     *
     * @throws \Exception
     */
    public function updateRestaurantSlugName()
    {
        try {
            $restaurants = $this->orderBy('id', 'ASC')->get();
            $franchise_restaurants = array();

            foreach ($restaurants as $restaurant) {
                $rest = $this->where('name', $restaurant->name)->get();
                $rest_count = $rest->count();

                if ($rest_count >= 2) {
                    $franchise_restaurants[] = $restaurant;
                    continue;
                } elseif ($rest_count <= 1) {
                    $slug_name = getSlugName($restaurant->name);
                    $restaurant->slug_name = $slug_name;
                    $restaurant->save();
                }

                $slug_name = '';
            }

            if ($franchise_restaurants) {
                $franchise_restaurants_tmp = array();

                foreach ($franchise_restaurants as $franchise_restaurant) {
                    $franchise_restaurants_tmp[$franchise_restaurant->name][] = $franchise_restaurant;
                }

                $franchise_restaurants = $franchise_restaurants_tmp;
                unset($franchise_restaurants_tmp);

                foreach ($franchise_restaurants as $franchise_restaurant) {
                    $count = 1;

                    foreach ($franchise_restaurant as $fr) {
                        $slug_name = getSlugName($fr->name);

                        if ($count > 1) {
                            $slug_name = $slug_name . '-' . $count;
                        }

                        if (!$fr->slug_name) {
                            $fr->slug_name = $slug_name;
                            $fr->save();
                        }

                        $count = $count + 1;
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}