<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class UsersCms extends Model
{
    protected $table = 'users';

    /**
     * Get all users by search criteria
     *
     * @param string $from
     * @param string $to
     * @return UsersCms
     */
    public static function getUsers($from, $to)
    {
        $users = new UsersCms();

        if ($from) {
            $users = $users->where('users.date_created', '>=', $from);
        }

        if ($to) {
            $users = $users->where('users.date_created', '<=', $to);
        }

        $users = $users->get();

        return $users;
    }
}