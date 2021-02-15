<?php

return [
	'base_url' => env('NOBD_URL', ''),
    'importSource' => env('NOBD_IMPORT_SOURCE', ''),
    'typeCode' => env('NOBD_TYPE_CODE', ''),
    'bin' => env('NOBD_BIN', ''),
    'status' => env('NOBD_STATUS', ''),
    'auth_username' => env('NOBD_USERNAME', ''),
    'auth_password' => env('NOBD_PASSWORD', ''),
    'group_logic_values' => [
    	'6943',
    	'6926',
    	'5783'
    ],
];