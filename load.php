<?php

include( "config.php" );

define( "SITE_PATH", dirname(__FILE__) . "/" );
define( "SITE_URL", "/" );

// This is where generated thumbnails and originals are kept
// It can be located anywhere, as long as it's writable and web-accessible
define( "STORAGE_URL", SITE_URL . "storage/" );
define( "STORAGE_PATH", SITE_PATH . "storage/" );

// File upload stuff
define( "UPLOAD_PATH", STORAGE_PATH . "originals/" );
define( "IMAGE_PATH", STORAGE_PATH . "images/" );
define( "IMAGE_URL", STORAGE_URL . "images/" );

// Includes URL
define( "INCLUDES_URL", SITE_URL . "includes/" );

// Admin-specific constants
define( "ADMIN_PATH", SITE_PATH . "admin/" );

if( !MOD_REWRITE_ENABLED ){
	define( "ADMIN_URL", SITE_URL . "admin/index.php/" );
} else {
	define( "ADMIN_URL", SITE_URL . "admin/" );
}

define( "ADMIN_INCLUDES_PATH", ADMIN_PATH . "includes/" );
define( "ADMIN_TEMPLATE_PATH", ADMIN_PATH . "templates/" );



?>