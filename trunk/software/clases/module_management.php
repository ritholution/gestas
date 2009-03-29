<?php
/**
 * module_management.php
 *
 * Description
 * This class manage the modules of the application.
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
require_once("module.php");
require_once("association.php");
require_once("database.php");

class ModuleManagement{
  private $active = null; // List of active modules
  private $inactive = null; // List of inactive modules

  // Constructor of the class
  public function __construct() {
    $this->load_active_modules();
    $this->load_inactive_modules();
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "active":
    case "inactive":
      // This variables are not accesible from the outside
      throw new GException(GException::$VAR_ACCESS);
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them..
  public function __set($var,$value) {
    switch($var){
    case "active":
    case "inactive":
      // This variables are not accesible from the outside
      throw new GException(GException::$VAR_ACCESS);
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Returns the plugin id given his name
  public function get_id_module($moduleName, $db=null) {
    if($moduleName == null || !is_string($moduleName))
      throw new GException(GException::$VAR_TYPE);

    // Obtain the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }
    
    $db->connect();
    $db->consult("select idPlugin from plugins where plugName='".$moduleName."'");
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
    else if($db->numRows() === 1) {
      $idPlugin = $db->getRow();
      return $idPlugin['idPlugin'];
    }

    return -1;
  }

  // This method checks if the module passed as parameter is active
  public function is_active($moduleCheck){
    if(!is_int($moduleCheck) && !Module::is_module($moduleCheck))
      throw new GException(GException::$VAR_TYPE);

    if(is_int($moduleCheck)) {
      $module = $moduleCheck;
    } else
      $module = $moduleCheck->idPlugin;

    // Check if the module is in the active vector
    for($i=0;$i < count($this->active);$i++)
      if($this->active[$i]->idPlugin == $module)
	return true;

    return false;
  }

  // This method activate the module passed as parameter
  public function activate($module, $assoc=null, $db=null) {
    // The module is already activated
    if($this->is_active($module))
      return true;

    // Obtain the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }
    
    // Obtain the module
    if(is_int($module)) {
      $plugin = new Module();
      $plugin->load_module($module);
    } else if(Module::is_module($module))
      $plugin = clone $module;

    // Move the module from the inactive vector to the active vector
    for($i=0;$i < count($this->inactive);$i++)
      if($this->inactive[$i]->idPlugin === $plugin->idPlugin) {
	$this->active[] = $this->inactive[$i];
	$this->inactive[$i] = null;
      }

    $db->connect();

    // Active the module in the db
    if($assoc !== null && Association::is_association($assoc) && $assoc->exists($db))
      $db->execute("update pluginAssociation set active=1 where idPlugin=".$plugin->idPlugin.
		   " and idAssociation=".$assoc->idAssociation);

    // Load the configurations of the activated module
    $db->consult("select * from configuration where idPlugin=".$plugin->idPlugin);
    $numConfigs=$db->numRows();
    for($i=0;$i < $numConfigs;$i++) {
      $mod_config = $db->getRow();
      $_SESSION['config'][$mod_config['idPlugin']][$mod_config['confAttrib']] = $mod_config['confValue'];
    }

    // Load the class which define the module
    require_once($_SESSION['config'][$plugin->idPlugin]['INDEX_DIR']."/module.php");
    $modName = $plugin->plugName;
    $mod = new $modName();
  }

  // This method deactivate the module passed as parameter
  public function deactivate($module, $assoc=null, $db=null){
    // The module is already inactive
    if(!$this->is_active($module))
      return true;

    // Obtain the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }
    
    // Obtain the module
    if(is_int($module)) {
      $plugin = new Module();
      $plugin->load_module($module);
    } else if(Module::is_module($module))
      $plugin = clone $module;

    // Move the module from the inactive vector to the active vector and load the configurations
    for($i=0;$i < count($this->inactive);$i++)
      if($this->active[$i]->idPlugin === $plugin->idPlugin) {
	$this->inactive[]=$this->active[$i];
	$this->active[$i]=null;
      }

    $db->connect();

    // Deactive the module in the db
    if($assoc !== null && Association::is_association($assoc) && $assoc->exists($db))
      $db->execute("update pluginAssociation set active=0 where idPlugin=".$plugin->idPlugin.
		   " and idAssociation=".$this->assoc->idAssociation);

    // Unload the configurations and the object of the module
    $_SESSION['config'][$plugin->idPlugin] = null;
    $mod = new $plugin->$plugName();
    if(method_exists($mod,'unload'))
      $mod->unload();
  }

  // This function loads all the active module definitions
  public function load_files(){
    if(!isset($_SESSION['config']))
      throw new GException(GException::$VAR_ACCESS);

    for($i=0;$i < count($this->active);$i++) {
      require_once($_SESSION['config'][$this->active[$i]->idPlugin]['INDEX_DIR']."/module.php");
      if(class_exists($this->active[$i]->plugName)) {
	$mod = new $this->active[$i]->plugName();
	$_SESSION[$this->active[$i]->plugName] = $mod;
      }
    }
  }

  // This method loads the active modules of an association.
  public function load_active_modules($assoc=null, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($assoc === null || !Association::is_association($assoc) || !$assoc->exists($db)) {
      if(isset($_SESSION['association']) && 
	 Association::is_association($_SESSION['association']) &&
	 $_SESSION['association']->exists_association(-1,$db))
	$assoc = $_SESSION['association'];
    }

    // If the active list is not empty we flush it out.
    if(count($this->active) > 0)
      $this->active = null;

    // We obtain the active plugins from the database.
    $db->connect();
    $db->consult("select idPlugin from plugins where idPlugin!=1 and base=1");
    $plugsId = $db->rows;
    for($i=0; $i < count($plugsId); $i++) {
      $plugin = new Module();
      $plugin->load_module(intval($plugsId[$i]['idPlugin']));
      $this->active[] = $plugin;
    }

    if($assoc !== null && Association::is_association($assoc)) {
      $db->consult("select idPlugin from pluginAssociation where idAssociation=".
		   $assoc->idAssociation." and active=1 and idPlugin!=1");
      $plugsId = $db->rows;
      for($i=0; $i < count($plugsId); $i++) {
	$plugin = new Module();
	$plugin->load_module(intval($plugsId[$i]['idPlugin']));
	$this->active[] = $plugin;
      }
    }


    // We load the configurations of the active plugins
    for($i=0;$i < count($this->active);$i++) {
      $db->consult("select * from configuration where idPlugin=".$this->active[$i]->idPlugin);
      $numConfigs = $db->numRows();
      for($j=0;$j < $numConfigs;$j++) {
 	$mod_config = $db->getRow();
 	$_SESSION['config'][$mod_config['idPlugin']][$mod_config['confAttrib']] = $mod_config['confValue'];
      }

      // Load the class which define the module
      require_once($_SESSION['config'][$this->active[$i]->idPlugin]['INDEX_DIR']."/module.php");
      if(class_exists($this->active[$i]->plugName)) {
	$mod = new $this->active[$i]->plugName();
	$_SESSION[$this->active[$i]->plugName] = $mod;
      }
    }
  }

  // This method loads the inactive modules.
  public function load_inactive_modules($db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // If the inactive list is not empty we flush it out.
    if(count($this->inactive) > 0)
      $this->inactive = null;

    // We get all the plugins from the database and store in the inactive list those who
    // are not in the active list.
    $db->connect();
    $db->consult("select idPlugin from plugins where idPlugin!=1");
    $numPlugins=$db->numRows();
    for($i=0;$i < $numPlugins;$i++) {
      $idPlugin = $db->getRow();
      $plugin = new Module();
      $plugin->load_module(intval($idPlugin['idPlugin']));
      $isInactive = true;
      if($this->active !== null) {
	foreach($this->active as $value) {
	  if($value->idPlugin === $plugin->idPlugin) {
	    $isInactive = false;
	    break;
	  }
	}
      }

      if($isInactive)
	$this->inactive[] = $plugin;
    }
  }

  // This function checks if the parameter passed is of the class File
  public static function is_module_management($var){
    if(is_object($var) && (get_class($var) == "ModuleManagement" || is_subclass_of($var, "ModuleManagement")))
      return true;
    return false;
  }
}
?>