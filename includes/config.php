<?php

//Database Contants. Checking to see if it is defined
//If defined, return null (do nothing), if not then define it
defined("DB_SERVER") ? null : define("DB_SERVER", "localhost");
defined("DB_USER") ? null : define("DB_USER", "root");
defined("DB_PASS") ? null : define("DB_PASS", "");
defined("DB_NAME") ? null : define("DB_NAME", "picture_gallery");

?>