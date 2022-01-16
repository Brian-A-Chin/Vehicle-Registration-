<?php
if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) { 
    header("location:../../404");
    exit;
}
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', realpath($_SERVER["DOCUMENT_ROOT"]) . '/' );
}

define( 'COUNTRY_CODE', 'US');

define( 'DB_NAME', '' );


define( 'DB_USER', '' );


define( 'DB_PASSWORD', '' );


define( 'DB_HOST', '' );


define( 'DB_CHARSET', 'utf8mb4' );


define( 'DB_COLLATE', '' );


define( 'SALT','W48Vhbh0pqwe23e23wq24q5wF3nTjgGQFx' );

define( 'SECRET_KEY','werwef4ew5f9Kg' );



$url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$url =  explode("/",$url);

define( 'URL', $url );


?>