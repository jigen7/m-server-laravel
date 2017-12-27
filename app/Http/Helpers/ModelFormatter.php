<?php namespace App\Http\Helpers;

use App\Http\Models\Activities;
use App\Http\Models\Bookmarks;
use App\Http\Models\Categories;
use App\Http\Models\CategoriesPhotos;
use App\Http\Models\CheckIns;
use App\Http\Models\Comments;
use App\Http\Models\Photos;
use App\Http\Models\Like;
use App\Http\Models\Restaurants;
use App\Http\Models\Reviews;
use Illuminate\Support\Facades\Input;

class ModelFormatter
{

    // Move to Formatter Class in Helper but problem if it can load this class
    /**
     * Returns an array of activity data
     *
     * @param $data
     * @return array
     */
    public static function activityFormat($data)
    {
        $arr = array();
        if (is_object($data)) {
            $arr[KeyParser::id] = $data->id;
            $arr[KeyParser::type] = $data->type;
            $arr[KeyParser::type_id] = $data->type_id;
            $arr[KeyParser::date] = elapsedTime($data->date_created);
        } else if (is_array($data)) {
            // Masarap Symfony Code Block
        }

        return $arr;
    } // end of activityFormat

    /**
     * Returns an array of user data
     *
     * @param $data
     * @return array
     */
    public static function userFormat($data)
    {
        $arr = array();
        $arr[KeyParser::id] = $data->id;
        $arr[KeyParser::firstname] = $data->firstname;
        $arr[KeyParser::lastname] = $data->lastname;
        $arr[KeyParser::facebook_id] = $data->facebook_id;
        return $arr;
    } // end of userFormat

    /**
     * Returns an array of user data with more fields than userFormat()
     *
     * @param $data
     * @return array
     */
    public static function userLongFormat($data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::firstname => $data->firstname,
            KeyParser::lastname => $data->lastname,
            KeyParser::gender => $data->gender,
            KeyParser::age => $data->age,
            KeyParser::nationality => $data->nationality,
            KeyParser::email => $data->email,
            KeyParser::income => $data->income,
            KeyParser::facebook_id => $data->facebook_id,
            KeyParser::twitter_id => $data->twitter_id,
            KeyParser::twitter_auth_token => $data->twitter_auth_token,
            KeyParser::twitter_auth_secret => $data->twitter_auth_secret,
            KeyParser::device_id => $data->device_id,
            KeyParser::device_type => $data->device_type,
            KeyParser::date_modified => elapsedTime($data->date_modified),
            KeyParser::date_created => elapsedTime($data->date_created),
            KeyParser::notification => $data->notification
        );
        return $arr;
    }

    //Used in Activities
    /**
     * Returns an array of restaurant data
     *
     * @param $data
     * @return array
     */
    public static function restaurantFormat($data)
    {
        $short_restaurant_url = route('short_restaurant_view', ['encoded_id' => recordEncode($data->id)]);
        $arr = array();
        $arr[KeyParser::id] = $data->id;
        $arr[KeyParser::name] = $data->name;
        $arr[KeyParser::slug_name] = $data->slug_name;
        $arr[KeyParser::short_url] = $short_restaurant_url;
        $arr[KeyParser::address] = $data->address;
        $arr[KeyParser::rating] = $data->rating;
        $arr[KeyParser::can_deliver] = $data->can_deliver;
        $arr[KeyParser::can_dinein] = $data->can_dinein;
        $arr[KeyParser::operating_time] = $data->operating_time;
        $arr[KeyParser::longitude] = $data->longitude;
        $arr[KeyParser::latitude] = $data->latitude;
        return $arr;
    }// end of restaurantFormat

    /**
     * Returns an array of restaurant data
     * @param $data
     * @return array
     */
    public static function restaurantLongFormat($data)
    {
        $short_restaurant_url = route('short_restaurant_view', ['encoded_id' => recordEncode($data->id)]);
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::short_url => $short_restaurant_url,
            KeyParser::name => $data->name,
            KeyParser::slug_name => $data->slug_name,
            KeyParser::address => $data->address,
            KeyParser::telephone => $data->telephone,
            KeyParser::budget => $data->budget,
            KeyParser::can_deliver => $data->can_deliver,
            KeyParser::can_dinein => $data->can_dinein,
            KeyParser::operating_time => $data->operating_time,
            KeyParser::longitude => $data->longitude,
            KeyParser::latitude => $data->latitude,
            KeyParser::rating => $data->rating,
            KeyParser::review_count => Reviews::getByRestaurantId($data->id)->count(),
            KeyParser::checkin_count => CheckIns::getByRestaurantId($data->id)->count(),
            KeyParser::view_count => $data->view_count,
            KeyParser::status_close => $data->status_close,
            KeyParser::status_verify => $data->status_verify,
            KeyParser::user_id => $data->user_id,
            KeyParser::thumbnail => $data->thumbnail,
        );

        return $arr;
    }

    public static function restaurantSuggestFormat($data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::name => $data->name,
            KeyParser::address => $data->address,
            KeyParser::telephone => $data->telephone,
            KeyParser::latitude => $data->latitude,
            KeyParser::longitude => $data->longitude,
            KeyParser::user_id => $data->user_id,
            KeyParser::cuisines => $data->cuisines,
            KeyParser::other_details => $data->other_details
        );

        return $arr;
    }

    /**
     * Returns an array of restaurant data for use in restaurantView API
     *
     * @param Restaurants $data
     * @param $is_bookmarked
     * @return array
     */
    public static function restaurantViewFormat(Restaurants $data, $is_bookmarked = 0)
    {
        $short_restaurant_url = route('short_restaurant_view', ['encoded_id' => recordEncode($data->id)]);
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::short_url => $short_restaurant_url,
            KeyParser::name => $data->name,
            KeyParser::slug_name => $data->slug_name,
            KeyParser::address => $data->address,
            KeyParser::telephone => $data->telephone,
            KeyParser::budget => $data->budget,
            KeyParser::can_deliver => $data->can_deliver,
            KeyParser::can_dinein => $data->can_dinein,
            KeyParser::operating_time => $data->operating_time,
            KeyParser::longitude => $data->longitude,
            KeyParser::latitude => $data->latitude,
            KeyParser::rating => $data->rating,
            KeyParser::review_count => Reviews::getByRestaurantId($data->id)->count(),
            KeyParser::checkin_count => CheckIns::getByRestaurantId($data->id)->count(),
            KeyParser::view_count => $data->view_count,
            KeyParser::status_close => $data->status_close,
            KeyParser::status_verify => $data->status_verify,
            KeyParser::user_id => $data->user_id,
            KeyParser::thumbnail => $data->thumbnail,
            KeyParser::is_bookmarked => $is_bookmarked
        );

        return $arr;
    }
    /**
     * Returns an array of bookmark data
     *
     * @param $data
     * @return array
     */
    public static function bookmarkFormat($data)
    {
        $arr = array();
        $arr[KeyParser::id] = $data->id;
        $arr[KeyParser::user_id] = $data->user_id;
        $arr[KeyParser::restaurant_id] = $data->restaurant_id;
        $arr[KeyParser::date_created] = elapsedTime($data->date_created);
        return $arr;
    }// end of bookmarksFormat

    /**
     * Returns an array of checkin data
     *
     * @param $data
     * @return array
     */
    public static function checkinFormat($data)
    {
        $viewer_id = Input::get('viewer_id');
        $short_checkin_url = route('short_checkin_view', ['id' => $data->id]);
        $arr = array();
        $arr[KeyParser::id] = $data->id;
        $arr[KeyParser::short_url] = $short_checkin_url;
        $arr[KeyParser::user_id] = $data->user_id;
        $arr[KeyParser::restaurant_id] = $data->restaurant_id;
        $arr[KeyParser::date_created] = elapsedTime($data->date_created);
        $arr[KeyParser::message] = $data->message;

        $arr[KeyParser::like_count] = Like::getCount(CONSTANTS::CHECKIN, $data->id);
        $arr[KeyParser::comment_count] = Comments::getCountByType(CONSTANTS::CHECKIN, $data->id);

        if($viewer_id){
            $arr[KeyParser::is_liked] = Like::isLiked($viewer_id, CONSTANTS::CHECKIN, $data->id);
        }
        return $arr;
    } // end of checkinFormat


    /**
     * Returns an array of review data
     *
     * @param $data
     * @return array
     */
    public static function reviewFormat($data)
    {
        $viewer_id = Input::get('viewer_id');
        $short_review_url = route('short_review_view', ['id' => $data->id]);
        $arr = array();
        $arr[KeyParser::id] = $data->id;
        $arr[KeyParser::short_url] = $short_review_url;
        $arr[KeyParser::user_id] = $data->user_id;
        $arr[KeyParser::restaurant_id] = $data->restaurant_id;
        $arr[KeyParser::rating] = $data->rating;
        $arr[KeyParser::title] = $data->title;
        $arr[KeyParser::text] = $data->text;
        $arr[KeyParser::date_created] = elapsedTime($data->date_created);
        $arr[KeyParser::date_modified] = elapsedTime($data->date_modified);
        $arr[KeyParser::like_count] = Like::getCount(CONSTANTS::REVIEW, $data->id);
        $arr[KeyParser::comment_count] = Comments::getCountByType(CONSTANTS::REVIEW, $data->id);

        if($viewer_id){
            $arr[KeyParser::is_liked] = Like::isLiked($viewer_id, CONSTANTS::REVIEW, $data->id);
        }

        return $arr;
    }// end of reviewFormat

    /**
     * Returns an array of comment data
     *
     * @param $data
     * @return array
     */
    public static function commentFormat($data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::type => $data->type,
            KeyParser::type_id => $data->type_id,
            KeyParser::user_id => $data->user_id,
            KeyParser::comment => $data->comment,
            KeyParser::date_created => elapsedTime($data->date_created),
        );
        return $arr;
    }

     /**
     * Returns an array of restaurant data for use in restaurantSearch API
     *
     * @param Restaurants $data
     * @return array
     */
    public static function restaurantSearchFormat(Restaurants $data)
    {
        $short_restaurant_url = route('short_restaurant_view', ['encoded_id' => recordEncode($data->id)]);
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::slug_name => $data->slug_name,
            KeyParser::short_url => $short_restaurant_url,
            KeyParser::name => $data->name,
            KeyParser::address => $data->address,
            KeyParser::distance => $data->distance,
            KeyParser::telephone => $data->telephone,
            KeyParser::budget => $data->budget,
            KeyParser::can_deliver => $data->can_deliver,
            KeyParser::can_dinein => $data->can_dinein,
            KeyParser::operating_time => $data->operating_time,
            KeyParser::longitude => $data->longitude,
            KeyParser::latitude => $data->latitude,
            KeyParser::rating => $data->rating,
            KeyParser::view_count => $data->view_count,
            KeyParser::review_count => $data->review_count,
            KeyParser::checkin_count => $data->checkin_count,
            KeyParser::status_close => $data->status_close,
            KeyParser::status_verify => $data->status_verify,
            KeyParser::user_id => $data->user_id,
            KeyParser::thumbnail => $data->thumbnail
        );

        return $arr;
    }

    /**
     * Returns an array of restaurant data for use /bookmarks/user/{user_id} API
     *
     * @param Restaurants $data
     * @return array
     */
    public static function restaurantBookmarkListViewFormat(Restaurants $data)
    {
        $short_restaurant_url = route('short_restaurant_view', ['encoded_id' => recordEncode($data->id)]);
        $array_return = array(
            KeyParser::id => $data->id,
            KeyParser::name => $data->name,
            KeyParser::short_url => $short_restaurant_url,
            KeyParser::address => $data->address,
            KeyParser::telephone => $data->telephone,
            KeyParser::budget => $data->budget,
            KeyParser::can_deliver => $data->can_deliver,
            KeyParser::can_dinein => $data->can_dinein,
            KeyParser::can_dineout => $data->can_dineout,
            KeyParser::is_24hours => $data->is_24hours,
            KeyParser::operating_to => $data->operating_to,
            KeyParser::operating_from => $data->operating_from,
            KeyParser::smoking => $data->smoking,
            KeyParser::credit_card => $data->credit_card,
            KeyParser::longitude => $data->longitude,
            KeyParser::latitude => $data->latitude,
            KeyParser::rating => $data->rating,
            KeyParser::review_count => Reviews::getByRestaurantId($data->id)->count(),
            KeyParser::checkin_count => CheckIns::getByRestaurantId($data->id)->count(),
            KeyParser::view_count => $data->view_count,
            KeyParser::status_close => $data->status_close,
            KeyParser::status_verify => $data->status_verify,
            KeyParser::user_id => $data->user_id,
            KeyParser::thumbnail => $data->thumbnail,
        );

        return $array_return;
    }

    /**
     * Returns an array of photo data
     *
     * @param Photos $data
     * @return array
     */
    public static function photosFormat(Photos $data)
    {
        $viewer_id = Input::get('viewer_id');
        $short_photo_url = route('short_photo_view', ['id' => $data->id]);
        $arr = array();

            $arr[KeyParser::id] =  $data->id;
            $arr[KeyParser::short_url] = $short_photo_url;
            $arr[KeyParser::restaurant_id] = $data->restaurant_id;
            $arr[KeyParser::type] = $data->type;
            $arr[KeyParser::type_id] = $data->type_id;
            $arr[KeyParser::user_id] = $data->user_id;
            $arr[KeyParser::text] = $data->text;
            $arr[KeyParser::url] = $data->url;
            $arr[KeyParser::date_uploaded] = elapsedTime($data->date_uploaded);
            $arr[KeyParser::status] = $data->status;
            $arr[KeyParser::comment_count] = Comments::getCountByType(CONSTANTS::PHOTO, $data->id);
            $arr[KeyParser::like_count] = Like::getCount(CONSTANTS::PHOTO, $data->id);
            if($viewer_id){
                $arr[KeyParser::is_liked] = Like::isLiked($viewer_id, CONSTANTS::PHOTO, $data->id);
            }

        return $arr;
    }

    /**
     * Returns an array of RestaurantsCategory data
     *
     * @param Categories $data
     * @return array
     */
    public static function categoryFormat(Categories $data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::type => $data->type,
            KeyParser::name => $data->name
        );

        return $arr;
    }

    /**
     * Returns an array of activity data for use in restaurantsView API
     *
     * @param Activities $data
     * @return array
     */
    public static function activityRestaurantViewFormat(Activities $data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::type => $data->type,
            KeyParser::type_id => $data->type_id,
            KeyParser::date => elapsedTime($data->date_created),
            KeyParser::restaurant_id => $data->restaurant_id,
            KeyParser::user_id => $data->user_id
        );

        return $arr;
    }


    /** Returns formatted array of restaurants for nearby restaurants API
     *
     * @params $data
     * @return array
     */
    public static function nearRestaurantFormat($data)
    {
        $short_restaurant_url = route('short_restaurant_view', ['encoded_id' => recordEncode($data->id)]);
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::short_url => $short_restaurant_url,
            KeyParser::name => $data->name,
            KeyParser::address => $data->address,
            KeyParser::thumbnail => $data->thumbnail,
            KeyParser::rating => $data->rating,
            KeyParser::budget => $data->budget,
            KeyParser::review_count => Reviews::getByRestaurantId($data->id)->count(),
            KeyParser::longitude => $data->longitude,
            KeyParser::latitude => $data->latitude,
            KeyParser::distance => $data->distance
        );
        return $arr;
    }

    /** Returns an array of bookmark data
     *
     * @params Bookmark $data
     * @params $is_bookmarked
     * @return array
     */
    public static function bookmarkViewFormat(Bookmarks $data)
    {
        $arr = array(
            KeyParser::bookmark_id => $data->id,
            KeyParser::date_created => elapsedTime($data->date_created),
            KeyParser::is_bookmarked => $data->is_bookmarked,
            KeyParser::restaurant_id =>$data->restaurant_id,
            KeyParser::status => $data->status,
            KeyParser::user_id => $data->user_id
        );

        return $arr;
    }


    /** Returns an array of cuisine data with photo URL
     *
     * @params $data
     * @params $array
     */
    public static function categoryWithPhotoFormat($data)
    {
        $cat_id = ($data->category_id) ? $data->category_id : $data->id;
        $category_photo = CategoriesPhotos::getPhotoByCategoryId($cat_id);

        $arr = array(
            KeyParser::id => $cat_id,
            KeyParser::type => $data->type,
            KeyParser::name => $data->name,
            KeyParser::photo => $category_photo->photo_url,
        );
        return $arr;
    } // end cuisinesFormat

    /**
     * Returns an array of report data
     *
     * @param $data
     * @return array
     */
    public static function reportFormat($data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::type => $data->type,
            KeyParser::type_id => $data->type_id,
            KeyParser::reason => $data->reason,
            KeyParser::report_status => $data->report_status,
            KeyParser::reported_by => $data->reported_by,
            KeyParser::date_created => elapsedTime($data->date_created),
            KeyParser::modified_by => $data->modified_by,
            KeyParser::date_modified => elapsedTime($data->date_modified)
        );
        return $arr;
    } // end reportFormat

    public static function notificationFormat($data)
    {
        $arr = array(
            KeyParser::id => $data->id,
            KeyParser::user_id_from => $data->user_id_from,
            KeyParser::user_id_to => $data->user_id_to,
            KeyParser::type => $data->type,
            KeyParser::type_id => $data->type_id,
            KeyParser::comment_id => $data->comment_id,
            KeyParser::restaurant_id => $data->restaurant_id,
            KeyParser::status => $data->status,
            KeyParser::pushed =>$data->pushed,
            KeyParser::date_read => elapsedTimeNotifications($data->date_read),
            KeyParser::date_created => elapsedTimeNotifications($data->date_created)
        );
        return $arr;
    }
}

