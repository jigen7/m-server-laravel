<?php
namespace App\Http\Helpers\Utilities;

class FileUtilities
{
    public static function fileSizeExceedCheck ($filesize_in_bytes)
    {
        $upload_max_filesize = ini_get('upload_max_filesize');
        $upload_max_filesize_unit = strtolower(
            $upload_max_filesize{strlen($upload_max_filesize) - 1}
        );
        $upload_max_filesize_value = substr($upload_max_filesize, 0, -1);
        switch($upload_max_filesize_unit) {
            case 'k':
                $upload_max_filesize_bytes = $upload_max_filesize_value * 1000;
                break;
            case 'm':
                $upload_max_filesize_bytes = $upload_max_filesize_value * 1000000;
                break;
            case 'g':
                $upload_max_filesize_bytes = $upload_max_filesize_value * 1000000000;
                break;
            default:
                $upload_max_filesize_bytes = $upload_max_filesize;
                break;
        }
        if($filesize_in_bytes > $upload_max_filesize_bytes) {
            return true;
        }
        return false;
    }
}

?>