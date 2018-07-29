<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Ajax Driver for FAQ - reset helpful stats
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../../lib-common.php';

if (!COM_isAjax()) {
    die();
}

if (!SEC_hasRights('faq.admin')) {
    die();
}

header("Cache-Control: no-cache");
header("Pragma: nocache");

if (!isset($_POST['id'])) die();

$faq_id = COM_applyFilter($_POST['id'],true);

$sql = "UPDATE {$_TABLES['faq_questions']} SET helpful_yes=0, helpful_no=0 WHERE id=" .(int) $faq_id;
DB_query($sql);

?>