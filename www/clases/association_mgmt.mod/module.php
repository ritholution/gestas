<?php
/**
 * module.php
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

require_once("request.php");
require_once($_SESSION['class_base']."/gexception.php");
require_once($_SESSION['class_base']."/association.php");
require_once($_SESSION['class_base']."/database.php");
require_once($_SESSION['class_base']."/user.php");

class AssociationManagement{
  private $association=null; // Association active
  private $assocs=null; // Associations of the application
  private $members=null; // List of members
  private $fundationalMembers=null; // List of fundational members
  private $modules=null; // Active and inactive modules (plugins) of the association

  // Constructor of the class
  public function __construct($newAssociation=null, $newMembers=null,
			      $newFundationalMembers=null, $db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newAssociation !== null && Association::is_association($newAssociation))
      $this->load_association($newAssociation);
    else if(isset($_SESSION['association']) && $_SESSION['association'] !== null &&
	    Association::is_association($_SESSION['association']))
      $this->load_association($_SESSION['association']);

    if($newMembers !== null && is_array($newMembers))
      $this->members = $this->check_members($newMembers);

    if($newFundationalMembers !== null && is_array($newFundationalMembers)) {
      $this->fundationalMembers = $this->check_members($newFundationalMembers);
      $this->addMembers($this->fundationalMembers);
    }

    if(!isset($_SESSION['association']))
      $this->update_assocs($db);
  }

  // This method return the value of the accesible class variables when it's
  // not permited to access directly to them.
  public function __get($var) {
    switch($var){
    case "association":
    case "assocs":
    case "members":
    case "fundationalMembers":
    case "modules":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method set the value of the accesible class variables when it's not
  // permited to access directly to them.
  public function __set($var,$value) {
    switch($var){
    case "association":
      if($value === null || !Association::is_association($newAssoc))
	throw new GException(GException::$VAR_TYPE);
      $this->load_association($value);
      break;
    case "assocs":
    case "members":
    case "fundationalMembers":
    case "modules":
      throw new GException(GException::$VAR_ACCESS);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method creates an association and saves it to DB.
  public function new_association($params){
    if($params === null) {
      // Generate the html output.
      $_SESSION['out']->content = RequestAssoc::html_request();
    } else {
      // Check the params.
      if(!is_array($params) || $params['name'] == null  || $params['nif'] == null ||
	 $params['year'] == null || $params['hq'] == null /*|| $params['webs'] == null*/ )
	throw new GException(GException::$VAR_TYPE);

      if(!isset($_SESSION['filter']))
	$_SESSION['filter'] = new TemplateFilter();
      $filter = $_SESSION['filter'];
      // Insert the requests and catch possible exceptions(From User or from RequestAssoc).
      try {
	if($params["extended"]!="extended"){
	  $user_id=$_SESSION['user']->idUser;
	}else{
	  $newUser= new User();
	  if(!$newUser->exists_login($params["login"]))
	    $newUser->new_user($params);

	  if($newUser->exists_login($params["login"])){
	    $newUser->load_user_by_login($params["login"]);
	    if(!$newUser->check_password($params['password']))
	      throw new GUserException(GUserException::$USER_EXISTS);
	    $user_id=$newUser->idUser;
	  }
	}
	$request = new RequestAssoc($params['name'], $params['nif'],
				    $params['year'], $params['hq'],null/*$params['webs']*/,$user_id);
	if(isset($user_id) && is_int($user_id) && User::exists($user_id) && !$request->exists_request()) {
	  $request->insert_db();
	  $filter->register_var('success',gettext("Petición enviada correctamente"));
	  $_SESSION['out']->content = $filter->filter_file('success.html');
	} else {
	  if(!isset($user_id) || $user_id <= 0){ $filter->register_var('success',gettext("UserID sin valor, error de autenticacion o registro."));}else{
	    $filter->register_var('success',gettext("Petición ya existente"));}
	  $_SESSION['out']->content = $filter->filter_file('success.html');
	}
      } catch(GException $e) {
	// Insert log
	$filter->register_var('msg',$e->getOutMessage());
	$_SESSION['out']->content = $filter->filter_file('recover_exception.html');
      }
    }
  }

  public function load_association($params) {
    // Check the database connection
    if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
      throw new GException(GException::$VAR_TYPE);
    $db = $_SESSION['db'];

    if($params !== null && is_array($params) && isset($params[0])) {
      $newAssoc = new Association();
      if(is_string($params[0]))
	$newAssoc->load_association(intval($params[0]));
      else if(is_int($params[0]))
	$newAssoc->load_association($params[0]);
    } else if($params !== null && Association::is_association($params))
      $newAssoc = clone $params;
    else
      throw new GException(GException::$VAR_TYPE);
    
    // We load the association
    if($newAssoc === null || !Association::is_association($newAssoc)) {
      if(!isset($_SESSION['association'])) {
	$db->connect();
	$db->consult("select * from association");
	if($db->numRows() === 0) {
	  // There's no association created, so we create a new default
	  // one and we store it in the database.
	  $this->association = new Association('Initial association','',2008,'My Home');
	  $this->insert_assoc($db);
	  $_SESSION['association'] = $this->association;
	} else if($db->numRows() === 1) {
	  $load = $db->getRow();
	  $this->association = new Association();
	  $this->association->load_association($load['idAssociation']);
	  $_SESSION['association'] = $this->association;
	} else
	  throw new GException(GException::$VAR_TYPE);
      } else if($_SESSION['association'] !== null &&
		Association::is_association($_SESSION['association']))
	$this->association = $_SESSION['association'];
      else
	throw new GException(GException::$VAR_TYPE);
    } else {
      $this->association = clone $newAssoc;
      $_SESSION['association'] = clone $this->association;
    }

    $this->load_members($db);
    $this->load_plugins($db);
  }

  // This method load the members of an association
  public function load_members($db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->association !== null && Association::is_association($this->association) &&
       $this->association->exists()) {
      $db->consult("select * from memberAssociation where idAssociation=".
		   $this->association->idAssociation);
      $memRows = $db->rows;
      for($i=0; $i < count($memRows); $i++) {
	$newMember = new Member();
	$newMember->load_member(intval($memRows[$i]['idMember']));
	$this->members[] = $newMember;
	if(intval($memRows[$i]['isFounder']) === 1)
	  $this->fundationalMembers[] = $newMember;
      }
    }
  }

  // This method store the members of an association
  public function store_members($db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // Insertion in the database
    if($this->association !== null && Association::is_association($this->association) &&
       $this->association->exists()) {
      if($this->fundationalMembers !== null && is_array($this->fundationalMembers)) {
	foreach($this->fundationalMembers as $value) {
	  if(Member::is_member($value)) {
	    if(!$value->exists())
	      $value->insert_member($db);

	    $db->consult("select * from memberAssociation where idAssociation=".$this->idAssociation.
			 " and idMember=".$value->idMember);
	    if ($db->numRows() === 0)
	      // TODO: Check if the fundationalMember is active
	      $db->execute("insert into memberAssociation values (".$value->idMember.",".
			   $this->association->idAssociation.",1,1)");
	  } else
	    throw new GException(GException::$CLASS_INTEGRITY);
	}
      }

      if($this->members !== null && is_array($this->members)) {
	foreach($this->members as $value) {
	  if(Member::is_member($value)) {
	    if(!$value->exists())
	      $value->insert_member($db);

	    $db->consult("select * from memberAssociation where idAssociation=".$this->idAssociation.
			 " and idMember=".$value->idMember);
	    if ($db->numRows() === 0)
	      $db->execute("insert into memberAssociation values (".$value->idMember.",".
			   $this->association->idAssociation.",0,1)");
	  } else
	    throw new GException(GException::$CLASS_INTEGRITY);
	}
      }
    }
  }

  // This method add several members to an association.
  public function addMembers($newMembers=null, $db=null){
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newMembers !== null) {
      if(Member::is_member($newMembers))
	$this->add_member($newMembers,$db);
      else if(is_array($newMembers)) {
	foreach($newMembers as $value)
	  if(Member::is_member($value))
	    $this->add_member($value,$db);
      } else
	throw new GException(GException::$VAR_TYPE);
    }
  }

  // This method delete several members of an association.
  public function delete_members($newMember=null, $db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newMembers !== null) {
      if(Member::is_member($newMembers))
	$this->drop_member($newMembers,$db);
      else if(is_array($newMembers)) {
	foreach($newMembers as $value)
	  if(Member::is_member($value))
	    $this->drop_member($value,$db);
      } else
	throw new GException(GException::$VAR_TYPE);
    }
  }

  // This method add a member to an association
  public function add_member($newMember, $db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newMember === null || !Member::is_member($newMember))
      throw new GException(GException::$VAR_TYPE);

    if($this->association !== null && Association::is_association($this->association)) {
      if(!$this->association->exists(-1,$db))
	$this->association->insertdb($db);
      
      if(!$newMember->exists(-1,$db))
	$newMember->insert_member($db);

      $this->members[] = $newMember;

      $db->connect();
      $db->execute("insert into memberAssociation values(".
		   $newMember->idMember.",".$this->association->idAssociation.
		   ",0,1)");
    }
  }

  // This method drop a member from an association
  public function drop_member($newMember, $db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newMember === null || !Member::is_member($newMember) ||
       !$newMember->exists(-1,$db))
      throw new GException(GException::$VAR_TYPE);

    if($this->association !== null && Association::is_association($this->association)
       && $this->association->exists(-1,$db)) {
      for($i=0;$i < count($this->members);$i++) {
	if($this->members[$i]->idMember === $newMember->idMember) {
	  $this->members[$i] = null;
	  $db->connect();
	  $db->execute("delete from memberAssociation where idMember=".$newMember->idMember.
		       " and idAssociation=".$this->association->idAssociation);
	}
      }
    }
  }

  // This method insert all the data related with an association to the database.
  public function insert_assoc($db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // --- TODO ---
  }

  // This method update all the data related with an association to the database.
  public function modify_association($params=null) {
    if (!isset($_SESSION['AssociationManagement']))
      throw new GException(GException::$MODULE_DEP,"AssociationManagement");

    if(!isset($_SESSION['user']) || !User::is_user($_SESSION['user']) ||
       !$_SESSION['user']->isAuthenticated)
      throw new GUserException(GUserException::$USER_PERM);
    if(!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
      throw new GException(GException::$ASSOCIATION_UNKNOWN);
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if ($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('AssociationManagement', 'modify_association')) !== false) {
	$newAssoc = new Association();
	$newAssoc->load_association($_SESSION['association']->idAssociation);
	$filter->register_var('action',$action);
	$filter->register_var('idAssociation',$newAssoc->idAssociation);
	$filter->register_var('name',$newAssoc->name);
	$filter->register_var('nif',$newAssoc->nif);
	$filter->register_var('year',$newAssoc->fundationYear);
	$filter->register_var('hq',$newAssoc->headquarters);

	$_SESSION['out']->content = $filter->filter_file('modify_association_data.html');
      }
    } else {
      $newAssoc = new Association();
      $newAssoc->load_association(intval($params['idAssociation']));
      $newAssoc->name = $params['name'];
      $newAssoc->nif = $params['nif'];
      $newAssoc->fundationYear = intval($params['year']);
      $newAssoc->headquarters = $params['hq'];
      $newAssoc->update_db();
    }
  }

  // This method drop all the data related with an association to the database.
  public function drop_assoc($db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // Drop of the database.
    //       $db->execute("delete from memberAssociation WHERE idAssociation=".$this->idAssociation);
    //       // Question: Check if there is some member with no association?
    //       $db->execute("delete from pluginAssociation WHERE idAssociation=".$this->idAssociation);

    // --- TODO ---
  }

  // This method loads the active and inactive modules related to the association
  public function load_plugins($db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    // We load the active modules and plugins
    //     if($this->modules === null || ModuleManagement::is_module_management($this->modules))
    //       $this->modules = new ModuleManagement($this);
    //     else {
    //       $this->modules->load_active_modules($db);
    //       $this->modules->load_inactive_modules($db);
    //     }

    // --- TODO ---
  }

  // this method save the active and inactive plugins of the association.
  public function save_plugins($db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $this->modules->insert_db($db);
    if($this->modules->active !== null && is_array($this->modules->active)) {
      foreach($this->modules as $value){
	$db->consult("select * from pluginAssociation where idAssociation=".$this->idAssociation.
		     " and idPlugin=".$value->idPlugin);
	if ($db->numRows() === 0)
	  $db->execute("insert into pluginAssociation values (".$value->idPlugin.",".
		       $this->idAssociation.",1)");
      }
    } else if($this->modules->inactive !== null && is_array($this->modules)) {
      foreach($this->modules as $value){
	$db->consult("select * from pluginAssociation where idAssociation=".$this->idAssociation.
		     " and idPlugin=".$value->idPlugin);
	if ($db->numRows() === 0)
	  $db->execute("insert into pluginAssociation values (".$value->idPlugin.",".
		       $this->idAssociation.",1)");
      }
    }

    // --- TODO ---
  }

  // Gets an html block to select an association
  public function get_assoc_selection($db=null) {
    // Check the database connection
    if($db == null || !Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $nextAction = new Action();
    if(($action = $nextAction->get_id_action_class_method('AssociationManagement','load_association')) !== false) {
      $filter->register_var('action',$action);
      $assocs_out = '';
      $this->update_assocs();

      // This might be a filter
      if($this->assocs !== null && is_array($this->assocs))
 	foreach($this->assocs as $value) {
	  $filter->register_var('idAssociation',$value->idAssociation);
 	  if(isset($_SESSION['association']) && Association::is_association($_SESSION['association']) &&
 	     $_SESSION['association']->idAssociation === $value->idAssociation)
	    $filter->register_var('sel','selected="selected"');
	  else
	    $filter->register_var('sel','');
	  $filter->register_var('name',$value->name);
	  $assocs_out .= $filter->filter_file('association_option.html');
 	}

      $filter->register_var('assocs',$assocs_out);
      $output = $filter->filter_file('association.html');
      return $output;
    }

    return '';
  }

  // This method check the validity of a list of members
  private function check_members($newMembers){
    if(!is_array($newMembers) || $newMembers == null)
      return null;

    $validMembers = null;
    foreach($newMembers as $value)
      if(Members::is_member($value))
	$validMembers[]=$value;

    return $validMembers;
  }

  // This method check if the parameter passed is an object of the type Association.
  public static function is_association_management($assoc){
    if(is_object($assoc) && (get_class($assoc) == "AssociationManagement" ||
			     is_subclass_of($assoc, "AssociationManagement")))
      return true;
    return false;
  }

  public function association_validate($params=null) {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if ($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('AssociationManagement', 'association_validate')) !== false) 																																																																				 	{
	$request_list = $this->signup_list();

	$out = '';
	if(is_array($request_list) && count($request_list) > 0)
	  foreach ($request_list as $rq) {
	    $filter->register_var('idPetition',$rq->idPetition);
	    $filter->register_var('name',$rq->name);
	    $filter->register_var('nif',$rq->nif);
	    $filter->register_var('hq',$rq->headquarters);
	    $filter->register_var('year',$rq->fundationYear);
	    $out .= $filter->filter_file('association_validate_entry.html');
	  }

	$filter->register_var('action',$action);
	$filter->register_var('list',$out);
	$_SESSION['out']->content = $filter->filter_file('association_validate.html');
      }
    } else {
      if (!is_array($params))
	throw new GException(GException::$VAR_TYPE);

      if($params['idPetition'] != null && is_array($params['idPetition']))
	foreach ($params['idPetition'] as $value) {
	  $request = new RequestAssoc();
	  $request->load_db($value);

	  if($params['reqAction'] === "Aprobar") {
	    $assoc = new Association();
	    $assoc->name = $request->name;
	    $assoc->nif = $request->nif;
	    $assoc->fundationYear = intval($request->fundationYear);
	    $assoc->webs = $request->webs;
	    $assoc->headquarters = $request->headquarters;

	    $assoc->insert_db();
	    $id_user=intval($request->userId);
	    $assoc->set_admin($id_user);
	  }

	  $request->delete_db();
	}
    }
  }

  public function signup_list() {
    if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
      throw new GException(GException::$VAR_TYPE);
    $db = $_SESSION['db'];
    
    /*if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
      $filter = $_SESSION['filter'];*/

    $out = '';
    $list = null;

    $db->connect();
    $db->consult("select id from associationRequest");
    if($db->numRows() > 0) {
      $result = $db->rows;

      foreach($result as $row) {
	$request = new RequestAssoc();
	$request->load_db($row['id']); // Implementar load_db(id) pendiente
	$list[] = $request;

	/*$filter->register_var('user',$request->login);
	  $filter->register_var('name',$request->name);
	  $filter->register_var('sname1',$request->sname1);
	  $filter->register_var('sname2',$request->sname2);
	  $filter->register_var('dni',$request->dni->dni);
	  $filter->register_var('address',$request->address);
	  
	  $out .= $filter->filter_file('request_entry.html');*/
      }
    }

    /*if($out !== null)
      $filter->register_var('entrys',$out);
      $_SESSION['out']->content = $filter->filter_file('request_list.html');*/

    return $list;
  }

  // Function that update the association list
  public function update_assocs($db=null) {
    // Check the database connection
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
 	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $db->connect();
    $db->consult("select idAssociation from association");
    $assocRows = $db->rows;
    foreach($assocRows as $value) {
      $tmp = new Association();
      $tmp->load_association(intval($value['idAssociation']),$db);
      $this->assocs[] = $tmp;
    }

    //$_SESSION['assocs'] = $this->assocs;
    if($this->association === null) {
      $this->association = $this->assocs[0];
      $_SESSION['association'] = $this->association;
    }
  }
}
?>
