<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesPhotos extends Model {

	protected $table = 'category_photos';

    public static function getPhotoByCategoryId($category_id)
    {
        return self::select('photo_url')
            ->where('category_id', $category_id)
            ->first();
    }

/*********************************** START ACCESSOR METHODS ************************************/




/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/



/*************************** END MUTATORS SETTER METHODS ************************************/


}