<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';

    /**
     * @param null $user
     * @return $this|NotificationTemplate
     */
    public function compile($user = null)
    {
        if(!$user)
        {
            return $this;
        }

        $params = [
            '[student_fio]' => $user->studentProfile->fio ?? '',
            '[current_speciality]' => $user->studentProfile->speciality->name ?? '',
            '[future_speciality]' => $user->studentProfile->originalSpeciality->name ?? ''
        ];

        $resultTemplate = $this;
        $resultTemplate->text = str_replace(array_keys($params), array_values($params), $resultTemplate->text);

        $mathesMaster = [];
        preg_match_all('#\[master\](.+?)\[\/master\]#is', $resultTemplate->text, $mathesMaster);

        $mathesBachelor = [];
        preg_match_all('#\[bachelor\](.+?)\[\/bachelor\]#is', $resultTemplate->text, $mathesBachelor);

        if( !empty($user->studentProfile->speciality->code_char) && ($user->studentProfile->speciality->code_char == Speciality::CODE_CHAR_MASTER) )
        {
            $resultTemplate->text = str_replace($mathesBachelor[0], '', $resultTemplate->text);
            $resultTemplate->text = str_replace(['[master]', '[/master]'], '', $resultTemplate->text);
        }

        if( !empty($user->studentProfile->speciality->code_char) && ($user->studentProfile->speciality->code_char == Speciality::CODE_CHAR_BACHELOR) )
        {
            $resultTemplate->text = str_replace($mathesMaster[0], '', $resultTemplate->text);
            $resultTemplate->text = str_replace(['[bachelor]', '[/bachelor]'], '', $resultTemplate->text);
        }

        return $resultTemplate;
    }

    /**
     * @param null $user
     * @return mixed
     */
    static function getListForAdmin($user = null)
    {
        $templateList = self::get();

        foreach ($templateList as $i => $tempalte)
        {
            $templateList[$i] = $tempalte->compile($user);
        }

        return $templateList;
    }
}
