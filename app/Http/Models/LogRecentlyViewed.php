<?php
namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;

class LogRecentlyViewed extends Model
{
    protected $table = 'log_recently_viewed';
    public $timestamps = false;

    /**
     * Get log_recently_viewed data by user_id
     *
     * @param $user_id
     * @param string $order_by
     * @return mixed
     */
    public static function getByUserId($user_id, $order_by = CONSTANTS::ORDER_DESC)
    {
        return self::where('user_id', $user_id)->orderBy('date_modified', $order_by)->paginate(CONSTANTS::LOG_RECENTLY_VIEWED_COUNT);
    }

    /**
     * Add new log_recently_viewed data
     *
     * @param $user_id
     * @param $restaurant_id
     * @throws \Exception
     */
    public function addNewLog($user_id, $restaurant_id)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $where = array(
                'user_id' => $user_id,
                'restaurant_id' => $restaurant_id
            );
            $log = LogRecentlyViewed::where($where)->get()->first();

            if ($log) {
                return;
            }

            /*$logs = LogRecentlyViewed::getByUserId($user_id, CONSTANTS::ORDER_ASC);

            if ($logs->count() == CONSTANTS::LOG_RECENTLY_VIEWED_COUNT) {
                $logs[0]->delete();
            }*/

            $log = new LogRecentlyViewed();
            $log->user_id = $user_id;
            $log->restaurant_id = $restaurant_id;
            $log->save();
            $logs = LogRecentlyViewed::getByUserId($user_id);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}