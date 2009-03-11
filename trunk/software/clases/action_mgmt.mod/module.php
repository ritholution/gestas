<?php
/**
 * module.php
 *
 * Description
 * This module manage the actions of the application.
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

require_once($_SESSION['class_base']."/gexception.php");
require_once($_SESSION['class_base']."/guserexception.php");
require_once($_SESSION['class_base']."/action.php");
require_once($_SESSION['class_base']."/menu.php");
require_once($_SESSION['class_base']."/content.php");

class ActionManagement{
  // Constructor of the class
  public function __construct() {
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them.
  public function __get($var) {
    switch($var){
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them.
  public function __set($var,$value) {
    switch($var){
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method runs the next action to execute.
  public function run_next_action($pUser=null, $db=null) {
    // We check the parameters.
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } if($pUser == null || !User::is_user($pUser)) {
      if(!isset($_SESSION['user']))
	throw new GException(GException::$VAR_TYPE);
      $pUser = $_SESSION['user'];
    }

    // We obtain the action to execute.
    $action = -1;
	
    //Preference Order: Session>Post>Get
    if(isset($_SESSION['action'])){
      $action = $_SESSION['action'];
    }else{
      if(isset($_POST['action'])) {
        if(is_int($_POST['action']))
	  $action = $_POST['action'];
	else if (is_string($_POST['action']))
	  $action = intval($_POST['action']);
      }else if(isset($_GET['a'])) {
	if(is_int($_GET['a']))
	  $action = $_GET['a'];
	else if (is_string($_GET['a']))
	  $action = intval($_GET['a']);
      }
    }

    if(!is_int($action))
      throw new GException(GException::$ACTION_UNKNOWN);
    else if($action === -1)
      return;

    // We load the action to execute.
    $selAction = new Action();
    if($action !== -1 && !$selAction->exists($action))
      throw new GException(GException::$ACTION_UNKNOWN);

    $selAction->load_action($action);

    // We check the permissions of the user
    $objAction = new Obj();
    $objAction->load_obj($selAction->object);
    if(!$objAction->has_permission($pUser,ACL::$EXECUTE))
      throw new GUserException(GUserException::$USER_PERM);

    // We execute the action.
    $params = null;
    if(isset($_POST['param'])) {
      if(is_array($_POST['param']))
	$params = $_POST['param'];
      else
	$params = array($_POST['param']);
    } else if(isset($_GET['p']))
      $params = explode(';',$_GET['p']);
    $selAction->run($params);
  }

  // This function checks if the parameter passed is of the class File
  public static function is_action_management($var){
    if(is_object($var) && (get_class($var) === "ActionManagement" || is_subclass_of($var,"ActionManagement")))
      return true;
    return false;
  }
}
?>