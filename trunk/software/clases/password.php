<?php
/**
 * password.php
 *
 * Description
 * This class implements a Password for both the Database and the User
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

class Password{
  private $pass = null;
  private $codType = 1;
  private $isCodificated = false;

  static $MD5 = 1;
  static $DIGEST = 2;

  // Constructor of the class
  public function __construct($newPass=null, $newCodType=1, $isCod=false) {
    if($newPass != null && is_string($newPass))
      $this->pass = $newPass;

    switch($newCodType) {
    case Password::$MD5:
    case Password::$DIGEST:
      $this->codType = $newCodType;
      break;
    }

    if($isCod !== null && is_bool($isCod))
      $this->isCodificated = $isCod;
  }

  // This method get the value of the internal variables.
  public function __get($var) {
    switch($var) {
    case "pass":
    case "codType":
    case "isCodificated":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the internal variables.
  public function __set($var, $value) {
    switch($var){
    case "pass":
      if(is_string($value) || $value == null)
	$this->$var = $value;
      else if(Password::is_password($value))
	$this->$var = $value->$var;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "tipoCodificacion":
      switch($value){
      case Password::$MD5:
      case Password::$DIGEST:
	$this->$var = $value;
	break;
      default:
	throw new GException(GException::$ENC_UNKNOWN);
      }
      break;
    case "isCodificated":
      if($value !== null && is_bool($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method return the password codificate by its method
  public function codificate($user=null,$password=null){
    switch($this->codType) {
    case PASSWORD::$MD5:
      return $this->enc_MD5($password);
      break;
    case PASSWORD::$DIGEST:
      return $this->enc_digestMD5($user,$password);
      break;
    default:
      throw new GException(GException::$ENC_UNKNOWN);
      break;
    }
  }

  // This method encode the password with MD5
  private function enc_MD5($password=null){
    if($password !== null && is_string($password))
      return MD5($password);
    else if($this->pass == null || !is_string($this->pass))
      return MD5('');
    else
      return MD5($this->pass);
  }

  // This method encode the password with DIGEST-MD5
  private function enc_digestMD5($user=null,$password=null){
    if($user == null || !is_string($user)){
      throw new GException(GException::$VAR_TYPE);
      return false;
    }

    if($password !== null && is_string($password))
      return MD5($user.'::'.$password);
    else if($this->pass == null || !is_string($this->pass))
      return MD5($user.'::');
    else
      return MD5($user.'::'.$this->pass);
  }

  // This method compares two passwords
  public function compare($passwd=null, $user=null) {
    if((is_string($passwd) && $this->pass === $passwd) ||
       (is_string($passwd) && $this->pass === $this->codificate($user,$passwd)) ||
       (Password::is_password($passwd) && $this->codType === $passwd->codType && 
	$passwd->compare($this->pass)))
      return true;
    return false;
  }

  // This static method checks if the parameter passed is an object of the type Password
  public static function is_password($pass) {
    if(is_object($pass) && (get_class($pass) === "Password" || is_subclass_of($pass, "Password")))
      return true;
    return false;
  }
}
?>