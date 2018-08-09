<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Plugin configuration
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

global $_DB_table_prefix, $_TABLES;

// Plugin info

$_FAQ_CONF['pi_name']            = 'faq';
$_FAQ_CONF['pi_display_name']    = 'FAQ';
$_FAQ_CONF['pi_version']         = '1.0.1';
$_FAQ_CONF['gl_version']         = '1.7.5';
$_FAQ_CONF['pi_url']             = 'https://www.glfusion.org/';

$_TABLES['faq_categories']       = $_DB_table_prefix . 'faq_categories';
$_TABLES['faq_questions']        = $_DB_table_prefix . 'faq_questions';
?>