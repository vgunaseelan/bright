<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
     */

    'guards'       => [
        'token'       => [
            'driver'   => 'access_token',
            'provider' => 'token',
        ],

        'auth_token'  => [
            'driver'   => 'auth_token',
            'provider' => 'token',
        ],

        'credentials' => [
            'driver'   => 'credentials',
            'provider' => 'credentials',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
     */

    'passwords'    => [
        'users' => [
            'table' => 'auth_password_resets',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Throttle Key
    |--------------------------------------------------------------------------
    |
    | You may choose to block ip address from failed attempts
    | of a combination of IP and Username
    |
    | Supported: "ip", "username|ip"
    |
     */
    'throttle_key' => 'ip',

    // Set columns for username and other
    'columns'      => [
        'username' => 'username',
        'address'  => 'mobile',
    ],

    'user'         => [
        'role' => 'default',
    ],
];
