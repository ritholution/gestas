<?php
/**
 * guserexception.php
 *
 * Description
 * Class for throw user exceptions with a custom message.
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

class GUserException extends GException{
  private $errorType=1;

  // Types of fail
  static $UNKNOWN=-1;
  static $USER_UNKNOWN=0;
  static $USER_PERM=1;
  static $IS_MEMBER=2;
  static $PASS_EQ_FAIL=3;
  static $LOGIN_FAIL=4;
  static $REQUEST_EXISTS=5;
  static $USER_EXISTS=6;

  // Construct of the class.
  public function __construct($newErrorType=-1,$addMsg=null){
    switch($newErrorType){
    case 0:
      $this->message = gettext("Error en la autenticación. Usuario o contraseña inválidos.");
      break;
    case 1:
      $this->message = gettext("El usuario no tiene permisos para utilizar el recurso seleccionado.");
      break;
    case 2:
      $this->message = gettext("El usuario ya está dado de alta en la asociación.");
      break;
    case 3:
      $this->message = gettext("Las contraseñas no coinciden.");
      break;
    case 4:
      $this->message = gettext("Usuario o contraseña incorrectos.");
      break;
    case 5:
      $this->message = gettext("La peticion ya había sido realizada.");
      break;
    case 6:
      $this->message = gettext("El usuario ya existe.");
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
