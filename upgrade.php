<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Plugin Upgrade Module
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

// this function is called by lib-plugin whenever the 'Upgrade' option is
// selected in the Plugin Administration screen for this plugin

function faq_upgrade()
{
    global $_TABLES, $_CONF, $_FAQ_CONF, $_DB_table_prefix;

    $currentVersion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='faq'");

    switch ($currentVersion) {
        case '0.0.1' :
        case '0.1.0' :
        case '0.2.0' :
        case '1.0.0' :
            // no changes

        default:
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_version='".$_FAQ_CONF['pi_version']."',pi_gl_version='".$_FAQ_CONF['gl_version']."' WHERE pi_name='faq' LIMIT 1");
            break;
    }

    faq_update_config();

    CTL_clearCache();

    if ( DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='faq'") == $_FAQ_CONF['pi_version']) {
        return true;
    } else {
        return false;
    }
}

function faq_update_config()
{
    global $_CONF, $_FAQ_CONF, $_TABLES;

    $c = config::get_instance();

    require_once $_CONF['path'].'plugins/faq/sql/faq_config_data.php';

    // remove stray items
    $result = DB_query("SELECT * FROM {$_TABLES['conf_values']} WHERE group_name='faq'");
    while ( $row = DB_fetchArray($result) ) {
        $item = $row['name'];
        if ( ($key = _searchForIdKey($item,$faqConfigData)) === NULL ) {
            DB_query("DELETE FROM {$_TABLES['conf_values']} WHERE name='".DB_escapeString($item)."' AND group_name='faq'");
        } else {
            $faqConfigData[$key]['indb'] = 1;
        }
    }
    // add any missing items
    foreach ($faqConfigData AS $cfgItem ) {
        if (!isset($cfgItem['indb']) ) {
            _addConfigItem( $cfgItem );
        }
    }
    $c = config::get_instance();
    $c->initConfig();
    $tcnf = $c->get_config('faq');
    // sync up sequence, etc.
    foreach ( $faqConfigData AS $cfgItem ) {
        $c->sync(
            $cfgItem['name'],
            $cfgItem['default_value'],
            $cfgItem['type'],
            $cfgItem['subgroup'],
            $cfgItem['fieldset'],
            $cfgItem['selection_array'],
            $cfgItem['sort'],
            $cfgItem['set'],
            $cfgItem['group']
        );
    }
}

if ( !function_exists('_searchForId')) {
    function _searchForId($id, $array) {
       foreach ($array as $key => $val) {
           if ($val['name'] === $id) {
               return $array[$key];
           }
       }
       return null;
    }
}

if ( !function_exists('_searchForIdKey')) {
    function _searchForIdKey($id, $array) {
       foreach ($array as $key => $val) {
           if ($val['name'] === $id) {
               return $key;
           }
       }
       return null;
    }
}

if ( !function_exists('_addConfigItem')) {
    function _addConfigItem($data = array() )
    {
        global $_TABLES;

        $Qargs = array(
                       $data['name'],
                       $data['set'] ? serialize($data['default_value']) : 'unset',
                       $data['type'],
                       $data['subgroup'],
                       $data['group'],
                       $data['fieldset'],
                       ($data['selection_array'] === null) ?
                        -1 : $data['selection_array'],
                       $data['sort'],
                       $data['set'],
                       serialize($data['default_value']));
        $Qargs = array_map('DB_escapeString', $Qargs);

        $sql = "INSERT INTO {$_TABLES['conf_values']} (name, value, type, " .
            "subgroup, group_name, selectionArray, sort_order,".
            " fieldset, default_value) VALUES ("
            ."'{$Qargs[0]}',"   // name
            ."'{$Qargs[1]}',"   // value
            ."'{$Qargs[2]}',"   // type
            ."{$Qargs[3]},"     // subgroup
            ."'{$Qargs[4]}',"   // groupname
            ."{$Qargs[6]},"     // selection array
            ."{$Qargs[7]},"     // sort order
            ."{$Qargs[5]},"     // fieldset
            ."'{$Qargs[9]}')";  // default value

        DB_query($sql);
    }
}
?>