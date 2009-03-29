<?php
/**
 * member.php
 *
 * Description
 * This class represent an association member.
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
require_once("user.php");

require_once("dni.php");
require_once("telephone.php");
require_once("email.php");
require_once("web.php");
require_once("project.php");

class Member extends User{
  protected $idMember=-1; // Database identification for the member
  private $name=''; // Member name
  private $firstSurname=''; // Member first surname
  private $secondSurname=''; // Member second surname
  private $address=''; // Member address
  private $dni=null; // Member dni

  private $webs=null; // List of webs of the member
  private $mails=null; // List of mails of the member
  private $telephones=null; // List of telephones of the member

  // Constructor of the class
  public function __construct($newName=null, $newFirstSurname=null, 
			      $newSecondSurname=null, $newAddress=null,
			      $newDNI = null, $newLogin=null,
			      $newPassword=null, $newUserType=null,
			      $newMails=null, $newWebs=null,
			      $newTelephones=null) {
    parent::__construct($newLogin, $newPassword, $newUserType);

    if($newName != null && is_string($newName))
      $this->name = $newName;

    if($newFirstSurname != null && is_string($newFirstSurname))
      $this->firstSurname = $newFirstSurname;

    if($newSecondSurname != null && is_string($newSecondSurname))
      $this->secondSurname = $newSecondSurname;

    if($newAddress != null && is_string($newAddress))
      $this->address = $newAddress;

    if($newDNI != null && is_string($newDNI))
      $this->dni = new DNI($newDNI);
    else if($newDNI != null && DNI::is_dni($newDNI))
      $this->dni = clone $newDNI;

    if($newWebs !== null && is_array($newWebs))
      $this->webs = $this->check_webs($newWebs);
    else if($newWebs !== null && Web::is_web($newWebs)) {
      $newWebs->idMember = $this->idMember;
      $this->webs[] = $newWebs;
    }

    if($newMails !== null && is_array($newMails))
      $this->mails = $this->check_mails($newMails);
    else if($newMails !== null && Mail::is_mail($newMails)) {
      $newMails->idMember = $this->idMember;
      $this->mails[] = $newMails;
    }

    if($newTelephones !== null && is_array($newTelephones))
      $this->telephones = $this->check_telephones($newTelephones);
    else if($newTelephones !== null && Telephone::is_telephone($newTelephones)) {
      $newTelephones->idMember = $this->idMember;
      $this->telephones[] = $newTelephones;
    }
  }

  // This method returns the values of the internal variables
  public function __get($var){
    switch ($var) {
    case "idMember":
    case "name":
    case "firstSurname":
    case "secondSurname":
    case "address":
    case "dni":
    case "webs":
    case "mails":
    case "telephones":
      return $this->$var;
    case "idUser":
    case "login":
    case "idType":
    case "typeUsers":
    case "isAuthenticated":
      return parent::__get($var);
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN,$var);
    }
  }

  public function __set($var, $value){
    switch($var){
    case "idMember":
      // This variable cannot be set from the outside.
      throw new GException(GException::$VAR_ACCESS);
    case "name":
    case "firstSurname":
    case "secondSurname":
    case "address":
      if($value == null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "dni":
      if(is_string($value))
	$this->$var = new DNI($value);
      else if(DNI::is_dni($value))
	$this->$var = clone $value;
      else if($value === null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "webs":
      if($value !== null && is_array($value))
	$this->$var = $this->check_webs($value);
      else if($value !== null && Web::is_web($value)) {
	$value->idMember = $this->idMember;
	$this->$var = array($value);
      } else if($value === null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "mails":
      if (is_array($value))
	$this->$var = $this->check_mails($value);
      else if($value !== null && Mail::is_mail($value)) {
	$value->idMember = $this->idMember;
	$this->$var = array($value);
      } else if($valor === null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "telephones":
      if (is_array($value))
	$this->$var = $this->check_telephones($value);
      else if($value !== null && Telephone::is_telephone($value)) {
	$value->idMember = $this->idMember;
	$this->$var = array($value);
      } else if($value === null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idUser":
    case "login":
    case "idType":
    case "typeUser":
      parent::__set($var,$value);
      break;
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
    return true;
  }

  // This method add a telephone to the list of telephones
  public function add_telephone($newTelephone=null) {
    if($newTelephone !== null && is_int($newTelephone)) {
      $phone = new Telephone($newTelephone,-1,'',-1,$this->idMember,-1);
      if(is_array($this->telephones) && !in_array($phone,$this->telephones))
	$this->telephones[] = $phone;
      else
	$this->telephones = array($phone);
    } else if($newTelephone !== null && Telephone::is_telephone($newTelephone)) {
      $newTelephone->idMember = $this->idMember;
      if($this->telephones !== null && is_array($this->telephones) &&
	 !in_array($newTelephone, $this->telephones))
	$this->telephones[] = $newTelephone;
      else
	$this->telephones = array($newTelephone);
    } else if($newTelephone !== null && is_array($newTelephone)) {
      foreach($newTelephone as $value) {
	if(Telephone::is_telephone($value)) {
	  $value->idMember = $this->idMember;
	  if($this->telephones !== null && is_array($this->telephones) &&
	     !in_array($value, $this->telephones))
	    $this->telephones[] = $value;
	  else
	    $this->telephones = array($value);
	}
      }
    }
  }

  // This method add a mail to the list of mails
  public function add_mail($newMail=null) {
    if($newMail !== null && is_string($newMail)) {
      $mail = new Email($newMail,$this->idMember,-1,-1);
      if(is_array($this->mails) && !in_array($mail,$this->mails))
	$this->mails[] = $mail;
      else
	$this->mails = array($mail);
    } else if($newMail !== null && Email::is_email($newMail)) {
      $newMail->idMember = $this->idMember;
      if($this->mails !== null && is_array($this->mails) &&
	 !in_array($newMail, $this->mails))
	$this->mails[] = $newMail;
      else
	$this->mails = array($newMail);
    } else if($newMail !== null && is_array($newMail)) {
      foreach($newMail as $value) {
	if(Email::is_email($value)) {
	  $value->idMember = $this->idMember;
	  if($this->mails !== null && is_array($this->mails) &&
	     !in_array($value, $this->mails))
	    $this->mails[] = $value;
	  else
	    $this->mails = array($value);
	}
      }
    }
  }

  // This method add a web to the list of webs.
  public function add_web($newWeb=null) {
    if($newWeb !== null && is_string($newWeb)) {
      $web = new Web($newWeb,'',$this->idMember,-1,-1,-1,-1);
      if(is_array($this->webs) && !in_array($web,$this->webs))
	$this->webs[] = $web;
      else
	$this->webs = array($web);
    } else if($newWeb !== null && Web::is_web($newWeb)) {
      $newWeb->idMember = $this->idMember;
      if($this->webs !== null && is_array($this->webs) &&
	 !in_array($newWeb, $this->webs))
	$this->webs[] = $newWeb;
      else
	$this->webs = array($newWeb);
    } else if($newWeb !== null && is_array($newWeb)) {
      foreach($newWeb as $value) {
	if(Web::is_web($value)) {
	  $value->idMember = $this->idMember;
	  if($this->webs !== null && is_array($this->webs) &&
	     !in_array($value, $this->webs))
	    $this->webs[] = $value;
	  else
	    $this->webs = array($value);
	}
      }
    }
  }

  // This method delete a telephone from the list of telephones
  public function delete_telephone($newTelephone=null) {
    if($newTelephone !== null && (is_int($newTelephone) || Telephone::is_telephone($newTelephone)))
      if(is_array($this->telephones))
	foreach($this->telephones as $key => $value)
	  if((is_int($newTelephone) && $value->number === $newTelephone) ||
	     (Telephone::is_telephone($newTelephone) && $value->number === $newTelephone->number)) {
	    $this->telephones[$key]->drop_db('member');
	    $this->telephones[$key] = null;
	  }
    if($newTelephone !== null && is_array($newTelephone))
      foreach($newTelephone as $phone)
	if(is_int($phone) || Telephone::is_telephone($phone))
	  if(is_array($this->telephones))
	    foreach($this->telephones as $key => $value)
	      if((is_int($phone) && $value->number === $phone) ||
		 (Telephone::is_telephone($phone) && $value->number === $phone->number)) {
		$this->telephones[$key]->drop_db('member');
		$this->telephones[$key] = null;
	      }

    $this->telephones = Telephone::load_telephones_member($this->idMember);
  }

  // This method delete a mail from the list of mails
  public function delete_mail($newMail=null) {
    if($newMail !== null && (is_string($newMail) || Email::is_email($newMail)))
      if(is_array($this->mails))
	foreach($this->mails as $key => $value)
	  if((is_int($newMail) && $value->mail === $newMail) ||
	     (Email::is_email($newMail) && $value->mail === $newMail->mail)) {
	    $this->mails[$key]->idMember = -1;
	    $this->mails[$key]->modify_db();
	    $this->mails[$key] = null;
	  }
    if($newMail !== null && is_array($newMail))
      foreach($newMail as $mail)
	if(is_string($mail) || Email::is_email($mail))
	  if(is_array($this->mails))
	    foreach($this->mails as $key => $value)
	      if((is_string($mail) && $value->mail === $mail) ||
		 (Email::is_email($mail) && $value->mail === $mail->mail)) {
		$this->mails[$key]->idMember = -1;
		$this->mails[$key]->modify_db();
		$this->mails[$key] = null;
	      }

    $this->mails = Email::load_mails_member($this->idMember);
  }

  // This method delete a web from the list of webs.
  public function delete_web($newWeb=null) {
    if($newWeb !== null && (is_string($newWeb) || Web::is_web($newWeb)))
      if(is_array($this->webs))
	foreach($this->webs as $key => $value)
	  if((is_int($neweb) && $value->url === $newWeb) ||
	     (Web::is_web($newWeb) && $value->url === $newWeb->url)) {
	    $this->webs[$key]->drop_web('member');
	  }
    if($newWeb !== null && is_array($newWeb))
      foreach($newWeb as $web)
	if(is_string($web) || Web::is_web($web))
	  if(is_array($this->webs))
	    foreach($this->webs as $key => $value)
	      if((is_string($web) && $value->url === $web) ||
		 (Web::is_web($web) && $value->url === $web->url)) {
		$this->webs[$key]->drop_web('member');
	      }

    $this->webs = Web::load_webs_member($this->idMember);
  }

  // This method insert a member in the database
  public function insert_member($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // Add type member
    if(!parent::is_type('member')) {
      $memberType = new TypeUser();
      $memberType->load_type_name('member');
      parent::add_type($memberType);
    }

    if($this->exists_member($this->idMember,$db))
      $this->modify_member($db);
    else {
      parent::insert_user($db);

      $db->connect();
      $db->execute("insert into member(memberName,firstSurname,lastSurname,".
		   "address,dni,idUser) values('".$this->name."','".$this->firstSurname.
		   "','".$this->secondSurname."','".$this->address."','".$this->dni->dni.
		   "',".$this->idUser.")");
      $this->idMember = $db->id;
      echo $db->id;

      if($this->webs !== null && is_array($this->webs))
	foreach($this->webs as $value) {
	  $value->idMember = $this->idMember;
	  $value->insert_web("member",$db);
	}

      if($this->mails !== null && is_array($this->mails))
	foreach($this->mails as $value) {
	  $value->idMember = $this->idMember;
	  $value->insert_db($db);
	}

      if($this->telephones !== null && is_array($this->telephones))
	foreach($this->telephones as $value) {
	  $value->idMember = $this->idMember;
	  $value->insert_db("member",$db);
	}
    }
  }

  // This method modify a member in the database
  public function modify_member($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_member($this->idMember,$db)) {
      parent::modify_user($db);

      $db->connect();
      $db->execute("update member set memberName='".$this->name."', firstSurname='".
		   $this->firstSurname."', lastSurname='".$this->secondSurname.
		   "', address='".$this->address."',dni='".$this->dni->dni.
		   "', idUser=".$this->idUser." where idMember=".$this->idMember);

      if($this->webs != null) {
	if(is_array($this->webs))
	  foreach($this->webs as $value)
	    $value->insert_web('member',$db);
	else 
	  $this->webs->insert_web('member',$db);
      }

      if($this->mails != null) {
	if(is_array($this->mails))
	  foreach($this->mails as $value)
	    $value->insert_db('member',$db);
	else
	  $this->mails->insert_db('member',$db);
      }

      if($this->telephones != null) {
	if(is_array($this->telephones))
	  foreach($this->telephones as $value)
	    $value->insert_db('member',$db);
	else
	  $this->telephones->insert_db('member',$db);
      }
    }
  }

  // This method drop a member from the database
  public function delete_member($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_member($this->idMember,$db)) {
      if ($this->webs !== null) {
	if (is_array($this->webs))
	  foreach($this->webs as $value) {
	    $value->idMember = -1;
	    $value->modify_web($db);
	  } 
	else {
	  $this->webs->idMember = -1;
	  $this->webs->modify_web($db);
	}
      }

      if ($this->mails !== null) {
	if (is_array($this->mails))
	  foreach($this->mails as $value) {
	    $value->idMember = -1;
	    $value->modify_db($db);
	  }
	else {
	  $this->mails->idMember = -1;
	  $this->mails->modify_db($db);
	}
      }

      if ($this->telephones !== null) {
	if (is_array($this->telephones))
	  foreach($this->telephones as $value) {
	    $value->idMember = -1;
	    $value->modify_db($db);
	  }
	else {
	  $this->telephones->idMember = -1;
	  $this->telephones->modify_db($db);
	}
      }

      $db->connect();
      $db->execute("delete from member where idMember=".$this->idMember);
      // This corresponds to the member_management module.
      $db->execute("delete from memberAssociation where idMember=".$this->idMember);
    }
  }

  // This method loads an User from de database given the id of the User
  public function load_user($newIdUser=-1, $db=null) {
    parent::load_user($newIdUser,$db);
  }

  // This method loads a member from the database
  public function load_member($newIdMember=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->idMember;
    if(is_int($newIdMember) && $newIdMember > 0)
      $load = $newIdMember;

    if($this->exists_member($load,$db)) {
      $db->connect();
      $db->consult("select * from member where idMember=".$load);

      $row = $db->getRow();
      $this->setRow($row);

      $this->telephones = Telephone::load_telephones_member($this->idMember,$db);
      $this->mails = Email::load_mails_member($this->idMember,$db);
      $this->webs = Web::load_webs_member($this->idMember,$db);
    }
  }

  // This method loads a member from the database
  public function load_member_by_user($newIdUser=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->idUser;
    if(is_int($newIdUser) && $newIdUser > 0)
      $load = $newIdUser;

    if($this->exists_user($load,$db)) {
      $this->load_user($load,$db);

      $db->connect();
      $db->consult("select * from member where idUser=".$load);

      if($db->numRows() === 1) {
	$row = $db->getRow();
	$this->setRow($row);

	$this->telephones = Telephone::load_telephones_member($this->idMember,$db);
	$this->mails = Email::load_mails_member($this->idMember,$db);
	$this->webs = Web::load_webs_member($this->idMember,$db);
      } else if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);
    }
  }

  // This method loads a member from the database
  public function load_member_by_dni($newDNI=null, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->dni;
    if($newDNI !== null && DNI::is_dni($newDNI))
      $load = $newDNI;

    if($this->exists_dni($load,$db)) {
      $db->connect();
      $db->consult("select * from member where dni='".$load->dni."'");

      $row = $db->getRow();
      $this->setRow($row);
      $this->telephones = Telephone::load_telephones_member($this->idMember,$db);
      $this->mails = Email::load_mails_member($this->idMember,$db);
      $this->webs = Web::load_webs_member($this->idMember,$db);
    }
  }

  // This method loads a member from the database
  public function load_member_by_login($newLogin=null, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->login;
    if($newLogin !== null && is_string($newLogin))
      $load = $newLogin;

    if(parent::exists_login($load)) {
      parent::load_user_by_login($load);
      $db->consult("select idMember from member where idUser=".$this->idUser);
      $id = $db->getRow();
      $this->load_member($id['idMember'],$db);
    }
  }

  // This method checks if a member exists in the database checked by the idUser.
  public function exists_member_by_user($newIdUser=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->idUser;
    if(is_int($newIdUser) && $newIdUser > 0)
      $check = $newIdUser;

    $db->connect();
    $db->consult("select idMember from member where idUser=".$newIdUser);
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method checks if a member exists in the database.
  public function exists_member($newIdMember=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->idMember;
    if(is_int($newIdMember) && $newIdMember > 0)
      $check = $newIdMember;

    return Member::exists($check);
  }

  // This method checks if a member exists in the database.
  public static function exists($newIdMember=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdMember))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select idMember from member where idMember=".$newIdMember);
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method checks if a member exists in the database.
  public function exists_dni($newDNI=null, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->dni;
    if($newDNI != null && is_string($newDNI) && $newDNI != null)
      $check = new DNI($newDNI);
    else if($newDNI != null && DNI::is_dni($newDNI))
      $check = clone $newDNI;

    if($check !== null) {
      $db->connect();
      $db->consult("select idMember from member where dni='".$check->dni."'");
      if($db->numRows() > 1)
	throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

      return ($db->numRows() === 1);
    }

    return false;
  }

  // This method check a list and return the valid telephones
  private function check_telephones($newTelephones) {
    if(!is_array($newTelephones))
      return null;

    $validTelephones = null;
    foreach($newTelephones as $value) {
      if(Telephone::is_telephone($value)) {
	$value->idMember = $this->idMember;
	$validTelephones[] = $value;
      }
    }

    return $validTelephones;
  }

  // This method check a list and return the valid mails
  private function check_mails($newMails) {
    if(!is_array($newMails))
      return null;

    $validMails = null;
    foreach($newMails as $value) {
      if(Email::is_email($alue)) {
	$value->idMember = $this->idMember;
	$validMails[] = $value;
      }
    }

    return $validMails;
  }

  // This method check a list and return the valid webs
  private function check_webs($newWebs) {
    if(!is_array($newWebs))
      return null;

    $validWebs = null;
    foreach($newWebs as $value) {
      if(Web::is_web($value)) {
	$value->idMember = $this->idMember;
	$validWebs[] = $value;
      }
    }

    return $validWebs;
  }

  // This method load all the data from a database row to the object
  private function setRow($row) {
    if($row === null || !is_array($row) || !isset($row['idMember']) ||
       !isset($row['memberName']) || !isset($row['firstSurname']) ||
       !isset($row['lastSurname']) || !isset($row['address']) || !isset($row['dni']))
      throw new GException(GException::$VAR_TYPE);

    $this->idMember = intval($row['idMember']);
    $this->name = $row['memberName'];
    $this->firstSurname = $row['firstSurname'];
    $this->secondSurname = $row['lastSurname'];
    $this->address = $row['address'];
    $this->dni = new DNI($row['dni']);
    
    $this->webs = Web::load_webs_member($this->idMember, $db);
    $this->mails = Email::load_mails_member($this->idMember, $db);
    $this->telephones = Telephone::load_telephones_member($this->idMember, $db);

    $this->load_user(intval($row['idUser']));
  }

  // This method check if the parameter passed is an object of the type Member.
  public static function is_member($member){
    if(is_object($member) && (get_class($member) === "Member" || is_subclass_of($member, "Member")))
      return true;
    return false;
  }
}
?>
