<?php


return [
    'base_url' => env('GLUU_BASE_URL', '127.0.0.1'),
    'client_id' => env('GLUU_CLIENT_ID', ''),
    'client_secret' => env('GLUU_CLIENT_SECRET', ''),
    'authentication_uri' => env('AUTHENTICATION_URI', '/oxauth/restv1/token'),
    'inspect_token_uri' => env('INSPECT_TOKEN_URI', '/oxauth/restv1/introspection'),
    'create_user_uri' => env('CREATE_USER_URI', '/identity/restv1/scim/v2/Users'),
    'update_user_uri' => env('UPDATE_USER_URI', ''),
];
