<?php
/**
 * gexception.php
 *
 * Description
 * Class for throw exceptions with a custom message.
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

class GException extends Exception{
  private $errorType=1;

  // Types of fail
  static $UNKNOWN=-1;
  static $VAR_ACCESS=0;
  static $VAR_UNKNOWN=1;
  static $VAR_TYPE=2;
  static $ENC_UNKNOWN=3;
  static $SYS_NOT_CONFIG=4;
  static $SESSION_START=5;
  static $FILTER_REGISTER=6;
  static $PARAM_MISSING=7;
  static $OBJECT_UNKNOWN=8;
  static $METHOD_UNKNOWN=9;
  static $MENU_UNKNOWN=10;
  static $OBJ_TYPE_UNKNOWN=11;
  static $ACTION_UNKNOWN=12;
  static $MODULE_LOAD_FAIL=13;
  static $CLASS_INTEGRITY=14;
  static $MODULE_DEP=15;
  static $ASSOCIATION_UNKNOWN=16;
  static $UNKNOWN_WEB_TYPE=17;

  // Construct of the class.
  public function __construct($newErrorType=-1,$addMsg=null){
    switch($newErrorType){
    case 0:
      $this->message = gettext("No puede acceder a la variable.");
      break;
    case 1:
      $this->message = gettext("Variable desconocida.");
      break;
    case 2:
      $this->message = gettext("Parámetro incorrecto.");
      break;
    case 3:
      $this->message = gettext("Algoritmo de encriptación no disponible.");
      break;
    case 4:
      $this->message = gettext("Error al acceder a la configuración del sistema.");
      break;
    case 5:
      $this->message = gettext("No se ha podido iniciar la sesion.");
      break;
    case 6:
      $this->message = gettext("Error en el registro del filtro html.");
      break;
    case 7:
      $this->message = gettext("Error por falta de párametro necesario.");
      break;
    case 8:
      $this->message = gettext("Objeto asociado a la acción desconocido.");
      break;
    case 9:
      $this->message = gettext("El objeto referido no tiene el método solicitado.");
      break;
    case 10:
      $this->message = gettext("Error al obtener el menú. Menú inexistente en el sistema.");
      break;
    case 11:
      $this->message = gettext("Error al obtener el tipo de objeto. Tipo de objeto desconocido.");
      break;
    case 12:
      $this->message = gettext("Acción desconocida.");
      break;
    case 13:
      $this->message = gettext("El modulo no se ha podido cargar correctamente.");
      break;
    case 14:
      $this->message = gettext("Error en la inegridad de la clase.");
      break;
    case 15:
      $this->message = gettext("Error de dependencias. Un módulo que depende del actual no esta cargado.");
      break;
    case 16:
      $this->message = gettext("La asociación no existe.");
      break;
    case 17:
      $this->message = gettext("El tipo de web seleccionado no existe.");
      break;
    default:
      // Unknown error
      $this->message = gettext("Error desconocido.");
      break;
    }

    if($addMsg != null && is_string($addMsg))
      $this->message .= " ".$addMsg;
  }

  public function getOutMessage() {
    $msg = gettext($this->getMessage());

    if(isset($_SESSION['config']) && $_SESSION['config'][1]['debug_mode'] === '1')
      $msg = gettext('Error in '.$this->getFile().' at line '.$this->getLine().': '.$this->getMessage().
		     '<br/>'.$this->getTraceAsString());

    return $msg;
  }
}
?>
