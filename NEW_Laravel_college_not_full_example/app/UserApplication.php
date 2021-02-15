<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Profiler\Profile;

class UserApplication extends Model
{
    const FILE_PATH = 'images/uploads/applications/';
    const STATUS_MODERATION = 'moderation';
    const STATUS_CONFIRM = 'confirm';
    const STATUS_DECLINE = 'decline';

    protected $table = 'user_applications';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentProfile()
    {
        return $this->hasOne(Profiles::class, 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'order_id', 'id');
    }

    public function getFileSrcAttribute()
    {
        return '/' . self::FILE_PATH . $this->file_name;
    }

    /**
     * @param $fileName
     * @return string
     */
    static function getFilePath($fileName)
    {
        return public_path(self::FILE_PATH . $fileName);
    }

    /**
     * @param $userId
     * @param $file
     */
    static function addApplication($userId, $file)
    {
        $userApplication = new UserApplication();
        $userApplication->user_id = $userId;
        $userApplication->file_name = null;
        $userApplication->save();

        $filename = 'application_' . $userApplication->id . '.jpg';

        list($type, $file) = explode(';', $file);
        list(, $file)      = explode(',', $file);

        $file = base64_decode($file);

        file_put_contents(self::getFilePath($filename), $file);

        $userApplication->file_name = $filename;
        $userApplication->save();

        return $userApplication;
    }


}
