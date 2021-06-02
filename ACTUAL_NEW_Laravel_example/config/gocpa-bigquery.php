<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PROJECT ID
    |--------------------------------------------------------------------------
    |
    | The project ID is a unique, user-assigned ID that can be used by Google APIs. If you do not specify a project ID during project creation, a project ID will be generated automatically.
    |
    | The project ID must be a unique string of 6 to 30 lowercase letters, digits, or hyphens. It must start with a letter, and cannot have a trailing hyphen. You cannot change a project ID once it has been created. You cannot re-use a project ID that is in use, or one that has been used for a deleted project.
    |
    | Some words are restricted from use in project IDs. If you use restricted words in the project name, such as google or ssl, the generated project ID will not include these words.
    |
    |
    */

    'project_id' => env('GOCPA_BIGQUERY_PROJECT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | PROJECT ID
    |--------------------------------------------------------------------------
    |
    | The project ID is a unique, user-assigned ID that can be used by Google APIs. If you do not specify a project ID during project creation, a project ID will be generated automatically.
    |
    | The project ID must be a unique string of 6 to 30 lowercase letters, digits, or hyphens. It must start with a letter, and cannot have a trailing hyphen. You cannot change a project ID once it has been created. You cannot re-use a project ID that is in use, or one that has been used for a deleted project.
    |
    | Some words are restricted from use in project IDs. If you use restricted words in the project name, such as google or ssl, the generated project ID will not include these words.
    |
    |
    */

    'key_file_path' => storage_path(env('GOCPA_BIGQUERY_KEY_FILE_PATH', 'gocpa-bigquery-key.json')),
];
