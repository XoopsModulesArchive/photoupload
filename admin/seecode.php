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
require_once '../../../include/cp_header.php';
require_once '../include/common.php';

require_once PHOTOUPLOAD_PATH.'admin/functions.php';

$resize_width = photoupload_utils::getModuleOption('resize_width');
$resize_height = photoupload_utils::getModuleOption('resize_height');
$createThumbs = photoupload_utils::getModuleOption('create_thumbs');
$thumbs_width = photoupload_utils::getModuleOption('thumbs_width');
$thumbs_height = photoupload_utils::getModuleOption('thumbs_height');
$thumbs_prefix = photoupload_utils::getModuleOption('thumbs_prefix');
$text_link = photoupload_utils::getModuleOption('text_link');
$text_link_ahref = photoupload_utils::makeHrefTitle($text_link);
$photosUrl = photoupload_utils::getModuleOption('images_url');
$photosPath = photoupload_utils::getModuleOption('images_path');
$thumbStyle = photoupload_utils::getModuleOption('thumb_style');
if($createThumbs) {
    $prefixLength = strlen($thumbs_prefix) - 1;
}

$photo = isset($_GET['photo']) ? $_GET['photo'] : '';
if($photo == '') {
     exit(_AM_PHOTOUPLOAD_ERROR_1);
}

$thumbName = photoupload_utils::getModuleOption('thumbs_path').DIRECTORY_SEPARATOR.$thumbs_prefix.$photo;
if(file_exists($thumbName)) {
    // Génération du code pour voir l'image
    $pictureFullUrl = "$photosUrl/$photo";
    $thumbFullUrl = photoupload_utils::getModuleOption('thumbs_url')."/$thumbs_prefix$photo";
    $pictureDimensions = getimagesize($photosPath.DIRECTORY_SEPARATOR.$photo);
    $thumbDimensions = getimagesize($thumbName);
    $pictureWidth = $pictureDimensions[0] + 20;
    $pictureHeight = $pictureDimensions[1] + 20;
    $thumbWidth = $thumbDimensions[0];
    $thumbHeight = $thumbDimensions[1];
    echo "<h3>"._AM_PHOTOUPLOAD_HERE_IS_THE_CODE."</h3>";
    $text_link = "<a onclick=\"pop=window.open('', 'wclose', 'width=$pictureWidth, height=$pictureHeight, dependent=yes, toolbar=no, scrollbars=no, status=no, resizable=no, fullscreen=no, titlebar=no, left=0, top=0', 'false'); pop.focus(); \" href=\"$pictureFullUrl\" target=\"wclose\" title=\"$text_link_ahref\"><img src=\"$thumbFullUrl\" width=\"$thumbWidth\" height=\"$thumbHeight\" alt=\"$text_link_ahref\" style=\"$thumbStyle\" /></a>";
    echo "<textarea rows=10 cols=70 readonly name='thecode' id='thecode'>".htmlentities($text_link)."</textarea>";
    echo "<br />"._AM_PHOTOUPLOAD_COPY;
    echo "<br /><br /><b>"._AM_PHOTOUPLOAD_COPY_WYSIWYG."</b><br />";
    echo "<div>".$text_link."</div>\n";
} else {
    echo _AM_PHOTOUPLOAD_ERROR_2;
}
?>