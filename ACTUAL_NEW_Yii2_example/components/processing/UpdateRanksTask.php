<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\reference\ConsoleTask;
use app\models\reference\Item;
use yii\base\BaseObject;
use yii\helpers\FileHelper;

class UpdateRanksTask extends BaseObject implements ConsoleTaskInterface
{
    public static $ranksUrl = 'https://irida.vseinstrumenti.ru/api/v1/biparser/index.php?path=%2Fshared%2FЦенообразование%2FДля%20Прайсинга%2Fproduct_ranks_for_matching&output';
    public static $ranksFileEncoding = 'UTF-16';

    public static function processTask(ConsoleTask $consoleTask)
    {
        $tmpFile = \Yii::getAlias('@app/runtime/item_ranks.csv');

        if (file_exists($tmpFile)) {
            FileHelper::unlink($tmpFile);
        }

        echo date('H:i:s').' Start download '.self::$ranksUrl.PHP_EOL;
        self::downloadFile(self::$ranksUrl, $tmpFile);
        echo date('H:i:s').' End download '.$tmpFile.PHP_EOL;

        $group      = [];
        $currRank = -1;
        $groupCount = 0;
        $fn = fopen($tmpFile,'rb');
        $encoding = null;
        $firstLine = true;
        $encoding = self::$ranksFileEncoding;
        while(! feof($fn))  {
            $line = fgets($fn);
            if ($firstLine) {
                $firstLine = false;
                continue;
            }
            if ($encoding === null) {
                $encoding = mb_detect_encoding($line, 'UTF-16,UTF-8,windows-1251', true);
                if (strtolower($encoding) === 'utf-8') {
                    $encoding = false;
                }
            }
            $row = str_getcsv($line,"\t", '"');
            if (count($row) !== 3) {
                echo 'bad row'.PHP_EOL;
                print_r($row);
                continue;
            }
            if (empty($row[0])) {
                break;
            }

            $row[1] = null;
            $id     = $row[0];
            $rank   = $row[2];
            if ($encoding) {
                $id = mb_convert_encoding($id, 'UTF-8', $encoding);
                $rank = (int)mb_convert_encoding($rank, 'UTF-8', $encoding);
            }
            if ($groupCount > 1000 || $currRank !== $rank) {
                if ($groupCount > 0 && $currRank > 0) {
                    Item::updateAll([
                        'sales_rank' => $currRank
                    ],[
                        'id'         => $group
                    ]);
                    echo $currRank.' rank updated '.$groupCount.PHP_EOL;
                }
                $group      = [];
                $groupCount = 0;
                $currRank = $rank;
            }
            $group[] = $id;
            $groupCount++;
        }

        if ($groupCount > 0) {
            echo 'processing last'.PHP_EOL;
            Item::updateAll([
                'sales_rank' => $currRank
            ],[
                'id'         => $group
            ]);
            echo $currRank.' rank updated '.$groupCount.PHP_EOL;
        }

        fclose($fn);

    }

    public static function downloadFile($url, $path)
    {
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $newfname = $path;
        $file = fopen ($url, 'rb', false, stream_context_create($arrContextOptions));
        if ($file) {
            $newf = fopen ($newfname, 'wb');
            if ($newf) {
                while(!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
        }
    }
}