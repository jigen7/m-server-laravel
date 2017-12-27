<?php namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Model;

/** Project Constants */

class CONSTANTS extends Model
{
    /**
     * Table Values with type column
     * activities: 1,2,3
     * comments:   1,2,3,5
     * like:       1,2,4,5
     * photos:     1,2,6
     * reported:   1,4,5,6
     */

    const REVIEW = 1;
    const CHECKIN = 2;
    const BOOKMARK = 3;
    const COMMENT = 4;
    const PHOTO = 5;
    const RESTAURANT = 6;
    const PHOTO_UPLOAD_RESTAURANT = 51; //Follow the first number related to the constant Photo '5'

    const ERROR_CODE_GENERAL = 1;
    const ERROR_CODE_BADWORDS_FOUND = 2;
    const ERROR_CODE_INVALID_TYPE = 3;
    const ERROR_CODE_REPORTED_ALREADY = 4;
    const ERROR_CODE_REVIEW_MISSING = 5;
    const ERROR_CODE_CHECKIN_MISSING = 6;
    const ERROR_CODE_PHOTO_MISSING = 7;
    const ERROR_CODE_USER_ALREADY_FOLLOWED = 8;
    const ERROR_CODE_FILE_SIZE_EXCEED = 9;
    const ERROR_CODE_BOOKMARK_MISSING = 10;

    const CATEGORY_CITY = 'city';
    const CATEGORY_CUISINE = 'cuisine';
    const CATEGORY_MALL = 'mall';
    const CATEGORY_TAG = 'tag';

    const REPORTED_UNREAD = 0;
    const REPORTED_APPROVED = 1;
    const REPORTED_REJECTED = 2;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const STATUS_UNVERIFIED = 0;
    const STATUS_VERIFIED = 1;
    const STATUS_PENDING = 2;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    const NO = 0;
    const YES = 1;

    const RESTAURANT_ONE_KILOMETER = 0.621371;
    const SUGGESTED_RESTAURANT_INITIAL_ID = 90000;

    const DELETE_SUCCESS = 1;
    const DELETE_FAIL = 0;

    const LIKE_IS_NOT_EXISTING = 0;
    const LIKE_IS_EXISTING = 1;

    const BOOKMARK_SAVED = 'saved';
    const BOOKMARK_EXISTS = 'exists';

    const COMMENT_NO_ERROR = 0;
    const COMMENT_HAS_ERROR = 2;

    const FOLLOW_FOLLOWED = 'followed';
    const FOLLOW_FOLLOWER = 'follower';
    const FOLLOW_IS_NOT_FOLLOWED = 0;
    const FOLLOW_IS_FOLLOWED = 1;

    const BOOKMARK_NOT_FOUND = 0;
    const BOOKMARK_FOUND = 1;

    const TWITTER_PUBLIC = 0;
    const TWITTER_PRIVATE = 1;

    const FIRST_PAGE = 1;
    const ACTIVITIES_GET_ALL_PAGINATION_LIMIT = 5;
    const BOOKMARKS_GET_BY_USER_ID_PAGINATION_LIMIT = 10;
    const COMMENTS_GET_BY_TYPE_PAGINATION_LIMIT = 50;
    const FOLLOW_GET_FOLLOWS_PAGINATION_LIMIT = 20;
    const LIKE_GET_LIST_PAGINATION_LIMIT = 20;
    const PHOTOS_GET_BY_TYPE_PAGINATION_LIMIT = 21;
    const RESTAURANTS_FULL_TEXT_SEARCH_PAGINATION_LIMIT = 20;
    const RESTAURANTS_GET_ACTIVITIES_PAGINATION_LIMIT = 20;
    const RESTAURANTS_GET_NEARBY_PAGINATION_LIMIT = 20;
    const RESTAURANTS_MAX_RESULTS_PAGINATION_LIMIT = 20;
    const RESTAURANTS_PARTIAL_SEARCH_PAGINATION_LIMIT = 20;
    const REVIEWS_GET_BY_RESTAURANT_ID_PAGINATION_LIMIT = 20;
    const REVIEWS_GET_BY_USER_ID_PAGINATION_LIMIT = 20;
    const USER_SEARCH_PAGINATION_LIMIT = 20;
    const NOTIFICATIONS_VIEW_PAGINATION_LIMIT = 20;

    const WEB_NOTIFICATIONS_PREVIEW_PAGINATION_LIMIT = 3;

    const DEVICE_IOS = 'iOS';
    const DEVICE_ANDROID = 'Android';
    const DEVICE_UNKNOWN = 'Unknown';

    const NOTIFICATION_STATUS_NEW = 0;
    const NOTIFICATION_STATUS_UNREAD = 1;
    const NOTIFICATION_STATUS_READ = 2;
    const NOTIFICATION_STATUS_DELETED = 3;

    const NOTIFICATION_TYPE_NEW_FOLLOWER = 'new_follower';
    const NOTIFICATION_TYPE_COMMENT_ON_CHECKIN = 'comment_on_checkin';
    const NOTIFICATION_TYPE_COMMENT_ON_REVIEW = 'comment_on_review';
    const NOTIFICATION_TYPE_COMMENT_ON_PHOTO = 'comment_on_photo';
    const NOTIFICATION_TYPE_LIKE_CHECKIN = 'like_checkin';
    const NOTIFICATION_TYPE_LIKE_REVIEW = 'like_review';
    const NOTIFICATION_TYPE_LIKE_PHOTO = 'like_photo';
    const NOTIFICATION_TYPE_FRIEND_JOIN = 'friend_join';
    const NOTIFICATION_TYPE_FOLLOWING_REVIEW = 'following_review';
    const NOTIFICATION_TYPE_FOLLOWING_CHECKIN = 'following_checkin';
    const NOTIFICATION_TYPE_UPLOADED_PHOTO = 'uploaded_photo';

    const NOTIFICATION_ENABLE = 1;
    const NOTIFICATION_DISABLE = 0;

    const NOTIFICATION_USER_GROUP_LIMIT = 3;

    const CHECKINS_GET_BY_RESTAURANT_ID_PAGINATION_LIMIT = 20;
    const CHECKINS_GET_BY_USER_ID_PAGINATION_LIMIT = 20;
    const LOG_RECENTLY_VIEWED_COUNT = 20;

    const EMPTY_VALUE = '';
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';
    const APNS_SOUND_DEFAULT = 'default';
    const NOTIFICATION_SERVER_FAILURE = 'Cannot connect to the server.';
    const NOTIFICATION_INVALID_DEVICE_ID = 'Invalid device id. Must be hexadecimal for iOS.';
    const FAIL = 'fail';

    //Keys Notification Table
    const KEY_FIRSTNAME = 'firstname';
    const KEY_LASTNAME = 'lastname';
    const KEY_GENDER = 'gender';
    const KEY_EMAIL = 'email';
    const KEY_INCOME = 'income';
    const KEY_NATIONALITY = 'nationality';
    const KEY_FACEBOOK_ID = 'facebook_id';
    const KEY_TWITTER_ID = 'twitter_id';
    const KEY_TWITTER_AUTH_TOKEN = 'twitter_auth_token';
    const KEY_TWITTER_AUTH_SECRET = 'twitter_auth_secret';
    const KEY_DEVICE_ID = 'device_id';
    const KEY_DEVICE_TYPE = 'device_type';
    const KEY_AGE = 'age';
    const KEY_FB_ACCESS_TOKEN = 'fb_access_token';

    const KEY_USER_ID_TO = 'user_id_to';
    const KEY_USER_ID_FROM = 'user_id_from';
    const KEY_TYPE = 'type';
    const KEY_TYPE_ID = 'type_id';

    const KEY_USER_ID = 'user_id';
    const KEY_ID = 'id';

    const DAY_SECOND_VALUE = 86400;

    const LOG_VIEW_RESTAURANT              = 1;
    const LOG_VIEW_REVIEW                  = 2;
    const LOG_VIEW_PHOTO                   = 3;
    const LOG_REVIEW_ADD                   = 100;
    const LOG_REVIEW_EDIT                  = 101;
    const LOG_CHECKIN_ADD                  = 200;
    const LOG_CHECKIN_EDIT                 = 201;
    const LOG_BOOKMARK_ADD                 = 300;
    const LOG_BOOKMARK_DELETE              = 301;
    const LOG_COMMENT_ADD                  = 400;
    const LOG_COMMENT_ADD_REVIEW           = 401;
    const LOG_COMMENT_ADD_CHECKIN          = 402;
    const LOG_COMMENT_ADD_PHOTO            = 403;
    const LOG_LIKE                         = 500;
    const LOG_LIKE_REVIEW                  = 501;
    const LOG_LIKE_CHECKIN                 = 502;
    const LOG_LIKE_COMMENT                 = 503;
    const LOG_LIKE_PHOTO                   = 504;
    const LOG_PHOTO_UPLOAD_REVIEW          = 600;
    const LOG_PHOTO_UPLOAD_CHECKIN         = 601;
    const LOG_PHOTO_UPLOAD_RESTAURANT      = 602;
    const LOG_FOLLOW_FOLLOW                = 700;
    const LOG_FOLLOW_UNFOLLOW              = 701;
    const LOG_SHARE_REVIEW                 = 800;
    const LOG_SHARE_CHECKIN                = 801;
    const LOG_SHARE_RESTAURNT              = 802;
    const LOG_SHARE_PHOTO                  = 803;
    const LOG_SEARCH_RESTAURANT            = 900;
    const LOG_SEARCH_USER                  = 901;

    const ALL_TAG_PHOTO = 'all_img.png';
    const ALL_TAG_NAME = 'All';
}
