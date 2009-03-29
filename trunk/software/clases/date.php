<?php
/**
 * date.php
 *
 * Description
 * This class represent a Date.
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

class Date {
  // Note that this will be a friendly integer.
  private $date = null;

  // Class constructor
  public function __construct($newDate=null) {
    if(is_integer($newDate))
      $this->date = $newDate;
    else
      $this->date = time();
  }

  // This function gets the value of the internal variables
  public function __get($var) {
    switch($var) {
    case "date":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This function sets the value of the internal variables
  public function __set($var, $value) {
    switch($var) {
    case "date":
      if(is_integer($value))
	$this->$var = $value;
      else if(Date::is_date($value))
	$this->$var = $value->$var;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method returns an array with the date separated in different fields
  public function date_array() {
    return getdate($this->date);
  }

  // This static method check if the parameter passed is an object of the type Date
  public static function is_date($var) {
    if(is_object($var) && (get_class($var) == "Date" || is_subclass_of($var, "Date")))
      return true;
    return false;
  }
}
?>