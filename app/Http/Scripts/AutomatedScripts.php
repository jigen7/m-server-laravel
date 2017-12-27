<?php namespace App\Http\Scripts;

use App\Http\Models\Notification;
use App\Http\Models\Restaurants;
use App\Http\Models\Reviews;

class AutomatedScripts {

    public static function getAllRestaurantRating()
    {
        $ratings = Reviews::getAllRestaurantAverageRating();

        foreach ($ratings as $rating) {
            try {
                $updated_rating = new Restaurants();
                $updated_rating->updateRating($rating['restaurant_id'], $rating['average']);
            } catch (\Exception $e) {
                // Error message here
            }
        }
    }

    public static function retrySendPush()
    {
        Notification::retrySendPush();
    }

}

?>