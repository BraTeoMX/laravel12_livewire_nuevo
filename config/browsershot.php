<?php

return [
    'node_binary' => env('BROWSERSHOT_NODE_BINARY'),
    'npm_binary' => env('BROWSERSHOT_NPM_BINARY'),
    'chrome_path' => env('BROWSERSHOT_CHROME_PATH'),
    'node_modules_path' => env('BROWSERSHOT_NODE_MODULES_PATH', base_path('node_modules')),
    'no_sandbox' => env('BROWSERSHOT_NO_SANDBOX', false),
];
