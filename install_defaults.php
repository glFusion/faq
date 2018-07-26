<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Configuration Defaults
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

/** Utility plugin configuration data
*   @global array */
global $_FAQ_CONF;
if (!isset($_FAQ_CONF) || empty($_FAQ_CONF)) {
    $_TST_CONF = array();
    require_once dirname(__FILE__) . '/faq.php';
}

/**
*   Initialize Testimonials plugin configuration
*
*   @return boolean             true: success; false: an error occurred
*/
function plugin_initconfig_faq()
{
    global $_CONF;

    $c = config::get_instance();

    if (!$c->group_exists('faq')) {
        require_once $_CONF['path'].'plugins/faq/sql/faq_config_data.php';

        foreach ( $faqConfigData AS $cfgItem ) {
            $c->add(
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
     return true;
}
?>
