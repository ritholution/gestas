<?php
/**
 * contact.php
 *
 * Description
 * This class represents a contact of the association.
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

class Contact{
  private $idContact=-1;
  private $contactName=null;
  private $firstSurname=null;
  private $lastSurname=null;
  private $address=null;

  // Class constructor
  public function __construct($newContactName=null, $newFirstSurname=null,
			      $newLastSurname=null, $newAddress=null) {
    if($newContactName != null && is_string($newContactName))
      $this->contactName = $newContactName;

    if($newFirstSurname != null && is_string($newFirstSurname))
      $this->firstSurname = $newFirstSurname;

    if($newLastSurname != null && is_string($newLastSurname))
      $this->lastSurname = $newLastSurname;

    if($newAddress != null && is_string($newAddress))
      $this->address = $newAddress;
  }

  // This method gets the value of the internal data.
  public function __get($var) {
    switch($var) {
    case "idContact":
    case "contactName":
    case "firstSurname":
    case "lastSurname":
    case "address":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This function sets the value of the internal variables
  public function __set($var, $value) {
    switch($var){
    case "idContact":
      throw new GException(GException::$VAR_ACCESS);
    case "contactName":
    case "firstSurname":
    case "lastSurname":
    case "address":
      if($value === null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method insert a contact in the database.
  public function insert_db($db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_contact())
      $this->modify_db($db);
    else {
      $db->connect();
      $db->execute("insert into contact(contactName,firstSurname,lastSurname,address) values('".
		   $this->contactName."', '".$this->firstSurname."', '".$this->lastSurname."', '".
		   $this->address."')");
      $this->idContact = $db->id;
    }
  }

  // This method modify a contact of the database.
  public function modify_db($db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_contact()) {
      $db->connect();
      $db->execute("update contact set contactName='".$this->contactName."', firstSurname='".
		   $this->firstSurname."', lastSurname='".$this->lastSurname."', address='".
		   $this->address."' where idContact=".$this->idContact);
    }
  }

  // This method drop contact from the database.
  public function drop_db($db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_contact()) {
      $db->connect();
      $db->execute("delete from contact where idContact=".$this->idContact);
    }
  }

  // This method check if a contact exists in the database
  public function exists_contact($newIdContact=-1, $db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->idContact;
    if(is_int($newIdContact) && $newIdContact > 0)
      $check = $newIdContact;

    return Contact::exists($check, $db);
  }

  // This method check if a contact exists in the database
  public static function exists($newIdContact=-1, $db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdContact))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select * from contact where idContact=".$newIdContact);

    if($db->numRows > 1)
      throw new GException(GException::$DATABASE_INTEGRITY);

    return ($db->numRows === 1);
  }

  // This method load a contact from the database
  public function load_contact($newIdContact=-1, $db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->idContact;
    if($newIdContact > 0)
      $load = $newIdContact;

    if($this->exists_contact($load)) {
      $db->connect();
      $db->consult("select * from contact where idContact=".$load);
      $newContact = $db->getRow();
      $this->idContact = intval($newContact['idContact']);
      $this->contactName = $newContact['contactName'];
      $this->firstSurname = $newContact['firstSurname'];
      $this->lastSurname = $newContact['lastSurname'];
      $this->address = $newContact['address'];
    }
  }

  // Checks that the parameter is an object of the type Contact
  public static function is_contact($var) {
    if(is_object($var) && (get_class($var) == "Contact" || is_subclass_of($var, "Contact")))
      return true;
    return false;
  }
}
?>