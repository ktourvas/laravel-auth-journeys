<?php

return [


    'ux' => [

        'login' => [

            'redirectTo' => '/',

            'view' => 'auth.login'

        ],

        'register' => [

            'allowset' => true, // allow only previously set users to register

            'view' => 'auth.register'

        ],

        'password' => [

            'forgot' => 'laj::passwords.email',

            'email' => 'laj::passwords.email',

            'reset' => 'auth.passwords.reset',

        ],

    ],

    /**
     *
     * password groups. array keys are checked against user roles for matching in order for rules
     * to be applied. Currently change policy only implemented.
     *
     */
    'password' => [

        'default' => [

            'complexity' => 0, // 0. min 8, 1. min 8, nums, letters, special, 2. min 8, nums, letters, capital letters, special,

            'changepolicy' => 'none' // none, days

        ],

        'admin' => [

            'complexity' => '3', // 1. min 8, 2. min 8, nums, letters, special, 3. min 8, nums, letters, capital letters, special,

            'changepolicy' => 'days', // none, days

            'days' => 90

        ]

    ]

];
