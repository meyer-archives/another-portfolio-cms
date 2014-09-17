<?php

// Incredibly simple template class
class Template {
	private $data = array();
	private $template_name;

	function __construct( $name, $render = false ){
		$portfolio = Portfolio::get_instance();

		if( file_exists( ADMIN_TEMPLATE_PATH . $name.".php" ) ){
			$this->template_name = ADMIN_TEMPLATE_PATH . $name.".php";
			if( $render )
				$this->render();
		} else {
			die( "Problem loading <strong>".$name.".php</strong>" );
		}

		$this->set("cache_age",$portfolio->meta("cache_age"));
		$this->set("cache_status",$portfolio->meta("last_updated") == $portfolio->meta("cache_age") ? "fresh" : "outdated" );
	}

	function set($k, $v){
		$this->data[$k] = $v;
	}

	function render(){
		extract($this->data);
		include_once( ADMIN_TEMPLATE_PATH . "snippets/header.php" );
		include_once( $this->template_name );
		include_once( ADMIN_TEMPLATE_PATH . "snippets/footer.php" );
	}
}


?>