<?php
/**
 * typo_user.php
 *
 * Description
 * This class represent a type of user of the application.
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

class TypeUser{
  protected $idType=1;
  private $idAssociation=-1;
  private $typeUser=null;

  // Constructor of the class
  function __construct($newTypeUser=null, $newIdAssociation=-1){
    if($newTypeUser !== null && is_string($newTypeUser))
      $this->typeUser=$newTypeUser;

    if($newIdAssociation > -1 && Association::exists($newIdAssociation))
	$this->idAssociation = $newIdAssociation;
  }

  // Method to get the internal variables from outside
  public function __get($var){
    switch($var){
    case "idType":
    case "idAssociation":
    case "typeUser":
      return $this->$var;
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Method to set the internal variables from outside checking the variable type.
  public function __set($var, $value){
    switch($var){
    case "idType":
      throw new GException(GException::$VAR_ACCESS);
    case "idAssociation":
      if(is_int($value)) {
	if(Association::exists($value))
	  $this->$var = $value;
	else
	  throw new GException(GException::$ASSOCIATION_UNKNOWN);
      } else
	throw new GException(GException::$VAR_TYPE);
    case "typeUser":
      if($value === null || is_string($value))
	$this->$var=$value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Checks if the type of user is valid in the database.
  public static function exists($newIdType=-1, $newIdAssoc=-1, $db=null){
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if(!is_int($newIdType) && !is_string($newIdType))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    if(is_int($newIdType)) {
      $consult = "select idType from userType where idType=".$newIdType;
      if(is_int($newIdAssoc) && $newIdAssoc > -1 && Association::exists($newIdAssoc))
	$consult .=" and idAssociation=".$newIdAssoc;
      $db->consult($consult);

      if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

      return ($db->numRows() === 1);
    } else {
      $consult = "select idType from userType where usrType='".$newIdType."'";
      if(is_int($newIdAssoc) && $newIdAssoc > -1 && Association::exists($newIdAssoc))
	$consult .= " and idAssociation=".$newIdAssoc;
      $db->consult($consult);

      return ($db->numRows() > 0);
    }
  }

  // Checks if the type of user is valid in the database.
  public function exists_type($newType=null, $newIdAssoc=-1,
			      $db=null){
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $check = null;
    if(is_int($newType)) {
      $check = $this->idType;
      if($newType >0)
	$check = $newType;
      return TypeUser::exists($check,-1,$db);
    } else if(is_string($newType)) {
      $check = $this->typeUser;
      if($newType != null)
	$check = $newType;
    }

    $checkAssoc = $this->idAssociation;
    if(is_int($newIdAssociation) && $newIdAssociation > -1 &&
       Association::exists($newIdAssociation))
      $checkAssoc = $newIdAssociation;

    if($check === null) {
      if($this->typeUser !== null && is_string($this->typeUser) &&
	 TypeUser::exists($this->typeUser,$checkAssoc,$db))
	return true;
      return TypeUser::exists($this->idType,$checkAssoc,$db);
    } else
      return TypeUser::exists($check,$checkAssoc,$db);
  }

  // This method loads a TypeUser from de database given the id of the TypeUser
  public function load_type($newIdType=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(is_int($newIdType) && $newIdType > -1)
      $this->idType = $newIdType;

    if($this->idType === null || !is_int($this->idType) || $this->idType < 0)
      throw new GException(GException::$VAR_TYPE);

    if($this->exists_type($this->idType,-1,$db)) {
      $db->connect();
      $db->consult("select * from userType where idType=".$this->idType);
      $row = $db->getRow();
      $this->typeUser = $row['usrType'];
      if($row['idAssociation'] != null)
	$this->idAssociation = intval($row['idAssociation']);
    }
  }

  // This method loads a TypeUser from de database given the name of the TypeUser
  public function load_type_name($newType=null, $assoc=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $ckeckAssoc = $this->idAssociation;
    if(is_int($assoc) && Association::exists($assoc))
      $ckeckAssoc = $assoc;

    if($newType != null && is_string($newType) &&
       $this->exists_type($newType,$checkAssoc,$db)) {
      $this->typeUser = $newType;
      $this->idAssociation = $checkAssoc;
    }

    if($this->typeUser == null || !is_string($this->typeUser) ||
       !$this->exists_type($this->typeUser,$this->idAssociation,$db))
      throw new GException(GException::$VAR_TYPE,$this->typeUser);

    if($this->exists_type($this->typeUser,$this->idAssociation,$db)) {
      $db->connect();
      $consult = "select * from userType where usrType='".$this->typeUser."'";
      if(is_int($assoc) && $assoc > -1 && Association::exists($assoc))
	$consult .= " and idAssociation=".$newIdAssoc;
      $db->consult($consult);
      $row = $db->getRow();
      $this->idType = intval($row['idType']);
      if($row['idAssociation'] != null)
	$this->idAssociation = intval($row['idAssociation']);
    }
  }

  // This method loads a TypeUser from de database given the id of the TypeUser
  public function load_type_assoc($newType=null, $newIdAssoc=-1,
				  $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newType != null && is_string($newType))
      $this->typeUser = $newType;

    if($this->typeUser == null || !is_string($this->typeUser))
      throw new GException(GException::$VAR_TYPE);

    if(is_int($newIdAssoc) && $newIdAssoc > -1 &&
       Association::exists($newIdAssoc))
      $this->idAssociation = $newIdAssoc;

    if($this->exists_type($this->idType,$this->idAssociation,$db)) {
      $consult = "select idType from userType where usrType='".$this->typeUser."'";
      if($this->idAssociation > -1 &&
	 Association::exists($this->idAssociation))
	$consult .= " and idAssociation=".$this->idAssociation;
      $db->connect();
      $db->consult($consult);
      $row = $db->getRow();
      $this->idType = $row['idType'];
    }
  }

  // This method insert a Type of user into the database
  public function insert_type($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_type($this->idType,$this->idAssociation,$db))
      $this->modify_type();
    else {
      $sentence = "insert into userType(usrType";
      if($this->idAssociation != null && is_int($this->idAssociation) &&
	 Association::exists($this->idAssociation))
	$sentence .= ",idAssociation";
      $sentence .= ") values('".$this->typeUser."'";
      if($this->idAssociation != null && is_int($this->idAssociation) &&
	 Association::exists($this->idAssociation))
	$sentence .= ",".$this->idAssociation;
      $sentence .= ")";

      $db->connect();
      $db->execute($sentence);
      $this->idType = $db->id;
    }
  }

  // This method update a Type of user in the database
  public function modify_type($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_type($this->idType,$this->idAssociation,$db)) {
      $sentence = "update userType set usrType='".$this->typeUser."'";
      if($this->idAssociation != null && is_int($this->idAssociation) &&
	 Association::exists($this->idAssociation))
	$sentence .= ", idAssociation=".$this->idAssociation;
      $sentence .= " where idType=".$this->idType;

      $db->connect();
      $db->execute($sentence);
    }
  }

  // This method drop a Type of user from the database
  public function drop_type($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_type($this->idType,$this->idAssociation,$db)) {
      $db->connect();
      $db->execute("delete from userType where idType=".$this->idType);
    }
  }

  // Method to check if the parameter passed is an object of the type TypeUser
  public static function is_type_user($type_user){
    if(is_object($type_user) && (get_class($type_user) === "TypeUser" || 
				 is_subclass_of($type_user, "TypeUser")))
      return true;
    return false;
  }
}
?>