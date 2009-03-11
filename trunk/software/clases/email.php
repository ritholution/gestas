<?php
/**
 * email.php
 *
 * Description
 * This class implements the Email address.
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
require_once("gdatabaseexception.php");
require_once("member.php");
require_once("contact.php");

class Email{
  private $mail=null;
  private $idMember=-1;
  private $idContact=-1;
  private $idPetition=-1;

  // Class constructor
  public function __construct($newMail=null, $newIdMember=-1, $newIdContact=-1,
			      $newIdPetition=-1) {
    if($newMail != null && is_string($newMail))
      $this->mail = $newMail;

    if(is_int($newIdMember) && $newIdMember > 0 && Member::exists($newIdMember))
      $this->idMember = $newIdMember;

    if(is_int($newIdContact) && $newIdContact > 0 && Contact::exists($newIdContact))
      $this->idContact = $newIdContact;

    if(class_exists($newIdPetition) && is_int($newIdPetition) && $newIdPetition > 0 &&
       Request::exists($newIdPetition))
      $this->idPetition = $newIdPetition;
  }

  // This method gets the value of the internal data.
  public function __get($var) {
    switch($var) {
    case "mail":
    case "idMember":
    case "idContact":
    case "idPetition":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This function sets the value of the internal variables
  public function __set($var, $value) {
    switch($var){
    case "mail":
      if($value !== null && is_string($value) && strpos($value,'@') && strpos($value,'.') && 
	 strpos($value,'@') < strrpos($value,'.'))
	$this->mail = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idMember":
      if(is_int($value) && (($value > 0 && Member::exists($value)) || $value === -1))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idContact":
      if(is_int($value) && (($value > 0 && Contact::exists($value)) || $value === -1))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idPetition":
      if(class_exists("Request") && is_int($value) && $value > 0 && Request::exists($value))
	$this->$var = $value;
      else if(is_int($value) && $value === -1)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method insert a mail in the database.
  public function insert_db($db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_mail())
      $this->modify_db($db);
    else {
      $db->connect();
      $db->execute("insert into email(address, idMember, idContact,idPetition) values('".
		   $this->mail."',".$this->idMember.",".$this->idContact.",".$this->idPetition.")");
    }
  }

  // This method modify a mail of the database.
  public function modify_db($db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_mail()) {
      if($this->idMember !== -1 || $this->idContact !== -1 || (class_exists($value) && 
							       $this->idPetition !== -1)) {
	$db->connect();
	$db->execute("update email set idMember=".$this->idMember.", idContact=".
		     $this->idContact.", idPetition=".$this->idPetition.
		     " where address='".$this->mail."'");
      } else
	$this->drop_db($db);
    }
  }

  // This method drop mail from the database.
  public function drop_db($db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_mail()) {
      $db->connect();
      $db->execute("delete from email where address='".$this->mail."'");
    }
  }

  // This method check if a mail exists in the database
  public static function exists($newMail=null, $db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newMail === null || !is_string($newMail))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select * from email where address='".$newMail."'");
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method check if a mail exists in the database
  public function exists_mail($newMail=null, $db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->mail;
    if($newMail !== null && is_string($newMail))
      $check = $newMail;

    return Email::exists($check);
  }

  // This method load a mail from the database
  public function load_mail($newMail=null, $db=null){
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->mail;
    if($newMail !== null)
      $load = $newMail;

    if($this->exists($load)) {
      $db->connect();
      $db->consult("select * from email where address='".$load."'");
      $email = $db->getRow();
      $this->mail = $email['address'];
      $this->idMember = intval($email['idMember']);
      $this->idContact = intval($email['idContact']);
      $this->idPetition = intval($email['idPetition']);
    }
  }

  // This method loads all the mails related with a Member
  // TODO: This method is better in the mail manager
  public static function load_mails_member($newIdMember=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdMember) || $newIdMember == -1 || !Member::exists($newIdMember))
      throw new GException(GException::$VAR_TYPE);

    $loadMail = null;
    $db->connect();
    $db->consult("select address from email where idMember=".$newIdMember);
    if($db->numRows() > 0) {
      $rows = $db->rows;
      foreach($rows as $row) {
	$newMail = new Email();
	$newMail->load_mail($row['address'],$db);
	$loadMail[] = $newMail;
      }
    }

    return $loadMail;
  }

  // This method loads all the mails related with a Request
  // TODO: This method is better in the mail manager
  public static function load_mails_request($newIdPetition=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdPetition) || $newIdPetition == -1 || !Request::exists($newIdPetition))
      throw new GException(GException::$VAR_TYPE);

    $loadMail = null;
    $db->connect();
    $db->consult("select address from email where idPetition=".$newIdPetition);
    if($db->numRows() > 0) {
      $rows = $db->rows;
      foreach($rows as $row) {
	$newMail = new Email();
	$newMail->load_mail($row['address'],$db);
	$loadMail[] = $newMail;
      }
    }

    return $loadMail;
  }

  // Checks that the parameter is an object of the type Email
  public static function is_email($var) {
    if(is_object($var) && (get_class($var) == "Email" || is_subclass_of($var, "Email")))
      return true;
    return false;
  }
}
?>
