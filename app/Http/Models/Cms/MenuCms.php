<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use League\Flysystem\Exception;

class MenuCms extends Model
{
    protected $table = 'menu';
    public $timestamps = false;

    /**
     * Add menu details
     *
     * @param $restaurant_id
     * @param $name
     * @param $category
     * @param $serving
     * @param $price
     * @param $description
     * @throws Exception
     */
    public function addMenu($restaurant_id, $name, $category, $serving, $price, $description)
    {
        try {
            $this->restaurant_id = $restaurant_id;
            $this->name = $name;
            $this->category = $category;
            $this->serving = $serving;
            $this->price = $price;
            $this->description = $description;
            $this->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update menu details
     *
     * @param $id
     * @param $name
     * @param $category
     * @param $size
     * @param $price
     * @param $description
     * @return \Illuminate\Support\Collection|null|static
     * @throws \Exception
     */
    public function editMenu($id, $name, $category, $size, $price, $description)
    {
        try {
            $restaurant = self::find($id);

            if (!$restaurant) {
                throw new Exception('Restaurant not found');
            }

            $restaurant->name = $name;
            $restaurant->category = $category;
            $restaurant->size= $size;
            $restaurant->price = $price;
            $restaurant->description = $description;
            $restaurant->save();

            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
