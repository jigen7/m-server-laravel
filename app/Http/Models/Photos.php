<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\ModelFormatter;
use DateTime;

class Photos extends Model {

	protected $table = 'photos';

    public $timestamps = false;

/*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Get all photos using restaurant ID
     *
     * @param $restaurant_id
     * @return mixed
     */
    public static function getByRestaurantId($restaurant_id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->get();
    } // end getByRestaurantId


    /**
     * Get checkin photos using restaurant ID and checkin ID
     *
     * @param $restaurant_id
     * @param $type
     * @param $type__id
     * @return mixed
     */
    public static function getByRestaurantIdAndType($restaurant_id, $type, $type__id)
    {
        return self::where('restaurant_id', $restaurant_id)
            ->where('type', $type)
            ->where('type_id', $type__id)
            ->get();
    }

    /**
     * Get photos using type
     *
     * @param $type
     * @param $type_id
     * @return mixed
     */
    public static function getByType($type, $type_id)
    {
        return self::where('type', $type)
            ->where('type_id', $type_id)
            ->orderBy('date_uploaded', CONSTANTS::ORDER_DESC)
            ->get();
    } // end getByType

    public static function getByTypePagination($type, $type_id)
    {

        $photos = self::select('photos.*')
            ->leftJoin('restaurants', 'photos.restaurant_id', '=', 'restaurants.id');
        switch ($type) {
            case 'review':
                $photos->where('photos.type', CONSTANTS::REVIEW)->where('type_id', $type_id);
                break;
            case 'checkin':
                $photos->where('photos.type', CONSTANTS::CHECKIN)->where('type_id', $type_id);
                break;
            case 'photo':
                $photos->where('photos.id', $type_id);
                break;
            case 'restaurant' :
                $photos->where('photos.restaurant_id', $type_id);
                break;
            case 'user':
                $photos->where('photos.user_id', $type_id);
                break;
        } // end switch

        return $photos->where('photos.status', CONSTANTS::STATUS_ENABLED)
            ->whereNull('restaurants.deleted_at')
            ->orderBy('photos.date_uploaded', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::PHOTOS_GET_BY_TYPE_PAGINATION_LIMIT);
    }

/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Save Photos in DB
     *
     * @param $data_photos
     * @param $data_json
     * @param $type
     * @param $type_id
     */
    public function saveUploadedPhotos($data_photos, $data_json, $type, $type_id)
    {
        if (!isset($data_photos)) {
            return;
        }
        $return_array = array();


        foreach($data_photos as $photo){
            $filename_new = $this->moveUploadPhotos($photo, $data_json['restaurant_id'],$type, $type_id);

            $photos = new Photos();
            $photos->user_id = $data_json['user_id'];
            $photos->restaurant_id = $data_json['restaurant_id'];
            $photos->type = $type;
            $photos->type_id = $type_id;
            $photos->url = $filename_new;
            $photos->date_uploaded = date('Y-m-d H:i:s');

            if(isset($photo->text)) {
                $photos->text = $photo->text;
            }

            $photos->save();
            $return_array[] = $photos;
        }
        return $return_array;
    } // end saveUploadedPhotos

    public  function moveUploadPhotos($photo, $restaurant_id, $type, $type_id)
    {
        $destination = API_UPLOAD_DIR;

        switch($type){
            case 1 :
                $type_name = '-rev-';
                break;
            case 2 :
                $type_name = '-chk-';
                break;
            case 6 :
                $type_name = '-res-';
                break;
        } // end switch

        //generate datetime to append in image filename
        $datetime = date("mdYHis").rand(0,1000);

        $filename = $photo->getClientOriginalName();
        $filename_new = $datetime."-res-". $restaurant_id . $type_name . $type_id . "-" . $filename;

        $photo->move($destination,$filename_new);

        return $filename_new;
    } // end moveUploadPhotos

/*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Get count of valid photos per user
     *
     * @param $user_id
     * @return mixed
     */
    public static function getCountByUserId ($user_id)
    {
        return self::leftJoin('restaurants', 'photos.restaurant_id', '=', 'restaurants.id')
            ->whereNull('restaurants.deleted_at')
            ->where('photos.user_id', $user_id)
            ->where('status', CONSTANTS::STATUS_ENABLED)
            ->count();
    } // end getCountByUserId

    /**
     * Convert Photos Object to Array of Datas
     *
     * @param $photos
     * @return array
     */
    public static function convertPhotosToArray($photos){

        $photos_array = array();
        if(!$photos){
            return $photos_array;
        }

        foreach($photos as $photo){
            $photos_array[] =  ModelFormatter::photosFormat($photo);
        }

        return $photos_array;
    } // end convertPhotosToArray

} //end class