<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Auto Installation
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

global $_DB_dbms;

require_once $_CONF['path'].'plugins/faq/functions.inc';
require_once $_CONF['path'].'plugins/faq/faq.php';
require_once $_CONF['path'].'plugins/faq/sql/mysql_install.php';

// +--------------------------------------------------------------------------+
// | Plugin installation options                                              |
// +--------------------------------------------------------------------------+

$INSTALL_plugin['faq'] = array(

    'installer' => array('type' => 'installer', 'version' => '1', 'mode' => 'install'),

    'plugin' => array('type' => 'plugin', 'name' => $_FAQ_CONF['pi_name'],
        'ver' => $_FAQ_CONF['pi_version'], 'gl_ver' => $_FAQ_CONF['gl_version'],
        'url' => $_FAQ_CONF['pi_url'], 'display' => $_FAQ_CONF['pi_display_name']),

    array('type' => 'table', 'table' => $_TABLES['faq_categories'], 'sql' => $_SQL['faq_categories']),
    array('type' => 'table', 'table' => $_TABLES['faq_questions'], 'sql' => $_SQL['faq_questions']),

    array('type' => 'group', 'group' => 'FAQ Admin', 'desc' => 'Users in this group can administer the FAQ plugin',
        'variable' => 'admin_group_id', 'addroot' => true, 'admin' => true),

    array('type' => 'feature', 'feature' => 'faq.admin', 'desc' => 'Ability to administer the FAQ plugin',
            'variable' => 'admin_feature_id'),

    array('type' => 'mapping', 'group' => 'admin_group_id', 'feature' => 'admin_feature_id',
            'log' => 'Adding faq.admin feature to the FAQ admin group'),

//    array('type' => 'block', 'name' => 'block_testimonials', 'title' => 'Testimonials',
//          'phpblockfn' => 'phpblock_testimonials', 'block_type' => 'phpblock',
//          'group_id' => 'admin_group_id' , 'onleft' => true),
);


/**
* Puts the datastructures for this plugin into the glFusion database
*
* Note: Corresponding uninstall routine is in functions.inc
*
* @return   boolean True if successful False otherwise
*
*/
function plugin_install_faq()
{
    global $INSTALL_plugin, $_FAQ_CONF;

    $pi_name            = $_FAQ_CONF['pi_name'];
    $pi_display_name    = $_FAQ_CONF['pi_display_name'];
    $pi_version         = $_FAQ_CONF['pi_version'];

    COM_errorLog("Attempting to install the $pi_display_name plugin", 1);

    $ret = INSTALLER_install($INSTALL_plugin[$pi_name]);
    if ($ret > 0) {
        return false;
    }

    return true;
}

/**
*   Loads the configuration records for the Online Config Manager.
*
*   @return boolean     True = proceed, False = an error occured
*/
function plugin_load_configuration_faq()
{
    require_once dirname(__FILE__) . '/install_defaults.php';
    return plugin_initconfig_faq();
}


/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_faq ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('faq_categories','faq_questions'),
        /* give the full name of the group, as in the db */
        'groups' => array('faq Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('faq.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars'=> array()
    );
    return $out;
}
?>