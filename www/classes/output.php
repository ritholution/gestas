<?php
/**
 * output.php
 *
 * Description
 * This class implements the object Output to manage the output of the
 * application.
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
require_once("template_filter.php");

class Output{
  private $output = null;
  private $content = null;

  // Constructor of the class
  public function __construct($newOutput = null, $newContent = null) {
    if($newOutput !== null && is_string($newOutput))
      $this->output = $newOutput;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them..
  public function __get($var) {
    switch($var){
    case "content":
    case "output":
      return $this->$var;
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN,$var);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them.
  public function __set($var,$value) {
    switch($var){
    case "content":
    case "output":
      if($value === null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE,$var);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method clear the output.
  public function clear() {
    $this->output = null;
    $this->content = null;
  }

  // This method shows the output.
  public function show($template=null) {
    if($template == null || !is_string($template)){
      if(!isset($_SESSION['config'][1]['def_template']))
	throw new GException(GException::$VAR_TYPE);
      $template=$_SESSION['config'][1]['def_template'];
    }

    $this->generate_system_menu();

    if($this->content === null) {
      if(isset($_SESSION['content'])) {
	$this->content = $_SESSION['content'];
	unset($_SESSION['content']);
      } else if(isset($_SESSION['prev_output']))
	$this->content = $_SESSION['prev_output'];
      else if(isset($_SESSION['config'][1]['def_output']))
	$this->content = $_SESSION['config'][1]['def_output'];
      else
	$this->generate_default_output();

       $_SESSION['prev_output'] = $this->content;
    }

    // We generate the output from a template, which it's filter to change
    // the system variables includes in the template by it's values.
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $filter->register_var('out',$this->content);
    if(!$_SESSION['user']->isAuthenticated)
      $filter->register_var('sys_menu','');
    else
      $filter->register_var('sys_menu',$_SESSION['sys_menu']);

    if(isset($_SESSION['AssociationManagement']))
      $filter->register_var('assoc',$_SESSION['AssociationManagement']->get_assoc_selection());
    else
      $filter->register_var('assoc','');

    // $this->output = readfile("php://filter/read=default/resource=".$_SESSION['template_base'].'/'.$template);
    $this->output = $filter->filter_file($template);
    echo $this->output;
  }

  // This method generate the default output
  public function generate_default_output() {
    $out_content = new Content(); // TODO: Check if it's neccesary.
    $out_content->load_content();
    $this->content = $out_content->content;
  }

  // This method generate the system menu
  public function generate_system_menu() {
      $out_menu = new Menu();
      $out_menu->load_menu($_SESSION['system_menu']);
      $out_menu->generate_html_output();
      $_SESSION['sys_menu'] = $out_menu->out;
  }

  // This function checks if the parameter passed is of the class File
  public static function is_output($var){
    if(is_object($var) && (get_class($var) == "Output" || is_subclass_of($var, "Output")))
      return true;
    return false;
  }
}
?>
