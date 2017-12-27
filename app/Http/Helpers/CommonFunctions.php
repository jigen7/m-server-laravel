<?php
use App\Http\Models\CheckIns;
use App\Http\Models\Comments;
use App\Http\Models\Bookmarks;
use App\Http\Models\Photos;
use App\Http\Models\Restaurants;
use App\Http\Models\Reviews;
use App\Http\Models\Users;
use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use Jenssegers\Agent\Facades\Agent;
use Carbon\Carbon;

/************** Common Functions ***************/

function testcommon()
{
    return "yo yo common";
}

function showErrorResponse($message, $status_code = HTTP_BAD_REQUEST, $error_code = CONSTANTS::ERROR_CODE_GENERAL)
{
    $response[KeyParser::error] = array(
        KeyParser::error_code => $error_code,
        KeyParser::message => $message,
        KeyParser::status_code => $status_code,
    );

    return response()->json($response, $status_code);
}

function validateParameters($data)
{
    if ((!isset($data['follower_id']) && !isset($data['follower_fb_id'])) ||
        (!isset($data['following_id']) && !isset($data['following_fb_id']))
    ) {
        return false;
    }

    return true;
}

function isSameUser($follower_id, $following_id)
{
    if ($follower_id == $following_id) {
        return true;
    }

    return false;
}

function convertFbIdToId($fb_id)
{
    if (is_array($fb_id)) {
        return Users::whereIn('facebook_id', $fb_id)
            ->lists('id');
    }

    $user = Users::where('facebook_id', $fb_id)
        ->pluck('id');

    if (isset($user)) {
        return $user;
    }

    return false;
}

function default_key_value($index, $array, $default)
{
    return array_key_exists($index, $array) ? $array[$index] : $default;
}

function checkDeviceType($detect_type)
{
    $device_type = 'Unknown';
    if (isset($detect_type)) {
        $d_type = strtolower(trim($detect_type));
        if ($d_type === strtolower(CONSTANTS::DEVICE_IOS)) {
            $device_type = CONSTANTS::DEVICE_IOS;
        } elseif ($d_type === strtolower(CONSTANTS::DEVICE_ANDROID)) {
            $device_type = CONSTANTS::DEVICE_ANDROID;
        }
    }

    return $device_type;
}


/**
 * Use in FilterAfter Log Functionalities
 * @return string
 */
function logGetDeviceType(){

    if (Agent::isAndroidOS()){
        return CONSTANTS::DEVICE_ANDROID;
    }
    elseif (Agent::is('iPhone')){
        return CONSTANTS::DEVICE_IOS;
    }
    else{
        //$browser = Agent::browser();
        //$version = Agent::version($browser);

        $platform = Agent::platform();
        $version = Agent::version($platform);

        return $platform.' '.$version;
    }
}

/**
 * Use in FilterAfter Log Functionalities
 * @return string
 */
function logGetDeviceName()
{

    if (Agent::is('Android')) {
        $device = Agent::device();
        $version = Agent::version($device);
        return $device . ' ' . $version;
    } elseif (Agent::is('iPhone')) {
        $device = Agent::device();
        $version = Agent::version($device);
        return $device . ' ' . $version;
    } else {
        $browser = Agent::browser();
        $version = Agent::version($browser);
        return $browser . ' ' . $version;
    }
}

function elapsedTime($timestamp)
{
    $diff = Carbon::createFromTimeStamp(strtotime($timestamp))->diffForHumans();

    $diff_array = explode(' ', $diff);

    switch ($diff_array[1]) {
        case 'second':
        case 'seconds':
            $diff_array[1] = 's';
            break;
        case 'minute':
        case 'minutes':
            $diff_array[1] = 'mi';
            break;
        case 'hour':
        case 'hours':
            $diff_array[1] = 'h';
            break;
        case 'day':
        case 'days':
            $diff_array[1] = 'd';
            break;
        case 'week':
        case 'weeks':
            $diff_array[1] = 'w';
            break;
        case 'month':
        case 'months':
            $diff_array[1] = 'mo';
            break;
        case 'year':
        case 'years':
            $diff_array[1] = 'y';
            break;
    }

    $elapsed = $diff_array[0] . $diff_array[1] . " " . $diff_array[2];

    return $elapsed;
}

function elapsedTimeNotifications($timestamp)
{
    $strtotime = strtotime($timestamp);
    $time = time() - $strtotime;
    $time = ($time == 0) ? 1 : $time;

    $year_diff = date('Y') - date('Y', $strtotime);

    if ($year_diff > 0) {
        $elapsed = date('d M y, h:i A', $strtotime);
    } elseif ($time > 2592000) {
        $elapsed = date('d M, h:i A', $strtotime);
    } else {
        $elapsed = Carbon::createFromTimeStamp(strtotime($timestamp))->diffForHumans();
    }

    return $elapsed;
}

function escapeKey($key)
{
    return str_replace(['%', '_'], ['\%', '\_'], $key);
}

function checkTypeId($type, $type_id) {
    switch ($type) {
        case CONSTANTS::REVIEW: $object = Reviews::find($type_id);
            $message = "Review does not exist";
            $error_code = CONSTANTS::ERROR_CODE_REVIEW_MISSING;
            break;
        case CONSTANTS::CHECKIN: $object = CheckIns::find($type_id);
            $message = "Checkin does not exist";
            $error_code = CONSTANTS::ERROR_CODE_CHECKIN_MISSING;
            break;
        case CONSTANTS::BOOKMARK: $object = Bookmarks::find($type_id);
            $message = "Bookmark does not exist";
            $error_code = CONSTANTS::ERROR_CODE_GENERAL;
            break;
        case CONSTANTS::COMMENT: $object = Comments::find($type_id);
            $message = "Comment does not exist";
            $error_code = CONSTANTS::ERROR_CODE_GENERAL;
            break;
        case CONSTANTS::PHOTO: $object = Photos::find($type_id);
            $message = "Photo does not exist";
            $error_code = CONSTANTS::ERROR_CODE_PHOTO_MISSING;
            break;
        case CONSTANTS::RESTAURANT: $object = Restaurants::find($type_id);
            $message = "Restaurant does not exist";
            $error_code = CONSTANTS::ERROR_CODE_GENERAL;
            break;
        case 'user': $object = Users::find($type_id);
            $message = "User does not exist";
            $error_code = CONSTANTS::ERROR_CODE_GENERAL;
            break;
        default: return showErrorResponse('Invalid type', HTTP_ACCEPTED, CONSTANTS::ERROR_CODE_INVALID_TYPE);
    }

    if (!$object) {
        return showErrorResponse($message, HTTP_ACCEPTED, $error_code);
    }

    return false;
}

function utf8UriEncode($utf8_string, $length = 0)
{
    $unicode = '';
    $values = array();
    $num_octets = 1;
    $unicode_length = 0;
    mbstringBinarySafeEncoding();
    $string_length = strlen($utf8_string);
    resetMbstringEncoding();

    for ($i = 0; $i < $string_length; $i++ ) {
        $value = ord( $utf8_string[ $i ] );

        if ($value < 128) {
            if ($length && ($unicode_length >= $length)) {
                break;
            }

            $unicode .= chr($value);
            $unicode_length++;
        } else {
            if (count($values) == 0) {
                if ($value < 224) {
                    $num_octets = 2;
                } elseif ($value < 240) {
                    $num_octets = 3;
                } else {
                    $num_octets = 4;
                }
            }

            $values[] = $value;

            if ($length && ($unicode_length + ($num_octets * 3)) > $length) {
                break;
            }

            if (count( $values ) == $num_octets) {
                for ($j = 0; $j < $num_octets; $j++) {
                    $unicode .= '%' . dechex($values[$j]);
                }

                $unicode_length += $num_octets * 3;
                $values = array();
                $num_octets = 1;
            }
        }
    }

    return $unicode;
}

function resetMbstringEncoding()
{
    mbstringBinarySafeEncoding(true);
}

function mbstringBinarySafeEncoding($reset = false)
{
    static $encodings = array();
    static $overloaded = null;

    if (is_null($overloaded)) {
        $overloaded = function_exists('mb_internal_encoding') && (ini_get('mbstring.func_overload') & 2 );
    }


    if ($overloaded === false) {
        return;
    }

    if (!$reset) {
        $encoding = mb_internal_encoding();
        array_push($encodings, $encoding);
        mb_internal_encoding('ISO-8859-1');
    }

    if ($reset && $encodings) {
        $encoding = array_pop($encodings);
        mb_internal_encoding($encoding);
    }
}

function seemsUtf8($str)
{
    mbstringBinarySafeEncoding();
    $length = strlen($str);
    resetMbstringEncoding();

    for ($i = 0; $i < $length; $i++) {
        $c = ord($str[$i]);

        if ($c < 0x80) {
            $n = 0;
        } elseif (($c & 0xE0) == 0xC0) {
            $n = 1;
        } elseif (($c & 0xF0) == 0xE0) {
            $n = 2;
        } elseif (($c & 0xF8) == 0xF0) {
            $n = 3;
        } elseif (($c & 0xFC) == 0xF8) {
            $n = 4;
        } elseif (($c & 0xFE) == 0xFC) {
            $n = 5;
        } else {
            return false;
        }

        for ($j = 0; $j < $n; $j++) {
            if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
                return false;
            }
        }
    }

    return true;
}

function getSlugName($title)
{
    $title = strip_tags($title);
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    $title = str_replace('%', '', $title);
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

    if (seemsUtf8($title)) {
        if (function_exists('mb_strtolower')) {
            $title = mb_strtolower($title, 'UTF-8');
        }

        $title = utf8UriEncode($title, 200);
    }

    $title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title);
    $title = str_replace('.', '-', $title);
    $title = str_replace(array('%c2%a0', '%e2%80%93', '%e2%80%94'), '-', $title);
    $title = str_replace(
        array(
            '%c2%a1', '%c2%bf',
            '%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
            '%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
            '%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
            '%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
            '%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
            '%cc%80', '%cc%84', '%cc%8c',
        ),
        '',
        $title
    );
    $title = str_replace('%c3%97', 'x', $title);
    $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
    $title = preg_replace('/\s+/', '-', $title);
    $title = preg_replace('|-+|', '-', $title);
    $title = trim($title, '-');
    return $title;
}

function searchArrayByIndex($input_array, $key, $value)
{
    $count = 0;

    foreach ($input_array as $element) {
        if ($element[$key] == $value) {
            $count = $count + 1;
        }
    }

    return $count;
}

/**
 * Return UUID in version 4 format
 *
 * @return string
 */
function getUuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function recordEncode ($number)
{
    $shortener = new cogpowered\Shortener\Shortener();
    return $shortener->encode($number);
}

