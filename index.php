<?php

define('SAGARA_APP', true);
define('FRONT_PATH', __DIR__);

$candidates = [
    __DIR__ . '/app/bootstrap.php',          // flattened: app/ next to index.php (docroot = root)
    dirname(__DIR__) . '/app/bootstrap.php',  // app/ sits above the webroot (public_html layout)
];

foreach ($candidates as $bootstrap) {
    if (is_file($bootstrap)) {
        require $bootstrap;
        return;
    }
}

http_response_code(500);
exit('Bootstrap not found. Ensure the /app folder was uploaded.');
