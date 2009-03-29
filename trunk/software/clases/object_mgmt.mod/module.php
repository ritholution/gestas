<?php
/**
 * module.php
 *
 * Description
 * This module manage the permissions associated to each object.
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

require_once($_SESSION['class_base']."/gexception.php");
require_once($_SESSION['class_base']."/object.php");
require_once($_SESSION['class_base']."/association.php");
require_once($_SESSION['class_base']."/user.php");
require_once($_SESSION['class_base']."/acl.php");

class ObjectManagement{
  private $user=null; // User to apply the permissions.
  private $association=null; // Association to apply the permissions
  private $actions=null; // Store the objects of type Action
  private $plugins=null; // Store the objects of type Module
  private $classes=null;
  private $tables=null;
  private $files=null;

  // Constructor of the class
  public function __construct($newUser=null, $newAssociation=null) {
    if($newUser !== null && User::is_user($newUser))
      $this->user = $newUser;
    else if(isset($_SESSION['user']) &&
	    User::is_user($_SESSION['user']))
      $this->user = $_SESSION['user'];

    if($newAssociation !== null && Association::is_association($newAssociation))
      $this->association = $newAssociation;
    else if(isset($_SESSION['association']) &&
	    Association::is_association($_SESSION['association']))
      $this->association = $_SESSION['association'];

    $this->load_objects();
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "user":
    case "association":
    case "classes":
    case "actions":
    case "plugins":
    case "tables":
    case "files":
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
    case "user":
      if($value !== null && User::is_user($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "association":
      if($value !== null && Association::is_association($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "classes":
    case "actions":
    case "plugins":
    case "tables":
    case "files":
      throw new GException(GException::$VAR_ACCESS);
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method loads the object permissions of a user
  public function load_objects($newUser=null, $newAssoc=null, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    }

    if($newUser === null || !User::is_user($newUser)) {
      if($this->user !== null && User::is_user($this->user))
	$newUser = $this->user;
      else if(isset($_SESSION['user']) && 
	      User::is_user($_SESSION['user']))
	$newUser = $_SESSION['user'];
      else
	throw new GException(GException::$VAR_TYPE);
    }

    if($newAssoc === null || !Association::is_association($newAssoc)) {
      if($this->association !== null && Association::is_association($this->association))
	$newAssoc = $this->association;
      else if(isset($_SESSION['association']) &&
	      Association::is_association($_SESSION['association']))
	$newAssoc = $_SESSION['association'];
      else
	throw new GException(GException::$VAR_TYPE);
    }

    if($newUser->exists()) {
      $this->user = clone $newUser;
      $this->association = clone $newAssoc;
      $db->connect();

      // We obtain the objects associated to the User.
      $db->consult("select idACL from aclUser where idUser=".$this->user->idUser);
      $acls = $db->rows;
      for($i=0;$i < count($acls);$i++){
	$db->consult("select idObj from acl where idACL=".intval($acls[$i]['idACL']));
	$objs = $db->rows;
	for($j=0;$j < count($objs);$j++){
	  $newObj = new Obj();
	  $newObj->load_obj(intval($objs[$j]['idObj']));
	  switch($newObj->objectType){
	  case Obj::$TYPE_CLASS:
	    if(is_array($this->classes) && !in_array($newObj,$this->classes))
	      $this->classes[] = $newObj;
	    break;
	  case Obj::$TYPE_ACTION:
	    if(is_array($this->actions) && !in_array($newObj,$this->actions))
	      $this->actions[] = $newObj;
	    break;
	  case Obj::$TYPE_PLUGIN:
	    if(is_array($this->plugins) && !in_array($newObj,$this->plugins))
	      $this->plugins[] = $newObj;
	    break;
	  case Obj::$TYPE_TABLE:
	    if(is_array($this->tables) && !in_array($newObj,$this->tables))
	      $this->tables[] = $newObj;
	    break;
	  case Obj::$TYPE_FILE:
	    if(is_array($this->files) && !in_array($newObj,$this->files))
	      $this->files[] = $newObj;
	    break;
	  default:
	    throw new GException(GException::$OBJ_TYPE_UNKNOWN);
	  }
	}
      }

      // We obtain the objects associated to the User.
      $db->consult("select idACL from aclMemberAssoc where idMember=".
		   $this->user->idUser." and idAssociation=".
		   $this->association->idAssociation);
      $acls = $db->rows;
      for($i=0;$i < count($acls);$i++){
	$db->consult("select idObj from acl where idACL=".intval($acls[$i]['idACL']));
	$objs = $db->rows;
	for($j=0;$j < count($objs);$j++){
	  $newObj = new Obj();
	  $newObj->load_obj(intval($objs[$j]['idObj']));
	  switch($newObj->objectType){
	  case Obj::$TYPE_CLASS:
	    if(is_array($this->classes) && !in_array($newObj,$this->classes))
	      $this->classes[] = $newObj;
	    break;
	  case Obj::$TYPE_ACTION:
	    if(is_array($this->actions) && !in_array($newObj,$this->actions))
	      $this->actions[] = $newObj;
	    break;
	  case Obj::$TYPE_PLUGIN:
	    if(is_array($this->plugins) && !in_array($newObj,$this->plugins))
	      $this->plugins[] = $newObj;
	    break;
	  case Obj::$TYPE_TABLE:
	    if(is_array($this->tables) && !in_array($newObj,$this->tables))
	      $this->tables[] = $newObj;
	    break;
	  case Obj::$TYPE_FILE:
	    if(is_array($this->files) && !in_array($newObj,$this->files))
	      $this->files[] = $newObj;
	    break;
	  default:
	    throw new GException(GException::$OBJ_TYPE_UNKNOWN);
	  }
	}
      }

      // We obtain the objects associated to the UserType.
      if($this->user->typeUsers !== null)
	foreach($this->user->typeUsers as $value) {
	  $db->consult("select idACL from aclUserType where idType=".$value->idType);
	  $acls = $db->rows;
	  for($i=0;$i < count($acls);$i++){
	    $db->consult("select idObj from acl where idACL=".intval($acls[$i]['idACL']));
	    $objs = $db->rows;
	    for($j=0;$j < count($objs);$j++){
	      $newObj = new Obj();
	      $newObj->load_obj(intval($objs[$j]['idObj']));
	      switch($newObj->objectType){
	      case Obj::$TYPE_CLASS:
		if(is_array($this->classes) && !in_array($newObj,$this->classes))
		  $this->classes[] = $newObj;
		break;
	      case Obj::$TYPE_ACTION:
		if(is_array($this->actions) && !in_array($newObj,$this->actions))
		  $this->actions[] = $newObj;
		break;
	      case Obj::$TYPE_PLUGIN:
		if(is_array($this->plugins) && !in_array($newObj,$this->plugins))
		  $this->plugins[] = $newObj;
		break;
	      case Obj::$TYPE_TABLE:
		if(is_array($this->tables) && !in_array($newObj,$this->tables))
		  $this->tables[] = $newObj;
		break;
	      case Obj::$TYPE_FILE:
		if(is_array($this->files) && !in_array($newObj,$this->files))
		  $this->files[] = $newObj;
		break;
	      default:
		throw new GException(GException::$OBJ_TYPE_UNKNOWN);
	      }
	    }
	  }
	}
    }
  }

  // This method adds a permission to the user over the object
  public function add_permission($newObject, $permType=0) {
    // First check if the object exists in his own list and, if true, update the acl
    // permission. Elsewhere add the object to the list and update the database.

    // Check if the user is member of the association (TypeUser & aclMemberAssoc).
  }

  // This method drops a permission to the user over the object
  public function drop_permission($newObject, $permType=0) {

  }

  // This function checks if the parameter passed is of the class ObjectManagement
  public static function is_object_management($var){
    if(is_object($var) && (get_class($var) == "ObjectManagement" || is_subclass_of($var, "ObjectManagement")))
      return true;
    return false;
  }
}
?>