<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 24.10.18
 * Time: 14:31
 */

namespace App\Services;


use Intervention\Image\ImageManagerStatic as Image;

class Avatar
{
    const   AVATAR_PATH = 'avatars';
    const   STUDENT_FACE_PATH = 'images/uploads/faces';

    /** @var \Intervention\Image\Image */
    private     $imageSource;

    /**
     * @param $photoFromRequest
     * @return Avatar
     */
    static function make($photoFromRequest)
    {
        $avatar = new self();
        $avatar->imageSource = Image::make($photoFromRequest->getRealPath());

        $resizeWidth = 600;
        $resizeHeight = 600;

        $avatar->imageSource->widen($resizeWidth);
        $avatar->imageSource->heighten($resizeHeight);

        return $avatar;
    }

    /**
     * @param $fileName
     * @return string
     */
    static function getPublicPath($fileName)
    {
        return '/' . self::AVATAR_PATH . '/' . $fileName;
    }

    static function getStudentFacePublicPath($fileName)
    {
        return '/' . self::STUDENT_FACE_PATH . '/' . $fileName;
    }

    static function getRealPath($fileName)
    {
        return public_path(self::AVATAR_PATH . '/' . $fileName);
    }

    /**
     * @param $fileName
     * @return bool
     */
    public function save($fileName)
    {
        return (bool)$this->imageSource->save(self::AVATAR_PATH . '/' . $fileName);
    }

    public function saveToFaces($fileName)
    {
        return (bool)$this->imageSource->save(self::STUDENT_FACE_PATH . '/' . $fileName);
    }

    /**
     * @param $fileName
     * @return bool
     */
    public function saveToCourse($fileName)
    {
        return (bool)$this->imageSource->save(public_path('images/uploads/courses/' . $fileName));
    }
}