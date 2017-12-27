<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitiesPeer extends Model {

	protected $table = 'activities';


/********************************* START CUSTOM METHODS / FUNCTION ************************************/
    public static function custFunc(){

        return Activities::find(3);

    }

/********************************* END METHODS / FUNCTION *************************************/


}



