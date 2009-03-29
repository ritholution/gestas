<?php
/**
 * log.php
 *
 * Description
 * This file represent a log entry.
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

require_once("date.php");
require_once("gexception.php");
require_once("gdatabaseexception.php");

class Log {
  private $idLog = -1;
  private $logText;
  private $logDate;

  // Constructor of the class
  function __constructor($newLogText=null, $newLogDate=null) {
    if(is_string($newLogText))
      $this->logText = $newLogText;

    if(Date::is_date($newLogDate))
      $this->logDate = $newLogDate;
  }

  // This method obtains the value of the internal variables.
  public function __get($var) {
    switch ($var) {
    case "logText":
    case "logDate":
      return $this->$var;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }
  }

  // This method sets the value of the internal variables
  public function __set($var, $value) {
    switch ($var) {
    case "logText":
      if($value == null || is_string($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    case "logDate":
      if($value != null && Date::is_date($value))
	$this->$var = $value;
      else
	throw new GException(GException::$VAR_TYPE);
      break;
    default:
      throw new GException(GException::$VAR_UNKNOWN);
    }

    return true;
  }

  // Insert a log in the database
  public function insert_log ($db, $newLog=null) {
    if(!Database::is_database($db))
      throw new GException(GException::$VAR_TYPE);

    if ($newLog != null) {
      if (Log::is_log($newLog)) {
	$newLog->insert_log($db);
      } else {
	throw new GException(GException::$VAR_TYPE);
      }
    } else {
      // If the log is in the database we update it.
      if($this->idLog != -1)
	$this->modify_log($db);
      else {
	// Insert the log entry.
	$query = sprintf("INSERT INTO logs VALUES (%d, %d, %s);",
			 $this->idLog,
			 $this->logDate->date,
			 $this->logText);
	$db->execute($query);
      }
    }
    return true;
  }

  // This function modify the log entry in the database
  public function modify_log ($db, $newLog=null) {
    if (!Database::is_database($db))
      throw new GException(GException::$VAR_TYPE);

    if ($newLog != null) {

      if (Log::is_log($newLog)) {
	$newLog->modify_log($db);
      } else {
	throw new GException(GException::$VAR_TYPE);
      }

    } else {

      if ($this->idLog == -1)
	throw new GDatabaseException(GDatabaseException::$DB_DUPLICATE);
      else {
	// Update the database entry.
	$query = sprintf("UPDATE logs SET fecha=%d, textoLog='%s' WHERE idLog=%d);",
			 $this->logDate->date,
			 $this->logText,
			 $this->idLog);
	$db->execute($query);
      }

    }

    return true;
  }

  // This method delete a log entry from the database
  public function delete_log ($db, $newIdLog=-1) {
    if(!Database::is_database($db))
      throw new GException(GException::$VAR_TYPE);

    if(is_integer($newIdLog) && $newIdLog > 0)
      $this->idLog = $newIdLog;

    // Delete from the database de log entry by $this->idLog.
    $query = sprintf("DELETE FROM logs WHERE idLog=%d",
		     $this->idLog);
    $db->execute($query);

    return true;
  }

  // This static method check if the parameter passed is an object of the type Log
  public static is_log($newLog) {
    if(is_object($newLog) && (get_class($newLog) == "Log" || is_subclass_of($newLog, "Log")))
      return true;
    return false;
  }
}
?>