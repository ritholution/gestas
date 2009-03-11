<?php
/**
 * module.php
 *
 * Description
 * This class represents a module (or plugin) of the application.
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
require_once("database.php");

class Module{
  private $idPlugin=-1;
  private $plugName=null;
  private $description=null;

  // Constructor of the class
  public function __construct($newPlugName=null, $newDescription=null) {
    if($newPlugName != null && is_string($newPlugName))
      $this->plugName = $newPlugName;

    if($newDescription !== null && is_string($newDescription))
      $this->description = $newDescription;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idPlugin":
    case "plugName":
    case "description":
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
    case "idPlugin":
      // This variables are not accesible from the outside
      throw new GException(GException::$VAR_ACCESS);
    case "plugName":
      if(is_string($value) && $value != null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "description":
      if(is_string($value) || $value == null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method inserts a module to the database
  public function insert_module($db=null){
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->plugName == null)
      throw new GException(GException::$PARAM_MISSING);

    // We load the active plugins and its configurations
    $db->connect();
    $db->execute("insert into plugins(plugName,description) values('".$this->plugName.
		 "','".$this->description."')");
    $this->idPlugin = $db->id;
  }

  // This method modify a module in the database and, if it's neccesary, in the
  // configs, active and inactive variables
  public function modify_module($db=null){
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->idPlugin === -1)
      throw new GException(GException::$PARAM_MISSING);

    // We load the active plugins and its configurations
    $db->connect();
    $db->execute("update plugins set plugName='".$this->plugName."' and description='".
		 $this->description."' where idPlugin=".$this->idPlugin);
  }

  // This method drops a module from de database and from the system variables.
  public function drop_module($db=null){
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    } else if($this->plugName == null && $this->idPlugin === -1)
      throw new GException(GException::$PARAM_MISSING);

    // We load the active plugins and its configurations
    $db->connect();
    if($this->idPlugin !== -1)
      $db->execute("delete from plugins where idPlugin='".$this->idPlugin."'");
    else
      $db->execute("delete from plugins where plugName='".$this->plugName."'");
  }

  // This method load a module from the database.
  public function load_module($newPlug=null, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(is_int($newPlug) && $newPlug > -1)
      $this->idPlugin = $newPlug;
    else if(is_string($newPlug) && $newPlug != null)
      $this->plugName = $newPlug;

    if($this->plugName == null && $this->idPlugin === -1)
      throw new GException(GException::$PARAM_MISSING);

    $db->connect();

    if($this->idPlugin > -1)
      $db->consult("select * from plugins where idPlugin=".$this->idPlugin);
    else
      $db->consult("select * from plugins where plugName='".$this->plugName."'");

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
    else if($db->numRows() === 1) {
      $row = $db->getRow();
      $this->idPlugin = intval($row['idPlugin']);
      $this->plugName = $row['plugName'];
      $this->description = $row['description'];
    }
  }

  // This method checks if a module exists search by id
  public function exists($newIdPlugin=-1, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $plugin_check = $this->idPlugin;
    if($newIdPlugin > -1)
      $plugin_check = $newIdPlugin;

    if($plugin_check > -1) {
      $db->connect();
      $db->consult("select * from plugins where idPlugin=".$plugin_check);

      if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
      return ($db->numRows() === 1);
    }

    return false;
  }

  // This function checks if the parameter passed is of the class File
  public static function is_module($var){
    echo "hola";
    if(is_object($var) && (get_class($var) == "Module" || is_subclass_of($var, "Module")))
      return true;
    return false;
  }
}
?>