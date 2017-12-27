<?php namespace App\Http\Models;

use App\Http\Helpers\CONSTANTS;
use App\Http\Helpers\KeyParser;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model {

    protected $table = 'comments';

    public $timestamps = false;

    /*********************************** START ACCESSOR METHODS ************************************/

    /**
     * Get comment count by user id
     *
     * @param $user_id
     * @return mixed
     */
    public static function getCountByUserId($user_id)
    {
        $user_comments = self::where('user_id', $user_id)->get();
        $user_valid_comments = 0;
        foreach ($user_comments as $user_comment) {
            switch ($user_comment->type) {
                case CONSTANTS::REVIEW:
                    $review = Reviews::find($user_comment->type_id);

                    if ($review && Restaurants::isExists($review->restaurant_id)) {
                        $user_valid_comments++;
                    }
                    break;
                case CONSTANTS::CHECKIN:
                    $checkin = CheckIns::find($user_comment->type_id);

                    if ($checkin && Restaurants::isExists($checkin->restaurant_id)) {
                        $user_valid_comments++;
                    }
                    break;
                case CONSTANTS::PHOTO:
                    $photo = Photos::find($user_comment->type_id);

                    if($photo && Restaurants::isExists($photo->restaurant_id)) {
                        $user_valid_comments++;
                    }
                    break;
            }
        }
        return $user_valid_comments;
    }

    /**
     * Get Count by type
     * 1 - Review , 2 - Checkin, 5 - Photo
     *
     * @param $type
     * @param $type_id
     */
    public static function getCountByType($type, $type_id)
    {
        return self::where('type_id', $type_id)
            ->where('type', $type)
            ->count();
    } // end getCountByType

    /**
     * Get by type
     * 1 - Review , 2 - Checkin, 5 - Photo
     *
     * @param $type
     * @param $type_id
     */
    public static function getByType($type, $type_id, $date_range = false)
    {
        $comments = self::where(array(
            'type' => $type,
            'type_id' => $type_id,
            'status' => CONSTANTS::STATUS_ENABLED

        ));
        if(isset($date_range[KeyParser::date_from]) && isset($date_range[KeyParser::date_to])) {
            $comments->whereBetween('date_created', array($date_range[KeyParser::date_from], $date_range[KeyParser::date_to]))
                ->orderBy('date_created', CONSTANTS::ORDER_DESC);
        }
        return $comments->get();
    } // end getCountByType

    /**
     * Get by type
     * With pagination
     *
     * @param $type
     * @param $type_id
     * @return mixed
     */
    public static function getByTypePaginate($type, $type_id)
    {
        return self::where('type', $type)
            ->where('type_id', $type_id)
            ->orderBy('date_created', CONSTANTS::ORDER_DESC)
            ->paginate(CONSTANTS::COMMENTS_GET_BY_TYPE_PAGINATION_LIMIT);
    }

    /*********************************** END ACCESSOR METHODS ************************************/


    /*************************** START MUTATORS SETTER METHODS ************************************/

    /**
     * Add new comment to table
     *
     * @param $text
     * @param $type
     * @param $type_id
     * @param $user_id
     * @throws \Exception
     */
    public function addComment($text, $type, $type_id, $user_id)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $type_object = null;

            switch ($type) {
                case CONSTANTS::REVIEW:
                    $type_object = Reviews::find($type_id);

                    if (!$type_object) {
                        //Review not Found
                        throw new \Exception(CONSTANTS::ERROR_CODE_REVIEW_MISSING);
                    }

                    $this->type = CONSTANTS::REVIEW;
                    $comment_type = CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_REVIEW;
                    break;
                case CONSTANTS::CHECKIN:
                    $type_object = CheckIns::find($type_id);

                    if (!$type_object) {
                        throw new \Exception(CONSTANTS::ERROR_CODE_CHECKIN_MISSING);
                    }

                    $this->type = CONSTANTS::CHECKIN;
                    $comment_type = CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_CHECKIN;
                    break;
                case CONSTANTS::PHOTO:
                    $type_object = Photos::find($type_id);

                    if (!$type_object) {
                        throw new \Exception(CONSTANTS::ERROR_CODE_PHOTO_MISSING);
                    }

                    $this->type = CONSTANTS::PHOTO;
                    $comment_type = CONSTANTS::NOTIFICATION_TYPE_COMMENT_ON_PHOTO;
                    break;
                default:
                    throw new \Exception(CONSTANTS::ERROR_CODE_INVALID_TYPE);
            }

            $this->type_id = $type_id;
            $this->comment = $text;
            $this->status = CONSTANTS::STATUS_ENABLED;
            $this->user_id = $user_id;
            $this->date_created = date('Y-m-d H:i:s');
            $this->save();

            $restaurant_id = $type_object['restaurant_id'];
            $owner_id = $type_object['user_id'];

            if ($user_id != $owner_id) {
                $notification_data = new Notification();
                $notification_data->addCommentNotification($user_id, $owner_id, $comment_type, $type_id, $restaurant_id);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end addComment

    /**
     * Delete comment and likes of the comment
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteComment()
    {
        $connection = $this->getConnection();
        try {
            $connection->beginTransaction();
                // TODO: Delete associated notification
            $like = new Like();
            $like->deleteLikes(CONSTANTS::COMMENT, $this->id);
            $this->delete();
            $connection->commit();
            return $this;
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end deleteComment

    /**
     * Delete comment/s and likes of the comment/s by type
     *
     * @param $type
     * @param $type_id
     * @return mixed
     * @throws \Exception
     */
    public function deleteCommentByType($type, $type_id)
    {
        try {
            $connection = $this->getConnection();
            $connection->beginTransaction();
            $comments = Comments::where('type', $type)->where('type_id', $type_id);

            if ($comments) {

                // TODO: Delete associated notification
                foreach($comments as $comment) {
                    $like = new Like();
                    $like->deleteLikes(CONSTANTS::COMMENT, $comment->id);
                    $comment->delete();
                }
            } else {
                throw new \Exception('No comments found');
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end deleteComment

    /**
     * Update comment
     *
     * @param $id
     * @param $user_id
     * @param $text
     * @return \Illuminate\Support\Collection|null|static
     * @throws \Exception
     */
    public function editComment($id, $user_id, $text)
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $comment = self::find($id);

            if (!$comment) {
                throw new \Exception('Comment not found');
            }

            if ($comment->user_id != $user_id) {
                throw new \Exception('User is not the original owner of the comment');
            }

            $comment->comment = $text;
            $comment->save();

            $connection->commit();

            return $comment;
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    } // end editComment

    /*************************** END MUTATORS SETTER METHODS ************************************/

}