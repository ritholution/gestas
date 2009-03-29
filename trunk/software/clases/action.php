<?php
/**
 * action.php
 *
 * Description
 * This class represent the actions to do in the application.
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

class Action{
  private $idAction = -1;
  private $object = -1;
  private $class = null;
  private $method = null;
  private $idPlugin = -1;
  private $numParams = 0;

  // Constructor of the class
  public function __construct($newObject=-1, $newClass=-1, $newMethod=null,
			      $newIdPlugin=-1, $newNumParams=0) {
    if($newObject > -1 && is_int($newIdPlugin))
      $this->object = $newObject;

    if($newClass !== null && is_string($newClass) && class_exists($newClass))
      $this->class = new $newClass();

    if($newMethod !== null && is_string($newMethod)) {
      if(!method_exists($this->class,$newMethod))
	throw new GException(GException::$METHOD_UNKNOWN);
      $this->method = $newMethod;
    }

    if($newIdPlugin > -1 && is_int($newIdPlugin))
      $this->idPlugin = $newIdPlugin;

    if($newNumParams > -1 && is_int($newNumParams))
      $this->numParams = $newNumParams;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idAction":
    case "object":
    case "method":
    case "idPlugin":
    case "numParams":
      return $this->$var;
      break;
    case "class":
      return get_class($this->$var);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them..
  public function __set($var,$value) {
    switch($var){
    case "idAction":
      throw new GException(GException::$VAR_ACCESS);
    case "class":
      if(is_string($value) && class_exists($value))
	$this->$var = new $newClass();
      else if($value === null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "method":
      if($value !== null && !is_string($value))
	throw new GException(GException::$VAR_TYPE);
      else if(!method_exists($this->class,$value))
	throw new GException(GException::$METHOD_UNKNOWN);
      $this->$var = $value;
      break;
    case "object":
    case "idPlugin":
    case "numParams":
      if(!is_int($value) || $value < 0)
	throw new GException(GException::$VAR_TYPE);
      $this->$var = $value;
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Loads an action from the database.
  public function load_action($newIdAction=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($this->exists($newIdAction,$db))
      $this->idAction = $newIdAction;

    if(!$this->exists($this->idAction,$db))
      throw new GException(GException::$ACTION_UNKNOWN);

    $db->connect();
    $db->consult("select * from objAction where idObjAction=".$this->idAction);
    $row = $db->getRow();
    $this->object = intval($row['idObject']);
    if(class_exists($row['classAction']))
      $this->class = new $row['classAction']();
    else
      $this->class = null;
    if(method_exists($this->class,$row['methodAction']))
      $this->method = $row['methodAction'];
    else
      $this->method = null;
    $this->idPlugin = intval($row['idPlugin']);
    $this->numParams = intval($row['numParams']);
  }

  // Loads an action from the database.
  public function get_id_action_class_method($newClass=null, $newMethod=null, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if($newClass == null || $newMethod == null || !is_string($newClass) || !is_string($newMethod))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select idObjAction from objAction where classAction='".
		 $newClass."' and methodAction='".$newMethod."'");

    if($db->numRows() === 1) {
      $row = $db->getRow();
      return intval($row['idObjAction']);
    } else
      return false;
  }

  // This method execute an action (or method) of a session object.
  public function run($params=null){
    $execute = $this->method;
    $this->class->$execute($params);
  }

  // This method checks if an action exists search by id
  public function exists($newIdAction=-1, $db=null) {
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $action_check = $this->idAction;

    if($newIdAction > -1)
      $action_check = $newIdAction;
    
    if(!is_int($action_check))
      return false;

    $db->connect();
    $db->consult("select * from objAction where idObjAction=".$action_check);

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This function checks if the parameter passed is of the class Action
  public static function is_action($var){
    if(is_object($var) && (get_class($var) == "Action" || is_subclass_of($var, "Action")))
      return true;
    return false;
  }
}
?>