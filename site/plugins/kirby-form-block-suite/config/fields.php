<?php 

return [
    'mailview' => [
        'props' => [
            'value' => function ($value = null) {
                return $value;
            },
            'dateformat' => function ($value = 'DD.MM.YYYY HH:mm') {
                return $value;
            },
            'forms' => function ($value = []) {
                return $value;
            }
        ]
    ]
];
