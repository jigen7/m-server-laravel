<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class PhotosCms extends Model
{
    protected $table = 'photos';
    public $timestamps = false;

    /**
     * Adds new photos data
     *
     * @param $type
     * @param $type_id
     * @param $restaurant_id
     * @param $url
     * @param $text
     * @param $status
     * @param $points
     * @param $user_id
     * @throws \Exception
     */
    public function addPhoto($type, $type_id, $restaurant_id, $url, $text, $status, $points, $user_id)
    {
        if (!$type) {
            throw new \Exception('Invalid type for photos');
        }

        if (!$type_id) {
            throw new \Exception('Invalid type_id ID for photos');
        }

        if (!$restaurant_id) {
            throw new \Exception('Invalid restaurant_id for photos');
        }

        if (!$url) {
            throw new \Exception('Invalid URL for photos');
        }

        if (!$text) {
            throw new \Exception('Invalid text for photos');
        }

        if (!$status) {
            throw new \Exception('Invalid status for photos');
        }

        if (!$points) {
            throw new \Exception('Invalid points for photos');
        }

        if (!$user_id) {
            throw new \Exception('Invalid user_id for photos');
        }

        try {
            $this->type = $type;
            $this->type_id = $type_id;
            $this->restaurant_id = $restaurant_id;
            $this->url = $url;
            $this->text = $text;
            $this->status = $status;
            $this->points = $points;
            $this->user_id = $user_id;
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}