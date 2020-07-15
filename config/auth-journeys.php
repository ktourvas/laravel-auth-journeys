<?php

return [


    'ux' => [

        'login' => [

            'redirectTo' => '/',

            'view' => 'auth.login',

            'maxAttempts' => 5,

            'decayMinutes' => 10,
        ],

        'register' => [

            'allowset' => true, // allow only previously set users to register

            'view' => 'auth.register'

        ],

        'password' => [

            'change' => 'laj::passwordchange',

            'email' => 'auth.passwords.email',

            'reset' => 'auth.passwords.reset',

        ],

    ],

    /**
     *
     * role rules. array keys are checked against user roles for matching in order for rules
     * to be applied. Currently change policy only implemented.
     *
     */
    'roles' => [

        'default' => [

            'complexity' => 0, // 0. min 8, 1. min 8, nums, letters, special, 2. min 8, nums, letters, capital letters, special,

            'changepolicy' => 'none', // none, days

            'inactivitylogout' => false

        ],

        'admin' => [

            'complexity' => 2, // 1. min 8, 2. min 8, nums, letters, special, 3. min 8, nums, letters, capital letters, special,

            'changepolicy' => 'days', // none, days

            'days' => 90,

            'inactivitylogout' => true,

            'logoutafter' => 1800 // seconds

        ]

    ]

];
