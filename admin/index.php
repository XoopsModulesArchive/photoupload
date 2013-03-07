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
 * @author 			Hervé Thouzard of Instant Zero
 * @link 			http://www.instant-zero.com
 *
 * Version : $Id:
 * ****************************************************************************
 */

require_once '../../../include/cp_header.php';
require_once '../include/common.php';
require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
require_once PHOTOUPLOAD_PATH.'admin/functions.php';
require_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH.'/class/xoopslists.php';

$op = 'default';
if (isset($_POST['op'])) {
	$op = $_POST['op'];
} else {
	if ( isset($_GET['op'])) {
    	$op = $_GET['op'];
	}
}

// Vérification de l'existence et de l'état d'écriture des différents répertoire de stockage et de cache
photoupload_utils::prepareFolder(photoupload_utils::getModuleOption('images_path'));

// Lecture de certains paramètres de l'application ********************************************************************
$baseurl = PHOTOUPLOAD_URL.'admin/'.basename(__FILE__);	// URL de ce script
$conf_msg = photoupload_utils::javascriptLinkConfirm(_AM_PHOTOUPLOAD_CONF_DELITEM);

$resize_width = photoupload_utils::getModuleOption('resize_width');
$resize_height = photoupload_utils::getModuleOption('resize_height');
$createThumbs = photoupload_utils::getModuleOption('create_thumbs');
$thumbs_width = photoupload_utils::getModuleOption('thumbs_width');
$thumbs_height = photoupload_utils::getModuleOption('thumbs_height');
$thumbs_prefix = photoupload_utils::getModuleOption('thumbs_prefix');
$text_link = photoupload_utils::getModuleOption('text_link');
$text_link_ahref = photoupload_utils::makeHrefTitle($text_link);
$limit = photoupload_utils::getModuleOption('per_page');

$destname = '';


/**
 * Affichage du pied de page de l'administration
 *
 * PLEASE, KEEP THIS COPYRIGHT *INTACT* !
 */
function show_footer()
{
	echo "<br /><br /><div align='center'><a href='http://www.instant-zero.com' target='_blank' title='Instant Zero'><img src='../images/instantzero.gif' alt='Instant Zero' /></a></div>";
}


global $xoopsConfig;
if (file_exists(PHOTOUPLOAD_PATH.'language/'.$xoopsConfig['language'].'/modinfo.php')) {
	require_once PHOTOUPLOAD_PATH.'language/'.$xoopsConfig['language'].'/modinfo.php';
} else {
	require_once PHOTOUPLOAD_PATH.'language/english/modinfo.php';
}

if (file_exists(PHOTOUPLOAD_PATH.'language/'.$xoopsConfig['language'].'/main.php')) {
	require_once PHOTOUPLOAD_PATH.'language/'.$xoopsConfig['language'].'/main.php';
} else {
	require_once PHOTOUPLOAD_PATH.'language/english/main.php';
}



// ******************************************************************************************************************************************
// **** Main ********************************************************************************************************************************
// ******************************************************************************************************************************************
switch ($op) {

	// ****************************************************************************************************************
	case 'default':
	// ****************************************************************************************************************
        xoops_cp_header();
        photoupload_adminMenu(0);
		photoupload_utils::htitle(_MI_PHOTOUPLOAD_ADMENU0, 4);

		$sform = new XoopsThemeForm(_AM_PHOTOUPLOAD_ADD_PHOTO, 'frmaddAddPhoto', $baseurl);
		$sform->setExtra('enctype="multipart/form-data"');
		$sform->addElement(new XoopsFormHidden('op', 'savePhoto'));
		$sform->addElement(new XoopsFormFile(_AM_PHOTOUPLOAD_PHOTO_FILE, 'attachedfile', photoupload_utils::getModuleOption('maxuploadsize')), false);
		if(photoupload_utils::getModuleOption('user_selectsize')) {
		    $resize_width = new XoopsFormText(_AM_PHOTOUPLOAD_RESIZE_WIDTH, 'resize_width',4, 4, $resize_width);
		    $resize_width->setDescription(_AM_PHOTOUPLOAD_RESIZE_WIDTH_DSC);
            $sform->addElement($resize_width, false);
            $resize_height = new XoopsFormText(_AM_PHOTOUPLOAD_RESIZE_HEIGHT, 'resize_height',4, 4, $resize_height);
            $resize_height->setDescription(_AM_PHOTOUPLOAD_RESIZE_HEIGHT_DSC);
            $sform->addElement($resize_height, false);
            if($createThumbs) {
                $thumbs_width = new XoopsFormText(_AM_PHOTOUPLOAD_THUMB_WIDTH, 'thumbs_width',4, 4, $thumbs_width);
                $thumbs_width->setDescription(_AM_PHOTOUPLOAD_THUMB_WIDTH_DSC);
                $sform->addElement($thumbs_width, false);
                $thumbs_height = new XoopsFormText(_AM_PHOTOUPLOAD_THUMB_HEIGHT, 'thumbs_height',4, 4, $thumbs_height);
                $thumbs_height->setDescription(_AM_PHOTOUPLOAD_THUMB_HEIGHT_DSC);
                $sform->addElement($thumbs_height, false);
            }
		}
		$button_tray = new XoopsFormElementTray('' ,'');
		$submit_btn = new XoopsFormButton('', 'post', _AM_PHOTOUPLOAD_ADD, 'submit');
		$button_tray->addElement($submit_btn);
		$sform->addElement($button_tray);
		$sform->display();
        show_footer();
		break;

	// ****************************************************************************************************************
	case 'savePhoto':    // Ajout d'une photo
	// ****************************************************************************************************************
        xoops_cp_header();
        photoupload_adminMenu(0);
		$opRedirect = 'default';

	    // Upload de l'image et création de la vignette
	    $destname = '';
	    $return = photoupload_utils::uploadFile(0, photoupload_utils::getModuleOption('images_path'));
	    if(photoupload_utils::getModuleOption('user_selectsize')) {    // Les utilisateurs peuvent choisir les dimensions des images et des vignettes
	        if(isset($_POST['resize_width'])) {
	             $resize_width = intval($_POST['resize_width']);
	        }
	        if(isset($_POST['resize_height'])) {
	            $resize_height = intval($_POST['resize_height']);
	        }
	        if($createThumbs) {
	            if(isset($_POST['thumbs_width'])) {
	                $thumbs_width = intval($_POST['thumbs_width']);
	            }
	            if(isset($_POST['thumbs_height'])) {
	                $thumbs_height = intval($_POST['thumbs_height']);
	            }
	        }
	    }
	    if($return === true) {
	    	$newDestName = $destname;
	        if($resize_width > 0 && $resize_height > 0) {
		        $retval = photoupload_utils::resizePicture(photoupload_utils::getModuleOption('images_path').'/'.basename($destname), photoupload_utils::getModuleOption('images_path').'/'.basename($destname), $resize_width, $resize_height, true);
	        } else {	            
	            $retval = 1;
	        }
	        if($createThumbs && $thumbs_width > 0 && $thumbs_height > 0) {
                $newDestName2 = photoupload_utils::getModuleOption('thumbs_path').'/'.$thumbs_prefix.basename($newDestName);
                $retval2 = photoupload_utils::resizePicture(photoupload_utils::getModuleOption('images_path').'/'.basename($newDestName), $newDestName2, $thumbs_width, $thumbs_height, true);
	        }
		    if($retval == 1 || $retval == 3) {
   				photoupload_utils::updateCache();
   				photoupload_utils::redirect(_AM_PHOTOUPLOAD_SAVE_OK, $baseurl.'?op='.$opRedirect, 2);
		    }
	    } else {
   			if($return !== false) {
			    photoupload_utils::redirect($return, $baseurl.'?op='.$opRedirect,5);
		    }
	    }
	    photoupload_utils::redirect(_AM_PHOTOUPLOAD_SAVE_PB, $baseurl.'?op='.$opRedirect,5);
		break;


	// ****************************************************************************************************************
	case 'deletePhoto':	// Suppression d'une photo
	// ****************************************************************************************************************
        xoops_cp_header();
		$id = isset($_GET['photo']) ? $_GET['photo'] : 0;
		if(empty($id)) {
			photoupload_utils::redirect(_AM_PHOTOUPLOAD_ERROR_1, $baseurl, 5);
		}
		$opRedirect = 'liste';
		$filename = photoupload_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.$id;
		$thumbName = photoupload_utils::getModuleOption('thumbs_path').DIRECTORY_SEPARATOR.$thumbs_prefix.$id;
		if(file_exists($filename)) {
            @unlink($filename);
            if(file_exists($thumbName)) {    // Suppression de la vignette associée
                @unlink($thumbName);
            }
			photoupload_utils::updateCache();
			photoupload_utils::redirect(_AM_PHOTOUPLOAD_FILE_DELETE_OK, $baseurl.'?op='.$opRedirect, 1);
		} else {
		    photoupload_utils::redirect(_AM_PHOTOUPLOAD_NOT_FOUND, $baseurl.'?op='.$opRedirect, 5);
		}
		break;

    // ****************************************************************************************************************
	case 'liste':    // Liste des photos existantes
    // ****************************************************************************************************************
        xoops_cp_header();
        photoupload_adminMenu(1);
		photoupload_utils::htitle(_MI_PHOTOUPLOAD_ADMENU1, 4);

        // Liste des photos existantes
        $photos = array();
        $photos = XoopsLists::getImgListAsArray(photoupload_utils::getModuleOption('images_path'));
        sort($photos);
        if(isset($_GET['start'])) {
            $start = intval($_GET['start']);
        } elseif(isset($_SESSION['photoupload_start'])) {
            $start = $_SESSION['photoupload_start'];
        } else {
            $start = 0;
        }
        $_SESSION['photoupload_start'] = $start;
        $itemsCount = count($photos);
        if( $itemsCount >  $limit) {
            $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=liste');
            $photos = array_slice($photos, $start, $limit);
        }
        $photosUrl = photoupload_utils::getModuleOption('images_url');
        $photosPath = photoupload_utils::getModuleOption('images_path');
        if($createThumbs) {
            $prefixLength = strlen($thumbs_prefix);
        } else {
        	$prefixLength = 512;
        }
        $thumbsPath = photoupload_utils::getModuleOption('thumbs_path');
        $thumbsUrl = photoupload_utils::getModuleOption('thumbs_url');

        if(count($photos) > 0) {
		    if(isset($pagenav) && is_object($pagenav)) {
    			echo "<div align='left'>".$pagenav->renderNav().'</div><br />';
		    }
            echo '<div id="PhotosList" style="width: 100%; height: 600px; overflow: auto;">';
            echo "<table border='0' style='size: auto;'><tr>\n";
            $rupture = 0;
            $picturesPerLine = photoupload_utils::getModuleOption('photos_per_line');
            foreach($photos as $photo) {
                if(substr($photo, 0, $prefixLength) != $thumbs_prefix) {
                    $rupture++;
                    if($createThumbs) {
                        echo "</tr><tr>\n";
                        $rupture = 0;
                    } elseif(!($rupture % $picturesPerLine)) {
                        echo "</tr><tr>\n";
                        $rupture = 0;
                    }
                    $pictureDimensions = getimagesize($photosPath.DIRECTORY_SEPARATOR.$photo);
                    $pictureWidth = $pictureDimensions[0];
                    $pictureHeight = $pictureDimensions[1];
                    echo "<td><a target='_blank' href='$photosUrl/$photo'><img style='max_width: 640px; max-height: 480px;' src='".$photosUrl.'/'.$photo."' /></a><br />$photo ($pictureWidth x $pictureHeight) - <a $conf_msg href='$baseurl?op=deletePhoto&photo=$photo'>"._DELETE."<br /><br /></a></td>";
                    if($createThumbs) {
                        $thumbName = $thumbsPath.DIRECTORY_SEPARATOR.$thumbs_prefix.$photo;
                        if(file_exists($thumbName)) {
                            echo "<td><a target='_blank' href='$thumbsUrl/$thumbs_prefix$photo'><img src='".$thumbsUrl.'/'.$thumbs_prefix.$photo."' /></a><br /><a $conf_msg href='$baseurl?op=deletePhoto&photo=$photo'>"._DELETE."</a></td>";
                            echo "<td><a target='_blank' href='".PHOTOUPLOAD_URL."admin/seecode.php?photo=$photo'><img src='".PHOTOUPLOAD_IMAGES_URL."code2.png' alt='"._AM_PHOTOUPLOAD_SEE_CODE."'/><br />"._AM_PHOTOUPLOAD_SEE_CODE."</a></td>\n";
                        } else {
                            echo "<td>&nbsp;</td><td>&nbsp;</td>\n";
                        }
                    }
                }
            }
            echo "</tr></table>\n";
            echo "</div>";
		    if(isset($pagenav) && is_object($pagenav)) {
    			echo "<br /><div align='left'>".$pagenav->renderNav().'</div>';
		    }
        }
        show_footer();
		break;


	// ****************************************************************************************************************
	case 'instant-zero';	// Publicité
	// ****************************************************************************************************************
        xoops_cp_header();
        photoupload_adminMenu(2);
		echo "<iframe src='http://www.instant-zero.com/modules/liaise/?form_id=2' width='100%' height='600' frameborder='0'></iframe>";
		show_footer();
		break;
}

xoops_cp_footer();
?>