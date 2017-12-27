<?php namespace Config;
/***
 *   Define App Constant Here
 *   Sample Usage
 */

/*** General Config ***/
    // WEB_HOST - check .env file
    if (!defined('API_UPLOAD_DIR')) { define('API_UPLOAD_DIR', public_path().'/uploads/default'); } // use in photo uploads

// Google
    if (!defined('GOOGLE_APP_NAME')) { define('GOOGLE_APP_NAME',"Masarap"); }
    if (!defined('GOOGLE_CLIENT_ID')) { define('GOOGLE_CLIENT_ID',"163737196649-jms6adflebd966ntq8lt4fl3vnp4b5nb.apps.googleusercontent.com"); }
    if (!defined('GOOGLE_CLIENT_SECRET')) { define('GOOGLE_CLIENT_SECRET',"MABhosVPa7laX-56rObA80FE"); }
    if (!defined('GOOGLE_CLIENT_REDIRECT')) { define('GOOGLE_CLIENT_REDIRECT',"http://masarap.com.ph/web/app.php/login"); }
    if (!defined('GOOGLE_CLIENT_DEV_KEY')) { define('GOOGLE_CLIENT_DEV_KEY',"AIzaSyDbeFj1SmbtcO0_lzymFxYQKUw8qZ4BSM0"); }

// Push notification
    if (!defined('PUSH_NOTIFICATION_DEBUG')) { define('PUSH_NOTIFICATION_DEBUG', false); }

// Push notification for iOS
    if (!defined('APNS_HOST_DEV')) { define('APNS_HOST_DEV', 'ssl://gateway.sandbox.push.apple.com:2195'); }
    if (!defined('APNS_HOST_PROD')) { define('APNS_HOST_PROD', 'ssl://gateway.push.apple.com:2195'); }
    if (!defined('APNS_PEM_FILE_DEV')) { define('APNS_PEM_FILE_DEV', 'pushnotify/masarap-dev.pem'); }
    if (!defined('APNS_PEM_FILE_PROD')) { define('APNS_PEM_FILE_PROD', 'pushnotify/MasarapPushProd.pem'); }
    if (!defined('APNS_PASSPHRASE_DEV')) { define('APNS_PASSPHRASE_DEV', 'masarap'); }
    if (!defined('APNS_PASSPHRASE_PROD')) { define('APNS_PASSPHRASE_PROD', 'tric rullan'); }

// Push notification for Android
    if (!defined('GCM_API_KEY')) { define('GCM_API_KEY', 'AIzaSyDXPw1-EpmJ_QTItUnmGvJ-wHguqJ29cwc'); }
    if (!defined('GCM_API_URL')) { define('GCM_API_URL', 'https://android.googleapis.com/gcm/send'); }

// App store/Google play links
    if (!defined('APP_STORE_LINK')) { define('APP_STORE_LINK', "https://itunes.apple.com/ph/app/masarap!!!/id944867364?mt=8"); }
    if (!defined('PLAY_STORE_LINK')) { define('PLAY_STORE_LINK', "https://play.google.com/store/apps/details?id=com.klabcyscorpions.masarap"); }

//Maintenance CONFIG
    if (!defined('SERVER_STATUS')) { define('SERVER_STATUS', 0); } // 0 - Non Maintenance 1- Maintenance Mode

//PreFilters Config
    if (!defined('BYPASS_USER_AGENT_CHECK')) { define('BYPASS_USER_AGENT_CHECK', 1); } // 0 - Disable 1 - Enable

// HTTP Status Codes
    if (!defined('SERVER_MAINTENANCE_STATUS_CODE')) { define('SERVER_MAINTENANCE_STATUS_CODE', 288); }
    if (!defined('FORCE_UPDATE_STATUS_CODE')) { define('FORCE_UPDATE_STATUS_CODE', 289); }
    if (!defined('HTTP_BAD_REQUEST')) { define('HTTP_BAD_REQUEST', 400); }
    if (!defined('HTTP_METHOD_NOT_ALLOWED')) { define('HTTP_METHOD_NOT_ALLOWED', 405); }
    if (!defined('HTTP_UNPROCESSABLE_ENTITY')) { define('HTTP_UNPROCESSABLE_ENTITY', 422); }
    if (!defined('HTTP_ACCEPTED')) { define('HTTP_ACCEPTED', 202); }
    if (!defined('HTTP_RESPONSE_OK')) { define('HTTP_RESPONSE_OK', 200); }

// PDOException Status Codes
    if (!defined('INTEGRITY_CONSTRAINT_VIOLATION')) { define('INTEGRITY_CONSTRAINT_VIOLATION', 23000); }

// Client & Server Versions
    if (!defined('APP_VERSION')) { define('APP_VERSION', '1.2'); }
    if (!defined('SERVER_VERSION')) { define('SERVER_VERSION', '1.2'); }