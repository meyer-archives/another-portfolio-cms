<?php

abstract class Registry{
	 abstract protected function get($key);
	 abstract protected function set($key,$val);
} 

Class My_Registry extends Registry {

	// Registry array of objects
	private static $objects = array();

	// The instance of the registry
	private static $instance;

	// Back off
	private function __construct(){}
	public function __clone(){}

	public static function singleton(){
		if( !empty(self::$instance) )
			self::$instance = new self();
		return self::$instance;
	 }

	protected function get($key){
		if( !empty($this->objects[$key]) )
			return $this->objects[$key];
		return NULL;
	 }

	protected function set($key,$val){
		$this->objects[$key] = $val;
	}

	static function getObject( $key ){
		return self::singleton()->get($key);
	}

	static function storeObject( $key, $instance ){
		return self::singleton()->set($key,$instance);
	}
}

Class ExampleClass{

public $numberOne;
public $numberTwo;

	function __construct(){
		$this->numberOne = "5";
		$this->numberTwo = "5";
	}

	function sum(){
		$total = $this->numberOne + $this->numberTwo;
		return $total;
	}

} // end class A.

$Registry = My_Registry::singleton();
$Registry->storeObject('example', new ExampleClass());

$a = $Registry->getObject('example');
$b = $Registry->getObject('example');
$c = $Registry->getObject('example');
$d = $Registry->getObject('example');
?>