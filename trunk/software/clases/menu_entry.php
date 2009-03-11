<?php
/**
 * menu_entry.php
 *
 * Description
 * This class represents a menu entry.
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
require_once("action.php");

class MenuEntry{
  private $idEntry = -1;
  private $entryName = null;
  private $action = null;

  // Constructor of the class
  public function __construct($newEntryName=null, $newAction=null) {
    if(is_array($newEntryName) && isset($newEntryName['idEntry']) &&
       isset($newEntryName['entryName']) && isset($newEntryName['idAction'])) {
      $this->idEntry = intval($newEntryName['idEntry']);
      $this->entryName = $newEntryName['entryName'];

      // Load the action from the database.
      $this->action = new Action();
      $this->action->load_action(intval($newEntryName['idAction']));
    }

    if($newEntryName !== null && is_string($newEntryName))
      $this->entryName = $newEntryName;

    if($newAction !== null && Action::is_action($newAction))
      $this->action = $newAction;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idEntry":
    case "entryName":
    case "action":
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
    case "idEntry":
      throw new GException(GException::$VAR_ACCESS);
    case "entryName":
      if($value === null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "action":
      if($value === null || Action::is_action($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method returns the html output that correspond to this menu entry.
  public function html_output(){
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $filter->register_var('action',$this->action->idAction);
    $filter->register_var('entryName',$this->entryName);
    return $filter->filter_file('menu_entry.html');
  }

  // This method checks if a user has permission to execute the menu entry.
  public function has_permission() {
    $obj = new Obj();
    $obj->load_obj($this->action->object);
    return $obj->has_permission($_SESSION['user']);
  }

  // This function checks if the parameter passed is of the class File
  public static function is_menu_entry($var){
    if(is_object($var) && (get_class($var) == "MenuEntry" || is_subclass_of($var, "MenuEntry")))
      return true;
    return false;
  }
}
?>