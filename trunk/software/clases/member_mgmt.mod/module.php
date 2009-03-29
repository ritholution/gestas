<?php
/**
 * module.php
 *
 * Description
 * This module manage the member actions.
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

require_once("request.php");
require_once($_SESSION['class_base']."/gexception.php");
require_once($_SESSION['class_base']."/gdatabaseexception.php");
require_once($_SESSION['class_base']."/guserexception.php");
require_once($_SESSION['class_base']."/output.php");

class MemberManagement {
  private $db = null;

  // Constructor of the class
  public function __construct($db=null) {
    if ($db == null | !Database::is_database($db)) {
      if (!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $this->db = $db;
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them.
  public function __get($var) {
    switch($var){
    case "db":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them.
  public function __set($var,$value) {
    switch($var){
    case "db":
      if($value === null || !Database::is_database($value))
	throw new GException(GException::$VAR_TYPE);
      $this->$var = clone $value();
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method show the list of member of the active association
  public function list_members() {
    if(!Database::is_database($this->db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $this->db = $_SESSION['db'];
    }

    if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
      throw new GException(GException::$VAR_TYPE);
    $association = $_SESSION['association'];

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $out = '';

    // Get a list of members belonging to give association.
    $this->db->connect();
    $this->db->consult("select * from memberAssociation where idAssociation=".$association->idAssociation);
    if($this->db->numRows() > 0) {
      $members = $this->db->rows;

      foreach($members as $value) {
	$this->db->consult("select idMember from member where idMember=".$value['idMember']);
	if($this->db->numRows() !== 1)
	  throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY,$value['idMember']);
	$row = $this->db->getRow();
	$member = new Member();
	$member->load_member(intval($row['idMember']));
	// Generate output
	$filter->register_var('user',$member->login);
	$filter->register_var('name',$member->name);
	$filter->register_var('sname1',$member->firstSurname);
	$filter->register_var('sname2',$member->secondSurname);
	$filter->register_var('dni',$member->dni->dni);
	$filter->register_var('address',$member->address);
	$filter->register_var('isFounder', intval($value['isFounder']));
	$filter->register_var('active', intval($value['isActive']));

	$telephones = '';
	if($member->telephones !== null && is_array($member->telephones))
	  foreach($member->telephones as $value) {
	    if($telephones !== '')
	      $telephones .= '<br/>';
	    $telephones .= $value->number;
	  }
	$filter->register_var('telephone',$telephones);

	$mails = '';
	if($member->mails !== null && is_array($member->mails))
	  foreach($member->mails as $value) {
	    if($mails !== '')
	      $mails .= '<br/>';
	    $mails .= $value->mail;
	  }
	$filter->register_var('mail',$mails);

	$webs = '';
	if($member->webs !== null && is_array($member->webs))
	  foreach($member->webs as $value) {
	    if($webs !== '')
	      $webs .= '<br/>';
	    $webs .= $value->url;
	  }
	$filter->register_var('web',$webs);

	$out .= $filter->filter_file('member_entry.html');
      }
    }

    if($out !== null)
      $filter->register_var('entrys',$out);
    $_SESSION['out']->content = $filter->filter_file('member_list.html');
  }


  // 'Send' a new registration request
  public function signup_request($params) {
    if(!isset($_SESSION['AssociationManagement']))
      throw new GException(GException::$MODULE_DEP,"AssociationManagement");

    if($params === null) {
      // Generate the html output.
      $_SESSION['out']->content = Request::html_request();
    } else {
      // Check the params.
      if(!is_array($params) || $params['login'] == null || $params['name'] == null ||
	 $params['sname1'] == null || $params['address'] == null || 
	 !isset($_SESSION['association']) || $_SESSION['association'] === null || 
	 !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);

      if(!isset($_SESSION['user']) || !User::is_user($_SESSION['user']) || !$_SESSION['user']->isAuthenticated) {
	if($params['password'] == null)
	  throw new GException(GException::$VAR_TYPE);
	else if($params['password'] !== $params['repeat_password'])
	  throw new GUserException(GUserException::$PASS_EQ_FAIL);
      }

      if(!isset($_SESSION['filter']))
	$_SESSION['filter'] = new TemplateFilter();
      $filter = $_SESSION['filter'];

      // Insert the request.
      try {
	$request = new Request($_SESSION['association'], $params['login'], $params['password'],
			       $params['name'], $params['sname1'], $params['sname2'], $params['dni'],
			       $params['mail'], $params['address'], $params['phone'],$params['web']);
	if(!$request->exists_request()) {
	  $request->insert_db();

	  $filter->register_var('success',gettext("Petición enviada correctamente"));
	  $_SESSION['out']->content = $filter->filter_file('success.html');
	} else {
	  $filter->register_var('success',gettext("Petición ya existente"));
	  $_SESSION['out']->content = $filter->filter_file('success.html');
	}
      } catch(GException $e) {
	// Insert log
	$this->filter->register_var('msg',$e->getOutMessage());
	$_SESSION['out']->content = $filter->filter_file('recover_exception.html');
      }
    }
  }

  public function signup_validate($params) {
    if (!isset($_SESSION['AssociationManagement']))
      throw new GException(GException::$MODULE_DEP, "AssociationManagement");

    if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
      throw new GException(GException::$VAR_TYPE);
    $association = $_SESSION['association'];

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if ($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('MemberManagement', 'signup_validate')) !== false) {
	if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	  throw new GException(GException::$ASSOCIATION_UNKNOWN);

	$request_list = $this->signup_list($_SESSION['association']);

	$out = '';
	if(is_array($request_list) && count($request_list) > 0)
	  foreach ($request_list as $rq) {
	    $filter->register_var('idPetition',$rq->idPetition);
	    $filter->register_var('name',$rq->name);
	    $filter->register_var('sname1',$rq->sname1);
	    $filter->register_var('sname2',$rq->sname2);
	    $filter->register_var('dni',$rq->dni->dni);
	    $filter->register_var('address',$rq->address);

	    $telephones = '';
	    if($rq->phone !== null && is_array($rq->phone))
	      foreach($rq->phone as $telephone) {
		if($telephones !== '')
		  $telephones .= '<br/>';
		$telephones .= $telephone->number;
	      }
	    $filter->register_var('telephone',$telephones);

	    $mails = '';
	    if($rq->mail !== null && is_array($rq->mail))
	      foreach($rq->mail as $email) {
		if($mails !== '')
		  $mails .= '<br/>';
		$mails .= $email->mail;
	      }
	    $filter->register_var('mail',$mails);

	    $webs = '';
	    if($rq->web !== null && is_array($rq->web))
	      foreach($rq->web as $newWeb) {
		if($webs !== '')
		  $webs .= '<br/>';
		$webs .= $newWeb->url;
	      }
	    $filter->register_var('web',$webs);

	    $out .= $filter->filter_file('signup_validate_entry.html');
	  }

	$filter->register_var('action',$action);
	$filter->register_var('list',$out);
	$_SESSION['out']->content = $filter->filter_file('signup_validate.html');
      }
    } else {
      if (!is_array($params) ||
	  (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association'])))
	throw new GException(GException::$VAR_TYPE);

      if($params['idPetition'] != null && is_array($params['idPetition']))
	foreach ($params['idPetition'] as $value) {
	  $request = new Request();
	  $request->load_db($value);

	  if($params['reqAction'] === "Aprobar") {
	    $member = new Member();
	    if($member->exists_member_by_user($request->idUser))
	      $member->load_member_by_user($request->idUser);
	    else {
	      $usr = new User();
	      if($usr->exists($request->idUser))
		$member->load_user($request->idUser);
	    }

	    $member->name = $request->name;
	    $member->firstSurname = $request->sname1;
	    $member->secondSurname = $request->sname2;
	    $member->address = $request->address;
	    $member->dni = $request->dni;

	    $member->insert_member();
	    $this->db->execute("insert into memberAssociation(idMember,idAssociation,isActive) values(".
			       $member->idMember.",".$association->idAssociation.",1)");

	    $member->add_telephone($request->phone);
	    $member->add_mail($request->mail);
	    $member->add_web($request->web);
	    $member->modify_member();
	  }

	  $request->delete_db();
	}
    }
  }

  public function signup_list($association=null) {
    if(!Database::is_database($this->db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $this->db = $_SESSION['db'];
    }

    if($association == null || !Association::is_association($association)) {
      if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);
      $association = $_SESSION['association'];
    }

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $out = '';
    $list = null;

    $this->db->connect();
    $this->db->consult("select idPetition from registrationRequest where idAssociation=".$association->idAssociation);
    if($this->db->numRows() > 0) {
      $result = $this->db->rows;

      foreach($result as $row) {
	$request = new Request();
	$request->load_db($row['idPetition'], $association);
	$list[] = $request;

	$filter->register_var('user',$request->login);
	$filter->register_var('name',$request->name);
	$filter->register_var('sname1',$request->sname1);
	$filter->register_var('sname2',$request->sname2);
	$filter->register_var('dni',$request->dni->dni);
	$filter->register_var('address',$request->address);

	$telephones = '';
	if($request->phone !== null && is_array($request->phone))
	  foreach($request->phone as $telephone)
	    $telephones .= $telephone->number.'</br>';
	$filter->register_var('telephone',$telephones);

	$mails = '';
	if($request->mail !== null && is_array($request->mail))
	  foreach($request->mail as $email)
	    $mails .= $email->mail.'</br>';
	$filter->register_var('mail',$mails);

	$webs = '';
	if($request->web !== null && is_array($request->web))
	  foreach($request->web as $newWeb)
	    $webs .= $newWeb->url.'</br>';
	$filter->register_var('web',$webs);

	$out .= $filter->filter_file('request_entry.html');
      }
    }

    if($out !== null)
      $filter->register_var('entrys',$out);
    $_SESSION['out']->content = $filter->filter_file('request_list.html');

    return $list;
  }

  // This method return the list of members of an association
  public function get_member_list($association=null) {
    if(!Database::is_database($this->db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $this->db = $_SESSION['db'];
    }

    if ($association == null || !Association::is_association($association)) {
      if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);
      $association = $_SESSION['association'];
    }

    $this->db->connect();
    $this->db->consult("select idMember from memberAssociation where idAssociation=".$association->idAssociation);
    $result = null;
    $nRows = $this->db->numRows();
    $rows = $this->db->rows;
    for ($i = 0; $i < $nRows; $i++) {
      $result[] = new Member();
      $result[$i]->load_user(intval($rows[$i]['idMember']));
      $result[$i]->load_member(intval($rows[$i]['idMember']));
    }

    return $result;
  }

  // This action modify the data of the member authenticated.
  public function modify_member($params=null) {
    if (!isset($_SESSION['AssociationManagement']))
      throw new GException(GException::$MODULE_DEP,"AssociationManagement");

    if(!isset($_SESSION['user']) || !User::is_user($_SESSION['user']) ||
       !$_SESSION['user']->isAuthenticated)
      throw new GUserException(GUserException::$USER_PERM);

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if ($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('MemberManagement', 'modify_member')) !== false) {
	$newMember = new Member();
	$newMember->load_member_by_user($_SESSION['user']->idUser);
	$filter->register_var('action',$action);
	$filter->register_var('idMember',$newMember->idMember);
	$filter->register_var('login',$newMember->login);
	$filter->register_var('name',$newMember->name);
	$filter->register_var('sname1',$newMember->firstSurname);
	$filter->register_var('sname2',$newMember->secondSurname);
	$filter->register_var('dni',$newMember->dni->dni);
	$filter->register_var('address',$newMember->address);

	$telephones = '';
	if($newMember->telephones !== null && is_array($newMember->telephones))
	  foreach($newMember->telephones as $value) {
	    if($telephones !== '')
	      $telephones .= '<br/>';
	    $telephones .= $value->number;
	  }
	$filter->register_var('telephone',$telephones);

	$mails = '';
	if($newMember->mails !== null && is_array($newMember->mails))
	  foreach($newMember->mails as $value) {
	    if($mails !== '')
	      $mails .= '<br/>';
	    $mails .= $value->mail;
	  }
	$filter->register_var('mail',$mails);

	$webs = '';
	if($newMember->webs !== null && is_array($newMember->webs))
	  foreach($newMember->webs as $value) {
	    if($webs !== '')
	      $webs .= '<br/>';
	    $webs .= $value->url;
	  }
	$filter->register_var('web',$webs);

	$_SESSION['out']->content = $filter->filter_file('modify_member_data.html');
      }
    } else {
      $newMember = new Member();
      $newMember->load_member(intval($params['member']));
      $newMember->login = $params['login'];
      $newMember->name = $params['name'];
      $newMember->firstSurname = $params['sname1'];
      $newMember->secondSurname = $params['sname2'];
      $newMember->dni = new DNI($params['dni']);
      $newMember->address = $params['address'];

      $newMember->delete_telephone($newMember->telephones);
      foreach($params['telephone'] as $value)
	$newMember->add_telephone(intval($value));

      $newMember->delete_mail($newMember->mails);
      foreach($params['email'] as $value)
	$newMember->add_mail($value);

      $newMember->delete_web($newMember->webs);
      foreach($params['web'] as $value)
	$newMember->add_web($value);

      $newMember->modify_member();
    }
  }

  // This method delete a membership of an association
  public function cancel_member($params=null) {
    if(!Database::is_database($this->db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $this->db = $_SESSION['db'];
    }

    if (!isset($_SESSION['AssociationManagement']) || !Association::is_association($_SESSION['association']))
      throw new GException(GException::$MODULE_DEP,"AssociationManagement");

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if ($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('MemberManagement', 'cancel_member')) !== false) {
	$member_list = $this->get_member_list($_SESSION['association']);
	$out = '';

	if (is_array($member_list) && count($member_list) > 0)
	  foreach ($member_list as $member) {
	    $filter->register_var('idMember',$member->idMember);
	    $filter->register_var('login',$member->login);
	    $filter->register_var('name',$member->name);
	    $filter->register_var('sname1',$member->firstSurname);
	    $filter->register_var('sname2',$member->secondSurname);
	    $filter->register_var('dni',$member->dni->dni);
	    $filter->register_var('address',$member->address);

	    $telephones = '';
	    if($member->telephones !== null && is_array($member->telephones))
	      foreach($member->telephones as $value) {
		if($telephones !== '')
		  $telephones .= '<br/>';
		$telephones .= $value->number;
	      }
	    $filter->register_var('telephone',$telephones);
	    
	    $mails = '';
	    if($member->mails !== null && is_array($member->mails))
	      foreach($member->mails as $value) {
		if($mails !== '')
		  $mails .= '<br/>';
		$mails .= $value->mail;
	      }
	    $filter->register_var('mail',$mails);

	    $webs = '';
	    if($member->webs !== null && is_array($member->webs))
	      foreach($member->webs as $value) {
		if($webs !== '')
		  $webs .= '<br/>';
		$webs .= $value->url;
	      }
	    $filter->register_var('web',$webs);

	    $out .= $filter->filter_file('cancel_member_entry.html');
	  }
	$filter->register_var('list',$out);
	$filter->register_var('action',$action);
	$_SESSION['out']->content = $filter->filter_file('cancel_member.html');
      }
    } else {
      if (!is_array($params) ||
	  (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association'])))
	throw new GException(GException::$VAR_TYPE);

      foreach ($params as $idMember) {
	if(Member::exists(intval($idMember))) {
	  $this->db->connect();
	  $this->db->execute("delete from memberAssociation where idMember=".$idMember." and idAssociation=".
			     $_SESSION['association']->idAssociation);
	}
      }
    }
  }

  // This method checks if the member is a member of the association passed as parameter.
  public function is_member_of($newMember,$assoc=null) {
    if(!Database::is_database($this->db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $this->db = $_SESSION['db'];
    }

    if($newMember === null || !Member::is_member($newMember))
      throw new GException(GException::$VAR_TYPE);

    if($assoc === null || (!is_int($assoc) && !Association::is_association($assoc))) {
      if(!isset($_SESSION['association']) || !Association::is_association($assoc))
	throw new GException(GEsception::$VAR_TYPE);
      $assoc = $_SESSION['association'];
    } else if(is_int($assoc) && !Association::exists($assoc))
      throw new GException(GEsception::$VAR_TYPE);

    $consult = "select * from memberAssociation where idMember=".$newMember->idMember." and idAssociation=";
    if(is_int($assoc))
      $consult .= $assoc;
    else
      $consult .= $assoc->idAssociation;

    $this->db->connect();
    $this->db->consult($consult);
    if($this->db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($this->db->numRows() === 1);
  }  

  // Check if the parameter passed is an object of the type Association.
  public static function is_member_management($mbr){
    if(is_object($mbr) && (get_class($mbr) == "MemberManagement" || is_subclass_of($mbr, "MemberManagement")))
      return true;
    return false;
  }
}
?>
