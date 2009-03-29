<?php
/**
 * content.php
 *
 * Description
 * This class implements the Content data type.
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
require_once("object.php");

class Content{
  private $idContent = -1;
  private $content = null;
  private $contentType = 1;
  private $object = null;

  static $TEXT_TYPE = 1;
  static $HTML_TYPE = 2;
  static $PHP_TYPE = 3;

  // Constructor of the class
  public function __construct($newContent = null, $newContentType = 1, $newObject=null) {
    if($newObject !== null && Obj::is_obj($newObject))
      $this->object = $newObject;

    if($newContent !== null && is_string($newContent))
      $this->content = $newContent;

    if(is_integer($newContentType) && $newContentType > 0 && $newContentType < 4)
      $this->contentType = $newContentType;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idContent":
    case "content":
    case "contentType":
    case "object":
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
    case "idContent":
      throw new GException(GException::$VAR_ACCESS);
    case "content":
      if($value === null || is_string($value))
	$this->$var = $this->$value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "contentType":
      if(is_int($value) && $value > 0 && $value < 4)
	$this->$var = $this->$value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "object":
      if($value === null || Obj::is_obj($value))
	$this->$var = $this->$value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method inserts in the database the association
  public function insert_db($db){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $db->connect();
    // ___TODO___
  }

  // This method updates the association in the database
  public function update_db($db){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $db->connect();
    // ___TODO___
  }

  // This method deletes the association from the database
  public function delete_db($db){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $db->connect();
    // ___TODO___
  }

  // This method loads the content of the section selected by the user.
  public function load_content($idContent=null){
    // If idContent === null load the section saved in $_SESSION. If it's not
    // saved any section in $_SESSION get the initial section.

    // Check if the user can see the content (all or only a part)

    // Load the section content.
    $this->content = '';
  }

  // This function checks if the parameter passed is of the class File
  public static function is_content($var){
    if(is_object($var) && (get_class($var) == "Content" || is_subclass_of($var, "Content")))
      return true;
    return false;
  }
}
?>