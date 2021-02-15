<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 24.10.18
 * Time: 11:22
 */

namespace App\Services;


class SmartId
{
    /**
     * @param array $photoPathList
     * @return array|object
     */
    static function parseAll($photoPathList = [])
    {
        if(!$photoPathList)
        {
            return [];
        }

        $resultDocument = [];

        foreach ($photoPathList as $photoPath)
        {
            if(file_exists(trim($photoPath))) {
                $resultOfParse = self::parseOnePhoto($photoPath);

                if(!empty($resultOfParse->str))
                {
                    foreach( $resultOfParse->str as $key => $field) {
                        $resultDocument[$key] = $field;
                    }
                    //array_collapse($resultDocument, $resultOfParse->str);
                }
            }
        }

        $resultDocument['init'] = 1;

        return (object) $resultDocument;
    }

    /**
     * @param $photoPath
     * @return mixed
     */
    static function parseOnePhoto($photoPath)
    {
        $command = [
            'php7.2',
            app_path('Http/Controllers/SmartID/SmartID.php'),
            $photoPath,
            app_path('Http/Controllers/SmartID/bundle_kaz_mrz_server.zip'),
            'kaz.id.type2'
        ];

        $result = shell_exec(implode(' ', $command));
        return json_decode($result);
    }
}