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

/* Class autoload function.
 *
 * @parameter name (string): The class name.
 * @return void
 */
function __autoload($name) {
	$configs = $GLOBALS['configs'];
	$fileExists = false;
	$className = strtolower($name) . $configs['class_ext'];
	$subdirs = array($configs['class_path']);
	while (!$fileExists && $dir = each($subdirs)) {
		$handle = opendir($dir['value']);
		if ($handle === false)
			throw new MissingException('Impossible to load ' . $name . '.');

		while ($entry = readdir($handle)) {
			if (strpos($entry, $className) === 0) {
				$fileExists = true;
			    include_once $dir['value'] . '/' . $className;
				break;
			} else if (strpos($entry, '.') !== 0 &&
				is_dir($dir['value'] . '/' . $entry)) {
				$subdirs[] = $dir['value'] . '/' . $entry;
			}
		}

		closedir($handle);
	}

	if (!$fileExists)
		log_e('Couldn\'t load the class ' . $name . '.');
}

/** Function to get the unix time in microseconds.
 *
 * @return int Time in microseconds.
 */
function getTime() {
	$time = explode(' ', microtime());
	$time = $time[1] + $time[0];

	return $time;
}
