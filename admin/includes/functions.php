<?php

function logged_in(){
	if( !empty( $_COOKIE["pw_hash"] ) && $_COOKIE["pw_hash"] == sha1( md5( USERPASS ) . md5( USERNAME ) . "i like salt" ) ){
		return true;
	}
	return false;
}

function go_to( $location = false ){
	if( !$location ){
		header( "Location: " . ADMIN_URL . "items" );
	} else {
		header( "Location: " . ADMIN_URL . $location );
	}
	exit;
}

// For use before entering data into the DB
function escape( $string ){
	$string = htmlspecialchars($string,ENT_QUOTES,"UTF-8",false);
	return sqlite_escape_string( $string );
}

function escape_typogrify( $string ){
	$string = typogrify( $string );
	return sqlite_escape_string( $string );
}

// Undo the previous function
// Don't call this on typogrify'd content
function unescape( $string, $htmlescape = true ){
	$string = stripslashes( $string );
	$string = preg_replace("#'{2,}#", "'", $string);
//	$string = htmlspecialchars($string,ENT_QUOTES,"UTF-8",false);
	return $string;
}

// Turns nasty slugs into nice ones
function sluginate( $string, $sep = "_" ){
	return trim( preg_replace("/[^a-z0-9]+/", $sep, strtolower( $string ) ), $sep );
}

?>