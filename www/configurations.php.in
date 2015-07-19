<?php
/**
 * copyright (c) 2008-2015 AUTHORS
 *
 * This file is part of GESTAS
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

/* Setting the default configuration variables */
$GLOBALS['configs'] = array(
	/* Paths. */
	'css_path' => '/css',
	'img_path' => '/img',
	'js_path' => '/js',
	'path' => '%WWW_DIR%',
	'class_path' => '%WWW_DIR%/classes',
	'include_path' => '%WWW_DIR%/include',
	'log_path' => '%LOG_DIR%',

	/* File extensions. */
	'class_ext' => '.php',
	'template_ext' => '.html',

	/* Log parameters. */
	'log_file' => '%LOG_FILE%',
	'log_file_prefix' => '%LOG_PREFIX%',

	/* Database parameters. */
	'db_engine' => '%DB_ENGINE%',
	'db_host' => '%DB_HOST%',
	'db_port' => '%DB_PORT%',
	'db_user' => '%DB_USER%',
	'db_password' => '%DB_PASSWD%',
	'db_name' => '%DATABASE%',
	'timezone' => 'Europe/Madrid',
	);

/* Loading functions. */
require_once $GLOBALS['configs']['include_path'] . '/functions.php';

/* Setting the autoload function. */
spl_autoload_register('__autoload');

/* Setting the timezone. */
date_default_timezone_set($GLOBALS['configs']['timezone']);
