<?php

return [
    'success' => [
        'title'  => 'Well done!',
        'reason' => [
            'submitted_to_post'       => 'Пікір сәтті қосылды.',
            'updated_post'            => 'Пікір жаңартылды.',
            'destroy_post'            => 'Пікір жойылды.',
            'destroy_from_discussion' => 'Пікір жойылды.',
            'created_discussion'      => 'Жаңа тақырып сәтті жасалды.',
        ],
    ],
    'info' => [
        'title' => 'Heads Up!',
    ],
    'warning' => [
        'title' => 'Wuh Oh!',
    ],
    'danger'  => [
        'title'  => '',
        'reason' => [
            'errors'            => 'Пожалуйста, исправьте ошибки:',
            'prevent_spam'      => 'Спамның алдын алу үшін хабарламалар арасындағы интервал кем дегенде :minutes минут болуы керек.',
            'trouble'           => 'Кешіріңіз, түсініктемеңізді жіберуде қиындықтар туындады.',
            'update_post'       => 'Жоқ ах ... Жауап жаңартылмады. Күмәнді ештеңе жасамағаныңызға көз жеткізіңіз.',
            'destroy_post'      => 'Нее ах ах ... Не удалось удалить ответ. Убедитесь, что вы не делаете ничего сомнительного.',
            'create_discussion' => 'Қап :( Жасау кезінде қиындықтар туындады '.mb_strtolower(trans('chatter::intro.titles.discussion')).'.',
            'title_required'    => 'Тақырыпты толтырыңыз',
            'title_min'		    => 'Тақырып кемінде минималды таңбадан тұруы керек.',
            'title_max'		    => 'Тақырып ұзақ :max таңбадан аспауы керек.',
            'content_required'  => 'Хабарлама жазыңыз',
            'content_min'  		=> 'Хабар кем дегенде :min таңбадан тұруы керек',
            'category_required' => 'Пән таңдаңыз',
        ],
    ],
];
