<?php

return [
    'parser' => [
        'name'          => 'IECCCOM',
        'enabled'       => true,
        'sender_map'    => [
            '/@r.iecc.com/',
        ],
        'body_map'      => [
            //
        ],
    ],

    'feeds' => [
        'default' => [
            'class'     => 'SPAM',
            'type'      => 'ABUSE',
            'enabled'   => true,
            'fields'    => [
                'Source-IP',
                'Feedback-Type',
                'Received-Date',
            ],
        ],

    ],
];
