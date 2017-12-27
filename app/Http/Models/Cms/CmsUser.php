<?php
namespace App\Http\Models\Cms;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Database\Eloquent\Model;

class CmsUser extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    protected $table = 'cms_user';
    public $timestamps = false;

    public static function validateCmsUser($email)
    {
        $user = CmsUser::where('email', $email)
            ->first();
        if ($user) {
            return $user;
        }
        return false;
    }
}