<?php namespace App\Http\Middleware;

use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use App\Http\Models\Logs;
use Illuminate\Contracts\Routing\Middleware;
use Closure;

class FiltersAfter implements Middleware {

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Do stuff
        try {
            $route_name = $request->route()->getAction()['as'];
            $status_code = $response->getStatusCode();

            //Check if Response is 200 and also have data array in response
            if ($status_code == HTTP_RESPONSE_OK && $response->getData()->data) {
                $data = $response->getData()->data;

                $access_id = (int)$request->get('access_id');
                $latitude = (double)$request->get('lat',0);
                $longitude = (double)$request->get('long',0);
                $device_type = logGetDeviceType();
                $params = array();
                $is_logged = true;

                $log = new Logs();
                
                switch ($route_name) {
                    case 'restaurant_view' :
                        $type = CONSTANTS::LOG_VIEW_RESTAURANT;
                        $type_id =  $data->restaurant->id;
                        break;
                    case 'reviews_view' :
                        $type = CONSTANTS::LOG_VIEW_REVIEW;
                        $type_id = $data->review->id;
                        break;
                    case 'photos_type_view' :
                        // ask client when viewing photo uses this api
                        // only log the first photo if uses /reviews /user /checkin many objects returned
                        $type = CONSTANTS::LOG_VIEW_PHOTO;
                        $photo_ids = array();

                        foreach ($data as $photos) {
                            $photo_ids[] = $photos->photo->id;
                        }

                        $params[KeyParser::photo_ids] = implode(',', $photo_ids);
                        $params[KeyParser::type] = CONSTANTS::LOG_VIEW_PHOTO;
                        $type_id = 0;
                        break;
                    case 'reviews_add' :
                        $type = CONSTANTS::LOG_REVIEW_ADD;
                        $type_id = $data->review->id;
                        break;
                    case 'reviews_edit' :
                        $type = CONSTANTS::LOG_REVIEW_EDIT;
                        $type_id = $data->review->id;
                        break;
                    case 'checkins_add' :
                        $type = CONSTANTS::LOG_CHECKIN_ADD;
                        $type_id = $data->checkin->id;
                        break;
                    case 'checkins_edit' :
                        $type = CONSTANTS::LOG_CHECKIN_EDIT;
                        $type_id = $data->checkin->id;
                        break;
                    case 'bookmarks_add' :
                        $type = CONSTANTS::LOG_BOOKMARK_ADD;
                        $type_id = $data->bookmark_id;
                        break;
                    case 'bookmarks_delete' :
                        $type = CONSTANTS::LOG_BOOKMARK_DELETE;
                        $type_id = $data->id;
                        break;
                    case 'comments_add' :
                        $data_type = $data->comment->type;

                        switch ($data_type) {
                            case CONSTANTS::REVIEW :
                                $type = CONSTANTS::LOG_COMMENT_ADD_REVIEW;
                                break;
                            case CONSTANTS::CHECKIN :
                                $type = CONSTANTS::LOG_COMMENT_ADD_CHECKIN;
                                break;
                            case CONSTANTS::PHOTO :
                                $type = CONSTANTS::LOG_COMMENT_ADD_PHOTO;
                                break;
                            default :
                                $type = CONSTANTS::LOG_COMMENT_ADD;
                                break;
                        }//end inner switch

                        $type_id = $data->comment->id;
                        break;
                    case 'like_add' :
                        $data_type = $data->type;

                        switch ($data_type) {
                            case CONSTANTS::REVIEW :
                                $type = CONSTANTS::LOG_LIKE_REVIEW;
                                break;
                            case CONSTANTS::CHECKIN :
                                $type = CONSTANTS::LOG_LIKE_CHECKIN;
                                break;
                            case CONSTANTS::COMMENT :
                                $type = CONSTANTS::LOG_LIKE_COMMENT;
                                break;
                            case CONSTANTS::PHOTO :
                                $type = CONSTANTS::LOG_LIKE_PHOTO;
                                break;
                            default :
                                $type = CONSTANTS::LOG_LIKE;
                                break;
                        } // end inner switch

                        $type_id = $data->type_id;
                        break;
                    case 'photos_upload' :
                        $type = CONSTANTS::PHOTO_UPLOAD_RESTAURANT;
                        $photo_ids = array();

                        foreach ($data->photos as $photo) {
                            $photo_ids[] = $photo->id;
                        }

                        $params[KeyParser::photo_ids] = implode(',', $photo_ids);
                        $type_id = 0;
                        break;
                    case 'follow_user' :
                        $type = CONSTANTS::LOG_FOLLOW_FOLLOW;
                        $type_id = $data->user->id;
                        break;
                    case 'unfollow_user ' :
                        $type = CONSTANTS::LOG_FOLLOW_UNFOLLOW;
                        $type_id = 0;
                        break;
                    case 'restaurant_search' :
                        $type = CONSTANTS::LOG_SEARCH_RESTAURANT;
                        $type_id = 0;
                        $params[KeyParser::search_key] = $request->get('name');
                        break;
                    case 'users_search' :
                        $type = CONSTANTS::LOG_SEARCH_USER;
                        $type_id = 0;
                        $params[KeyParser::search_key] = $request->get('key');
                        break;
                    default :
                        $type = 0;
                        $type_id = 0;
                        $is_logged = false;
                        break;

                } // end switch

                $params = $params ? json_encode($params) : '';

                $array = array(
                    KeyParser::type => $type,
                    KeyParser::type_id => $type_id,
                    KeyParser::params => $params,
                    KeyParser::device_type => $device_type,
                    KeyParser::latitude => $latitude,
                    KeyParser::longitude => $longitude,
                    KeyParser::user_id => $access_id,
                );

                // save logs only logs specified routes
                if($is_logged === TRUE){
                    $log->addLog($array);
                }
            } else { // end if status code ok
                //log error also
                // if error array display
            }
        } catch (\Exception $e) { //end try
            // TODO find a way to separate the log generated for postfilter errors
            // \Log::useDailyFiles(storage_path().'/logs/postfilter/postfilter.log',0 ,'info');
            // \Log::debug('This is some useful information.');

            //if error continue
            //return $response;
            throw ($e);
        } //end catch

        // dd($response); to get data
        return $response;
    }

}