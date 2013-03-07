<?php
/**
 * ****************************************************************************
 * photoupload - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         photoupload
 * @author 			Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */
if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}

if( !defined("PHOTOUPLOAD_DIRNAME") ) {
	define("PHOTOUPLOAD_DIRNAME", 'photoupload');
	define("PHOTOUPLOAD_URL", XOOPS_URL.'/modules/'.PHOTOUPLOAD_DIRNAME.'/');
	define("PHOTOUPLOAD_PATH", XOOPS_ROOT_PATH.'/modules/'.PHOTOUPLOAD_DIRNAME.'/');
	define("PHOTOUPLOAD_IMAGES_URL", PHOTOUPLOAD_URL.'images/');		// Les images du module (l'url)
	define("PHOTOUPLOAD_IMAGES_PATH", PHOTOUPLOAD_PATH.'images/');	// Les images du module (le chemin)
	define("PHOTOUPLOAD_JS_URL", PHOTOUPLOAD_URL.'js/');
}

// Chargement des handler et des autres classes
require_once PHOTOUPLOAD_PATH.'class/photoupload_utils.php';
require_once PHOTOUPLOAD_PATH.'config.php';

// Définition des images
if( !defined("_PHOTOUPLOAD_EDIT")) {
	global $xoopsConfig;
	if (isset($xoopsConfig) && file_exists(PHOTOUPLOAD_PATH.'language/'.$xoopsConfig['language'].'/main.php')) {
			require PHOTOUPLOAD_PATH.'language/'.$xoopsConfig['language'].'/main.php';
	} else {
		require PHOTOUPLOAD_PATH.'language/english/main.php';
	}

	$icones = array(
		'edit' => "<img src='". PHOTOUPLOAD_IMAGES_URL ."edit.png' alt='"._PHOTOUPLOAD_EDIT."' align='middle' />",
		'delete' => "<img src='". PHOTOUPLOAD_IMAGES_URL ."delete.png' alt='"._PHOTOUPLOAD_DELETE."' align='middle' />"
	);
}

?>