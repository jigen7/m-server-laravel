<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;

class Bookmarks extends Model {

	protected $table = 'bookmarks';

    public $timestamps = false;

/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Return all bookmarks of a user with $user_id
     *
     * @param $user_id
     * @return mixed
     */
    public static function getBookmarkByUserId($user_id)
    {
        return self::where('user_id', $user_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::BOOKMARKS_GET_BY_USER_ID_PAGINATION_LIMIT);
    } // end getBookmarkByUserId

    /**
     * Get bookmark count per user
     *
     * @param $user_id
     * @return mixed
     */
    public static function getCountByUserId ($user_id)
    {
        return self::leftJoin('restaurants', 'bookmarks.restaurant_id', '=', 'restaurants.id')
            ->where('bookmarks.user_id', $user_id)
            ->whereNull('restaurants.deleted_at')
            ->count();
    } // getCountByUserId


    /**
     * Get bookmark information
     *
     * @param $user_id
     * @param $restaurant_id
     * @return mixed
     */
    public static function getBookmarkByUserIdRestaurantId($user_id, $restaurant_id) {
        return self::where('user_id', $user_id)
            ->where('restaurant_id', $restaurant_id)
            ->first();
    } // end getBookmarkByUserIdRestaurantId

/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Add bookmark on a restaurant
     *
     * @param $user_id
     * @param $restaurant_id
     * @return mixed
     * @throws \Exception
     */
    public function addBookmark($user_id, $restaurant_id)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();

            $this->user_id = $user_id;
            $this->user_id = $user_id;
            $this->restaurant_id = $restaurant_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();

            $connection->commit();

            return $this;
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end addBookmark

    /**
     * Delete bookmark/s and activity when it was bookmarked
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteBookmark($id)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $bookmark = self::find($id);

            if ($bookmark) {
                $activity = new Activities();
                $activity->deleteActivity(CONSTANTS::BOOKMARK, $id);
                $bookmark->delete();
            } else {
                throw new \Exception('No bookmark found');
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end deleteBookmark

/*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Return 1 if $user_id bookmarked $restaurant_id. Return 0 if otherwise.
     *
     * @param $user_id
     * @param $restaurant_id
     * @return int
     */
    public static function isBookmarked($user_id, $restaurant_id)
    {
        $bookmarks = self::where('user_id', $user_id)
            ->where('restaurant_id', $restaurant_id)
            ->first();

        if ($bookmarks) {
            return CONSTANTS::BOOKMARK_FOUND;
        }

        return CONSTANTS::BOOKMARK_NOT_FOUND;
    } // end isBookmarked

}