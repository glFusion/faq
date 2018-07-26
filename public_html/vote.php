<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Ajax Driver for FAQ
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../lib-common.php';

if (!COM_isAjax()) die('no no');

if ( !isset($_CONF['rating_speedlimit']) ) {
    $_CONF['rating_speedlimit'] = 15;
}

header("Cache-Control: no-cache");
header("Pragma: nocache");

if (!isset($_POST['id']) || (!isset($_POST['type']) )) die('invalid');

$faq_id = COM_applyFilter($_POST['id'],true);
$type   = COM_applyFilter($_POST['type']);

switch ($type) {
    case 'yes' :
        DB_change($_TABLES['faq_questions'], 'helpful_yes', 'helpful_yes + 1', 'id', (int) $faq_id, '', true);
        break;
    case 'no' :
        DB_change($_TABLES['faq_questions'], 'helpful_no', 'helpful_no + 1', 'id', (int) $faq_id, '', true);
        break;
}

?>