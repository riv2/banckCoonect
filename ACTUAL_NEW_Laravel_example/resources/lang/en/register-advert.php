<?php

return [
    'fields' => [
        'name' => [
            'placeholder' => 'Steve',
            'label' => 'Name',
        ],
        'domain' => [
            'placeholder' => 'website',
            'popover' => 'Enter your company domain to create your affiliate program URL',
            'label' => 'Your company domain *',
            'errors' => [
                'exists' => 'The specified domain name is already reserved, please choose another one',
            ],
        ],
        'email' => [
            'popover' => 'We\'ll send access data and all the necessary details to this email',
            'placeholder' => 'mail@website.com',
            'label' => 'Email',
            'errors' => [
                'exists' => 'User with this E-mail is already registered',
            ],
        ],
        'phone' => [
            'popover' => 'For example your phone number or @skype_login',
            'placeholder' => '+7 (999) 123-45-67',
            'label' => 'Contact number (optional)',
            'errors' => [],
        ],
        'policy' => [
            'agree' => 'I have read and accept ',
            'privacy' => 'Privacy Policy',
            'and' => ' and ',
            'legal' => 'Service Agreement',
            'link_privacy' => 'https://gocpa.net/privacy',
            'link_legal' => 'https://gocpa.net/legal',
            'label' => 'Privacy Policy and Service Agreement',
            'errors' => [
                'not_accepted' => 'Confirm reading and accepting the Privacy Policy and Service Agreement',
            ],
        ],
        'submit' => 'Register',
    ],
    'welcome' => 'Platform for quick affiliate program launch',
    'screen.image' => 'https://gocpa.cloud/images/cloud/previews/1_en.jpg',
    'right' => [
        0 => [
            'text-info' => 'Affiliate marketing platform for rent',
            'loginright-header' => 'Main dashboard',
            'loginright-text' => '<div>Includes statistics and analytics</div><div>Comissions and payment calculation mechanisms</div><div>Can be customized on demand within a selected service plan</div>',
        ],
    ],
    'navbar_a' => 'https://gocpa.ru/privacy',
    'navbar_a_text' => 'Privacy policy',
    'already-registered' => 'Already have an account?',
];
