<?php
/**
 * file.php
 *
 * Description
 * This class implements the File data type.
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
require_once("gdatabaseexception.php");
require_once("object.php");

class File{
  private $idFile = -1;
  private $path = null;
  private $object = null;

  // Constructor of the class
  public function __construct($newPath = null, $newObject=null) {
    if($newObject !== null && Obj::is_obj($newObject))
      $this->object = $newObject;

    if($newPath !== null && is_string($newPath))
      $this->path = $newPath;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idFile":
    case "path":
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
    case "idFile":
      throw new GException(GException::$VAR_ACCESS);
    case "path":
      if($value === null || is_string($value))
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

  // This method inserts a file to the database
  public function insert_file($db=null){
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->path == null)
      throw new GException(GException::$PARAM_MISSING);

    // Check if the object exists.
    if($this->object->exists()) {
      // We load the active plugins and its configurations
      $db->connect();
      $db->execute("insert into file(path,object) values('".$this->path."','".
		   $this->object->idObject."')");
      $this->idFile = $db->id;
    }
  }

  // This method modify a module in the database and, if it's neccesary, in the
  // configs, active and inactive variables
  public function modify_file($db=null){
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->idFile === -1)
      throw new GException(GException::$PARAM_MISSING);

    // Check if the object exists.
    if($this->object->exists()) {
      // We load the active plugins and its configurations
      $db->connect();
      $db->execute("update file set path='".$this->path."' and object='".
		   $this->object->idObject."' where idFile=".$this->idFile);
    }
  }

  // This method drops a module from de database and from the system variables.
  public function drop_file($db=null){
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->path == null && $this->idFile === -1)
      throw new GException(GException::$PARAM_MISSING);

    // We load the active plugins and its configurations
    $db->connect();
    if($this->idFile !== -1)
      $db->execute("delete from file where idFile='".$this->idFile."'");
    else
      $db->execute("delete from file where path='".$this->path."'");
  }

  // This method loads a File from the database.
  public function load_file($newFile=null, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(is_int($newFile) && $newFile > -1)
      $this->idFile = $newFile;
    else if(is_string($newFile) && $newFile != null)
      $this->path = $newFile;

    if($this->path == null && $this->idFile === -1)
      throw new GException(GException::$PARAM_MISSING);

    $db->connect();
    if($this->idFile > -1)
      $db->consult("select * from file where idFile=".$this->idFile);
    else
      $db->consult("select * from file where path='".$this->path."'");

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
    else if($db->numRows() === 1) {
      $row = $db->getRow();
      $this->idFile = intval($row['idFile']);
      $this->path = $row['path'];
      $this->object = new Obj();
      $this->object->load_obj(intval($row['object']));
    }
  }

  // This method checks if a file exists search by id.
  public function exists($newIdFile=-1, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(is_int($newIdFile)) {
      $file_check = $this->idFile;
      if($newIdFile > -1)
	$file_check = $newIdFile;
    } else if(is_string($newIdFile)) {
      $file_check = $this->path;
      if($newIdFile != null)
	$file_check = $newIdFile;
    }

    if((is_string($file_check) && $file_check!=null) || 
       (is_int($file_check) && $file_check > -1)) {
      $db->connect();
      if(is_int($file_check))
	$db->consult("select * from file where idFile=".$file_check);
      else
	$db->consult("select * from file where path='".$file_check."'");

      if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
      return ($db->numRows() === 1);
    }

    return false;
  }

  // This function checks if the parameter passed is of the class File
  public static function is_file($var){
    if(is_object($var) && (get_class($var) == "File" || is_subclass_of($var, "File")))
      return true;
    return false;
  }
}
?>