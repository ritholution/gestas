<?php
/**
 * language.php
 *
 * Description
 * This class implements the localization and internationalization of
 * the application.
 *
 * copyright (c) 2008-2009 OPENTIA s.l. (http://www.opentia.com)
 *
 * This file is part of GESTAS (http://gestas.opentia.org)
 * 
 * GESTAS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once("gexception.php");

class Language{
  private $lang = null;
  private $domain = null;
  private $dir = null;

  // Constructor of the class
  public function __construct($newLanguage=null, $langDomain=null, $dirLocales=null) {
    // The default language is the spanish
    if($newLanguage !== null && is_string($newLanguage) && $this->is_valid($newLanguage)) {
      $_SESSION['config'][1]['language'] = $newLanguage;
      $this->lang = $newLanguage;
    } else if(isset($_SESSION['config'][1]['language']))
      $this->lang = $_SESSION['config'][1]['language'];
    else {
      $_SESSION['config'][1]['language'] = 'es_ES';
      $this->lang = 'es_ES';
    }

    if($langDomain !== null && is_string($langDomain)) {
      $_SESSION['config'][1]['lang_domain'] = $langDomain;
      $this->domain = $langDomain;
    } else if(!isset($_SESSION['config'][1]['lang_domain']))
      $this->domain = $_SESSION['config'][1]['lang_domain'];
    else {
      $_SESSION['config'][1]['lang_domain'] = 'messages';
      $this->domain = 'messages';
    }

    if($dirLocales !== null && is_string($dirLocales)) {
      $this->dir = $dirLocales;
      $_SESSION['config'][1]['dir_locales'] = $dirLocales;
    } else if(!isset($_SESSION['config'][1]['dir_locales']))
      $this->dir = $_SESSION['config'][1]['dir_locales'];
    else {
      $this->dir = 'locales';
      $_SESSION['config'][1]['dir_locales'] = 'locales';
    }

    putenv("LC_ALL=".$this->lang);
    setlocale(LC_ALL,  $this->lang);
    bindtextdomain($this->domain,$_SESSION['config'][1]['dir_base']."/".$this->dir);
    textdomain($this->domain);
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "lang":
    case "domain":
    case "dir":
      return $this->$var;
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them..
  public function __set($var,$value) {
    switch($var){
    case "lang":
      if(is_string($value) && !$this->is_valid($newLanguage))
	throw new GException(GException::$VAR_TYPE);
    case "domain":
    case "dir":
      if($value === null || is_string($value))
	$this->$var = $this->$value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Checks if the parameter is a valid language for the system.
  public function is_valid($check) {
    if(!isset($_SESSION['config'][1]['languages_supported']) ||
       !is_array($_SESSION['config'][1]['languages_supported']))
      require_once($_SESSION['config'][1]['dir_base']."/".$this->dir."/langs_suported.php");

    foreach($_SESSION['config'][1]['languages_supported'] as $value)
      if($check === $value)
	return true;
  }

  // This function checks if the parameter passed is of the class File
  public static function is_language($var){
    if(is_object($var) && (get_class($var) == "Language" || is_subclass_of($var, "Language")))
      return true;
    return false;
  }
}
?>