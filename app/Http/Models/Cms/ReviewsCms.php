<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class ReviewsCms extends Model
{

    protected $table = 'reviews';

    /**
     * Get all reviews by search criteria
     *
     * @param string $from
     * @param string $to
     * @return ReviewsCms
     */
    public static function getReviews($from, $to)
    {
        $columns = array(
            'reviews.id',
            'reviews.title',
            'restaurants.name AS restaurant_name',
            'reviews.rating',
            'users.firstname',
            'users.lastname',
            'reviews.date_created'
        );

        $reviews = New ReviewsCms();
        $reviews = $reviews->leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->leftJoin('restaurants', 'restaurants.id', '=', 'reviews.restaurant_id');

        if ($from) {
            $reviews = $reviews->where('reviews.date_created', '>=', $from);
        }

        if ($to) {
            $reviews = $reviews->where('reviews.date_created', '<=', $to);
        }

        $reviews = $reviews->latest('reviews.date_created')
            ->get($columns);

        return $reviews;
    }

    /**
     * Get the details for a single review
     *
     * @param $review_id
     * @return mixed
     */
    public static function getReviewDetails($review_id)
    {
        $columns = array(
            'reviews.id',
            'reviews.title',
            'reviews.text',
            'restaurants.name AS restaurant_name',
            'reviews.rating',
            'users.firstname',
            'users.lastname',
            'reviews.date_created'
        );

        return self::leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->leftJoin('restaurants', 'restaurants.id', '=', 'reviews.restaurant_id')
            ->find($review_id, $columns);
    }
}