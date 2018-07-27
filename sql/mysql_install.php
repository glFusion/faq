<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* FAQ Database Schema
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

$_SQL['faq_categories'] = "CREATE TABLE {$_TABLES['faq_categories']} (
    cat_id int(15) NOT NULL auto_increment,
    title varchar(255) NOT NULL default '',
    description text,
    last_updated datetime default NULL,
    owner_id mediumint(8) unsigned NOT NULL default '1',
    group_id mediumint(9) NOT NULL default '0',
    perm_owner tinyint(1) unsigned NOT NULL default '0',
    perm_group tinyint(1) unsigned NOT NULL default '0',
    perm_members tinyint(1) unsigned NOT NULL default '0',
    perm_anon tinyint(1) unsigned NOT NULL default '0',
    sort_order mediumint(8) NOT NULL default '0',
    PRIMARY KEY  (cat_id)
) ENGINE=MyISAM
";

$_SQL['faq_questions'] = "CREATE TABLE {$_TABLES['faq_questions']} (
    id int(15) NOT NULL auto_increment,
    cat_id int(15),
    draft tinyint(1) NOT NULL default '0',
    last_updated datetime default NULL,
    question text,
    answer text,
    owner_uid mediumint(8) unsigned NOT NULL default '1',
    helpful_yes mediumint(8) unsigned NOT NULL default '0',
    helpful_no mediumint(8) unsigned NOT NULL default '0',
    hits mediumint(8) unsigned NOT NULL default 0,
    PRIMARY KEY  (id)
) ENGINE=MyISAM
";

?>