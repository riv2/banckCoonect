<?php

namespace App\Models;


use Illuminate\Support\Facades\Storage;


/**
 * Class MultiStorage
 * @package App\Models
 */
class MultiStorage extends Storage
{

    protected static function multiPath($path)
    {
        if (is_string($path)) {
            return config("app.domain", "default") . "/" . $path;
        } else {
            $prefix = config("app.domain", "default") . "/";
            foreach ($path as $k=>$v) {
                $v = $prefix . $v;
                $path[$k] = $v;
            }
            return $path;
        }
    }

    public static function put(string $path, $contents, $options = [])
    {
        return parent::put(static::multiPath($path), $contents, $options);
    }

    public static function exists(string $path)
    {
        return parent::exists(static::multiPath($path));
    }

    public static function getMime(string $path)
    {
        $path = static::multiPath($path);
        return mime_content_type(Storage::disk('')->path($path));
    }

    public static function getRealPath(string $path)
    {
        $path = static::multiPath($path);
        return Storage::disk('')->path($path);
    }

    public static function get(string $path)
    {
        return parent::get(static::multiPath($path));
    }

    public static function delete(array $paths)
    {
        return parent::delete(static::multiPath($paths));
    }

}
