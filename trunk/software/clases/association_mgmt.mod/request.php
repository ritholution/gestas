<?php
/**
 * request.php
 *
 * Description
 * This class represents a request of association.
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
require_once($_SESSION['class_base']."/web.php");
require_once($_SESSION['class_base']."/association.php");

class RequestAssoc extends Association {
  private $idPetition=-1;
  private $name=null; // Name of the association
  private $nif=null; // Fiscal identification of the association
  private $fundationYear=0; // Year of fundation
  private $webs=null; // Websites
  private $headquarters=null; // Headquarters of the association
  private $userId=0;

  public function __construct($newName=null, $newNIF=null, $newFundationYear=0,
			      $newHeadquarters=null, $newWebs=null, $newUserId=0) {
	parent::__construct($newName, $newNIF, $newFundationYear,
			 $newHeadquarters, $newWebs);
    if($newName != null && is_string($newName))
      $this->name = $newName;
    if($newNIF != null && is_string($newNIF))
      $this->nif = $newNIF;
	if($newFundationYear != null && is_string($newFundationYear))
      $this->fundationYear = $newFundationYear;
	if($newHeadquarters != null && is_string($newHeadquarters))
      $this->headquarters = $newHeadquarters;
	 if($newUserId != null && is_int($newUserId)) 
      $this->userId = $newUserId;	  

    if($newWebs != null && is_string($newWebs))
      $this->webs[] = new Web($newWebs);
    else if($newWebs != null && Web::is_web($newWebs))
      $this->webs[] = clone $newWebs;
    else if(is_array($newWebs)) {
      foreach($newWebs as $value) {
	if(is_string($value))
	  $this->webs[] = new Web($value);
	else if(Web::is_web($value))
	  $this->webs[] = clone $value;
      }
    }
  }


  public function __get($var) {
    switch ($var) {
    case "name":
    case "nif":
    case "fundationYear":
    case "headquarters":
    	return parent::__get($var);
	case "userId":
	case "isAuthenticated":
    case "idPetition":
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
    case "idPetition":
      // This variable cannot be set from the outside.
      throw new GException(GException::$VAR_ACCESS);
    case "name":
    case "nif":
    case "fundationYear":
    case "headquarters":
    case "isAuthenticated":
	case "userId":
      if($value == null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
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
  
    case "association":
      if ($value != null && !Association::is_association($value))
	throw new GException(GException::$VAR_TYPE);
      $this->$var = $value;
      break;
    case "mail":
      if($value != null && is_string($value))
	$this->$var = array(new Email($value));
      else if($value === null || Email::is_email($value))
	$this->$var = array($value);
      else if($value != null && is_array($value)) {
	$this->$value = null;
	foreach($value as $newMail) {
	  if(is_string($newMail))
	    array_push($this->$value,new Email($newMail));
	  else if(Email::is_email($newMail))
	    array_push($this->$value,clone $newMail);
	}
      } else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "landPhone":
      if($value != null && is_int($value))
	$this->$var = new Telephone($value,Telephone::$LAND);
      else if($value === null || Telephone::is_telephone($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "cellPhone":
      if($value != null && is_int($value))
	$this->$var = new Telephone($value,Telephone::$CELLULAR);
      else if($value != null && Telephone::is_telephone($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      // Unknown variable
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  public function exists_request($newIdPetition=-1, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->idPetition;
    if (is_int($newIdPetition) && $newIdPetition > 0)
      $check = $newIdPetition;

    return RequestAssoc::exists($check,$this->name,$this->nif,$this->fundationYear,$this->headquarters,$db);
  }

  public static function exists($newIdPetition=-1, $name=null, $nif=null, $fundationYear=null, $headquarters=null, $db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdPetition))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select id from associationRequest where id=".$newIdPetition);

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    if($db->numRows() === 1)
      return true;

    if($nif !== null /*&& DNI::is_dni($dni) && $dni->dni != null*/) { //Clase NIF por implementar//
      $db->consult("select id from associationRequest where nif='".$nif."'");
      if($db->numRows() === 1)
	return true;
    }

    return false;
  }

  public function insert_db($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_request())
      throw new GException(GException::$REQUEST_EXISTS);
    else {
      // If this request doesn't exist, no more checking needed
      //if($insertRequest) {
	// Insert request

	$db->execute("insert into associationRequest(nif,assocName,fundationYear,headquarters,idUser) values ('".
		     $this->nif."','".$this->name."','".$this->fundationYear."','".$this->headquarters."','".$this->userId."')");

	$this->idPetition = $db->id;

	// TODO: Insert webs
      //}
    }
  }

  /*public function modify_db($db=null) {
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

      // TODO: Update mails
      // TODO: Update telephones
    }
  }*/

 /* public function delete_db($db=null) {
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_request()) {
      $db->connect();
      $db->execute("delete from associationRequest where idPetition=".$this->idPetition);
    }
  }*/

 /* public function load_db($id=null, $db=null) {
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
    // $this->mail = new Email($row['email']);
    $this->address = $row['address'];
    // $this->landPhone = new Telephone(intval($row['landPhone']), Telephone::$LAND);
    // $this->cellPhone = new Telephone(intval($row['cellPhone']), Telephone::$CELLULAR);

    parent::load_user_by_login($row['login'], $db);
  }
*/
  // This method returns the html form to request a membership into an association
  public static function html_request() {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $out = '';
    $nextAction = new Action();
    if(($action = $nextAction->get_id_action_class_method('AssociationManagement','new_association')) !== false) {
      $filter->register_var('action',$action);
    if(isset($_SESSION['user']) && User::is_user($_SESSION['user']) && $_SESSION['user']->isAuthenticated) {
      $out = $filter->filter_file('new_association.html');
    }else{
      $out = $filter->filter_file('new_association_extended.html');
	  }
	}

    return $out;
  }

  // This method check if the object passed is of the type RequestAssoc.
  public static function is_request($request) {
    if(is_object($request) && (get_class($request) == "RequestAssoc" ||
			       is_subclass_of($request, "RequestAssoc")))
      return true;
    return false;
  }
}
