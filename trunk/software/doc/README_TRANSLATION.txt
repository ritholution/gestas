******************************************************************************
* README_TRANSLATION.txt						     *
*									     *
* Description								     *
* In this file we explain how to make a new translation.		     *
*									     *
* copyright (c) 2008-2009 OPENTIA s.l. (http://www.opentia.com)		     *
* 	    		  	       					     *
* This file is part of GESTAS (http://gestas.opentia.org)		     *
*									     *
* GESTAS is free software: you can redistribute it and/or modify	     *
* it under the terms of the GNU Affero General Public License as	     *
* published by the Free Software Foundation, either version 3 of the	     *
* License, or (at your option) any later version.   	      	 	     *
*									     *
* This program is distributed in the hope that it will be useful,	     *
* but WITHOUT ANY WARRANTY; without even the implied warranty of	     *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the		     *
* GNU General Public License for more details.		    		     *
*     	      	     	     	      					     *
* You should have received a copy of the GNU General Public License	     *
* along with this program.  If not, see <http://www.gnu.org/licenses/>.	     *
* 	     	  	       	    					     *
******************************************************************************

Este README contiene informacion de como realizar las traducciones del programa y sus actualizaciones. GESTAS esta desarrollado para utilizar gettext, con lo que las traducciones se realizaran a traves de los ficheros .po correspondientes a la traduccion.

Para crear el fichero template del gettext que servira para todas las traducciones hay que ejecutar el siguiente comando:

	$ xgettext ficheros.php

	donde ficheros.php son los ficheros que tienen las cadenas de gettext a traducir. Si no lo sabes bien siempre puedes ejecutar el siguiente comando:

	$ for i in `find -name *.php directorio`; do
		xgettext $i
	  done
	
	donde directorio es el directorio donde esta el codigo fuente del proyecto.

Para crear la traduccion para un nuevo idioma hay que seguir los siguientes pasos:

1.- Se crea el arbol de directorios locales/$LANG/LC_MESSAGES, donde $LANG es el idioma a crear (por ejemplo el es_ES para el castellano de Espanya).
2.- Se genera el fichero .po mediante el comando:
  $ msginit message.pot -l lenguaje
	donde message.pot es la plantilla del proyecto para gettext y que se ha tenido que generar antes de ejecutar el comando. El parametro lenguaje sigue siendo el lenguaje que vamos a incluir.
3.- El comando anterior ha generado un archivo lenguaje.po, por lo que hay que moverlo a $LENGUAJE/LC_MESSAGES/message.po
4.- Se va al directorio $LENGUAJE/LC_MESSAGES, se edita el fichero message.po, se cambian las variables de cabecera que hagan falta.
5.- Se hace la traduccion de las cadenas que van a ir al gettext.
6.- Cuando se termine la traduccion se ejecuta el siguiente comando para actualizarla en GESTAS:
	$ msgfmt message.po

Una vez que la traduccion este creada se querra actualizar conforme se vaya avanzando en el desarrollo del programa. Para eso, y suponiendo que la plantilla ya esta actualizada, hay que ejecutar los siguientes comandos:

	$ msgmerge message.pot message.po > message2.po
	$ mv message2.po message.po

	suponiendo que message.pot es el fichero de la plantilla y message.po es el de la traduccion. En este caso el nuevo fichero generado mantiene las traducciones ya realizadas. Como en el caso anterior, este fichero debe de estar en $LENGUAJE/LC_MESSAGES y se tiene que generar el binario con el comando msgfmt.

