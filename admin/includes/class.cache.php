<?php

// Simple cache class
class Cache {
	public $age = 0;
	private static $instance;
	private static $data;
 
	private function __construct(){
		return;
	}
 
	public function &get_instance() {
		if( self::$instance === null ){
			self::$instance = new Cache();
		}
		return self::$instance;
	}

	public static function set( $key, $value ){
		self::$data[$key] = $value;
	}

	public static function update(){
		// Get instances
		$p = Portfolio::get_instance();

		self::set( 'items_by_id', $p->items(true) );
		self::set( 'items_by_project', $p->items() );
		self::set( 'projects', $p->projects() );
		self::write();
	}

	private static function write(){
		$p = Portfolio::get_instance();

		$cache_file = SITE_PATH . "storage/cache.php";

		if( !file_exists( $cache_file ) )
			touch( $cache_file ) or die( "Error creating cache file!" );
		if( !is_writable( $cache_file ) )
			die( "Cache file is not writable!" );		

		// Clear the cache file
		$fp = fopen($cache_file, 'w');

		// Update last_updated
		$last_updated = time();

		$p->meta("last_updated",$last_updated);
		$p->meta("cache_age",$last_updated);

//		$file_contents = '<'.'?php die("DO NOT EDIT THIS FILE"); \n" . 
		$file_contents = var_export(self::$data,true);
		$file_contents = '<'.'?php // DO NOT EDIT THIS FILE'."\n".'$cache='.$file_contents.";\n?".">";
		fwrite( $fp, $file_contents );
		fclose( $fp );
	}
}


?>