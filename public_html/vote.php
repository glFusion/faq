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

if (!COM_isAjax()) die();

header("Cache-Control: no-cache");
header("Pragma: nocache");

if (!isset($_POST['id']) || (!isset($_POST['type']) )) die();

$faq_id = COM_applyFilter($_POST['id'],true);
$type   = COM_applyFilter($_POST['type']);

$result = DB_query("SELECT * FROM {$_TABLES['faq_questions']} AS f LEFT JOIN {$_TABLES['faq_categories']} AS c ON f.cat_id=c.cat_id WHERE f.id = ".(int) $faq_id);
if (DB_numRows($result) == 1) {
    $faqRecord = DB_fetchArray($result);

    $permission = COM_getEffectivePermission( $faqRecord['owner_id'],
                                              $faqRecord['group_id'],
                                              $faqRecord['perm_owner'],
                                              $faqRecord['perm_group'],
                                              $faqRecord['perm_members'],
                                              $faqRecord['perm_anon']
                                            );

    if ($permission == 0) {
        die();
    }
}

switch ($type) {
    case 'yes' :
        DB_change($_TABLES['faq_questions'], 'helpful_yes', 'helpful_yes + 1', 'id', (int) $faq_id, '', true);
        break;
    case 'no' :
        DB_change($_TABLES['faq_questions'], 'helpful_no', 'helpful_no + 1', 'id', (int) $faq_id, '', true);
        break;
    default :
        die();
        break;
}

?>