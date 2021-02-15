<?php


namespace App\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SearchCache
{
    const PREFIX = 'search_cache';

    const TABLE_ADMIN_USERS = 'admin_users';

    private static function getTablePrefix(string $tableName)
    {
        return self::PREFIX . ':' . $tableName . ':';
    }

    private static function getVarKey(string $varName)
    {
        return self::PREFIX . ':' . $varName;
    }

    /**
     * @param $tableName
     * @param $fieldNameList
     * @param null $query
     * @return bool
     */
    static function refresh($tableName, $fieldNameList, $query = null)
    {
        $prefix = self::getTablePrefix($tableName);

        $currentKeyList = Redis::keys($prefix . '*');

        if ($currentKeyList) {
            Redis::del($currentKeyList);
        }

        if ($query) {
            $allRows = $query;
        } else {
            $allRows = DB::table($tableName);
        }

        $rowsForCount = $allRows;
        $count = $rowsForCount->count();

        $fieldNameList[] = 'id';
        $allRows->select($fieldNameList)
            ->orderBy('id')
            ->chunk(1000, function ($rows) use ($prefix, $fieldNameList) {
                foreach ($rows as $row) {
                    foreach ($fieldNameList as $fieldName) {
                        if ($fieldName != 'id') {
                            Redis::set($prefix . $fieldName . ':' . urlencode(self::strtolower($row->$fieldName)) . ':' . $row->id, $row->id);
                        }
                    }
                }
            });

        Redis::set(self::PREFIX . ':' . $tableName . ':total_count', $count);

        return true;
    }

    /**
     * @param $tableName
     * @param $data
     * @return bool
     */
    static function refreshByData($tableName, $data)
    {
        $prefix = self::getTablePrefix($tableName);

        $currentKeyList = Redis::keys($prefix . '*');

        if ($currentKeyList) {
            Redis::del($currentKeyList);
        }

        $totalCount = 0;

        foreach ($data as $item) {
            if (isset($item['id'])) {
                $id = $item['id'];
                $totalCount++;

                foreach ($item as $field => $val) {
                    Redis::set($prefix . $field . ':' . urlencode(self::strtolower($val)) . ':' . $id, $id);
                }
            }
        }

        Redis::set(self::PREFIX . ':' . $tableName . ':total_count', $totalCount);

        return true;
    }

    /**
     * @param $tableName
     * @param $partText
     * @param null $fieldName
     * @param bool $fullMatch
     * @return mixed
     */
    static function search($tableName, $partText, $fieldName = null, $fullMatch = false)
    {
        $prefix = self::getTablePrefix($tableName);

        if ($fieldName) {
            $prefix .= $fieldName . ':';
        } else {
            $prefix .= '*:';
        }

        $postfix = $fullMatch ? ':*' : '*';
        $keys = Redis::keys($prefix . urlencode(self::strtolower($partText)) . $postfix);

        $idList = [];
        foreach ($keys as $key) {
            $idList[] = Redis::get($key);
        }

        return $idList;
    }

    /**
     * @param $tableName
     * @param $partText
     * @param null $fieldName
     * @return mixed
     */
    static function searchFull($tableName, $partText, $fieldName = null)
    {
        $prefix = self::getTablePrefix($tableName);

        if ($fieldName) {
            $prefix = $prefix . $fieldName . ':';
        } else {
            $prefix = $prefix . '*:';
        }

        $keys = Redis::keys($prefix . '*' . urlencode(self::strtolower($partText)) . '*');

        $idList = [];
        foreach ($keys as $key) {
            $idList[] = Redis::get($key);
        }

        return $idList;
    }

    /**
     * @param $tableName
     * @return mixed
     */
    static function totalCount($tableName)
    {
        /*$keys = Redis::keys(self::PREFIX . ':' . $tableName . ':id:*');

        return count($keys);*/
        return Redis::get(self::PREFIX . ':' . $tableName . ':total_count');
    }

    /**
     * @param $tableName
     * @param $id
     * @return mixed
     */
    static function delete($tableName, $id)
    {
        $keys = Redis::keys(self::PREFIX . ':' . $tableName . ':*:*:' . $id);

        if ($keys) {
            return Redis::del($keys);
        }

        return true;
    }

    /**
     * @param $tableName
     * @param $id
     * @param $data
     * @return bool
     */
    static function addOrUpdate($tableName, $id, $data)
    {
        $prefix = self::getTablePrefix($tableName);

        self::delete($tableName, $id);

        foreach ($data as $field => $val) {
            Redis::set($prefix . $field . ':' . urlencode(self::strtolower($val)) . ':' . $id, $id);
        }

        return true;
    }

    /**
     * @param $tableName
     * @param $id
     * @param $data
     * @return bool
     */
    static function addOrUpdateRepeatField($tableName, $id, $data)
    {
        $prefix = self::getTablePrefix($tableName);

        self::delete($tableName, $id);

        foreach ($data as $key => $value) {
            foreach ($value as $val) {
                Redis::set($prefix . $key . ':' . urlencode(self::strtolower($val)) . ':' . $id, $id);
            }
        }

        return true;
    }

    /**
     * @param $str
     * @return bool|false|mixed|string|string[]|null
     */
    static function strtolower($str)
    {
        return mb_strtolower($str);
    }

    public static function refreshJSONString(string $key, $data)
    {
        $redisKey = self::getVarKey($key);

        return Redis::set($redisKey, json_encode($data));
    }

    public static function getJsonData(string $varName)
    {
        $redisKey = self::getVarKey($varName);

        return json_decode(Redis::get($redisKey), true);
    }

    public static function filterDataByString(array $data, string $search) : array
    {
        $filteredData = [];

        foreach ($data as $item) {
            foreach ($item as $value) {
                if (strpos($value, $search) !== false) {
                    $filteredData[] = $item;
                    break;
                }
            }
        }

        return $filteredData;
    }

    public static function updateField($tableName, $id, $field, $value)
    {
        $prefix = self::getTablePrefix($tableName);

        self::deleteField($tableName, $id, $field);

        Redis::set($prefix . $field . ':' . urlencode(self::strtolower($value)) . ':' . $id, $id);

        return true;
    }

    private static function deleteField($tableName, $id, $field)
    {
        $prefix = self::getTablePrefix($tableName);

        $keys = Redis::keys($prefix . $field .':*:' . $id);
        if ($keys) {
            return Redis::del($keys);
        }
    }
}