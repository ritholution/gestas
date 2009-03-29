<?php
/**
 * user.php
 *
 * Description
 * This class represent an user of the application.
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

require_once("type_user.php");
require_once("password.php");
require_once("gexception.php");
require_once("guserexception.php");
require_once("gdatabaseexception.php");
require_once("action.php");
require_once("template_filter.php");

class User{
  protected $idUser = 1;
  private $login = 'anonymous';
  private $typeUsers = null; // List of user types that the user belongs

  // TODO: How are we going to determinate the codification (MD5, DIGEST-MD5, ...) of the Password?
  // Options: Store in the appUser table, store as configuration parameter in the configuration table
  // (maybe the best option), ...
  private $password = null;
  private $isAuthenticated = false;

  // This method is the construct of the class
  public function __construct($newLogin='anonymous', $newPassword=null,
			      $newUserType='anonymous'){
    if($newLogin !== null && is_string($newLogin))
      $this->login = $newLogin;

    if($newPassword != null && Password::is_password($newPassword))
      $this->password = $newPassword;
    else if($newPassword != null && is_string($newPassword))
      $this->password = new Password($newPassword);
    else
      $this->password = new Password();

    if($newUserType !== null && TypeUser::is_type_user($newUserType)) {
      if($newUserType->exists_type())
	$this->typeUsers = array(clone $newUserType);
    } else if($newUserType !== null && is_string($newUserType)) {
      $this->typeUsers = array(new TypeUser($newUserType,$newIdAssoc));
      if($this->typeUsers[0]->exists_type())
	$this->typeUsers[0]->load_type_name($newUserType);
      else
	$this->typeUsers[0]->load_type(1);
    } else if($newUserType !== null && is_array($newUserType))
      $this->typeUsers = $this->check_user_types($newUserType);
  }

  // This method return the value of the internal variables
  public function __get($var) {
    switch($var){
    case "idUser":
    case "login":
    case "isAuthenticated":
    case "typeUsers":
      return $this->$var;
    case "password":
      // This variable are only accessible by this class
      throw new GException(GException::$VAR_ACCESS);
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This function sets the value of the internal variables, checking the input value.
  public function __set($var, $value){
    switch($var){
    case "idUser":
      // This variable can't be set from the outside.
      throw new GException(GException::$VAR_ACCESS);
    case "login":
      // The user login must be a string
      if($value === null || is_string($value)) {
	if($this->isAuthenticated)
	  $this->unauthentication();
	$this->$var = $value;
      } else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "isAuthenticated":
    case "password":
      // This variables cannot be set from the outside
      throw new GException(GException::$VAR_ACCESS);
    case "typeUsers":
      $this->$var = $this->check_user_types($value);
      break;
    default:
      // Variable unknown
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method authenticate the user
  public function authentication($params=null) {
    if(!isset($_SESSION['db']))
      throw new GException(GException::$VAR_TYPE);
    $db = $_SESSION['db'];

    if(!is_array($params) || count($params) < 2 ||
       !is_string($params[0]) || !is_string($params[1]))
      throw new GException(GException::$VAR_TYPE);

    if($params[0] !== 'anonymous') {
      if($this->isAuthenticated)
	$this->unauthentication();

      $userTmp = new User($params[0]);
      if($userTmp->load_user_by_login()) {
	$passCheck = new Password($params[1]);
      
	if($userTmp->password->compare($passCheck->codificate())) {
	  $this->load_user($userTmp->idUser);
	  $this->isAuthenticated = true;
	  $_SESSION['user'] = clone $this;
	  $_SESSION['out']->content = null;
	} else
	  throw new GUserException(GUserException::$LOGIN_FAIL);
      }
    }

    return $this->isAuthenticated;
  }

  // This method unauthenticate the user
  public function unauthentication() {
    $this->load_user_by_login('anonymous');
    $this->isAuthenticated = false;
    $_SESSION['user'] = $this;
    $_SESSION['out']->content = $this->get_login();
  }

  // This function updates the password if $oldPassword is the actual password and $newPassword is the
  // same password as $repeatNewPassword
  public function update_password($oldPassword, $newPassword, $repeatNewPassword) {
    // We check if the parameters are correct
    if($oldPassword === null || $newPassword === null || $repeatNewPassword === null ||
       (!is_string($oldPassword) && !Password::is_password($oldPassword)) ||
       (!is_string($newPassword) && !Password::is_password($newPassword)) ||
       (!is_string($repeatNewPassword) && !Password::is_password($repeatNewPassword)))
      throw new GException(GException::$VAR_TYPE);

    // We compare the actual password with $oldPassword
    if(!$this->password->compare($oldPassword))
      return false;

    // We compare $newPassword with $repeatNewPassword
    if($newPassword == null ||
       (Password::is_password($newPassword) &&
	!$newPassword->compare($repeatNewPassword)) ||
       (Password::is_password($$repeatNewPassword) &&
	!$$repeatNewPassword->compare($newPassword)) ||
       ($newPassword !== $repeatNewPassword))
      return false;

    // We asignate the new password
    if(Password::is_password($newPassword))
      $this->password = clone $newPassword;
    else {
      $this->password = new Password($newPassword);
      $this->password->isCodificated = false;
    }
    $this->modify_user($db);

    return true;
  }

  // This method indicates if the user is a valid user in the database.
  public function exists_user($newIdUser=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $check = $this->idUser;
    if(is_int($newIdUser) && $newIdUser > 0)
      $check = $newIdUser;

    return User::exists($check,$db);
  }

  // This method indicates if the user is a valid user in the database.
  public static function exists($newIdUser=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if(!is_int($newIdUser))
      throw new GException(GException::$VAR_TYPE);
    
    $db->connect();
    $db->consult("select idUser from appUser where idUser=".$newIdUser);
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method indicates if the user is a valid user in the database.
  public function exists_login($newLogin=null, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $check = $this->login;
    if($newLogin != null && is_string($newLogin))
      $check = $newLogin;

    $db->connect();
    $db->consult("select idUser from appUser where login='".$check."'");
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method loads an User from de database given the id of the User
  public function load_user($newIdUser=-1, $db=null) {
    if($newIdUser === null || !is_int($newIdUser) || $newIdUser < 0)
      throw new GException(GException::$VAR_TYPE);
    else if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $usr = $this->idUser;
    if(is_int($newIdUser) && $newIdUser > -1)
      $usr = $newIdUser;

    if($this->exists_user($usr,$db)) {
      $db->connect();
      $db->consult("select * from appUser where idUser=".$usr);
      $this->idUser = $usr;
      $row = $db->getRow();
      $this->login = $row['login'];
      // Maybe not convenient.
      $this->password = new Password($row['password']);
      $this->password->isCodificated = true;

      $this->load_user_types($db);

      return true;
    }

    return false;
  }

  // This method loads an user by its login
  public function load_user_by_login($newLogin=null, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $usr = $this->login;
    if($newLogin != null && is_string($newLogin))
      $usr = $newLogin;

    if($this->exists_login($usr)) {
      $db->connect();
      $db->consult("select idUser from appUser where login='".$usr."'");
      $row = $db->getRow();
      return $this->load_user(intval($row['idUser']),$db);
    }
    
    return false;
  }

  public function load_user_types($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $consult = "select idType from userUserType where idUser=".$this->idUser." and (idAssociation=0";
    if(isset($_SESSION['association']) && Association::is_association($_SESSION['association'])
       && $_SESSION['association']->exists_association())
      $consult .= " or idAssociation=".$_SESSION['association']->idAssociation;
    $consult .= ")";

    $db->connect();
    $db->consult($consult);
    $rows = $db->rows;
    $this->typeUsers = null;
    for($i=0; $i < count($rows); $i++) {
      $usrType = new TypeUser();
      $usrType->load_type(intval($rows[$i]['idType']));
      $this->typeUsers[] = $usrType;
    }
  }

  // This method insert an user in the database.
  public function insert_user($db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($this->login == null)
      throw new GException(GException::$PARAM_MISSING);

    if(is_int($this->idUser) && $this->idUser !== 1 &&
       $this->exists_user($this->idUser, $db))
      $this->modify_user($db);
    else if($this->exists_login($this->login, $db))
      $this->modify_user($db);
    else {
      $pass = $this->password->codificate();
      if($this->password->isCodificated)
	$pass = $this->password->pass;

      $db->connect();
      $db->execute("insert into appUser(login,password) values('".$this->login."','".
		   $pass."')");
      $this->idUser = $db->id;

      if($this->typeUsers !== null && is_array($this->typeUsers)) {
	foreach($this->typeUsers as $value)
	  $db->execute("insert into userUserType(idUser,idType) values(".$this->idUser.",".$value->idType.")");
      }
    }
  }

  // This method update an user in the database
  public function modify_user($db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($this->exists_user($this->idUser, $db) ||
       $this->exists_user_by_login($this->login, $db)) {
      $pass = $this->password->codificate();
      if($this->password->isCodificated)
	$pass = $this->password->pass;

      $db->connect();

      if(!$this->exists_user($this->idUser, $db) &&
	 $this->exists_user_by_login($this->login, $db)) {
	$db->consult("select idUser from appUser where login='".$this->login."'");
	if($db->numRows() !== 1)
	  throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
	$row = $db->getRow();
	$this->idUser = $row['idUser'];
      }

      $db->execute("update appUser set login='".$this->login."', password='".
		   $pass."' where idUser=".$this->idUser);

      if($this->typeUsers != null && is_array($this->typeUsers))
	foreach($this->typeUsers as $value) {
	  $value->modify_type($db);
	  $db->consult("select idUser from userUserType where idType=".$value->idType.
		       " and idUser=".$this->idUser);
	  if($db->numRows() === 0)
	    $db->execute("insert into userUserType values(".$this->idUser.",".$value->idType.")");
	  else if($db->numRows() > 1)
	    throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
	}
    }
  }

  // This method delete an user in the database
  public function delete_user($db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if(!$this->exists_user($this->idUser, $db) &&
       $this->exists_login($this->login, $db))
      $this->load_user_by_login();

    if($this->exists_user($this->idUser, $db)) {
      $db->connect();
      $db->execute("delete from userUserType where idUser=".$this->idUser);
      $db->execute("delete from appUser where idUser=".$this->idUser);
    }
  }

  // This method add an user type to the list
  public function add_type($newType, $newIdAssoc=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($newType === null)
      throw new GException(GException::$VAR_TYPE);

    $type = null;
    if(is_int($newType) && $newType > -1) {
      $type = new TypeUser();
      if($type->exists_type($newType,-1,$db))
	$type->load_type($newType);
      else
	throw new GException(GException::$VAR_TYPE);
    } else if(is_string($newType)) {
      $type = new TypeUser();
      if($type->exists_type($newType,$newIdAssoc,$db))
	$type->load_type_assoc($newType,$newIdAssoc);
      else
	throw new GException(GException::$VAR_TYPE);
    } else if(TypeUser::is_type_user($newType) &&
	      $newType->exists_type($newType->idType,$newType->idAssociation,$db))
      $type = clone $newType;
    else
      throw new GException(GException::$VAR_TYPE);

    $this->typeUsers[] = $type;
    $db->connect();
    $db->execute("insert into userUserType values(".$this->idUser.",".$type->idType.")");
  }

  // This method checks if the user is an user type
  public function is_type($newType, $newAssoc=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($newType === null || !TypeUser::exists($newType,$newAssoc,$db))
      throw new GException(GException::$VAR_TYPE);

    $type = new TypeUser();
    if(is_int($newType))
      $type->load_type($newType,$db);
    else
      $type->load_type_name($newType,$newAssoc,$db);

    if($this->typeUsers !== null && is_array($this->typeUsers))
      foreach($this->typeUsers as $value)
	if($value->idType === $type->idType)
	  return true;

    return false;
  }

  // This method delete an user type from the list
  public function del_type($newType, $newAssoc=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($newType === null)
      throw new GException(GException::$VAR_TYPE);

    $type = null;
    if(is_int($newType) && $newType > -1) {
      $type = new TypeUser();
      if($type->exists_type($newType,-1,$db))
	$type->load_type($newType);
      else
	throw new GException(GException::$VAR_TYPE);
    } else if(is_string($newType)) {
      $type = new TypeUser();
      if($type->exists_type($newType,$newIdAssoc,$db))
	$type->load_type_assoc($newType.$newIdAssoc);
      else
	throw new GException(GException::$VAR_TYPE);
    } else if(TypeUser::is_type_user($newType) &&
	      $newType->exists_type($newType->idType,$newType->idAssociation,$db))
      $type = clone $newType;
    else
      throw new GException(GException::$VAR_TYPE);

    for($i=0;$i < count($this->typeUsers);$i++) {
      if($this->typeUsers[$i]->idType === $type->idType) {
	$this->typeUsers[$i] = null;
	$db->connect();
	$db->execute("delete from userUserType where idUser=".$this->idUser." and idType=".$value->idType);
      }
    }
  }

  // This method modify the password associated to a user
  public function modify_password($params=null) {
    if(!isset($_SESSION['db']))
      throw new GException(GException::$VAR_TYPE);
    $db = $_SESSION['db'];

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('user', 'modify_password')) !== false) {
	$filter->register_var('action',$action);
	$_SESSION['out']->content = $filter->filter_file('change_password.html');
      }
    } else {
      if(!is_array($params) || count($params) < 3 ||
	 !isset($params['password']) || !is_string($params['password']) || 
	 !isset($params['newPassword']) || !is_string($params['newPassword']) ||
	 !isset($params['repeatPassword']) || !is_string($params['repeatPassword']))
	throw new GException(GException::$VAR_TYPE);

      $this->load_user_by_login($_SESSION['user']->login);

      if(!$this->password->compare($params['password'],$this->login))
	throw new GUserException(GUserException::$USER_UNKNOWN);

      if($params['newPassword'] !== $params['repeatPassword'])
	throw new GUserException(GUserException::$PASS_EQ_FAIL);

      $this->password->pass = $params['newPassword'];
      $this->password->isCodificated = false;
      $this->modify_user($db);

      $filter->register_var('success','Contrase&ntilde;a cambiada');
      $_SESSION['out']->content = $filter->filter_file('success.html');
    }
  }

  // This method implement the action 'New User'
  public function new_user($params=null) {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('User', 'new_user')) !== false) {
	$filter->register_var('action',$action);
	$_SESSION['out']->content = $filter->filter_file('new_user.html');
      }
    } else {
      if(!is_array($params) || count($params) < 3 ||
 	 !isset($params['login']) || !is_string($params['login']) || 
 	 !isset($params['password']) || !is_string($params['password']) ||
 	 !isset($params['repeatPassword']) || !is_string($params['repeatPassword']))
 	throw new GException(GException::$VAR_TYPE);

      if($this->exists_login($params['login']))
	throw new GUserException(GUserException::$USER_EXISTS);
      else if($params['password'] !== $params['repeatPassword'])
	throw new GUserException(GUserException::$PASS_EQ_FAIL);

      $newUser = new User($params['login'],$params['password'],'user');
      $newUser->insert_user();

      $filter->register_var('success','Usuario creado correctamente');
      $_SESSION['out']->content = $filter->filter_file('success.html');
    }
  }

  // This method returns the html output for the login block
  public function get_login() {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];
    
    $nextAction = new Action();
    if(($action = $nextAction->get_id_action_class_method('User','authentication')) !== false) {
      // $entry = $nextAction->get_id_action_class_method('User','lost_password');
      // $filter->register_var('action',$entry);
      $filter->register_var('action','-1');
      $filter->register_var('entryName',htmlentities('¿Olvidó su contraseña?'));
      $lostPass = $filter->filter_file("login_action.html");

      $entry = $nextAction->get_id_action_class_method('User','new_user');
      $filter->register_var('action',$entry);
            $filter->register_var('entryName','Alta de nuevo usuario');
      $newUser = $filter->filter_file("login_action.html");

      $entry = $nextAction->get_id_action_class_method('MemberManagement','signup_request');
      $filter->register_var('action',$entry);
      $filter->register_var('entryName','Alta de nuevo socio');
      $newMember = $filter->filter_file("login_action.html");

      $entry = $nextAction->get_id_action_class_method('AssociationManagement','new_association');
      $filter->register_var('action',$entry);
      $filter->register_var('entryName',htmlentities('Alta de nueva asociación'));
      $newAssoc = $filter->filter_file("login_action.html");

      $filter->register_var('action',$action);
      $filter->register_var('lostPass',$lostPass);
      $filter->register_var('newUser',$newUser);
      $filter->register_var('newMember',$newMember);
      $filter->register_var('newAssoc',$newAssoc);
      $block = $filter->filter_file('login.html');
    }
    
    return $block;
  }

  // This method returns all the valid user types of an array.
  private function check_user_types($array) {
    if($array=null)
      return null;

    $result = null;
    if(is_string($array)) {
      $result = array(new TypeUser($array));
      if($result[0]->exists_type())
	$result[0]->load_type_name();
      else
	$result[0]->load_type(1);
    } else if(TypeUser::is_type_user($array)) {
      if($array->exists_type())
	$result = array(clone $array);
    } else if(is_array($array)) {
      foreach($array as $value) {
	if(is_string($value)) {
	  $tmp = new TypeUser($value);
	  if($tmp->exists_type())
	    $result[] = $tmp;
	} else if(TypeUser::is_type_user($value)) {
	  if($value->exists_type())
	    $result[] = clone $value;
	}
      }
    }

    return $result;
  }

  // This method check if the parameter passed is an object of the type User.
  public static function is_user($user){
    if(is_object($user) && (get_class($user) === "User" || is_subclass_of($user, "User")))
      return true;
    return false;
  }
}
?>