<?php
/**
 * acl.php
 *
 * Description
 * This class implements the Access Control List to the different
 * components of the application by different users and type of users.
 *
 * copyright (c) 2008-2009 OPENTIA s.l. (http://www.opentia.com)
 *
 * This file is part of GESTAS (http://gestas.opentia.org)
 * 
 * GESTAS will be free software as soon as it is released under a minimally
 * stable version: at that time will be able to redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is provided in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once("gexception.php");
require_once("gdatabaseexception.php");
require_once("user.php");
require_once("type_user.php");
require_once("object.php");
require_once("database.php");

class acl{
  private $idACL=-1;

  private $obj = null; // Object which referes the permission
  private $users = null; // Array of Users who have permission.
  private $userTypes = null; // Array of TypeUsers who have permission.
  private $members = null; // Array of Members who have permission.
  private $type = 0; // Read (0), write (1) or execute (2) permission type.
  private $idEnv = 0; // Environment in which the acl is valid.

  // Types of permission
  static $READ=0;
  static $WRITE=1;
  static $EXECUTE=2;

  // Constructor of the class
  public function __construct($newObj=null, $newUser=null, $newUserTypes=null,
			      $newType=0, $newIdEnv=0, $db=null) {
    if($newObj !== null) {
      if(Obj::is_obj($newObj))
	$this->obj = $newObj;
      else
	throw new GException(GException::$VAR_TYPE);
    }

    if($newUser !== null) {
      if(is_array($newUser) || User::is_user($newUser))
	$this->users = $this->check_users($newUser);
      else
	throw new GException(GException::$VAR_TYPE);
    }
    
    if($newUserTypes !== null) {
      if(!is_array($newUserTypes) && !TypeUser::is_type_user($newUserTypes))
	throw new GException(GException::$VAR_TYPE);
      $this->userTypes = $this->check_users_type($newUserTypes);
    }

    if($newType !== null && is_int($newType) && $newType >=0 && $newType < 3)
      $this->type = $newType;

    if($newIdEnv !== null && is_int($newIdEnv) && $newIdEnv > 0)
      $this->idEnv = $newIdEnv;

    if($this->obj !== null)
      $this->load_acl($db);
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idACL":
    case "users":
    case "userTypes":
    case "type":
    case "obj":
    case "idEnv":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them..
  public function __set($var,$value) {
    switch($var){
    case "idACL":
      throw new GException(GException::$VAR_ACCESS);
    case "users":
      if($value !== null) {
	if(!is_array($value) && !User::is_user($value))
	  throw new GException(GException::$VAR_TYPE);
	$this->$var = $this->check_users($value);
      } else
	$this->$var = $value;
      break;
    case "userTypes":
      if($value !== null) {
	if(!is_array($value) && !TypeUser::is_type_user($value))
	  throw new GException(GException::$VAR_TYPE);
	$this->$var = $this->check_users_type($value);
      } else
	$this->$var = $value;
      break;
    case "type":
      if($value === null || !is_int($value) || $value < 0 || $value > 2)
	throw new GException(GException::$VAR_TYPE);
      $this->$var = $value;
      break;
    case "obj":
      if($value != null && !Obj::is_obj($value))
	throw new GException(GException::$VAR_TYPE);
      $this->$var = $value;
      break;
    case "idEnv":
      if($newIdEnv === null || !is_int($newIdEnv) || $newIdEnv < 0)
	throw new GException(GException::$VAR_TYPE);
      $this->$var = $value;
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method checks if a user (the current or another passed
  // by parameter) has this acl permission.
  public function user_has_permission($checkUser){
    if($checkUser === null || !User::is_user($checkUser))
      throw new GException(GException::$VAR_TYPE);

    if($this->users != null && is_array($this->users))
      foreach($this->users as $value) {
	if($value->idUser === $checkUser->idUser)
	  return true;
      }

    return false;
  }

  // This method checks if a member (the current or another passed
  // by parameter) has this acl permission.
  public function member_has_permission($checkMember){
    if($checkMember === null || !Member::is_member($checkMember))
      throw new GException(GException::$VAR_TYPE);

    if($this->members != null && is_array($this->members))
      foreach($this->members as $value)
	if($value->idMember === $checkMember->idMember)
	  return true;

    return false;
  }

  // This method checks if a type of user (the current or another passed
  // by parameter) has this acl permission.
  public function user_type_has_permission($checkUserType){
    if($checkUserType == null || !TypeUser::is_type_user($checkUserType))
      throw new GException(GException::$VAR_TYPE);

    if($this->userTypes !== null && is_array($this->userTypes))
      foreach($this->userTypes as $value)
	if($value->idType === $checkUserType->idType)
	  return true;

    return false;
  }

  // Adds an user to the user permission list
  public function add_user($newUser, $db=null){
    if($newUser == null || !User::is_user($newUser))
      throw new GException(GException::$VAR_TYPE);

    // TODO: Check if this rules with objects
    if(array_search($newUser,$this->users) === false)
      $this->users[] = $newUser;

    $this->insert_user($newUser, $db);

    return true;
  }

  // Drop an user to the user permission list
  public function drop_user($newUser, $db=null){
    if($newUser == null || !User::is_user($newUser))
      throw new GException(GException::$VAR_TYPE);

    // TODO: Check if this rules with objects
    if(($index = array_search($newUser,$this->users)) !== false)
      $this->users[$index] = null;

    $this->drop_user_db($newUser->idUser, $db);

    return true;
  }

  // Adds an user type to the user type permission list
  public function add_user_type($newUserType, $db=null){
    if($newUserType == null || !TypeUser::is_type_user($newUserType))
      throw new GException(GException::$VAR_TYPE);

    // TODO: Check if this rules with objects
    if(array_search($newUserType,$this->user_types) === false)
      $this->user_types[] = $newUserType;

    $this->insert_type_user($newUserType, $db);

    return true;
  }

  // drop an user type to the user type permission list
  public function drop_user_type($newUserType, $db=null){
    if($newUserType == null || !User::is_user($newUserType))
      throw new GException(GException::$VAR_TYPE);

    // TODO: Check if this rules with objects
    if(($index = array_search($newUserType,$this->user_types)) !== false)
      $this->user_types[$index] = null;

    $this->drop_type_user($newUserType->idUserType, $db);

    return true;
  }


  // This method returns all the valid User objects stored in the parameter
  private function check_users($var){
    $users = null;

    if($var === null || (!is_array($var) && !User::is_user($var)))
      throw new GException(GException::$VAR_TYPE);
    else if(!is_array($var))
      $users = array($var);
    else {
      foreach($var as $value)
	if(TypeUser::is_type_user($value))
	  $users[] = $value;
    }

    return $users;
  }

  // This method returns all the valid User_Type objects stored in the parameter
  private function check_users_type($var){
    $types = null;

    if($var === null || (!is_array($var) && !TypeUser::is_type_user($var)))
      throw new GException(GException::$VAR_TYPE);
    else if(!is_array($var))
      $types = array($var);
    else {
      foreach($var as $value)
	if(TypeUser::is_type_user($value))
	  $types[] = $value;
    }

    return $types;
  }

  // This method inserts a row in the aclUser table
  private function insert_user($newUser, $db=null) {
    if($newUser == null || !User::is_user($newUser))
      throw new GException(GException::$VAR_TYPE);
    else if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $db->connect();
    $db->execute("insert into aclUser(idUser,idACL) values(".$newUser->idUser.",".$this->idACL.")");
  }

  // This method drops a row in the aclUser table
  private function drop_user_db($newIdUser, $db=null) {
    if($newIdUser === null || !is_int($newIdUser))
      throw new GException(GException::$VAR_TYPE);
    else if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $db->connect();
    $db->execute("delete from aclUser where idACL=".$this->idACL." and idUser=".$newIdUser);
  }

  // This method inserts a row in the aclUserType table
  private function insert_type_user($newUserType, $assoc=0, $db=null) {
    if($newUserType == null || !TypeUser::is_type_user($newUserType))
      throw new GException(GException::$VAR_TYPE);
    else if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($assoc !== null && is_int($assoc) && 
       ($assoc < 0 || ($assoc > 0 && !Association::exists($asoc, $db))))
	throw new GException(GException::$ASSOCIATION_UNKNOWN);

    $db->connect();
    $db->execute("insert into aclUserType(idType,idACL,idAssociation) values(".$newUserType->idType.",".$this->idACL.",".$assoc.")");
  }

  // This method drops a row in the aclUserType table
  private function drop_type_user($newIdUserType, $assoc=0, $db=null) {
    if($newIdUserType === null || !is_int($newIdUserType))
      throw new GException(GException::$VAR_TYPE);
    else if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($assoc !== null && is_int($assoc) && 
       ($assoc < 0 || ($assoc > 0 && !Association::exists($asoc, $db))))
	throw new GException(GException::$ASSOCIATION_UNKNOWN);

    $consult = "delete from aclUserTypes where idACL=".$this->idACL." and idType=".$newIdUserType;
    if($assoc > 0)
      $consult .= " and idAssociation=".$assoc;

    $db->connect();
    $db->execute($consult);
  }

  // Inserts an acl permission into the database
  public function insert_acl($db=null){
    // We check the input parameters
    if($objOwner == null || !Obj::is_obj($objOwner))
      throw new GException(GException::$VAR_TYPE);
    else if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    // If the permission is not present in the database we insert in. In other case
    // we update it.
    $db->connect();

    if(!$this->exists_acl()) {
      $db->execute("insert into acl(idObj,permType) values(".$this->obj->idObject.",".$objOwner->type.")");
      $this->idACL = $db->id;

      // Check the validity of each user & usertype in the database
      // Insert the references
      foreach($this->users as $value) {
	if(!$value->check_user_db())
	  throw new GDatabaseException(GDatabaseException::$DB_EXTERN);
	$this->insert_user($value, $db);
      }

      // We insert the users and user types associated to the permission.
      foreach($this->userTypes as $value) {
	if(!$value->check_userType_db())
	  throw new GDatabaseException(GDatabaseException::$DB_EXTERN);
	insert_type_user($value, $db);
      }
    } else
      $this->modify_acl($db);

    return true;
  }

  // Modify the acl permission into the database
  public function modify_acl($db=null) {
    // Check input parameters.
    if($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_acl()) {
      $db->connect();
      $db->execute("update acl set idObj=".$this->obj->idObject.", permType=".$this->type.
		   " where idACL=".$this->idACL);

      // Add new users to the database ...
      $db->consult("select idUser from aclUser where idACL=".$this->idACL);
      $rows = null;
      foreach($db->rows as $value)
	$rows[] = intval($value['idUser']);

      foreach ($this->users as $value) {
	if ($value->check_user_db()) {
	  if(array_search($value->idUser,$rows) === false)
	    $this->insert_user($value, $db);
	} else
	  throw new GDatabaseException(GDatabaseException::$DB_EXTERN);
      }

      // ... and purge the old ones.
      foreach($rows as $value) {
	$drop = true;
	foreach ($this->users as $value2) {
	  if ($value === $value2->idUser) {
	    $drop = false;
	    break;
	  }
	}
	if($drop)
	  $this->drop_user_db($value['idUser'], $db);
      }

      // Add new type of users to the database ...
      $db->consult("select idType from aclUserType where idACL=".$this->idACL);
      $rows = null;
      foreach($db->rows as $value)
	$rows[] = intval($value['idType']);

      foreach($this->userTypes as $value) {
	if($value->check_userType_db()) {
	  if(array_search($value,$rows) === false)
	    $this->insert_type_user($value, $db);
	} else
	  throw new GDatabaseException(GDatabaseException::$DB_EXTERN);
      }

      // ... and purge the old ones.
      foreach($rows as $value) {
	$drop = true;
	foreach ($this->userTypes as $value2) {
	  if ($value === $value2->idUserType) {
	    $drop = false;
	    break;
	  }
	}
	if($drop)
	  $this->drop_type_user($value['idUser'], $db);
      }
    } else
      $this->insert_acl($db);
  }

  // Drop the acl permission from the database
  public function drop_acl($db=null){
    // Check input parameters.
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_acl()) {
      $db->connect();
      $db->execute("delete from aclUserType where idACL=".$this->idACL);
      $db->execute("delete from aclUser where idACL=".$this->idACL);
      $db->execute("delete from acl where idACL=".$this->idACL);
    }
  }

  // This method load the acl associated to the object and the type of permission.
  public function load_acl($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->obj == null || !Obj::is_obj($this->obj))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select * from acl where idObj=".$this->obj->idObject." and permType=".
		 $this->type);
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DATABASE_INTEGRITY);
    else if($db->numRows() === 1) {
      $row = $db->getRow();
      $this->idACL = intval($row['idACL']);
      $this->idEnv = intval($row['idEnv']);
      $this->load_users($db);
      $this->load_members(0,$db);
      $this->load_user_types($db);
    }
  }

  // This method load the users associated to the acl.
  private function load_users($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $this->users = array();
    $db->connect();
    $db->consult("select idUser from aclUser where idACL=".$this->idACL);
    $newUsers = $db->rows;
    for($i=0;$i < count($newUsers);$i++) {
      $user = new User();
      $user->load_user(intval($newUsers[$i]['idUser']),$db);
      $this->users[] = $user;
    }
  }

  // This method load the members associated to the acl.
  private function load_members($association=0, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // We obtain the idAssociation in which the acl is apply
    if(isset($_SESSION['association']) && Association::is_association($_SESSION['association']))
      $assoc = $_SESSION['association']->idAssociation;

    if($association !== null && is_int($association) && $association > 0 &&
       Association::exists($association,$db))
      $assoc = $association;

    if($assoc === null || !is_int($assoc) || $assoc < 1 || !Association::exists($assoc,$db))
      throw new GException(GException::$VAR_TYPE);

    // We load the members from the database
    $this->members = array();
    $db->connect();
    $db->consult("select idMember from aclMemberAssoc where idACL=".$this->idACL." and idAssociation=".$assoc);
    $newMembers = $db->rows;
    for($i=0;$i < count($newMembers);$i++) {
      $memer = new Member();
      $member->load_member(intval($newMembers[$i]['idMember']),$db);
      $this->members[] = $member;
    }
  }

  // This method load the user types associated to the acl.
  private function load_user_types($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $this->userTypes = array();
    $db->connect();
    $db->consult("select idType from aclUserType where idACL=".$this->idACL);
    $newTypes = $db->rows;
    for($i=0;$i < count($newTypes);$i++) {
      $type = new TypeUser();
      $type->load_type(intval($newTypes[$i]['idType']),$db);
      $this->userTypes[] = $type;
    }
  }

  // This method check if an acl exists in the database
  public  function exists_acl($newIdACL=0, $db=null) {
    if($db === null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->idACL;
    if(is_int($newIdACL) && $newIdACL > 0)
      $check = $newIdACL;

    if($check === null || !is_int($check))
      throw new GException(GException::$VAR_TYPE);

    return ACL::exists($check,$db);
  }

  // This method check if an acl exists in the database
  public static function exists($newIdACL, $db=null) {
    if($db === null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newIdACL === null || !is_int($newIdACL))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select idACL from acl where idACL=".$newIdACL);
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
    return ($db->numRows() === 1);
  }

  // This static method check if the parameter passed is an object of the type acl
  public static function is_acl($var){
    if(is_object($var) && (get_class($var) == "acl" || is_subclass_of($var, "acl")))
      return true;
    return false;
  }
}
?>