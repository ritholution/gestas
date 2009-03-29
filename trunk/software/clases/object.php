<?php
/**
 * object.php
 *
 * Description
 * This class represents the Objects on which we apply the ACL permissions
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
require_once("acl.php");

class Obj{
  private $idObject = -1;
  private $name = null;
  private $object = null; // object puede ser un identificador en el caso de que lo haya
                          // (de Action, de File, ...) o el nombre del objeto en otro
                          // caso (clase, tabla, ...)
  private $description = null;
  private $objectType = 1;
  private $acl = null;

  static $TYPE_CLASS = 1;
  static $TYPE_ACTION = 2;
  static $TYPE_PLUGIN = 3;
  static $TYPE_TABLE = 4;
  static $TYPE_FILE = 5;

  // Constructor of the class
  public function __construct($newName=null, $newObject=null, $newDescription=null,
			      $newObjectType=null, $db=null) {
    if($newName !== null && is_string($newName))
      $this->name = $newName;

    if($newDescription !== null && is_string($newDescription))
      $this->description = $newDescription;

    if(is_integer($newObjectType) && $newObjectType > 0 && $newObjectType < 5)
      $this->objectType = $newObjectType;

    // Reference to the object
    switch($this->objectType){
    case Obj::$TYPE_CLASS:
      // We store the class name.
      if($newObject !== null && is_string($newObject) && class_exists($newObject))
	$this->object = $newObject;
      break;
    case Obj::$TYPE_ACTION:
      // We store the action id.
      $this->object = $this->get_id_action($newObject);
      break;
    case Obj::$TYPE_PLUGIN:
      // We store the plugin id.
      $this->object = $this->get_id_plugin($newObject);
      break;
    case Obj::$TYPE_TABLE:
      // We store the table name.
      if($newObject !== null && is_string($newObject))
	// Check if the table exists
	$this->object = $newObject;
      break;
    case Obj::$TYPE_FILE:
      // We store the file id
      $this->object = $this->get_id_file($newObject);
      break;
    default:
      throw new GException(GException::$VAR_TYPE);
    }

    // Load acls from database.
    $this->acl[0] = new acl($this,null,null,acl::$READ, $db); // Permission to read
    $this->acl[1] = new acl($this,null,null,acl::$WRITE, $db); // Permission to write
    $this->acl[2] = new acl($this,null,null,acl::$EXECUTE, $db); // Permission to execute
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idObject":
    case "name":
    case "object":
    case "description":
    case "objectType":
      return $this->$var;
      break;
    case "acl":
      throw new GException(GException::$VAR_ACCESS);
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them..
  public function __set($var,$value) {
    switch($var){
    case "idObject":
      throw new GException(GException::$VAR_ACCESS);
    case "object":
      switch($this->objectType){
      case Obj::$TYPE_CLASS:
	// We store the class name.
	if($value !== null && is_string($value) && class_exists($value))
	  $this->object = $value;
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_ACTION:
	// We store the action id.
	if($value !== null && $value > -1)
	  $this->object = $this->get_id_action($value);
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_PLUGIN:
	// We store the plugin id.
	if($value !== null && $value > -1)
	  $this->object = $this->get_id_action($value);
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_TABLE:
	// We store the table name.
	if($value !== null && is_string($value))
	  // Check if the table exists
	  $this->object = $value;
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_FILE:
	// We store the file id
	if($value !== null && $value > -1)
	  $this->object = $this->get_id_file($value);
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      default:
	throw new GException(GException::$VAR_TYPE);
      }
      break;
    case "name":
    case "description":
      if($value === null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "objectType":
      if(is_integer($value) && $value > 0 && $value < 5)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "acl":
      throw new GException(GException::$VAR_ACCESS);
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method insert an object in the database
  public function insert_obj($db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    }

    $db->connect();

    $update = false;
    if($this->idObject > -1) {
      $db->consult("select idObject from obj where idObject=".$this->idObject);
      if($db->numRows() == 1)
	$update = true;
      else if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
    }

    if($update)
      $this->modify_obj($db);
    else {
      $db->execute("insert into obj(objectName,description,objectType,objectValue) values('".
		   $this->name."','".$this->description."',".$this->objectType.",'".
		   $this->object."')");
      $this->idObject = $db->id;
      foreach($this->acl as $value)
	$value->insert_acl($db);
    }
  }

  // This method update an object in the database
  public function modify_obj($db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    }

    $db->connect();
    $db->consult("select idObjecj from obj where idObject".$this->idObject);
    if($db->numRows() == 1) {
      $db->execute("update obj set objectName='".$this->name."', description='".$this->description.
		   "', objectType=".$this->objectType.", objectValue='".
		   $this->object."' where idObject".$this->idObject);
      foreach($this->acl as $value)
	$value->modify_acl($db);
    } else if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
  }

  // This method drop an object of the database
  public function drop_obj($db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    }

    foreach($this->acl as $value)
      $value->drop_acl($db);

    $db->connect();
    $db->execute("delete from obj where idObject=".$this->idObject);
  }

  // Loads an object from the database
  public function load_obj($newIdObj=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    }

    if($this->exists($newIdObj))
      $this->idObject = $newIdObj;

    if(!$this->exists($this->idObject))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select * from obj where idObject=".$this->idObject);
    $row = $db->getRow();
    $this->name = $row['objectName'];
    $this->description = $row['description'];
    $this->objectType = intval($row['objectType']);

    // We load the object reference
    switch($this->objectType){
    case Obj::$TYPE_CLASS:
      // We store the class name.
      if($row['objectValue'] !== null && is_string($row['objectValue']) &&
	 class_exists($row['objectValue']))
	$this->object = $value;
      break;
    case Obj::$TYPE_ACTION:
      // We store the action id.
      if($row['objectValue'] !== null && $row['objectValue'] > -1)
	$this->object = $this->get_id_action(intval($row['objectValue']));
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case Obj::$TYPE_PLUGIN:
      // We store the plugin id.
      if($row['objectValue'] !== null && $row['objectValue'] > -1)
	$this->object = $this->get_id_plugin(intval($row['objectValue']));
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case Obj::$TYPE_TABLE:
      // We store the table name.
      if($row['objectValue'] !== null && is_string($row['objectValue']))
	// Check if the table exists
	$this->object = $row['objectValue'];
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case Obj::$TYPE_FILE:
      // We store the file id
      if($row['objectValue'] !== null && $row['objectValue'] > -1)
	$this->object = $this->get_id_file(intval($row['objectValue']));
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_TYPE);
    }    

    // We load the three acls related with thr object
    foreach($this->acl as $value) {
      $value->obj = $this;
      $value->load_acl($db);
    }
  }

  // Loads an object from the database based on the object name
  public function load_obj_name($newName=null, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    } else if($newName !== null && is_string($newName))
      $this->name = $newName;

    if($this->name == null)
      throw new GException(GException::$PARAM_MISSING);

    $db->connect();
    $db->consult("select * from obj where objectName=".$this->name);
    if($db->numRows() == 1) {
      $row = $db->getRow();
      $this->idObject = intval($row['idObject']);
      $this->description = $row['description'];
      $this->objectType = intval($row['objectType']);

      // We load the three acls related with thr object
      foreach($this->acl as $value) {
	$value->obj = $this;
	$value->load_acl($db);
      }

      // We load the object reference
      switch($this->objectType){
      case Obj::$TYPE_CLASS:
	// We store the class name.
	if($row['objectValue'] !== null && is_string($row['objectValue']) &&
	   class_exists($row['objectValue']))
	  $this->object = $value;
	break;
      case Obj::$TYPE_ACTION:
	// We store the action id.
	if($row['objectValue'] !== null && $row['objectValue'] > -1)
	  $this->object = $this->get_id_action(intval($row['objectValue']));
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_PLUGIN:
	// We store the plugin id.
	if($row['objectValue'] !== null && $row['objectValue'] > -1)
	  $this->object = $this->get_id_plugin(intval($row['objectValue']));
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_TABLE:
	// We store the table name.
	if($row['objectValue'] !== null && is_string($row['objectValue']))
	  // Check if the table exists
	  $this->object = $row['objectValue'];
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      case Obj::$TYPE_FILE:
	// We store the file id
	if($row['objectValue'] !== null && $row['objectValue'] > -1)
	  $this->object = $this->get_id_file(intval($row['objectValue']));
	else
	  throw new GException(GException::$VAR_TYPE);
	break;
      default:
	throw new GException(GException::$VAR_TYPE);
      }      
    } else if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
  }

  // This method checks if an object exists search by id.
  public function exists($newIdObject=-1, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $object_check = $this->idObject;
    if($newIdObject > -1)
      $object_check = $newIdObject;

    if($object_check > -1) {
      $db->connect();
      $db->consult("select * from obj where idObject=".$object_check);

      if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

      return ($db->numRows() === 1);
    }

    return false;
  }

  // This method checks if a user has the $permType permission
  public function has_permission($newUser, $permType=0) {
    if($newUser === null || !User::is_user($newUser))
      throw new GException(GException::$VAR_TYPE);

    $member = new Member();
    $member->load_member_by_user($newUser->idUser);
    $newUser->load_user_types();

    if($permType >= 0 && $permType < 3) {
      if($this->acl[$permType]->user_has_permission($newUser))
	return true;
      else if($this->acl[$permType]->member_has_permission($member))
	return true;
      else if($newUser->typeUsers !== null && is_array($newUser->typeUsers)) {
	foreach($newUser->typeUsers as $value)
	  if($this->acl[$permType]->user_type_has_permission($value))
	    return true;
      }
    }
    return false;
  }

  // This method obtain the id of an Action from a parameter in various formats
  private function get_id_action($pAction=null) {
    require_once("action.php");
    if($pAction !== null) {
      if(Action::is_action($pAction) && $pAction->idAction->exists())
	return $pAction->idAction;
      else if(is_int($pAction)){
	// Check if the action exists
	$action_tmp = new Action();
	if($action_tmp->exists($pAction))
	  return $pAction;
      }
    }

    return -1;
  }

  // This method obtain the id of a Plugin from a parameter in various formats
  private function get_id_plugin($pPlugin=null) {
    require_once("module.php");
    if($pPlugin !== null) {
      if(is_int($pPlugin)) {
	// Check if the action exists
	$module_tmp = new Module();
	if($module_tmp->exists($pPlugin))
	  return $pPlugin;
      } else if(is_object($pPlugin) && Module::is_module($pPlugin))
	return $pPlugin->idPlugin;
    }

    return -1;
  }

  // This method obtain the id of a File from a parameter in various formats
  private function get_id_file($pFile=null) {
      require_once("file.php");
      if($pFile !== null) {
	if(is_string($pFile)) {
	  $tmp_file = new File();
	  if($tmp_file->exists($pFile)) {
	    $tmp_file->loadFile($pFile);
	    return  $tmp_file->idFile;
	  }
	} else if(is_int($pFile)) {
	  $tmp_file = new File();
	  if($tmp_file->exists($pFile))
	    return $pFile;
	} else if(File::is_file($pFile))
	  return $pFile->idFile;
      }
  }

  // This function checks if the parameter passed is of the class Obj
  public static function is_obj($var) {
    if(is_object($var) && (get_class($var) == "Obj" || is_subclass_of($var, "Obj")))
      return true;
    return false;
  }
}
?>