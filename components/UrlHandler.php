<?php
/**
* 
* Version 1.0.0
* 
* Author: Roberto Serra - obi.serra@gmail.com
* 
*
* Collection of useful functions to handle url
*
*
**/
class UrlHandler {
	public static function makeSlug($string){
		$string = preg_replace("/[\s]+/", '-', $string);
		$string = preg_replace("/[\.|,|\?|\!|;|:|'|\"]+/", '', $string);
		$string = preg_replace("/[è|é]+/", 'e', $string);
		$string = preg_replace("/[à]+/", 'a', $string);
		$string = preg_replace("/[ò]+/", 'o', $string);
		$string = preg_replace("/[ì]+/", 'i', $string);
		$string = preg_replace("/[ù]+/", 'u', $string);
		$string = strtolower($string);
		$string = urlencode($string);
		return $string;
	}


}
?>