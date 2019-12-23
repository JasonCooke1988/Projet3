<?php
/* Globale constants for database acess with PDO */
define('DB_DSN', 'mysql:host=localhost;dbname=projet3');
define('DB_USER', 'root');
define('DB_PASS', '');
/* You don't need to change anything here : this array is for PDO options */
define('DB_OPTIONS', array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));