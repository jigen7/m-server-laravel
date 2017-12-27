<?php
namespace App\Http\Models\Cms;

use App\Http\Helpers\CONSTANTS;
use App\Http\Models\Reported;
use Illuminate\Database\Eloquent\Model;

class ReportedCms extends Model
{
    protected $table = 'reported';

    /**
     * Get reported restaurants by search criteria
     *
     * @param string $from
     * @param string $to
     * @return Reported
     */
    public static function getRestaurants($from, $to)
    {
        $columns = array(
            'reported.id',
            'restaurants.id as restaurant_id',
            'restaurants.name',
            'restaurants.address',
            'reported.reason',
            'reported.report_status',
            'reported.date_created'
        );

        $reports = New Reported();
        $reports = $reports->leftJoin('restaurants', 'reported.type_id', '=', 'restaurants.id')
            ->where('type', CONSTANTS::RESTAURANT);

        if ($from) {
            $reports = $reports->where('reported.date_created', '>=', $from);
        }

        if ($to) {
            $reports = $reports->where('reported.date_created', '<=', $to);
        }

        $reports = $reports->latest('reported.date_created')
            ->get($columns);

        return $reports;
    }

    /**
     * Get reported photos by search criteria
     *
     * @param string $from
     * @param string $to
     * @return Reported
     */
    public static function getPhotos($from, $to)
    {
        $columns = array(
            'reported.id',
            'restaurants.name',
            'photos.id as photo_id',
            'photos.url',
            'reported.reason',
            'reported.report_status',
            'reported.date_created'
        );

        $reports = New Reported();
        $reports = $reports->leftJoin('photos', 'reported.type_id', '=', 'photos.id')
            ->leftJoin('restaurants', 'photos.restaurant_id', '=', 'restaurants.id')
            ->where('reported.type', CONSTANTS::PHOTO);

        if ($from) {
            $reports = $reports->where('reported.date_created', '>=', $from);
        }

        if ($to) {
            $reports = $reports->where('reported.date_created', '<=', $to);
        }

        $reports = $reports->latest('reported.date_created')
            ->get($columns);

        return $reports;
    }

    /**
     * Update report status
     *
     * @param $reported_id
     * @param $report_status
     */
    public static function updateReportStatus($reported_id, $report_status)
    {
        $report = New Reported();
        $report->where('id', $reported_id )->update(['report_status' => $report_status]);
    }

}
