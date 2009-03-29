<?php
/**
 * dni.php
 *
 * Description
 * This class represents the spanish national document of identification.
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

class DNI{
  private $dni = '';

  // Constructor of the class
  public function __construct($newDNI=null) {
    if($newDNI != null && is_string($newDNI)) {
      if(!$this->checkDNI($newDNI))
	throw new GException(GException::$VAR_TYPE,$newDNI);
      $this->dni = $newDNI;
    }
  }

  // This method gets the value of the internal variables
  public function __get($var) {
    switch($var){
    case "dni":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method sets the value of the internal variables
  public function __set($var, $value) {
    switch($var){
    case "dni":
      if($value != null && is_string($value) && $this->checkDNI($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method checks if a DNI is valid.
  private function checkDNI($check) {
    if($check != null && is_string($check)) {
      if(strlen($check) === 10 && 
	 strtoupper($check[8]) === '-') {
	$check[8] = $check[9];
	$check = substr($check,0,9);
      }
      
      if(strlen($check) === 9) {
	$number = intval(substr($check,0,8));
	$str = "TRWAGMYFPDXBNJZSQVHLCKET";
	$pos = $number % 23;
	if(strtoupper($check[8]) === $str[$pos])
	  return true;
      }
    }
    
    return false;
  }

  // This method checks if the parameter passed is an object of the type DNI
  public static function is_dni($var) {
    if(is_object($var) && (get_class($var) == "DNI" || is_subclass_of($var, "DNI")))
      return true;
    return false;
  }
}
?>