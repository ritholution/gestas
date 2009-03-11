<?php
/**
 * project.php
 *
 * Description
 * This class represents a project of the association.
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
require_once("date.php");
require_once("database.php");
require_once("member.php");

class Project{
  private $idProject=-1;
  private $projectName=null;
  private $dateBegin=null;
  private $dateEnd=null;

  // Constructor of the class
  function __construct($newProjectName=null, $newDateBegin=null, $newDateEnd=null) {
    if(is_string($newProjectName) && $newProjectName != null)
      $this->projectName = $newProjectName;

    if($newDateBegin !== null && Date::is_date($newDateBegin))
      $this->dateBegin = $newDateBegin;
    else if($newDateBegin !== null && is_int($newDateBegin))
      $this->dateBegin = new Date($newDateBegin);

    if($newDateEnd !== null && Date::is_date($newDateEnd))
      $this->dateEnd = $newDateEnd;
    else if($newDateEnd !== null && is_int($newDateEnd))
      $this->dateEnd = new Date($newDateEnd);
  }

  // Method to get the internal variables from outside
  public function __get($var) {
    switch($var){
     case "idProject":
     case "projectName":
     case "dateBegin":
     case "dateEnd":
       return $this->$var;
       break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Method to set the internal variables from outside checking the variable type.
  public function __set($var, $value) {
    switch($var){
    case "idProject":
      throw new GException(GException::$VAR_ACCESS);
    case "projectName":
      if(is_string($value))
 	$this->$var=$value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "dateBegin":
    case "dateEnd":
      if($value !== null && Date::is_date($value))
 	$this->$var=$value;
      else if($value !== null && is_int($value))
 	$this->$var=new Date($value);
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // Checks if the project exists in the database.
  public function exists_project($newIdProject=-1, $db=null){
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    $check = $this->idProject;
    if(is_int($newIdProject) && $newIdProject > -1)
      $check = $newIdProject;

    return Project::exists($check);
  }

  // Checks if the project exists in the database.
  public static function exists($newIdProject=-1, $db=null){
    if($db == null || !Database::is_database($db)){
      if(!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db=$_SESSION['db'];
    }

    if(!is_int($newIdProject))
      throw new GException(GException::$VAR_TYPE);

    $db->connect();
    $db->consult("select * from project where idProject=".$newIdProject);
    if($db->numRows() > 1)
      throw new GDatabaseException(GDatabaseException::$DB_INTEGRITY);

    return ($db->numRows() === 1);
  }

  // This method loads a Project from de database given the id of the TypeUser
  public function load_project($newIdProject=-1, $db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $load = $this->idProject;
    if(is_int($newIdProject) && $newIdProject > -1)
      $load = $newIdProject;

    if($this->exists_project($load)) {
      $this->idProject = $load;
      $db->connect();
      $db->consult("select * from project where idProject=".$this->idProject);
      $row = $db->getRow();
      $this->projectName = $row['projectName'];
      $this->dateBegin = new Date(intval($row['dateBegin']));
      $this->dateEnd = new Date(intval($row['dateEnd']));
    }
  }

  // This method insert a Project into the database
  public function insert_project($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_project())
      $this->modify_project($db);
    else {
      $db->connect();
      $db->execute("insert into project(projectName,dateBegin,dateEnd) values('".
		   $this->projectName."',".$this->dateBegin->date.",".
		   $this->dateEnd->date.")");
      $this->idProject = $db->id;
    }
  }

  // This method update a Project in the database
  public function modify_project($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $db->connect();
    if($this->exists_project()) {
      $db->connect();
      $db->execute("update project set projectName='".$this->projectName."', dateBegin=".
		   $this->dateBegin->date.", dateEnd=".$this->dateEnd->date." where idProject=".
		   $this->idProject);
    }
  }

  // This method drop a Project from the database
  public function drop_project($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    if($this->exists_project()) {
      $db->connect();
      $db->execute("delete from project where idProject=".$this->idProject);
    }
  }

  // This method gets all the members related to a project
  public function getMembers($db=null) {
    if ($db == null || !Database::is_database($db)) {
      if (!isset($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $members = null;

    if($this->exists_project()) {
      $db->consult("select * from projectMember where idProject=".$this->idProject);
      $rows = $db->rows;
      foreach($rows as $row) {
	$newMember = new Member();
	$newMember->load_member(intval($row['idMember']));
	$members[] = $newMember;
      }
    }

    return $members;
  }

  // Method to check if the parameter passed is an object of the type TypeUser
  public static function is_project($var){
    if(is_object($var) && (get_class($var) === "Project" || is_subclass_of($var, "Project")))
      return true;
    return false;
  }
}
?>