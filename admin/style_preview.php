<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: 			*/
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2008 Armia Systems, Inc                                    |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts SocialWare                    |
// +----------------------------------------------------------------------+
// | Authors: simi<simi@armia.com>             		                      |
// |          										                      |
// +----------------------------------------------------------------------+
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');

$tImg=TemplatePreview($_REQUEST['q']);

echo '<img src="'.$tImg.'" width="200" height="150" id="templateShow" alt="Preview" title="Preview">';
?>
