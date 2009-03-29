<?php
/**
 * association.php
 *
 * Description
 * This class represents an association.
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
require_once("member.php");
require_once("module_management.php");
require_once("web.php");
require_once("database.php");

class Association{
  protected $idAssociation=-1; // Id of the association for the database
  private $name=null; // Name of the association
  private $nif=null; // Fiscal identification of the association
  private $fundationYear=0; // Year of fundation
  private $webs=null; // Website
  private $headquarters=null; // Headquarters of the association

  // Constructor of the class
  function __construct($newName=null, $newNIF=null, $newFundationYear=0,
			 $newHeadquarters=null, $newWebs=null){
    if(Association::is_association($newName)) {
      $this->idAssociation = $newName->idAssociation;
      $this->name = $newName->name;
      $this->nif = $newName->nif;
      $this->fundationYear = $newName->fundationYear;
      $this->webs = $newName->webs;
      $this->headquarters = $newName->headquarters;
    } else if(is_string($newName))
      $this->name = $newName;

    if($newNIF !== null && is_string($newNIF))
      $this->nif = $newNIF;

    if(is_int($newFundationYear) && $newFundationYear > 1500)
      $this->fundationYear = $newFundationYear;

    if($newWeb !== null && Web::is_web($newWeb))
      $this->webs[]=$newWeb;
    else if($newWeb != null && is_string($newWeb))
      $this->webs[]=new Web($newWeb);

    if($newHeadquarters != null && is_string($newHeadquarters))
      $this->headquarters = $newHeadquarters;
  }

  // Method to obtain the value of the internal variables
  public function __get($var){
    switch ($var) {
    case "idAssociation":
    case "name":
    case "nif":
    case "fundationYear":
    case "webs":
    case "headquarters":
      return $this->$var;
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Method to set the value of the internal variables
  public function __set($var, $value){
    // chequear el tipo de variables.
    switch($var){
    case "idAssociation":
      // This variable cannot be set from outside
      throw new GException(GException::$VAR_ACCESS);
    case "name":
      // This variable must be a string
      if($value === null || is_string($value))
	$this->name = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "nif":
      // This variable must be a string
      if($value === null || is_string($value))
	$this->nif = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "fundationYear":
      // This variable must be an integer greater than 1500
      if(is_int($value) && $value > 1500)
	$this->fundationYear = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "webs":
      // This variable must be a Web object or a string
      if($value === null || Web::is_web($value))
	$this->webs = array($value);
      else if(is_string($value))
	$this->webs = array(new Web($value));
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "headquarters":
      // This variable must be a string
      if($value === null || is_string($value))
	$this->headquarters = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
   default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method check if the association exist in the database
  public function exists_association($newIdAssoc=-1, $db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->idAssociation;
    if(is_int($newIdAssoc) && $newIdAssoc > -1)
      $check = $newIdAssoc;

    return Association::exists($check,$db);
  }

  // This method check if the association exist in the database
  public static function exists($newIdAssoc=-1, $db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdAssoc))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select * from association where idAssociation=".$newIdAssoc);

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method inserts the association in the database
  public function insert_db($db=null){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if ($this->exists_association($this->idAssociation,$db))
      $this->update_db($db);
    else {
      $db->connect();
      $db->execute("insert into association(nif,assocName,fundationYear,headquarters) values('".
		   $this->nif."','".$this->name."',".$this->fundationYear.",'".
		   $this->headquarters."')");
      $this->idAssociation = $db->id;
      
    //  if($this->webs !== null && is_array($this->webs))
	//foreach($this->webs as $value)
	 // if(!$value->exists_association())
	 //   $value->insert_web();
    }
  }
 //This method checks if the user with id id_user is admin of the association
public function is_admin($id_user,$db=null){
	if( ($id_user===null) || !(is_int($id_user)) || ($id_user==0))
	throw new GException(GException::$VAR_TYPE);
      if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
	  }
	  $db->connect();
	  $db->consult("SELECT * FROM userUserType WHERE idUser='".$id_user."' AND idType='4' AND idAssociation='".$this->idAssociation."'");
	  return $db->numRows()>0;
} 
 //This method sets the user with id id_user as admin of the association
public function set_admin($id_user,$db=null){
    
	if( ($id_user===null) || !(is_int($id_user)) || ($id_user==0))
	throw new GException(GException::$VAR_TYPE);
      if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
	  }
	  if(!$this->is_admin($id_user,$db)){
	  $db->connect();
	  $db->execute("INSERT into userUserType(idUser,idType,idAssociation) VALUES ('".$id_user."','4','".$this->idAssociation."')");
	  }
    }
  // This method updates the association in the database
  public function update_db($db=null){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_association($this->idAssociation,$db)) {
      $db->connect();
      $db->consult("update association set nif='".$this->nif."', assocName='".
		   $this->name."', fundationYear=".$this->fundationYear.
		   ", headquarters='".$this->headquarters."' WHERE idAssociation=".
		   $this->idAssociation);
    }else throw new GException(GException::$VAR_TYPE);
  }

  // This method deletes the association from the database
  public function delete_db($db){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_association($this->idAssociation,$db)) {
      $db->connect();
      $db->execute("delete from asociation where idAssociation=".$this->idAssociation);

      foreach($this->webs as $value) {
	$value->idAssociation = -1;
	$value->update_web($db);
      }
    }
  }

  // This method loads an association from the database
  public function load_association($newIdAssoc=-1, $db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->idAssociation;
    if(is_int($newIdAssoc) && $newIdAssoc > 0)
      $load = $newIdAssoc;
    if($this->exists_association($load)) {
      $db->connect();
      $db->consult("select * from association where idAssociation=".$load);
      $row = $db->getRow();
      $this->idAssociation = intval($row['idAssociation']);
      $this->name = $row['assocName'];
      $this->nif = $row['nif'];
      $this->fundationYear = intval($row['fundationYear']);
      $this->headquarters = $row['headquarters'];
      $this->webs = Web::load_webs_assoc($this->idAssociation);
    }
  }


  // This method check if the parameter passed is an object of the type Association.
  public static function is_association($assoc){
    if(is_object($assoc) && (get_class($assoc) == "Association" || is_subclass_of($assoc, "Association")))
      return true;
    return false;
  }
}
?>
