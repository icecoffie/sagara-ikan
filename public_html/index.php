<?php

define('SAGARA_APP', true);

$candidates = [
    dirname(__DIR__) . '/app/bootstrap.php', 
    __DIR__ . '/app/bootstrap.php',          
];

foreach ($candidates as $bootstrap) {
    if (is_file($bootstrap)) {
        require $bootstrap;
        return;
    }
}

http_response_code(500);
exit('Bootstrap not found. Ensure the /app folder was uploaded.');
