<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model {

	protected $table = 'logs';
    public $timestamps = false;


/*********************************** START ACCESSOR METHODS ************************************/


/*********************************** END ACCESSOR METHODS ************************************/



/*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Save Log Data
     * @param $array
     * @return none
     */
     public function addLog($array)
     {
         $this->type = $array['type'];
         $this->type_id = $array['type_id'];
         $this->params = $array['params'];
         $this->device_type = $array['device_type'];
         $this->latitude = $array['latitude'];
         $this->longitude = $array['longitude'];
         $this->user_id = $array['user_id'];
         $this->date_created = date('Y-m-d H:i:s');
         $this->save();
     }


/*************************** END MUTATORS SETTER METHODS ************************************/


}