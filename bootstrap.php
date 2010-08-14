<?php
// --------------------------------------------------
// additional tasks to run on application start (boot)
// --------------------------------------------------

switch (true) {
    case (strpos($_SERVER['HTTP_HOST'], $indx['stage']) !== false):
        define('MODE', PRODUCTION);
        break;
    case (strpos($_SERVER['HTTP_HOST'], $indx['prod']) !== false):
        define('MODE', PRODUCTION);
        break;
    case (strpos($_SERVER['HTTP_HOST'], $indx['dev']) !== false):
    default:
        define('MODE', DEVELOPMENT);
        break;
}

// turn this on if you want to check things
error_reporting((MODE === PRODUCTION) ? 0 : E_ALL);