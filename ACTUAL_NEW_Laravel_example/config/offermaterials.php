<?php
//Список материалов для офферов, с настройками
return [
    "default"=>[
        "types"=>[
            "landing" => "Лендинги",
            "banner" => "Баннеры",
            "xmlfeed" => "XML-фид",
            //"link" => "Произвольная ссылка",
        ],
        "settings"=>[
            "landing" => ["single"=>false],
            "banner" => ["single"=>false],
            "xmlfeed" => ["single"=>false],
            //"link" => ["single"=>true],
        ]
    ],
    "aff.pushtraff.com"=>[
        "types"=>[
            "landing" => "Лендинг",
            "banner" => "Баннеры",
            "xmlfeed" => "XML-фид",
            "link" => "Ссылка на произвольную страницу сайта",
            "pwa" => "PWA",
        ],
        "settings"=>[
            "link" => ["single"=>true], //true = один элемент на оффер
            "landing" => ["single"=>false],
            "banner" => ["single"=>false],
            "xmlfeed" => ["single"=>false],
            "pwa" => ["single"=>true],

        ]
    ]


];
