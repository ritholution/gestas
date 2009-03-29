<?php
/**
 * gdatabaseexception.php
 *
 * Description
 * Class for throw exceptions with a custom message.
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

class GDatabaseException extends Exception{
  private $errorType=1;

  // Types of fail
  static $UNKNOWN=-1;
  static $DB_CONN=0;
  static $DB_TYPE=1;
  static $DB_CONSULT=2;
  static $DB_INTEGRITY=3;
  static $DB_SEL=4;
  static $DB_CLOSE=5;
  static $DB_DUPLICATE=6;
  static $DB_EXTERN=7;

  // Construct of the class.
  public function __construct($newErrorType=-1,$addMsg=null){
    switch($newErrorType){
    case 0:
      $this->message = gettext("Error de conexión a la base de datos.");
      break;
    case 1:
      $this->message = gettext("Tipo de base de datos no definido.");
      break;
    case 2:
      $this->message = gettext("Error en la consulta de la base de datos.");
      break;
    case 3:
      $this->message = gettext("Error en la integridad de la base de datos.");
      break;
    case 4:
      $this->message = gettext("Error al seleccionar la base de datos.");
      break;
    case 5:
      $this->message = gettext("Error al cerrar la conexión con la base de datos.");
      break;
    case 6:
      $this->message = gettext("Entrada en la base de datos ya existente.");
      break;
    case 7:
      $this->message = gettext("Error al insertar en la base de datos. Falta clave externa.");
      break;
    default:
      // Unknown error
      $this->message = gettext("Error desconocido.");
      break;
    }

    if($addMsg != null && is_string($addMsg))
      $this->message .= " ".$addMsg;
  }
}
?>