<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\ModelFormatter;
use App\Http\Helpers\KeyParser;
use Illuminate\Support\Facades\DB;

class Reviews extends Model {

	protected $table = 'reviews';
    public $timestamps = false;



/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Get review count per user
     *
     * @param $user_id
     * @return mixed
     */
    public static function getCountByUserId ($user_id)
    {
        return self::leftJoin('restaurants', 'reviews.restaurant_id', '=', 'restaurants.id')
            ->where('reviews.user_id', $user_id)
            ->whereNull('restaurants.deleted_at')
            ->count();
    }

    /**
     * Get all reviews by restaurant ID
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getByRestaurantId($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)->orderby('date_created', CONSTANTS::ORDER_DESC)->get();
    }

    /**
     * Get all reviews by restaurant ID Paginated
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getByRestaurantIdPaginated($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->orderby('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::REVIEWS_GET_BY_RESTAURANT_ID_PAGINATION_LIMIT);
    }

    /**
     * Get all reviews by user ID
     *
     * @param $user_id
     * @return mixed
     */
    public static function getByUserIdPaginated($user_id)
    {
        return self::where('user_id', $user_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::REVIEWS_GET_BY_USER_ID_PAGINATION_LIMIT);
    }

    /**
     * Get average rating of all restaurants
     *
     * @return mixed
     */
    public static function getAllRestaurantAverageRating()
    {
        return self::select('restaurant_id', DB::raw('ROUND(AVG(rating), 2) AS average, COUNT(id) AS review_count'))
            ->groupBy('restaurant_id')
            ->having('review_count', '>', '4')
            ->get();
    }


/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * @param $data
     * @return review object
     */
    public function addReview($data)
    {

        $this->user_id = $data['user_id'];
        $this->restaurant_id = $data['restaurant_id'];
        $this->rating = $data['rating'];
        $this->title = $data['title'];
        $this->text = $data['text'];
        $this->status = CONSTANTS::STATUS_ENABLED;
        $this->date_created = date('Y-m-d H:i:s');
        $this->save();

        return $this;

    }// end of addReview

    /**
     * @param $id
     * @param $data
     * @return Review $review
     * @throws $e
     */
    public function editReview($id, $data)
    {
        try {
            $review = self::find($id);

            if (!$review) {
                throw new \Exception('Review not found');
            }

            if ($review->user_id != $data['user_id']) {
                throw new \Exception('User is not the original owner of the review');
            }

            $review->title = $data['title'];
            $review->text = $data['text'];
            $review->rating = $data['rating'];
            $review->date_modified = date('Y-m-d H:i:s');
            $review->save();

            return $review;
        } catch (\Exception $e) {
            throw $e;
        }
    }// end of editReview

    /**
     * Delete Review
     * @param $id
     * @throws $e
     */
    public function deleteReview($id)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $review = Reviews::find($id);

            if ($review) {
                //delete review
                $review->delete();

                //delete activities
                $activities = new Activities();
                $activities->deleteActivity(CONSTANTS::REVIEW,$id);

                //delete comments
                $comments = new Comments();
                $comments->deleteCommentByType(CONSTANTS::REVIEW,$id);

                //delete likes
                $like = new Like();
                $like->deleteLikes(CONSTANTS::REVIEW, $id);
            } else {
                throw new \Exception('No review found');
            }

            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end of deleteReview

/*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Construct the needed Array for Reviews User and Restaurant
     * action: userAction, restaurantAction
     *
     * @param $reviews
     * @return mixed
     */
    public static function reviewsQueries($reviews){

        $reviews_array = array();
        if(!$reviews){
            return $reviews_array;
        }

        foreach($reviews as $review){

            $restaurant = Restaurants::find($review->restaurant_id);

            if (!$restaurant) {
               continue;
            }

            $user = Users::find($review->user_id);
            $photos = Photos::getByType(CONSTANTS::REVIEW, $review->id);
            $photos_array = Photos::convertPhotosToArray($photos);
            if($user){
                $reviews_array[] = array(
                    KeyParser::review => ModelFormatter::reviewFormat($review),
                    KeyParser::restaurant => ModelFormatter::restaurantLongFormat($restaurant),
                    KeyParser::user   => ModelFormatter::userLongFormat($user),
                    KeyParser::photos => $photos_array,
                );
            }//end of check user
            unset($restaurant);
            unset($user);
            unset($photos);
            unset($photos_array);
        } //end foreach

        return $reviews_array;
    } // end reviewsQueries

}