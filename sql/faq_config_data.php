<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* FAQ Plugin Configuration
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

$faqConfigData = array(

    array(
        'name' => 'sg_main',
        'default_value' => NULL,
        'type' => 'subgroup',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
        'name' => 'fs_main',
        'default_value' => NULL,
        'type' => 'fieldset',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
        'name' => 'category_sort_field',
        'default_value' => 'title',
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 4,
        'sort' => 10,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
        'name' => 'category_sort_dir',
        'default_value' => 'DESC',
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 5,
        'sort' => 20,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
        'name' => 'fs_whatsnew',
        'default_value' => NULL,
        'type' => 'fieldset',
        'subgroup' => 0,
        'fieldset' => 1,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
        'name' => 'whatsnew_enabled',
        'default_value' => true,
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 1,
        'selection_array' => 0,
        'sort' => 10,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
        'name' => 'whatsnew_interval',
        'default_value' => 1209600, // 2 weeks
        'type' => 'text',
        'subgroup' => 0,
        'fieldset' => 1,
        'selection_array' => NULL,
        'sort' => 20,
        'set' => TRUE,
        'group' => 'faq'
    ),

    array(
    	'name' => 'fs_perm_defaults',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'faq'
    ),
    array(
    	'name' => 'default_permissions_category',
    	'default_value' => array(3,2,2,2),
    	'type' => '@select',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => 6,
    	'sort' => 10,
    	'set' => TRUE,
    	'group' => 'faq'
    ),
);
?>