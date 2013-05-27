<?php
define('MODE', 'development');
if (MODE == 'production') {
    define('PRODUCTION',    true);
    define('DEVELOPMENT',   false);
}
else {
    define('PRODUCTION',    false);
    define('DEVELOPMENT',   true);
}