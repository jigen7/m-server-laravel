<?php
namespace App\Http\Models\Cms;

use App\Http\Helpers\CONSTANTS;
use Illuminate\Database\Eloquent\Model;

class CommentsCms extends Model
{
    protected $table = 'comments';

    /**
     * Get comments with their corresponding user names by review id
     *
     * @param $review_id
     * @return mixed
     */
    public static function getByReviewId($review_id)
    {
        $columns = array(
            'comments.comment',
            'users.firstname',
            'users.lastname',
            'comments.date_created'
        );

        return self::leftJoin('users', 'users.id', '=', 'comments.user_id')
            ->where('type', CONSTANTS::REVIEW)
            ->where('type_id', $review_id)
            ->orderBy('comments.date_created', CONSTANTS::ORDER_DESC)
            ->get($columns);
    }
}