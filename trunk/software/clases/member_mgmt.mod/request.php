<?php
/**
 * request.php
 *
 * Description
 * This class represents a request of membership.
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

require_once($_SESSION['class_base']."/gexception.php");
require_once($_SESSION['class_base']."/gdatabaseexception.php");
require_once($_SESSION['class_base']."/guserexception.php");
require_once($_SESSION['class_base']."/email.php");
require_once($_SESSION['class_base']."/dni.php");

class Request extends User {
  private $idPetition=-1;
  private $name=null; // Name of the applicant
  private $sname1=null; // First surname of the applicant
  private $sname2=null; // Second surname of the applicant
  private $dni=null; // DNI of the applicant
  private $mail=null;
  private $address=null; // Address of the applicant
  private $phone=null; // Land phone of the applicant
  private $association=null;
  private $web=null;
  private $password=null;

  public function __construct($newAssociation=null, $newLogin=null, $newPassword=null,
			      $newName=null, $newSname1=null, $newSname2=null, $newDNI=null,
			      $newMail=null, $newAddress=null, $newPhone=null, $newWeb=null) {
    parent::__construct($newLogin, $newPassword, "user"); // We set the userType when the user is accepted

    if ($newAssociation === null || !Association::is_association($newAssociation)) {
      if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);
      $newAssociation = $_SESSION['association'];
    }
    $this->association = $newAssociation;

    if($newPassword !== null && is_string($newPassword))
      $this->password = $newPassword;

    if($newName != null && is_string($newName))
      $this->name = $newName;

    if($newSname1 != null && is_string($newSname1))
      $this->sname1 = $newSname1;

    if($newSname2 != null && is_string($newSname2))
      $this->sname2 = $newSname2;

    if($newDNI != null && is_string($newDNI))
      $this->dni = new DNI($newDNI);
    else if($newDNI != null && DNI::is_dni($newDNI))
      $this->dni = clone $newDNI;

    if($newMail != null && is_string($newMail))
      $this->mail[] = new Email($newMail);
    else if($newMail !== null && Email::is_email($newMail))
      $this->mail[] = clone $newMail;
    else if($newMail !== null && is_array($newMail)) {
      foreach($newMail as $value) {
	if(is_string($value) && $value != null)
	  $this->mail[] = new Email($value);
	else if(Email::is_email($value))
	  $this->mail[] = clone $value;
      }
    }

    if($newAddress != null && is_string($newAddress))
      $this->address = $newAddress;

    if($newPhone !== null && is_int($newPhone))
      $this->phone[] = new Telephone($newPhone);
    else if($newPhone !== null && Telephone::is_telephone($newPhone))
      $this->phone[] = $newPhone;
    else if($newPhone !== null && is_array($newPhone)) {
      foreach($newPhone as $value) {
	if(is_string($value) && $value != null)
	  $this->phone[] = new Telephone(intval($value));
	else if(Telephone::is_telephone($value))
	  $this->phone[] = clone $value;
      }
    }

    if($newWeb != null && is_string($newWeb))
      $this->web[] = new Web($newWeb);
    else if($newWeb !== null && Web::is_web($newWeb))
      $this->web[] = clone $newWeb;
    else if($newWeb !== null && is_array($newWeb)) {
      foreach($newWeb as $value) {
	if(is_string($value) && $value != null)
	  $this->web[] = new Web($value);
	else if(Web::is_web($value))
	  $this->web[] = clone $value;
      }
    }
  }

  public function __get($var) {
    switch ($var) {
    case "idType":
    case "typeUser":
    case "idUser":
    case "login":
    case "isAuthenticated":
      return parent::__get($var);
    case "idPetition":
    case "name":
    case "sname1":
    case "sname2":
    case "association":
    case "dni":
    case "mail":
    case "address":
    case "phone":
    case "web":
      return $this->$var;
    case "password":
      throw new GException(GException::$VAR_ACCESS);
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }


  public function __set($var, $value) {
    switch ($var) {
    case "idType":
    case "idUser":
    case "idPetition":
    case "password":
      // This variable cannot be set from the outside.
      throw new GException(GException::$VAR_ACCESS);
    case "typeUser":
    case "login":
    case "isAuthenticated":
      parent::__set($var,$value);
      break;
    case "dni":
      if($value != null && is_string($value))
	$this->$var = new DNI($value);
      else if($value != null && DNI::is_dni($value))
	$this->$var = clone $value;
      else if($value === null)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "name":
    case "sname1":
    case "sname2":
    case "address":
      if($value == null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "association":
      if ($value != null && !Association::is_association($value))
	throw new GException(GException::$VAR_TYPE);
      $this->$var = $value;
      break;
    case "mail":
      if($value != null && is_string($value)) {
	$tmp = new Email($value);
	if($this->idPetition > -1) {
	  $tmp->idPetition = $this->idPetition;
	  if($tmp->exists_mail())
	    $tmp->load_mail();
	}
	$this->$var = array($tmp);
      } else if($value === null || Email::is_email($value)) {
	if($this->idPetition > -1) {
	  $value->idPetition = $this->idPetition;
	  if($value->exists_mail())
	    $value->load_mail();
	}
	$this->$var = array($value);
      } else if($value != null && is_array($value)) {
	$this->$value = null;
	foreach($value as $newMail) {
	  if(is_string($newMail)) {
	    $tmp = new Email($newMail);
	    if($this->idPetition > -1) {
	      $tmp->idPetition = $this->idPetition;
	      if($tmp->exists_mail())
		$tmp->load_mail();
	    }
	    array_push($this->$value,$tmp);
	  } else if(Email::is_email($newMail)) {
	    if($this->idPetition > -1) {
	      $newMail->idPetition = $this->idPetition;
	      if($newMail->exists_mail())
		$newMail->load_mail();
	    }
	    array_push($this->$value,clone $newMail);
	  }
	}
      } else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "phone":
      if($value != null && is_int($value)) {
	$tmp = new Telephone($value);
	if($this->idPetition > -1) {
	  $tmp->idPetition = $this->idPetition;
	  if($tmp->exists_telephone('request'))
	    $tmp->load_telephone('request');
	}
	$this->$var = array($tmp);
      } else if($value === null || Telephone::is_telephone($value)) {
	if($this->idPetition > -1) {
	  $value->idPetition = $this->idPetition;
	  if($value->exists_telephone('request'))
	    $value->load_telephone('request');
	}
	$this->$var = array($value);
      } else if($value != null && is_array($value)) {
	$this->$value = null;
	foreach($value as $newPhone) {
	  if(is_int($newPhone)) {
	    $tmp = new Telephone($newPhone);
	    if($this->idPetition > -1) {
	      $tmp->idPetition = $this->idPetition;
	      if($tmp->exists_telephone('request'))
		$tmp->load_telephone('request');
	    }
	    array_push($this->$value,$tmp);
	  } else if(Telephone::is_telephone($newPhone)) {
	    if($this->idPetition > -1) {
	      $newPhone->idPetition = $this->idPetition;
	      if($newPhone->exists_telephone('request'))
		$newPhone->load_telephone('request');
	    }
	    array_push($this->$value,clone $newPhone);
	  }
	}
      } else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "web":
      if($value != null && is_string($value)) {
	$tmp = new Web($vaue);
	if($this->idPetition > -1) {
	  $tmp->idPetition = $this->idPetition;
	  if($tmp->exists_web("request"))
	    $tmp->load_web("request");
	}
	$this->$var = array($tmp);
      } else if($value !== null && Web::is_web($value)) {
	if($this->idPetition > -1) {
	  $value->idPetition = $this->idPetition;
	  if($value->exists_web("request"))
	    $value->load_web("request");
	}
	$this->$var = array(clone $value);
      } else if($value !== null && is_array($value)) {
	$this->$var = array();
	foreach($value as $webValue) {
	  if(is_string($webValue)) {
	    $tmp = new Web($webValue);
	    if($this->idPetition > -1) {
	      $tmp->idPetition = $this->idPetition;
	      if($tmp->exists_web("request"))
		$tmp->load_web("request");
	    }
	    $this->$var = array_push($tmp);
	  } else if(Web::is_web($webValue)) {
	    if($this->idPetition > -1) {
	      $webValue->idPetition = $this->idPetition;
	      if($webValue->exists_web("request"))
		$webValue->load_web("request");
	    }
	    $this->$var = array_push(clone $webValue);
	  }
	}
      }
      break;
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method checks if a petition is already requested
  public function exists_request($newIdPetition=-1, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if (is_int($newIdPetition) && $newIdPetition > 0)
      return Request::exists($check);
    return Request::exists($this->idPetition,$this->login,$this->dni,$this->association,$db);
  }

  // This method checks if a petition is already requested
  public static function exists($newIdPetition=-1, $login=null, $dni=null, $association=null, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdPetition))
      throw new GException(GException::$VAR_TYPE);

    if($association === null || !Association::is_association($association)) {
      if(!isset($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);
      $association = $_SESSION['association'];
    }

    $db->connect();
    $db->consult("select idPetition from registrationRequest where idPetition=".$newIdPetition);

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    if($db->numRows() === 1)
      return true;

    if($dni !== null && DNI::is_dni($dni) && $dni->dni != null) {
      $db->consult("select idPetition from registrationRequest where dni='".$dni->dni.
		   "' and idAssociation=".$association->idAssociation);
      if($db->numRows() === 1)
	return true;
    }

    if($login != null && is_string($login) && $association !== null &&
       Association::is_association($association)) {
      $db->consult("select idPetition from registrationRequest where login='".$login.
		   "' and idAssociation=".$association->idAssociation);
      return ($db->numRows() === 1);
    }

    return false;
  }

  public function exists_dni($newDNI=null, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->dni;
    if($newDNI != null && is_string($newDNI))
      $check = new DNI($newDNI);
    else if($newDNI != null && DNI::is_dni($newDNI))
      $check = clone $newDNI;

    $db->connect();
    $db->consult("select idPetition from registrationRequest where dni='".$check->dni."'");

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  public function insert_db($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_request())
      throw new GUserException(GUserException::$REQUEST_EXISTS);
    else {
      $insertRequest = false;
      $newMember = new Member();
      if($newMember->exists_dni($this->dni,$db)) {
	$newMember->load_member_by_dni($this->dni,$db);

	// Check if the new member exists in the association.
 	if($_SESSION['MemberManagement']->is_member_of($newMember,$this->association))
 	  throw new GUserException(GUserException::$IS_MEMBER);
	else if($this->exists_request())
	  throw new GUserException(GUserException::$REQUEST_EXISTS);

	$insertRequest = true;
      } else if($newMember->exists_login($this->login,$db)) {
	$newMember->load_member_by_login($this->login,$db);

	// Check if the new member exists in the association.
 	if($_SESSION['MemberManagement']->is_member_of($newMember,$this->association))
 	  throw new GUserException(GUserException::$IS_MEMBER);
	else if($this->exists_request())
	  throw new GUserException(GUserException::$REQUEST_EXISTS);

	$insertRequest = true;
      } else if($this->login != 'anonymous')
	$insertRequest = true;

      // If this request is ok, then submit it.
      if($insertRequest) {
	if(parent::exists_login($this->login)) {
	  parent::load_user_by_login($this->login);
	  if(!parent::check_password($this->password))
	    throw new GUserException(GUserException::$USER_EXISTS);
	}
	parent::insert_user();

	if($newMember->exists_member()) {
	  $newMember->name = $this->name;
	  $newMember->firstSurname = $this->sname1;
	  $newMember->secondSurname = $this->sname2;
	  $newMember->dni = $this->dni;
	  $newMember->address = $this->address;
	  $newMember->mails = $this->mail;
	  $newMember->telephones = $this->phone;
	  $newMember->webs = $this->web;
	  $newMember->modify_member();
	}

	// Insert request
	$db->execute("insert into registrationRequest(name,firstSurname,lastSurname,dni,address,login,idAssociation) values ('".
		     $this->name."','".$this->sname1."','".$this->sname2."','".$this->dni->dni.
		     "','".$this->address."','".$this->login."',".$this->association->idAssociation.")");

	$this->idPetition = $db->id;

	if($this->mail !== null && is_array($this->mail))
	  foreach($this->mail as $value) {
	    $value->idPetition = $this->idPetition;
	    $value->insert_db($db);
	  }

	if($this->phone !== null && is_array($this->phone))
	  foreach($this->phone as $value) {
	    $value->idPetition = $this->idPetition;
	    $value->insert_db('request',$db);
	  }

	if($this->web !== null && is_array($this->web))
	  foreach($this->web as $value) {
	    $value->idPetition = $this->idPetition;
	    $value->insert_web('request',$db);
	  }
      }
    }
  }

  public function modify_db($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_request()) {
      $db->connect();
      $db->execute("modify registrationRequest set name='".$this->name."', firstSurname='".
		   $this->sname1."', lastSurname='".$this->sname2."', dni='".$this->dni->dni
		   ."', address='".$this->address."', login='".$this->login."', idAssociation".
		   $this->association->idAssociation." where idPetition=".$this->idPetition);

      if($this->mail !== null && is_array($this->mail))
	foreach($this->mail as $value) {
	  $value->idPetition = $this->idPetition;
	  $value->modify_db($db);
	}

      if($this->phone !== null && is_array($this->phone))
	foreach($this->phone as $value) {
	  $value->idPetition = $this->idPetition;
	  $value->modify_db('request',$db);
	}

      if($this->web !== null && is_array($this->web))
	foreach($this->web as $value) {
	  $value->idPetition = $this->idPetition;
	  $value->modify_web('request',$db);
	}
    }
  }

  public function delete_db($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_request()) {
      $db->connect();
      $db->execute("delete from registrationRequest where idPetition=".$this->idPetition);

      if($this->mail !== null && is_array($this->mail))
	foreach($this->mail as $value) {
	  $value->idPetition = -1;
	  $value->modify_db($db);
	}

      if($this->phone !== null && is_array($this->phone))
	foreach($this->phone as $value)
	  $value->drop_db('request',$db);
 
      if($this->web !== null && is_array($this->web))
	foreach($this->web as $value)
	  $value->drop_web('request',$db);

      $this->mail = null;
      $this->phone = null;
      $this->web = null;
   }
  }

  public function load_db($id=null, $db=null) {
    if ($association === null || !Association::is_association($association)) {
      if ($this->association === null || !Association::is_association($this->association)) {
	if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	  throw new GException(GException::$VAR_TYPE);
	$this->association = $_SESSION['association'];
      }

      $association = $this->association;
    }

    if ($db === null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $db->connect();
    $db->consult("select * from registrationRequest where idPetition=".$id);

    if ($db->numRows() === 0)
      throw new GDatabaseException(GDatabaseException::$DB_SEL);
    if ($db->numRows() !== 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    $row = $db->getRow();
    $this->idPetition = intval($row['idPetition']);
    $this->name = $row['name'];
    $this->sname1 = $row['firstSurname'];
    $this->sname2 = $row['lastSurname'];
    $this->dni = new DNI($row['dni']);
    $this->address = $row['address'];
    $this->mail = Email::load_mails_request($this->idPetition,$db);
    $this->phone = Telephone::load_telephones("request",$this->idPetition,$db);
    $this->web = Web::load_webs_request($this->idPetition,$db);

    parent::load_user_by_login($row['login'], $db);
  }

  // This method returns the html form to request a membership into an association
  public static function html_request() {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $out = '';
    $nextAction = new Action();
    if(($action = $nextAction->get_id_action_class_method('MemberManagement','signup_request')) !== false) {
      $filter->register_var('action',$action);
      if(isset($_SESSION['user']) && User::is_user($_SESSION['user']) && $_SESSION['user']->isAuthenticated) {
	$member = new Member();
	$member->load_member_by_user($_SESSION['user']->idUser);

	$filter->register_var('oculto','oculto');
	$filter->register_var('login',$member->login);
	$filter->register_var('name',$member->name);
	$filter->register_var('sname1',$member->firstSurname);
	$filter->register_var('sname2',$member->secondSurname);
	$filter->register_var('dni',$member->dni->dni);
	$filter->register_var('address',$member->address);

	$mails = '';
	if($member->mails !== null && is_array($member->mails))
	  foreach($member->mails as $value)
	    $mails .= $value->mail;
	$filter->register_var('mail',$mails);

	$telephones = '';
	if($member->telephones !== null && is_array($member->telephones))
	  foreach($member->telephones as $value)
	    $telephones .= $value->number;
	$filter->register_var('phone',$telephones);

	$webs = '';
	if($member->webs !== null && is_array($member->webs))
	  foreach($member->webs as $value)
	    $webs .= $value->url;
	$filter->register_var('web',$webs);
      } else {
	$filter->register_var('oculto','');
	$filter->register_var('login','');
	$filter->register_var('name','');
	$filter->register_var('sname1','');
	$filter->register_var('sname2','');
	$filter->register_var('dni','');
	$filter->register_var('address','');
	$filter->register_var('mail','');
	$filter->register_var('phone','');
	$filter->register_var('web','');
      }
      $out = $filter->filter_file('signup_request.html');
    }

    return $out;
  }

  // This method check if the object passed is of the type Request.
  public static function is_request($request) {
    if(is_object($request) && (get_class($request) == "Request" ||
			       is_subclass_of($request, "Request")))
      return true;
    return false;
  }
}
