<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.12.18
 * Time: 12:19
 */

namespace App\Services;


use Illuminate\Support\Facades\File;

class FileType
{
    static $microsoftOfficeFormats = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];

    /**
     * @param $fileName
     * @return bool|false|string
     */
    static function getType($fileName)
    {
        $fileUrl = false;

        $file = null;

        if(!file_exists($fileName))
        {
            try {
                $content = file_get_contents($fileName);
            } catch (\Exception $e)
            {
                return null;
            }

            if(!$content)
            {
                return null;
            }

            $fileName = storage_path(str_random(25));
            file_put_contents($fileName, $content);
            $fileUrl = true;
        }

        $mimeType = File::mimeType($fileName);

        if($fileUrl)
        {
            File::delete($fileName);
        }

        return in_array($mimeType, self::$microsoftOfficeFormats) ? 'msoffice' : $mimeType;
    }
}