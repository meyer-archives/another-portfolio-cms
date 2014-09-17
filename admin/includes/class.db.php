<?php

class DB{
	private static $pdo;
	public function &get_handle() {
		if( self::$pdo === null ){
			try {
				self::$pdo = new PDO('sqlite:'.SITE_PATH.'storage/gallery.sqlite3');
			} catch( PDOException $e ){ 
				die( "PDO Error: " . $e->getMessage() ); 
			}
		}
		return self::$pdo;
	}
}

?>