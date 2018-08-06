<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Sitemap plugin interface
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


class sitemap_faq extends sitemap_base
{
    protected $name = 'faq';

    public function getDisplayName()
    {
        global $LANG_FAQ;
        return $LANG_FAQ['plugin_name'];
    }


    /**
    * @param $pid int/string/boolean id of the parent category
    * @param $current_groups array ids of groups the current user belongs to
    * @return array(
    *   'id'        => $id (string),
    *   'pid'       => $pid (string: id of its parent)
    *   'title'     => $title (string),
    *   'uri'       => $uri (string),
    *   'date'      => $date (int: Unix timestamp),
    *   'image_uri' => $image_uri (string)
    *  )
    */
    public function getChildCategories($pid = false)
    {
        global $_CONF, $_TABLES;

        $entries = array();

        if ($pid != false) return $entries;

        $permSQL = COM_getPermSql('AND',$this->uid);
        $sql = "SELECT cat_id,title,description FROM {$_TABLES['faq_categories']} WHERE 1=1 " . $permSQL . " ORDER BY sort_order ASC";
        $result = DB_query($sql,1);

        while (($A = DB_fetchArray($result, false)) !== FALSE) {
            $entries[] = array(
                'id'        => (int)$A['cat_id'],
                'title'     => $A['title'],
                'uri'       => $_CONF['site_url'] . '/faq/index.php',
                'date'      => false,
            );
        }
        return $entries;
    }


    /**
    * Returns an array of (
    *   'id'        => $id (string),
    *   'title'     => $title (string),
    *   'uri'       => $uri (string),
    *   'date'      => $date (int: Unix timestamp),
    *   'image_uri' => $image_uri (string)
    * )
    */
    public function getItems($cid = false)
    {
        global $_CONF, $_TABLES;

        if ($cid === false) {
            $where = "WHERE 1=1 ";
        } else {
            $where = "WHERE (f.cat_id = '".DB_escapeString($cid)."') ";
        }

        $entries = array();

        $permSQL = COM_getPermSql('AND',$this->uid);

        $sql = "SELECT id, question,UNIX_TIMESTAMP(f.last_updated) as last_updated
                FROM {$_TABLES['faq_questions']} AS f
                LEFT JOIN {$_TABLES['faq_categories']} AS c
                ON f.cat_id = c.cat_id " . $where . " " . $permSQL;

        $result = DB_query($sql, 1);
        if (DB_error()) {
            COM_errorLog("sitemap_faq::getItems() error: $sql");
            return $entries;
        }

        while (($A = DB_fetchArray($result, false)) !== FALSE) {
            $entries[] = array(
                'id'        => $A['id'],
                'title'     => $A['question'],
                'uri'       => COM_buildURL($_CONF['site_url'] . '/faq/index.php?faqid='.(int) $A['id']),
                'date'      => $A['last_updated'],
                'image_uri' => false,
            );
        }
        return $entries;
    }
}
?>
