<?php
namespace App\Http\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class CategoriesCms extends Model
{
    protected $table = 'categories';
    public $timestamps = false;

    /**
     * Adds new category data
     *
     * @param $type
     * @param $name
     * @param int $id
     * @throws \Exception
     */
    public function addCategory($type, $name, $id = 0)
    {
        if (!$type) {
            throw new \Exception('Invalid category type');
        }

        if (!$name) {
            throw new \Exception('Invalid category name');
        }

        try {
            if ($id) {
                $this->id = $id;
            }

            $this->type = $type;
            $this->name = $name;
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Find a category by type and name
     *
     * @param $type
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public static function getByTypeAndName($type, $name)
    {
        if (!$type) {
            throw new \Exception('Invalid type for category');
        }

        if (!$name) {
            throw new \Exception('Invalid name for category');
        }

        $where = array(
            'type' => $type,
            'name' => $name
        );
        return self::where($where)->get();
    }
}