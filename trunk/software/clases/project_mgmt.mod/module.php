<?php
/**
 * module.php
 *
 * Description
 * This modulw manage the projects of the association.
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
require_once($_SESSION['class_base']."/output.php");

class ProjectManagement {
  private $db = null;

  public function __construct($db=null) {
    if ($db == null | !Database::is_database($db)) {
      if (!isset($_SESSION['db']) || !Database::is_database($_SESSION['db']))
	throw new GException(GException::$VAR_TYPE);
      $db = $_SESSION['db'];
    }

    $this->db = $db;
  }


  public function __get($var) {
    switch($var){
    case "db":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }


  public function __set($var,$value) {
    switch($var){
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }


  public function list_projects($association=null) {
    if ($association == null || !Association::is_association($association)) {
      if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);
      $association = $_SESSION['association'];
    }

    // Get a list of projects from given association.
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    $out = '';
    $project_list = $this->get_project_list($association);
    if (is_array($project_list)) {
      foreach ($project_list as $project) {
	$filter->register_filter('default');

	$filter->register_var('projectName', $project->projectName);
	$filter->register_var('dateBegin', $project->dateBegin);
	$filter->register_var('dateEnd', $project->dateEnd);

	$out .= $filter->filter_file('project_entry.html');
      }
    }

    $filter->register_var('entrys', $out);
    $_SESSION['out']->content = $filter->filter_file('project_list.html');
  }


  public function cancel_project($params=null) {
    if ($params === null) {
      $nextAction = new Action();
      if (($action = $nextAction->get_id_action_class_method('ProjectManagement', 'cancel_project')) !== false) {
	$_SESSION['parAction'] = $action;

	if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	  throw new GException(GException::$ASSOCIATION_UNKNOWN);

	$project_list = $this->get_project_list($_SESSION['association']);

	if (is_array($project_list) && count($project_list) > 0)
	  foreach ($project_list as $project) {
	    $project_html_list[$project->idProject] = "<td>".$project->projectName."</td><td>".$project->dateBegin." ".$project->dateEnd."</td>";
	  }
	$_SESSION['list'] = $project_html_list;
	$_SESSION['out']->content = file_get_contents("php://filter/read=signup_validate/resource=".$_SESSION['dir_base']."/templates/signup_validate.html");
      }
    } else {
      if (!is_array($params) ||
	  (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association'])))
	throw new GException(GException::$VAR_TYPE);

      foreach ($params as $p => $val) {
	$project = new Project();
	$project->load_project($p);
	$project->drop_project();
      }
    }
  }


  public function project_request($params=null) {
    if(!isset($_SESSION['filter']))
      $_SESSION['filter'] = new TemplateFilter();
    $filter = $_SESSION['filter'];

    if($params === null) {
      // Generate the html output.
      $nextAction = new Action();
      if(($action = $nextAction->get_id_action_class_method('ProjectManagement','project_request')) !== false) {
	$filter->register_var('action',$action);
	$_SESSION['out']->content = $filter->filter_file('project_request.html');
      }
    } else {
      // Check the params.
      if(!is_array($params) || $params['projectName'] == null ||
	 $params['dateBegin'] == null || $params['dateEnd'] == null ||
	 !isset($_SESSION['association']) || $_SESSION['association'] === null ||
	 !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);

      // Insert the project
      $project = new Project($params['projectName'], $params['dateBegin'], $params['dateEnd']);
      $project->insert_project();

      $filter->register_var('success',gettext("Proyecto registrado correctamente"));
      $_SESSION['out']->content = $filter->filter_file('success.html');
    }
  }

  public function get_project_list($association=null) {
    if ($association == null || !Association::is_association($association)) {
      if (!isset($_SESSION['association']) || !Association::is_association($_SESSION['association']))
	throw new GException(GException::$VAR_TYPE);
      $association = $_SESSION['association'];
    }

    $project_list = null;
    $this->check_connection();
    $this->db->consult("SELECT idMember FROM memberAssociation WHERE".
		       " idAssociation=" . $association->idAssociation);
    if ($this->db->numRows() > 0) {
      $rowsIdMember = $this->db->rows;
      foreach ($rowsIdMember as $rowMember) {
	$idMember = intval($rowMember['idMember']);
	$this->db->consult("SELECT idProject FROM projectMembers WHERE".
			   " idMember=" . $idMember);
	if ($this->db->numRows() > 0) {
	  $rowsIdProject = $this->db->rows;
	  foreach ($rowsIdProject as $i => $rowProject) {
	    $idProject = intval($rowProject['idProject']);
	    $project_list[] = new Project();
	    $project_list[$i]->load_project($idProject);
	  }
	}
      }
    }

    return $project_list;
  }
}

?>