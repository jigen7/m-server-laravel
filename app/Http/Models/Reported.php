<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;

class Reported extends Model {

    protected $table = 'reported';

    public $timestamps = false;

    /*********************************** START ACCESSOR METHODS ************************************/



    /*********************************** END ACCESSOR METHODS ************************************/



    /*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Adds a new report and returns inserted data upon success
     *
     * @param $data
     * @return Reported
     * @throws \Exception
     */
    public function addReport($data)
    {
        try {
            $this->type = $data['type'];
            $this->type_id = $data['type_id'];
            $this->reason  = $data['reason'];
            $this->report_status  = CONSTANTS::REPORTED_UNREAD;
            $this->reported_by  = $data['user_id'];
            $this->date_created  = date('Y-m-d H:i:s');
            $this->modified_by  = $data['user_id'];
            $this->date_modified  = date('Y-m-d H:i:s');
            $this->save();

            return $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*************************** END MUTATORS SETTER METHODS ************************************/

    /**
     * Checks if the photo has already been reported by the user
     *
     * @param $type_id
     * @param $user_id
     * @return bool
     */
    public static function isExistingPhotoReport($type_id, $user_id)
    {
        $report_data = self::WHERE('type', CONSTANTS::PHOTO)
            ->WHERE('type_id', $type_id)
            ->WHERE('reported_by', $user_id)
            ->get();

        if ($report_data->count()) {
            return true;
        }

        return false;
    }

}
