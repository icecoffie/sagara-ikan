<?php
/**
 * Sagara Resto — front controller.
 *
 * Deploy models supported:
 *   (A) /app sits NEXT TO public_html (recommended; keeps app code out of webroot)
 *   (B) everything flattened INTO public_html (/app becomes a subfolder here)
 */

define('SAGARA_APP', true);

$candidates = [
    dirname(__DIR__) . '/app/bootstrap.php', // (A) sibling layout
    __DIR__ . '/app/bootstrap.php',          // (B) flattened layout
];

foreach ($candidates as $bootstrap) {
    if (is_file($bootstrap)) {
        require $bootstrap;
        return;
    }
}

http_response_code(500);
exit('Bootstrap not found. Ensure the /app folder was uploaded.');
