<?php

//TODO Education Prices Config - maybe migrate in DB

/**
 *  resident  - резиденты РК
 *  alien     - иностранцы
 *  b         - Бакалавриат (кроме дизайна)
 *  b_design  - Бакалавриат дизайн
 *  m         - Магистратура
 */

return [
    'resident' => [
        'b'        => 150000,
        'b_design' => 300000,
        'm'        => 204000
    ],
    'alien' => [
        'b'        => 300000,
        'b_design' => 600000,
        'm'        => 408000
    ],
    'trends_desing_ids' => [
        13
    ]
];
