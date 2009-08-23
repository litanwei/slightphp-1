<?php
/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2008 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Hetal <hetao@hetao.name>                                    |
  |          SlightPHP <admin@slightphp.com>                             |
  |          http://www.slightphp.com                                    |
  +----------------------------------------------------------------------+
*/
//if(!class_exists("SlightPHP")){
//zone/page/entry

final class SlightPHP{
	/**
	 * @var string
	 */
	public static $appDir=".";
	/**
	 * @var string
	 */
	public static $pluginsDir="plugins";
	/**
	 * @var string
	 */
	public static $zone;
	public static $defaultZone="zone";
	
	/**
	 * @var string
	 */
	public static $page;
	public static $defaultPage="page";
	/**
	 * @var string
	 */
	public static $entry;
	public static $defaultEntry="entry";
	/**
	 * split flag of zone,classs,method
	 *
	 * @var string
	 */
	public static $splitFlag="/";

	
	/**
	 * defaultZone set
	 * 
	 * @param string $zone
	 * @return boolean
	 */

	public static function setDefaultZone($zone){
		SlightPHP::$defaultZone = $zone;
		return true;
	}
	/**
	 * defaultZone get
	 * 
	 * @return string
	 */

	public static function getDefaultZone(){
		return SlightPHP::$defaultZone;
	}
	/**
	 * defaultClass set
	 * 
	 * @param string $class
	 * @return boolean
	 */
	public static function setDefaultPage($page){
		SlightPHP::$defaultPage = $page;
		return true;
	}
	/**
	 * getDefaultClass get
	 * 
	 * @return string
	 */
	public static function getDefaultPage(){
		return SlightPHP::$defaultPage;
	}
	/**
	 * defaultMethod set
	 * 
	 * @param string $method
	 * @return boolean
	 */
	public static function setDefaultEntry($entry){
		SlightPHP::$defaultEntry = $entry;
		return true;
	}
	/**
	 * defaultMethod get
	 * 
	 * @return string $method
	 */
	public static function getDefaultEntry(){
		return SlightPHP::$defaultEntry;
	}
	/**
	 * splitFlag set
	 * 
	 * @param string $flag
	 * @return boolean
	 */
	public static function setSplitFlag($flag){
		SlightPHP::$splitFlag = $flag;
		return true;
	}
	/**
	 * defaultMethod get
	 * 
	 * @return string
	 */
	public static function getSplitFlag(){
		return SlightPHP::$splitFlag;
	}
	/**
	 * appDir set && get
	 *
	 * @param string $dir
	 * @return boolean
	 */

	public static function setAppDir($dir){
		SlightPHP::$appDir = $dir;
		return true;
	}
	/**
	 * appDir get
	 * 
	 * @return string
	 */
	public static function getAppDir(){
		return SlightPHP::$appDir;
	}
	/**
	 * pluginsDir set && get
	 * @param string $dir
	 * @return boolean
	 */
	public static function setPluginsDir($dir){
		SlightPHP::$pluginsDir = $dir;
		return true;
	}
	/**
	 * pluginsDir get
	 * 
	 * @return string
	 */
	public static function getPluginsDir(){
		return SlightPHP::$pluginsDir;
	}
	/**
	 * debug status set
	 *
	 * @param boolean $debug
	 * @return boolean
	 */
	public static function setDebug($debug){
		SlightPHP::$_debug = $debug;
		return true;
	}
	/**
	 * debug status get
	 * 
	 * @return boolean 
	 */
	public static function getDebug(){
		return SlightPHP::$_debug;
	}

	/**
	 * main method!
	 *
	 * @param
	 * @return boolean
	 */

	final public static function run($path=""){
		//{{{
		$splitFlag = preg_quote(SlightPHP::$splitFlag,"/");
		$path_array = array();
		if(!empty($path)){
			$isPart = true;
			$path_array = preg_split("/[$splitFlag\/]/",$path,-1,PREG_SPLIT_NO_EMPTY);
		}else{
			$isPart = false;
			if(!empty($_SERVER["PATH_INFO"]))$path_array = preg_split("/[$splitFlag\/]/",$_SERVER["PATH_INFO"],-1,PREG_SPLIT_NO_EMPTY);
		}

		$zone	= !empty($path_array[0]) ? $path_array[0] : SlightPHP::$defaultZone ;
		$page	= !empty($path_array[1]) ? $path_array[1] : SlightPHP::$defaultPage ;
		$entry	= !empty($path_array[2]) ? $path_array[2] : SlightPHP::$defaultEntry ;

		if(!$isPart){
			SlightPHP::$zone	= $zone;
			SlightPHP::$page	= $page;
			SlightPHP::$entry	= $entry;
		}else{
			if($zone == SlightPHP::$zone && $page == SlightPHP::$page && $entry == SlightPHP::$entry){
				SlightPHP::debug("part ignored [$path]");
				return;
			}
		}

		$app_file = SlightPHP::$appDir . DIRECTORY_SEPARATOR . $zone . DIRECTORY_SEPARATOR . $page . ".page.php";
		if(!file_exists($app_file)){
			SlightPHP::debug("file[$app_file] not exists");
			return false;
		}else{
			SlightPHP::loadFile($app_file);
		}
		$method = "Page".$entry;
		$classname = $zone ."_". $page;
		
		if(!class_exists($classname)){
			SlightPHP::debug("class[$classname] not exists");
			return false;
		}
		$classInstance = new $classname;
		if(!method_exists($classInstance,$method)){
			SlightPHP::debug("method[$method] not exists in class[$classname]");
			return false;
		}
		return call_user_func(array(&$classInstance,$method),$path_array);

	}

	/**
	 * loadFile,like require_once
	 *
	 * @param string $filePath
	 * @return boolean
	 */
	public static function loadFile($filePath){
		if(file_exists($filePath)){
			require_once($filePath);
			return true;
		}else{
			SlightPHP::debug("file[$filePath] not exists");
			return false;
		}
	}
	/**
	 * loadPlugin in $pluginsDir 
	 *
	 * @param string $pluginName
	 * @return boolean
	 */
	public static function loadPlugin($pluginName){
		$app_file = SlightPHP::$pluginsDir. DIRECTORY_SEPARATOR . $pluginName. ".class.php";
		return SlightPHP::loadFile($app_file);

	}



	/**
	 * @var boolean
	 */
	public static $_debug=0;

	/*private*/
	private function debug($debugmsg){
		if(SlightPHP::$_debug){
			error_log($debugmsg);
			echo "<!--slightphp debug: ".$debugmsg."-->";
		}
	}
}

//}
?>
