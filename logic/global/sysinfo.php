<?php
if(isset($_SESSION['info'])) {
    define('SYSINFO', $_SESSION['info']);
    unset($_SESSION['info']);
} else
    define('SYSINFO', NULL);
