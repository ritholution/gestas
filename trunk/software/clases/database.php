<?php
/**
 * database.php
 *
 * Description
 * This class provide access to a database. This class is abstract, so
 * the implementations of each type of database must inherit from this
 * class and implement the abstract methods.
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

require_once("password.php");
require_once("gexception.php");

abstract class Database{
  // Database connection data
  private $host = null;
  private $port = -1;
  private $user = null;
  protected $pass = null;
  private $dbname = null;
  private $type = 1;
  private $id = 0;

  protected $isConnected = false; // Variable to indicate if we are connected to the database.
  protected $result = null; // Variable to store the result of a select execution.
  protected $rows = null; // Array to navigate along the rows returned by a select execution.
  protected $iter = 0; // Iterator of the previous array
  protected $conn = null; // Connection to the database.

  // Construct of the class for initialize the variables of the object.
  public function __construct($newHost=null, $newPort=-1, $newUser=null,
			      $newPass=null, $newDbName=null, $newType=1){
    if($newHost != null && is_string($newHost))
      $this->host = $newHost;

    if(is_int($newPort) && $newPort > 0)
      $this->port = $newPort;

    if($newUser != null && is_string($newUser))
      $this->user = $newUser;

    if(Password::is_password($newPass))
      $this->pass = new Password($newPass->pass);
    else if($newPass != null && is_string($newPass))
      $this->pass = new Password($newPass);

    if($newDbName != null && is_string($newDbName))
      $this->dbname = $newDbName;

    if(is_int($newType) && $newType > 0)
      $this->type = $newType;

    $this->isConnected = false;
    $this->iter = 0;
  }

  // Method to get the value of the internal variables.
  public function __get($var){
    switch($var){
    case "result":
    case "iter":
    case "pass":
      // This variable are only accessible by this class and her heiress
      throw new GException(GException::$VAR_ACCESS);
    case "host":
    case "port":
    case "user":
    case "dbname":
    case "type":
    case "isConnected":
    case "rows":
    case "conn":
      return $this->$var;
    case "id":
      return $this->getId();
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Method to check & set the values to the internal variables.
  public function __set($var, $valor){
    switch($var){
    case "host":
      // The variable must be string.
      if(is_string($valor) || $valor == null)
	$this->host=$valor;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "port":
      // The variable must be a positive integer
      if(is_int($valor) && ($valor > 0 || $valor == -1))
	$this->port=$valor;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "user":
      // The variable must be a string
      if(is_string($valor) || $valor == null)
	$this->user=$valor;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "pass":
      // The variable must be a Password or a string
      if(Password::is_password($valor) || $valor == null)
	$this->pass=$valor;
      else if(is_string($valor))
	$this->pass=new Password($valor);
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "dbname":
      // The variable must be a string
      if(is_string($valor) || $valor == null)
	$this->dbname=$valor;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "type":
      // The variable must be a positive integer.
      if(is_int($valor) && ($valor > 0 || $valor == -1))
	$this->type=$valor;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "conn":
      // The connection must be active
      if($this->check_connection($valor) || $valor == null)
	$this->conn=$valor;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "result":
      // This variable is not accesible from outside.
      throw new GException(GException::$VAR_TYPE);
    case "isConnected":
      // This variable is not accesible from outside.
      throw new GException(GException::$VAR_ACCESS);
    case "rows":
      // This variable is not accesible from outside.
      throw new GException(GException::$VAR_ACCESS);
    case "iter":
      // This variable is not accesible from outside.
      throw new GException(GException::$VAR_ACCESS);
    case "id":
      // This variable is not accesible from outside.
      throw new GException(GException::$VAR_ACCESS);
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // The iterator goes to the first row of the array rows
  public function firstRow(){
    $this->iter = 0;
  }

  // The iterator goes to the last row of the array rows
  public function lastRow(){
    $this->iter = count($this->rows)-1;
  }

  // The iterator goes to the previous row of the array rows
  public function prevRow(){
    if($this->iter > 0){
      $this->iter--;
      return true;
    }

    return false;
  }

  // The iterator goes to the next row of the array rows
  public function nextRow(){
    if($this->iter < $this->numRows()){
      $this->iter++;
      return true;
    }

    return false;
  }

  // This static method check if the parameter passed to it is an object of the type database.
  public static function is_database($db) {
    if(is_object($db) && (get_class($db) == "Database" || is_subclass_of($db, "Database")))
      return true;
    return false;
  }

  // This method is the destructor of the class.
  public function __destruct(){
    if($this->isConnected)
      $this->disconnect();
  }

  abstract public function connect(); // Abstract method to connect to the database
  abstract public function disconnect(); // Abstract method to disconnect to the database
  abstract public function consult($consulta=null); // Abstract method to execute a consult to the database which generate some returned data
  abstract public function execute($consulta=null); // Abstract method to execute a consult to the database which not generate any returned data
  abstract public function numRows(); // Abstract method to obtain the number of rows returned by the last consult.
  abstract public function getRow(); // Abstract method to obtain the row indicated by the iterator.
  abstract protected function getId();
}
?>