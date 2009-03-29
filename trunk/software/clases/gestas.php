<?php
/**
 * gestas.php
 *
 * Description
 * This class is the main class of the application, because it control 
 * the execution flow of the program. It receives the actions requested
 * by the user, call to the specific module to perform the action and 
 * generates the output that gets the user back.
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

require_once("database.php");
require_once("gexception.php");
require_once("guserexception.php");
require_once("gdatabaseexception.php");
require_once("user.php");
require_once("language.php");
require_once("output.php");
require_once("module_management.php");
require_once("dni.php");
require_once("template_filter.php");

class Gestas{
  private $db = null;
  private $config = null;
  private $lang = null;
  private $action_mgmt = null;
  private $user = null;
  private $assoc = null;
  private $out = null;
  private $module_mgmt = null;
  private $filter = null;
  // private $log = null;

  // Constructor of the class.
  public function __construct() {
    try{
      $this->run();
    } catch(GException $e) {
      if(!isset($_SESSION['filter']))
 	$this->filter = new TemplateFilter();
      else
 	$this->filter = $_SESSION['filter'];

      // Insert log
      $this->filter->register_var('error',$e->getOutMessage());
      $output = $this->filter->filter_file('exception.html');
      echo $output;
    }
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "db":
      return $this->db;
    case "config":
      // Variable only for internal use.
      throw new GException(GException::$VAR_ACCESS);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them..
  public function __set($var,$value) {
    switch($var){
    case "db":
      if($value == null || Database::is_database($value) === false)
	throw new GException(GException::$VAR_TYPE);
      $this->db = $value;
      break;
    case "config":
      // Variable for internal use only. It can't be assigned from outside.
      throw new GException(GException::$VAR_ACCESS);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method execute the application
  public function run() {
    switch(BBDD_TYPE){
    case "MySQL":
      require_once("mysql.php");
      $this->db = new Mysql_db(BBDD_HOST,BBDD_PORT,BBDD_USER,BBDD_PASS,BBDD_DBNAME);
      break;
    default:
      throw new GDatabaseException(GDatabaseException::$DB_TYPE,BBDD_TYPE);
      break;
    }

    session_start();

    iconv_set_encoding('input_encoding','UTF-8');
    iconv_set_encoding('output_encoding','UTF-8');
    iconv_set_encoding('internal_encoding','UTF-8');

    if (!isset($_SESSION['db']))
      $_SESSION['db']=$this->db;
    else
      $this->db=$_SESSION['db'];

    // We load the log manager.
/*     if(!isset($_SESSION['log'])) { */
/*       $this->log = new LogManagement(); */
/*       $_SESSION['log'] = $this->log; */
/*     } else */
/*       $this->log = $_SESSION['log']; */

    // We load the configurations
    if(!isset($_SESSION['config']))
      $this->load_system_configurations();

    // We load the language settings
    if(!isset($_SESSION['lang'])) {
      $this->lang = new Language();
      $_SESSION['lang'] = $this->lang;
    } else
      $this->lang = $_SESSION['lang'];

    // We initialize the filter
    if(!isset($_SESSION['filter'])) {
      $this->filter = new TemplateFilter();
      $_SESSION['filter'] = $this->out;
    } else
      $this->filter = $_SESSION['filter'];

    // We initialize the output
    if(!isset($_SESSION['out'])) {
      $this->out = new Output();
      $_SESSION['out'] = $this->out;
    } else {
      $this->out = $_SESSION['out'];
      $this->out->clear();
    }

    // We load the user
    if(!isset($_SESSION['user'])) {
      $this->user = new User('anonymous','','anonymous');
      $this->user->load_user_by_login();
      $_SESSION['out']->content = $this->user->get_login();
      $_SESSION['user'] = $this->user;
    } else {
      $this->user = $_SESSION['user'];
      if(!$this->user->isAuthenticated)
 	$_SESSION['out']->content = $this->user->get_login();
    }

    // We load the core plugins (modules)
    if(!isset($_SESSION['module_mgmt'])) {
     $this->module_mgmt = new ModuleManagement();
     $_SESSION['module_mgmt'] = $this->module_mgmt;
    } else {
      $this->module_mgmt = $_SESSION['module_mgmt'];
      $this->module_mgmt->load_files();
    }

    // We execute the next action
    try {
      if(!isset($_SESSION['ActionManagement']) ||
	 !ActionManagement::is_action_management($_SESSION['ActionManagement']))
	throw new GException(GException::$MODULE_LOAD_FAIL);
      $_SESSION['ActionManagement']->run_next_action();
    } catch(GUserException $e){
      // Insert log
      $this->filter->register_var('msg',$e->getOutMessage());
      $_SESSION['out']->content = $this->filter->filter_file('recover_exception.html');
    }

    // We show the output generated by the action.
    $this->out->show();
  }

  // This method loads the system configurations
  public function load_system_configurations(){
    if($this->db == null)
      throw new GException(GException::$VAR_TYPE);

    // Load of configurations of all the modules and plugins in $config
    $this->db->connect();
    $this->db->consult("select * from configuration where idPlugin=1");
    $numConfigs=$this->db->numRows();
    for($i=0;$i < $numConfigs;$i++) {
      $mod_config = $this->db->getRow();
      $this->config[$mod_config['idPlugin']][$mod_config['confAttrib']] = $mod_config['confValue'];
    }

    if(!isset($this->config[1]['dir_base']))
      $this->config[1]['dir_base'] = '/var/www';

    if(!isset($this->config[1]['class_base']))
      $this->config[1]['class_base'] = 'clases';

    if(!isset($this->config[1]['template_base']))
      $this->config[1]['template_base'] = 'templates';

    if(!isset($this->config[1]['def_template']))
      $this->config[1]['def_template'] = 'template1.html';

    // We made the configurations accesible to all the system.
    $_SESSION['config'] = $this->config;
    $_SESSION['dir_base'] = $_SESSION['config'][1]['dir_base'];
    $_SESSION['class_base'] = $_SESSION['dir_base'].'/'.$_SESSION['config'][1]['class_base'];
    $_SESSION['template_base'] = $_SESSION['dir_base'].'/'.$_SESSION['config'][1]['template_base'];
  }

  // This method saves the system configurations.
  public function save_configurations() {
    if($this->db == null)
      throw new GException(GException::$VAR_TYPE);

    if(!$this->db->isConnected)
      $this->db->connect();

    // This two loops get each configuration key & value from each plugin and, if
    // it's inserted in the database, it's update. Elsewhere the configuration it's
    // inserted.
    foreach($this->config as $plugin => $key) {
      foreach($this->config[$plugin] as $key => $value) {
	if(!$this->db->consult("select confValue from configuration where confAttrib='".$key.
			       "' and idPlugin=".$plugin.""))
	  throw new GDatabaseException(GDatabaseException::$DB_CONSULT);

	if($this->db->numRows() == 0) {
	  if(!$this->db->execute("insert into configuration values (".$plugin.",'".$key."','".$value."')"))
	    throw new GDatabaseException(GDatabaseException::$DB_CONSULT);
	} else if($this->db->numRows() == 1){
	  $row = $this->db->getRow();
	  if($row['valorConfiguracion'] != $value) {
	    if(!$this->db->execute("update configuration set confValue='".$value."' where idPlugin=".
				   $plugin." and confAttrib='".$key."'"))
	      throw new GDatabaseException(GDatabaseException::$DB_CONSULT);
	  }
	} else
	  throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
      }
    }
  }
}
?>
