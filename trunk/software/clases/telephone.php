<?php
/**
 * telephone.php
 *
 * Description
 * This class represents a telephone number (movil, fax or land phone).
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
require_once("contact.php");
require_once("member.php");

class Telephone{
  private $number = 900000000;
  private $type = 1;
  private $idContact = -1;
  private $idMember = -1;
  private $idPetition = -1;
  private $description = '';

  static $LAND = 1;
  static $CELLULAR = 2;
  static $FAX = 3;

  // Constructor of the class
  public function __construct($newNumber=-1, $newType=-1, $newDescription=null,
			      $newIdContact=-1, $newIdMember=-1, $newIdPetition=-1) {
    if($newType !== null && is_int($newType) && $newType > 0 && $newType < 4)
	$this->type = $newType;

    if($newNumber !== null && is_int($newNumber) && $newNumber !== -1) {
      if(!$this->check_number($newNumber,$this->type))
	$this->type = $this->get_type($newNumber);
      $this->number = $newNumber;
    }

    if(is_int($newIdContact) && $newIdContact > -1 && Contact::exists($newIdContact))
      $this->idContact = $newIdContact;

    if(is_int($newIdMember) && $newIdMember > -1 && Member::exists($newIdMember))
      $this->idMember = $newIdMember;

    if(class_exists("Request") && is_int($newIdPetition) && $newIdPetition > -1 && Request::exists($newIdPetition))
      $this->idPetition = $newIdPetition;

    if($newDescription !== null && is_string($newDescription))
      $this->description = $newDescription;
  }

  // This method gets the value of the internal variables
  public function __get($var) {
    switch($var){
    case "number":
    case "type":
    case "idContact":
    case "idMember":
    case "idPetition":
    case "description":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method sets the value of the internal variables
  public function __set($var, $value) {
    switch($var){
    case "number":
      if($this->check_number($value,$this->type))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "type":
      if(is_integer($value) && $value > 0 && $value < 4)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idContact":
      if(is_int($value) && (($value > -1 && Contact::exists($value)) || $value === -1))
	  $this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idMember":
      if(is_int($value) && (($value > -1 && Member::exists($value)) || $value === -1))
	  $this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idPetition":
      if(class_exists("Request") && is_int($value) && $value > -1 && Request::exists($value))
	$this->$var = $value;
      else if(is_int($value) && $value === -1)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "description":
      if($value == null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method checks if the phone is a land phone
  public function is_land(){
    return ($this->type === Telephone::$LAND);
  }

  // This method checks if the phone is a cellular phone  
  public function is_cellular(){
    return ($this->type === Telephone::$CELLULAR);
  }

  // This method checks if the phone is a fax phone
  public function is_fax(){
    return ($this->type === Telephone::$FAX);
  }

  // This method insert a telephone in the database
  public function insert_db($telTable,$db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $id = $this->getId($telTable);
    $table = $this->getTable($telTable);

    if($this->exists_telephone($telTable,$this->number,$this->type,$id,$db))
      $this->modify_db($telTable,$db);
    else {
      $consult = "insert into ".$table." values(".$this->number.",".$this->type.",'".
	$this->description."',".$id.")";
      $db->connect();
      $db->execute($consult);
    }
  }

  // This method modify a telephone of the database
  public function modify_db($telTable,$db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $id = $this->getId($telTable);
    $table = $this->getTable($telTable);

    if($this->exists_telephone($telTable,$this->number,$this->type,$id,$db)) {
      if($id !== -1) {
	$db->connect();
	$db->execute("update ".$table." set phoneType=".$this->type.", id=".$id
		     .", description='".$this->description."' where phoneNumber=".
		     $this->number);
      } else
	$this->drop_db($telTable,$db);
    }
  }

  // This method drops a telephone from the database
  public function drop_db($telTable,$db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $id = $this->getId($telTable);
    $table = $this->getTable($telTable);

    if($this->exists_telephone($telTable,$this->number,$this->type,$id,$db)) {
      $db->connect();
      $db->execute("delete from ".$table." where phoneNumber=".$this->number." and id=".$id);
    }
  }

  // This method check if a telephone number exist in the database
  public function exists_telephone($telTable, $newTelephone=-1, $newTelephoneType=-1, $id=-1,
				   $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $checkType = $this->type;
    if($newTelephoneType > 0 && $newTelephoneType < 4)
      $checkType = $newTelephoneType;

    $check = $this->number;
    if($this->check_number($newTelephone,$checkType))
      $check = $newTelephone;
    else if($this->get_type($newTelephone) !== -1) {
      $check = $newTelephone;
      $checkType = $this->get_type($newTelephone);
    }

    $checkId = $this->getId($telTable);
    if($id !== null && is_int($id) && $id > 0)
      $checkId = $id;

    if(!$this->check_number($check,$checkType))
      throw new GException(GException::$VAR_TYPE);
    
    return Telephone::exists($telTable,$check,$checkType,$checkId,$db);
  }

  // This method loads a telephone from the database
  public function load_telephone($telTable, $newTelephone=-1, $newTelephoneType=-1,
				 $id=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newTelephoneType) || $newTelephoneType < 1 || $newTelephoneType > 3)
      $newTelephoneType = $this->type;
    if(!$this->check_number($newTelephone,$newTelephoneType))
      $newTelephoneType = $this->get_type($newTelephone);

    $table = $this->getTable($telTable);
    $newId = $this->getId($telTable);
    if($id > 0)
      $newId = $id;

    if($this->exists_telephone($telTable, $newTelephone, $newTelephoneType, $newId, $db)) {
      $consult = "select * from ".$table." where phoneNumber=".$newTelephone;
      if($newTelephoneType > 0 && $newTelephoneType < 4)
	$consult .= " and phoneType=".$newTelephoneType;
      if(is_int($newId) && $newId > 0)
	$consult .= " and id=".$newId;

      $db->connect();
      $db->consult($consult);

      $newTel = $db->getRow();
      $this->number = intval($newTel['phoneNumber']);
      $this->type = intval($newTel['phoneType']);
      $this->description = $newTel['description'];

      switch($telTable) {
      case "member":
	$this->idMember = intval($newTel['id']);
	$this->idContact = -1;
	$this->idPetition = -1;
	break;
      case "contact":
	$this->idContact = intval($newTel['id']);
	$this->idMember = -1;
	$this->idPetition = -1;
	break;
      case "request":
	$this->idPetition = intval($newTel['id']);
	$this->idMember = -1;
	$this->idContact = -1;
	break;
      default:
	throw new GException();
      }
    }
  }

  // This method check if a telephone number exist in the database
  public static function exists($telTable, $newTelephone=-1, $newTelephoneType=-1, $id=-1,
				$db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newTelephoneType) || $newTelephoneType < 1 || $newTelephoneType > 3)
      throw new GException(GException::$VAR_TYPE);

    if(!($newTelephoneType === Telephone::$CELLULAR && ($newTelephone > 599999999 && $newTelephone < 700000000)) &&
	!(($newTelephoneType === Telephone::$LAND || $newTelephoneType === Telephone::$FAX) &&
	($newTelephone > 899999999 && $newTelephone < 1000000000)))
      throw new GException(GException::$VAR_TYPE);

    if($id === null || !is_int($id))
      throw new GException(GException::$VAR_TYPE);

    $consult = " where phoneNumber=".$newTelephone." and phoneType=".$newTelephoneType.
      " and id=".$id;

    $db->connect();
    switch($telTable) {
    case "member":
      $db->consult("select * from telephoneMember".$consult);
      break;
    case "contact":
      $db->consult("select * from telephoneContact".$consult);
      break;
    case "request": 
      $db->consult("select * from telephoneRequest".$consult);
      break;
    default:
      throw new GException();
    }
    
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method loads all the telephones related with a Member
  public static function load_telephones($telTable, $newId=-1, $db=null) {
    // Check the db
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // Check if the member exists
    if(!is_int($newId) || $newId === -1)
      throw new GException(GException::$VAR_TYPE);

    $existsId = false;

    // Get the table name
    switch($telTable) {
    case 'member':
      $table = "telephoneMember";
      $existsId = Member::exists($newId);
      break;
    case 'contact':
      $table = "telephoneContact";
      $existsId = Contact::exists($newId);
      break;
    case 'request':
      $table = "telephoneRequest";
      if(class_exists("Request"))
	$existsId = Request::exists($newId);
      break;
    default:
      throw new GException();
    }

    // Obtain the telephones
    $result = null;
    if($existsId) {
      $db->connect();
      $db->consult("select phoneNumber from ".$table." where id=".$newId);
      $rows = $db->rows;
      if(count($rows) > 0)
	foreach($rows as $row) {
	  $newTel = new Telephone();
	  $newTel->load_telephone($telTable,intval($row['phoneNumber']),-1,$newId,$db);
	  $result[] = $newTel;
	}
    }

    return $result;
  }

  // This method loads all the telephones related with a Member
  // TODO: This method might be in the telephone manager.
  public static function load_telephones_member($newIdMember=-1, $db=null) {
    // Check the db
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // Check if the member exists
    if(!is_int($newIdMember) || $newIdMember === -1 || !Member::exists($newIdMember))
      throw new GException(GException::$VAR_TYPE);

    // Obtain the telephones
    $db->connect();
    $db->consult("select phoneNumber from telephoneMember where id=".$newIdMember);
    $rows = $db->rows;
    $loadTel = null;
    if(count($rows) > 0)
      foreach($rows as $row) {
	$newTel = new Telephone();
	$newTel->load_telephone("member",intval($row['phoneNumber']),-1,$newIdMember,$db);
	$loadTel[] = $newTel;
      }

    return $loadTel;
  }

  // This method gets the type of telephone number base on the number passed as parameter
  private function get_type($check) {
    if($check === null || !is_int($check))
      throw new GException(GException::$VAR_TYPE);

    if($check > 599999999 && $check < 700000000)
      return Telephone::$CELLULAR;
    else if($check > 899999999 && $check < 1000000000)
      return Telephone::$LAND;    

    return -1;
  }

  // This method checks if a telephone number is valid.
  private function check_number($check, $checkType=1) {
    if($check === null || !is_integer($check))
      throw new GException(GException::$VAR_TYPE);

    if(($checkType === Telephone::$CELLULAR && ($check > 599999999 && $cgeck < 700000000) || 
	($checkType === Telephone::$LAND || $checkType === Telephone::$FAX) && 
	($check > 899999999 && $check < 1000000000)))
      return true;
    
    return false;
  }

  // This method return the id of the relation
  private function getId($telTable) {
    switch($telTable) {
    case "member":
      return $this->idMember;
    case "contact":
      return $this->idContact;
    case "request":
      return $this->idPetition;
    default:
      throw new GException();
    }
  }

  // This method return the table name of the relation
  private function getTable($telTable) {
    switch($telTable) {
    case "member":
      return "telephoneMember";
    case "contact":
      return "telephoneContact";
    case "request":
      return "telephoneRequest";
    default:
      throw new GException();
    }
  }

  // This method return the table name of the relation
  private function checkExistsId($telTable,$id) {
    switch($telTable) {
    case "member":
      return Member::exists($id);
    case "contact":
      return Contact::exists($id);
    case "request":
      if(class_exists("Request"))
	return Request::exists($id);
      return false;
    default:
      throw new GException();
    }
  }

  // This method checks if the parameter passed is an object of the type Telephone
  public static function is_telephone($phone) {
    if(is_object($phone) && (get_class($phone) == "Telephone" || is_subclass_of($phone, "Telephone")))
      return true;
    return false;
  }
}
?>
