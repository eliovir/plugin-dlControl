<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

global $listCmddlControl;
$listCmddlControl = array(
    array(
        'name' => 'Pause Sab',
        'configuration' => array(
            'request' => 'sabnzbd',
            'parameters' => 'pause',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Mettre en pause Sabnzbd',
		'group' => 'sab',
		'icon' => 'fa fa-pause',
    ),
    array(
        'name' => 'Resume Sab',
        'configuration' => array(
            'request' => 'sabnzbd',
            'parameters' => 'resume',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Enlever la pause de Sabnzbd',
		'group' => 'sab',
		'icon' => 'fa fa-play',
    ),
	array(
        'name' => 'Pause Transmission',
        'configuration' => array(
            'request' => 'transmission',
            'parameters' => 'pause',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Mettre en pause Transmission',
		'group' => 'transmission',
		'icon' => 'fa fa-pause',
    ),
    array(
        'name' => 'Resume Transmission',
        'configuration' => array(
            'request' => 'transmission',
            'parameters' => 'resume',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Enlever la pause de Transmission',
		'group' => 'transmission',
		'icon' => 'fa fa-play',
    ),
	array(
        'name' => 'Pause Rutorrent',
        'configuration' => array(
            'request' => 'rutorrent',
            'parameters' => 'pause',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Mettre en pause Rutorrent',
		'group' => 'rutorrent',
		'icon' => 'fa fa-pause',
    ),
    array(
        'name' => 'Resume Rutorrent',
        'configuration' => array(
            'request' => 'rutorrent',
            'parameters' => 'resume',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Enlever la pause de Rutorrent',
		'group' => 'rutorrent',
		'icon' => 'fa fa-play',
    ),
	array(
        'name' => 'Pause Nzbget',
        'configuration' => array(
            'request' => 'nzbget',
            'parameters' => 'pausedownload',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Mettre en pause Nzbget',
		'group' => 'nzbget',
		'icon' => 'fa fa-pause',
    ),
    array(
        'name' => 'Resume Nzbget',
        'configuration' => array(
            'request' => 'nzbget',
            'parameters' => 'resumedownload',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Enlever la pause de Nzbget',
		'group' => 'nzbget',
		'icon' => 'fa fa-play',
    )
);
?>
