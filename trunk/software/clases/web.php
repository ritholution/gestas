<?php
/**
 * web.php
 *
 * Description
 * This class implement the Web type object.
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
require_once("project.php");
require_once("association.php");

class Web{
  private $url = null;
  private $idMember = -1;
  private $idContact = -1;
  private $idProject = -1;
  private $idAssociation = -1;
  private $idPetition = -1;
  private $description = '';

  // Construct of the class
  public function __construct($newURL=null, $newDescription=null, $newIdMember=-1,
			      $newIdContact=-1, $newIdProject=-1, $newIdAssociation=-1,
			      $newIdPetition=-1) {
    if(Web::is_web($newURL)) {
      $this->url = $newURL->url;
      $this->idMember = $newURL->idMember;
      $this->idContact = $newURL->idContact;
      $this->idProject = $newURL->idProject;
      $this->idAssociation = $newURL->idAssociation;
      $this->idPetition = $newURL->idPetition;
      $this->description = $newURL->description;
    } else if($newURL !== null && is_string($newURL))
      $this->url = $newURL;

    if(is_int($newIdMember) && $newIdMember > -1 && Member::exists($newIdMember))
	$this->idMember = $newIdMember;

    if(is_int($newIdContact) && $newIdContact > -1 && Contact::exists($newIdContact))
	$this->idContact = $newIdContact;

    if(is_int($newIdProject) && $newIdProject > -1 &&
       Project::exists($newIdProject))
	$this->idProject = $newIdProject;

    if(is_int($newIdAssociation) && $newIdAssociation > -1 &&
       Association::exists($newIdAssociation))
	$this->idAssociation = $newIdAssociation;

    if(class_exists("Request") && is_int($newIdPetition) && $newIdPetition > -1 &&
       Request::exists($newIdPetition))
	$this->idPetition = $newIdPetition;

    if($newDescription !== null && is_string($newDescription))
      $this->description = $newDescription;
  }

  // Method who returns the value of the internal variables
  public function __get($var) {
    switch($var){
    case "url":
    case "idMember":
    case "idContact":
    case "idProject":
    case "idAssociation":
    case "idPetition":
    case "description":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Method to set the value of the internal variables.
  public function __set($var, $value) {
    switch($var){
    case "url":
      if($value !== null && is_string($value))
	$this->$var = $value;
      else if(Web::is_web($value))
	$this->$var = $value->$var;
      else
	throw new GException(GException::$VAR_TYPE);	
      break;
    case "idMember":
      if(is_int($value) && (($value > -1 && Member::exists($value)) || $value === -1))
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
    case "idProject":
      if(is_int($value) && (($value > -1 && Project::exists($value)) || $value === -1))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "idAssociation":
      if(is_int($value) && (($value > -1 && Association::exists($value)) || $value === -1))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);	
      break;
    case "idPetition":
      if(class_exists("Request") && is_int($value) && $value > -1 &&
	 Request::exists($value))
	$this->$var = $value;
      else if(is_int($value) && $value === -1)
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);	
      break;
    case "description":
      if($value === null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);	
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method insert a web in the database.
  public function insert_web($idType,$db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $id = $this->getId($idType);
    $table = $this->getTable($idType);


    if($this->exists_web($idType,$this->url,$id,$db))
      $this->modify_web($idType,$db);
    else {
      $db->connect();
      $db->execute("insert into ".$table." values('".$this->url."','".
		   $this->description."',".$id.")");
    }
  }

  // This method modify a web in the database.
  public function modify_web($idType,$db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $id = $this->getId($idType);
    $table = $this->getTable($idType);

    if($this->exists_web($idType,$this->url,$id,$db)) {
      if($id === -1)
	$this->drop_web($idType,$db);
      else
	$db->execute("update ".$table." set description='".$this->description.
		     "' where url='".$this->url."' and id=".$id);
    }
  }

  // This method drop a web in the database.
  public function drop_web($idType,$db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $id = $this->getId($idType);
    $table = $this->getTable($idType);

    if($this->exists_web($idType,$this->url,$id,$db)) {
      $db->connect();
      $db->execute("delete from ".$table." where url='".$this->url."' and id=".$id);
    }
  }

  // This method load all the data related to an url
  public function load_web($idType, $newURL=null, $newId=-1, $db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($newURL != null && is_string($newURL))
      $this->url = $newURL;

    if($this->url === null || !is_string($this->url))
      throw new GException(GException::$PARAM_MISSING);

    $id = $this->getId($idType);
    $table = $this->getTable($idType);

    if(is_int($newId) && $newId>0)
      $id = $newId;

    if($this->exists_web($idType,$this->url,$id,$db)) {
      $db->connect();
      $db->consult("select description from ".$table." where url='".$this->url.
		   "' and id=".$id);
      $row = $db->getRow();
      $this->description = $row['description'];
      switch($idType) {
      case "member":
	$this->idMember = $id;
	break;
      case "contact":
	$this->idContact = $id;
	break;
      case "project":
	$this->idProject = $id;
	break;
      case "association":
	$this->idAssociation = $id;
	break;
      case "request":
	$this->idPetition = $id;
	break;
      default:
	throw new GException(GException::$UNKNOWN_WEB_TYPE);
      }
    }
  }

  // This method check if the web exists in the database.
  public function exists_web($idType, $newURL=null, $id=-1, $db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $check = $this->url;
    if($newURL != null && is_string($newURL))
      $check = $newURL;

    $checkId = $this->getId($idType);
    if(is_int($id) && $id>0)
      $checkId = $id;;

    return Web::exists($idType,$check,$checkId,$db);
  }

  // This method check if the web exists in the database.
  public static function exists($idType, $newURL=null, $id=-1, $db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_string($newURL))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $consult = " where url='".$newURL."' and id=".$id;

    switch($idType) {
    case 'member':
      $db->consult("select * from webMember".$consult);
      break;
    case 'contact':
      $db->consult("select * from webContact".$consult);
      break;
    case 'project':
      $db->consult("select * from webProject".$consult);
      break;
    case 'association':
      $db->consult("select * from webAssociation".$consult);
      break;
    case 'request':
      $db->consult("select * from webRequest".$consult);
      break;
    default:
      throw new GException(GException::$UNKNOWN_WEB_TYPE);
    }

    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method loads all the webs related with a Request
  public static function load_webs_request($newIdPetition, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdPetition) || $newIdPetition === -1)
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select url from webRequest where id=".$newIdPetition);
    $rows = $db->rows;
    $result = null;
    if(count($rows) > 0)
      foreach($rows as $row) {
	$newWeb = new Web();
	$newWeb->load_web("request",$row['url'],$newIdPetition,$db);
	$result[] = $newWeb;
      }

    return $result;
  }

  // This method loads all the webs related with a Member
  public static function load_webs_member($newIdMember, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdMember) || $newIdMember === -1)
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select url from webMember where id=".$newIdMember);
    $rows = $db->rows;
    $result = null;
    if(count($rows) > 0)
      foreach($rows as $row) {
	$newWeb = new Web();
	$newWeb->load_web("member",$row['url'],$newIdMember,$db);
	$result[] = $newWeb;
      }

    return $result;
  }

  // This method return a list of urls which correspond with the newIdAssoc given.
  public static function load_webs_assoc($newIdAssoc, $db=null) {
    if(!Database::is_database($db)) {
      if(!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if(!is_int($newIdAssoc) || $newIdAssoc === -1)
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select url from webAssociation where id='".$newIdAssoc."'");
    $rows = $db->rows;
    $result = null;
    if(count($rows) > 0)
      foreach($rows as $row) {
	$newWeb = new Web();
	$newWeb->load_web("association",$row['url'],$newIdAssoc,$db);
	$result[] = $newWeb;
      }
    
    return $result;
  }

  // This method return the id of the relation
  private function getId($webTable) {
    switch($webTable) {
    case "member":
      return $this->idMember;
    case "contact":
      return $this->idContact;
    case "project":
      return $this->idProject;
    case "association":
      return $this->idAssociation;
    case "request":
      return $this->idPetition;
    default:
      throw new GException(GException::$UNKNOWN_WEB_TYPE);
    }
  }

  // This method return the table name of the relation
  private function getTable($telTable) {
    switch($telTable) {
    case "member":
      return "webMember";
    case "contact":
      return "webContact";
    case "project":
      return "webProject";
    case "association":
      return "webAssociation";
    case "request":
      return "webRequest";
    default:
      throw new GException(GException::$UNKNOWN_WEB_TYPE);
    }
  }

  // Method to check if the variable passed is an object of type Web
  public static function is_web($web) {
    if(is_object($web) && (get_class($web) === "Web" || is_subclass_of($web, "Web")))
      return true;
    return false;
  }
}
?>
