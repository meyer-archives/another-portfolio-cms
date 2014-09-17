<?php

/**
* Meta class
*/
class Meta{
	private $meta = array();
	private static $instance;
 
	public function &get_instance() {
		if( self::$instance === null )
			self::$instance = new Portfolio();
		return self::$instance;
	}

	private function __construct(){
		return;
	}
}


?>