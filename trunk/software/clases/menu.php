<?php
/**
 * menu.php
 *
 * Description
 * This class implements the Menu data type.
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
require_once("menu_entry.php");
require_once("acl.php");

class Menu{
  private $idMenu = null;
  private $entrys = null;
  private $title = null;
  private $out = null;

  // Constructor of the class
  public function __construct($newEntry=null, $newTitle=null) {
    if($newEntry !== null && is_array($newEntry))
      $this->entrys = $this->check_entrys($newEntry);
    else if(MenuEntry::is_menu_entry($newEntry))
      $this->entrys[] = $newEntry;

    if($newTitle !== null && is_string($newTitle))
      $this->title = $newTitle;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "idMenu":
    case "entrys":
    case "title":
    case "out":
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
    case "idMenu":
    case "out":
      throw new GException(GException::$VAR_ACCESS);
    case "entrys":
      if($value === null)
	$this->$var = $value;
      else if(MenuEntry::is_menu_entry($value))
	$this->$var = array($value);
      else if(is_array($value))
	$this->$var = $this->check_entrys($value);
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "title":
      if($value === null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method loads the content of the section selected by the user.
  public function load_menu($newMenu, $db=null){
    // We check the method parameters
    if($newMenu === null || (!is_int($newMenu) && !is_string($newMenu)))
      throw new GException(GException::$VAR_TYPE);
    else if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      else
	$db=$_SESSION['db'];
    }

    // We get the idMenu and the title from the database.
    $db->connect();
    $consult = "select * from menu where ";

    if(is_int($newMenu))
      $consult .= "idMenu=";
    else
      $consult .= "title=";

    $db->consult($consult.$newMenu);
    if($db->numRows() === 0)
      throw new GException(GException::$MENU_UNKNOWN);
    else if($db->numRows() === 1) {
      $row = $db->getRow();
      $this->idMenu = intval($row['idMenu']);
      $this->title = $row['title'];
    } else
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    // We get the menu entrys related with this menu from the database.
    $db->consult("select * from menuEntry where idMenu=".$this->idMenu);
    $entry_rows = $db->rows;
    for($i=0; $i < count($entry_rows); $i++) {
      $newMenuEntry = new MenuEntry($entry_rows[$i]);
      if($newMenuEntry->has_permission())
	$this->entrys[] = $newMenuEntry;
    }
  }

  private function check_entrys($var){
    if(!is_array($var))
      throw new GException(GException::$VAR_TYPE);
    
    $valids = null;
    for($i=0;$i < count($var);$i++)
      if(MenuEntry::is_menu_entry($var[$i]) && $_SESSION['acl']->has_permission($var[$i]->action))
 	$valids[] = $var[$i];

    return $valids;
  }

  // This method returns the html output that correspond to this menu.
  public function generate_html_output() {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $filter->register_var('title',$this->title);
    $entrys_out = '';
    if($this->entrys !== null && is_array($this->entrys))
      foreach($this->entrys as $value)
	if($value->has_permission())
	  $entrys_out .= $value->html_output();
    $filter->register_var('entrys',$entrys_out);

    $this->out = $filter->filter_file('menu.html');
  }

  // This function checks if the parameter passed is of the class File
  public static function is_menu($var){
    if(is_object($var) && (get_class($var) == "Menu" || is_subclass_of($var, "Menu")))
      return true;
    return false;
  }
}
?>