<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Helpers\ModelFormatter;
use App\Http\Models\Bookmarks;
use App\Http\Models\Restaurants;
use App\Http\Models\RestaurantsCategory;
use App\Http\Models\Users;
use Illuminate\Http\Request;
use App\Http\Models\Categories;

class BookmarkController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Add bookmark
     * route: /bookmarks/add
     * 
     * @param Request $request
     * @return Response
     */
    public function addBookmarkAction(Request $request)
    {
        $data = $request->json()->get('Bookmark');
        $json_return = array();

        if (!isset($data['user_id']) ||
            !isset($data['restaurant_id'])) {
            $message = "Format should be: {'Bookmark': {'user_id': <int>, 'restaurant_id': <int>}}";

            return showErrorResponse($message, HTTP_UNPROCESSABLE_ENTITY);
        }

        $user_id = $data['user_id'];
        $restaurant_id = $data['restaurant_id'];

        if (!Bookmarks::isBookmarked($user_id, $restaurant_id)) {
            try {
                $bookmark = new Bookmarks();
                $bookmark = $bookmark->addBookmark($user_id, $restaurant_id);
                $bookmark[KeyParser::is_bookmarked] = Bookmarks::isBookmarked($user_id, $restaurant_id);
                $bookmark[KeyParser::status] = CONSTANTS::BOOKMARK_SAVED;
                $json_return[KeyParser::data] = ModelFormatter::bookmarkViewFormat($bookmark);
            } catch (\Exception $e) {
                return showErrorResponse('Error adding bookmark');
            }
        } else {
            $bookmark = Bookmarks::getBookmarkByUserIdRestaurantId($user_id, $restaurant_id);
            $bookmark[KeyParser::is_bookmarked] = Bookmarks::isBookmarked($user_id, $restaurant_id);
            $bookmark[KeyParser::status] = CONSTANTS::BOOKMARK_EXISTS;
            $json_return[KeyParser::data] = ModelFormatter::bookmarkViewFormat($bookmark);
        }

        return response()->json($json_return);
    }

    /**
     * Delete bookmark/s
     * route: bookmarks/delete/restaurant/{restaurant_id}/user/{user_id}
     * 
     * @param Request $request
     * @return Response
     */
    public function deleteBookmarkAction(Request $request)
    {
        $json_return = array();

        $user_id = $request->user_id;
        $restaurant_id = $request->restaurant_id;
        $bookmark_object = Bookmarks::getBookmarkByUserIdRestaurantId($user_id, $restaurant_id);

        if ($bookmark_object) {
            $bookmark_id = $bookmark_object['id'];

            try {
                $bookmark = new Bookmarks();
                $bookmark->deleteBookmark($bookmark_id);

                $json_return[KeyParser::data] = array(
                    KeyParser::id => $bookmark_id,
                    KeyParser::is_success => CONSTANTS::DELETE_SUCCESS
                );
            } catch (\Exception $e) {
                return showErrorResponse($e->getMessage());
            }
        } else {
            return showErrorResponse('Bookmark not found', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_BOOKMARK_MISSING);
        }

        return response()->json($json_return);
    }

    /**
     * get all of the bookmarks of a user
     * /bookmarks/user/{user_id}
     *
     * @param $user_id
     * @return Response
     */
    public function userBookmarkListAction($user_id)
    {
        if (!is_numeric($user_id)) {
            return showErrorResponse('Incorrect User ID format');
        }

        $bookmark_list = Bookmarks::getBookmarkByUserId($user_id);
        $user_data = Users::find($user_id);

        $json_return[KeyParser::data] = array();
        if ($bookmark_list) {
            foreach($bookmark_list as $bookmark) {
                $restaurant_data = Restaurants::find($bookmark->restaurant_id);
                if ($restaurant_data) {
                    $json_return[KeyParser::data][] = array(
                        KeyParser::bookmark => ModelFormatter::bookmarkFormat($bookmark),
                        KeyParser::user => ModelFormatter::userFormat($user_data),
                        KeyParser::restaurant => ModelFormatter::restaurantBookmarkListViewFormat($restaurant_data),
                        KeyParser::categories => Categories::getFormattedRestaurantCategories($bookmark->restaurant_id),

                    );
                }
            }
        }

        $json_return[KeyParser::page] = array(
            KeyParser::current => $bookmark_list->currentPage(),
            KeyParser::number => $bookmark_list->lastPage()
        );

        return response()->json($json_return);
    } // end of userBookmarkListAction

} // End of Class

?>