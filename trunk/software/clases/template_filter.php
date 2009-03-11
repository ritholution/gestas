<?php
/**
 * template_filter.php
 *
 * Description
 * This class loads html templates and substitutes the system variables
 * and configurations by its values.
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

class TemplateFilter extends php_user_filter{
  private $mode=0;
  private $varsReg=null;

  // Method reimplemented to filter the html template
  public function filter($input, $output, &$used, $closed) {
    while ($packet = stream_bucket_make_writeable($input)) {
      if($this->mode === 0)
	$packet = $this->default_filter($packet);
      
      $used += $packet->datalen;
      stream_bucket_append($output, $packet);
    }

    return PSFS_PASS_ON;
  }

  // This method acts as constructor of the filter, because it's execute when the filter is created.
  function onCreate() {
    // We load the configs.
    if(!isset($_SESSION['config']))
      throw new GException(GException::$SYS_NOT_CONFIG);

    switch($this->filtername) {
    default:
      $this->mode = 0;
      break;
    }

    return true;
  }

  // This method replace the config variables of the template with his value.
  private function load_config($packet) {
    $config = $_SESSION['config'];

    while(($str1 = strstr($packet->data,'$config[')) !== false) {
      if(($pos = strpos($str1,']')) !== false) {
	if(($pos2 = strpos($str1,'[',$pos)) !== false) {
	  if(($pos3 = strpos($str1,']',$pos2)) !== false) {
	    $string = substr($str1,0,$pos3+1);
	    $val1 = intval(substr($str1,8,$pos-8));
	    $val2 = substr($str1,$pos2+1,$pos3-$pos2-1);
	    if($val2[0] === "'")
	      $val2 = substr($val2,1);
	    if($val2[strlen($val2)-1] === "'")
	      $val2 = substr($val2,0,strlen($val2)-1);
	    $packet->data = str_replace($string,$config[$val1][$val2],$packet->data);
	  }
	}
      }
    }

    return $packet;
  }

  // This method apply the internationalisation with gettext
  private function load_gettext($packet) {
    while(($str1 = strstr($packet->data,'gettext("')) !== false) {
      if(($pos = strpos($str1,'")')) !== false) {
	$string = substr($str1,0,$pos+2);
	$packet->data = str_replace($string,gettext($string),$packet->data);
      }
    }

    return $packet;
  }

  // This method apply the default filter
  private function default_filter($packet) {
    $packet = $this->load_config($packet);
    $packet = $this->load_gettext($packet);

    $this->varsReg = $_SESSION['varsReg'];

    if($this->varsReg != null && is_array($this->varsReg)) {
      foreach($this->varsReg as $key => $value) {
	if(($str1 = strstr($packet->data,"$".$key)) !== false) {
	  $packet->data = str_replace("$".$key,$value,$packet->data);
	  unset($this->varsReg[$key]);
	  $_SESSION['varsReg'] = $this->varsReg;
	}
      }
    }

    return $packet;
  }

  // This method register a new filter
  public function register_filter($filterName) {
    if($filterName == null || !is_string($filterName))
      throw new GException(GException::$VAR_TYPE);

    $filters = stream_get_filters();

    foreach($filters as $value)
      if($value === $filterName)
	return true;

    if(stream_filter_register($filterName, "TemplateFilter") === false)
      throw new GException(GException::$FILTER_REGISTER);

    return true;
  }

  // This method filter a file and return the filtered content
  public function filter_file($filename,$filterName='default') {
    if($filterName == null || !is_string($filterName))
      throw new GException(GException::$VAR_TYPE);

    if($filename == null || !is_string($filename))
      throw new GException(GException::$VAR_TYPE);

    if(!isset($_SESSION['template_base']))
      $_SESSION['template_base'] = getcwd().'/templates';

    $this->register_filter($filterName);
    $output = file_get_contents("php://filter/read=".$filterName."/resource=".$_SESSION['template_base'].'/'.$filename);
    return $output;
  }

  // This method check if a variable is registered in the filter
  public function var_registered($var) { 
    if($var == null || !is_string($var))
      throw new GException(GException::$VAR_TYPE);

    if($this->varsReg != null && is_array($this->varsReg))
      return isset($this->varsReg[$key]);

    return false;
  }

  // This method register a variable in the filter
  public function register_var($var,$value) {
    if($var == null || !is_string($var))
      throw new GException(GException::$VAR_TYPE);

    if($value === null)
      $value = '';

    if(($this->varsReg == null || !is_array($this->varsReg)) && isset($_SESSION['varsReg']))
      $this->varsReg = $_SESSION['varsReg'];

    if(!$this->var_registered($var))
      $this->varsReg[$var] = $value;

    $_SESSION['varsReg'] = $this->varsReg;
  }

  // This method unregister a variable in the filter
  public function unregister_var($var) {
    if($var == null || !is_string($var))
      throw new GException(GException::$VAR_TYPE);

    if(($this->varsReg == null || !is_array($this->varsReg)) && isset($_SESSION['varsReg']))
      $this->varsReg = $_SESSION['varsReg'];

    if($this->varsReg != null && is_array($this->varsReg))
      foreach($this->varsReg as $key => $value)
	if($key === $var)
	  $this->varsReg[$key] = null;

    $_SESSION['varsReg'] = $this->varsReg;
  }

  // This method checks if the object passed as parameter is an instance of the TemplateFilter class.
  public function is_template_filter($param) {
    if(is_object($param) && (get_class($param) == "TemplateFilter" || is_subclass_of($param, "TemplateFilter")))
      return true;
    return false;
  }
}
?>