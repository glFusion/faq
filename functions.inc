<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* glFusion Interface functions.inc
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

$faq_config = \config::get_instance();
$_FAQ_CONF = $faq_config->get_config('faq');

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/faq/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    include_once $langfile;
} else {
    include_once $_CONF['path'] . 'plugins/faq/language/english_utf-8.php';
}

/*
* Include faqman config file
*/
require_once $_CONF['path'] . 'plugins/faq/faq.php';

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* Universal Plugin:  Uncomment and fill in variables;
*
* @return    array   Associative array of menu items to be added to menu
*
*/
function plugin_getmenuitems_faq()
{
    global $_CONF, $_TABLES, $LANG_FAQ;

    $menulabel = $LANG_FAQ['plugin_name'];
    $menurul = $_CONF['site_url'] . "/faq/index.php";

    $menuitems = array();
  	$menuitems[$menulabel] = $menurul;

    return $menuitems;
}

/**
* Check to see if this Plugin uses comments
*
* glFusion is checking to see if this plugin supports comments, tell it no!
* NOTE: to support comments you must use the same date/time based ID for your
* widget.  In other words, to make primary keys for your plugin you should call
* makesid().  Comments are keyed off of that...it is a limitation on how glFusion
* does comments.
*
* @return   boolean true or false
*
*/
function plugin_commentsupport_faq()
{
    return false;
}

/**
* This will put an option for the plugin in the command and control block on moderation.php
*
* Add the plugin name, icon and link to the command and control block in moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_faq()
{
    global $_CONF, $LANG_FAQ;

    if (SEC_hasRights('faq.admin')) {
        return array(
            $LANG_FAQ['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/faq/index.php',
            $_CONF['site_url'] . '/faq/images/faq.png'
        );
    }
}

/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* Universal Plugin:  Modify/Uncomment use it
*
* @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')
*
*/
function plugin_getadminoption_faq()
{
    global $_CONF, $_TABLES, $LANG_FAQ;

    if (SEC_hasRights('faq.admin')) {
        return array(
            $LANG_FAQ['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/faq/index.php',
            DB_count($_TABLES['faq_questions'],'published',1)
        );
    }
}

/**
* Counts the items that are submitted
*
* @return   int     number of items in submission queue
*
*/
function plugin_submissioncount_faq()
{
    global $_TABLES;

    $retval = 0;

    $retval = DB_count ($_TABLES['faq_questions'],'published',0);

    return $retval;
}

/**
* returns list of moderation values
*
* The array returned contains (in order): the row 'id' label, main plugin
* table, moderation fields (comma seperated), and plugin submission table
*
* @return       array        Returns array of useful moderation values
*
*/
function plugin_moderationvalues_faq()
{
    global $_TABLES;

    return array (
        'id',
        $_TABLES['faq_questions'],
        "id,published",
        ''
    );
}

/**
* Performs plugin exclusive work for items approved by moderation
*
* While moderation.php handles the actual move from mediagallery submission
* to mediagallery tables, within the function we handle all other approval
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationapprove_faq($id)
{
    global $_CONF, $_TABLES, $LANG_FAQ;

    if ( (int) $id <= 0 ) return '';

    $sql = "UPDATE {$_TABLES['faq_questions']} SET published=0 WHERE id=".(int) $id;
    DB_query($sql);

    PLG_itemSaved($id,'faq');
    CACHE_remove_instance('menu');
    COM_setMsg($LANG_FAQ['submission_mod_approved'],'warning');

    return '';
}


/**
* Performs plugin exclusive work for items deleted by moderation
*
* While moderation.php handles the actual removal from mediagallery submission
* table, within this function we handle all other deletion
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationdelete_faq($id)
{
    global $_CONF, $_TABLES;

    if ( (int) $id <= 0 ) return '';

    $sql = "DELETE FROM {$_TABLES['faq']} WHERE id=".(int) $id . " AND queued=1";
    DB_query($sql);
    PLG_itemDeleted($id, 'faq');
    CACHE_remove_instance('menu');
    return;
}

/**
* show files for moderation on submissions page
*
* Uses the Plugin class to return data required by moderation.php to list
* plugin objects that need to be moderated.
*
* @param        string token The
* @return       Plugin       return HTML
*
*/
function plugin_itemlist_faq($token)
{
    global $_CONF, $_TABLES, $_USER, $LANG_FAQ;
    global $LANG01, $LANG24, $LANG29, $LANG_ADMIN, $_IMAGE_TYPE;

    $retval = '';
    $key='media_id';

    if ( COM_isAnonUser() ) {
        $uid = 1;
    } else {
        $uid = $_USER['uid'];
    }

    $sql = "SELECT * FROM {$_TABLES['faq']} WHERE queued = 1";

    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    if ( $nrows == 0 ) return;

    $data_arr = array();
    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);
        $A['edit'] = 'edit';
        $A['_type_']  = 'faq';
        $A['_key_']   = 'id';      // name of key/id field
        $data_arr[$i] = $A;     // push row data into array
    }

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'testid', 'align' => 'center', 'width' => '25px'),
        array('text' => $LANG_FAQ['client'], 'field' => 'clientname'),
        array('text' => $LANG_FAQ['company'], 'field' => 'company'),
        array('text' => $LANG_FAQ['tstdate'], 'field' => 'tst_date', 'align' => 'center', 'width' => '15%'),
        array('text' => $LANG_FAQ['answer'],'field'=>'text_full','align' => 'center'),
        array('text' => $LANG29[1], 'field' => 'approve', 'align' => 'center', 'width' => '35px'),
        array('text' => $LANG_ADMIN['delete'], 'field' => 'delete', 'align' => 'center', 'width' => '35px')
    );

    $text_arr = array('has_menu'    => false,
                      'title'       => $LANG_FAQ['submissions'],
                      'help_url'    => '',
                      'no_data'     => $LANG29[39],
                      'form_url'    => "{$_CONF['site_admin_url']}/moderation.php"
    );

    $actions = '<input name="approve" type="image" src="'
        . $_CONF['layout_url'] . '/images/admin/accept.' . $_IMAGE_TYPE
        . '" style="vertical-align:bottom;" title="' . $LANG29[44]
        . '" onclick="return confirm(\'' . $LANG29[45] . '\');"'
        . '/>&nbsp;' . $LANG29[1];
    $actions .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    $actions .= '<input name="delbutton" type="image" src="'
        . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
        . '" style="vertical-align:text-bottom;" title="' . $LANG01[124]
        . '" onclick="return confirm(\'' . $LANG01[125] . '\');"'
        . '/>&nbsp;' . $LANG_ADMIN['delete'];

    $options = array('chkselect' => true,
                     'chkfield' => 'id',
                     'chkname' => 'selitem',
                     'chkminimum' => 0,
                     'chkall' => true,
                     'chkactions' => $actions,
                     );

    $form_arr['bottom'] = '<input type="hidden" name="type" value="faq"/>' . LB
            . '<input type="hidden" name="' . CSRF_TOKEN . '" value="' . $token . '"/>' . LB
            . '<input type="hidden" name="moderation" value="x"/>' . LB
            . '<input type="hidden" name="count" value="' . $nrows . '"/>';

    $retval .= ADMIN_simpleList('_faq_getListField', $header_arr,
                              $text_arr, $data_arr, $options, $form_arr, $token);
    return $retval;
}

/**
* Loads CSS files
*
* Uses the Plugin class to return data required by moderation.php to list
* plugin objects that need to be moderated.
*
* @param        string token The
* @return       Plugin       return HTML
*
*/
function plugin_getheadercss_faq() {
    global $_CONF;

    $styles = array();

    if ( @file_exists($_CONF['path_layout'].'plugins/faq/style.css') ) {
        $styles[] = $_CONF['path_layout'].'plugins/faq/style.css';
    } else if ( @file_exists($_CONF['path'] . 'plugins/faq/custom/style.css') ) {
        $styles[] = $_CONF['path'] . 'plugins/faq/css/custom/style.css';
    } else {
        $styles[] = $_CONF['path'] . 'plugins/faq/style.css';
    }
    return($styles);
}


/**
*   Get the URL to the configuration help file.
*
*   @param  string  $option     Config option selected
*   @param  string  $doclang    Document language
*   @return string              URL to document
*/
function plugin_getconfigelementhelp_faq($option, $doclang='english')
{
    global $_CONF, $_FAQ_CONF;

    $file = 'config.html';

    $doc_url = $_CONF['site_url'] . '/faq/docs/'.$doclang.'/config.html';

     if (empty($doc_url))
        return false;

    if (!empty($option)) {
        $doc_url .= '#desc_' . $option;
    }

    return array($doc_url, 0);
}

/**
*   Returns the search information
*
*   @return array
*/
function plugin_searchtypes_faq()
{
    global $_CONF, $_FAQ_CONF, $_USER, $LANG_FAQ;

    $tmp['faq'] = $LANG_FAQ['plugin_name'];

    return $tmp;
}

/**
* this searches for FAQ matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
* @query            string          Keywords user is looking for
* @datestart        date/time       Start date to get results for
* @dateend          date/time       End date to get results for
* @topic            string          The topic they were searching in
* @type             string          Type of items they are searching
* @author           string          Get all results by this author
*
*/
function plugin_dopluginsearch_faq($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_USER, $_FAQ_CONF, $LANG_FAQ, $_TABLES, $_CONF;

    $query = trim(DB_escapeString(htmlspecialchars($query)));

    $sql = " SELECT id AS id, answer AS description,answer,question,UNIX_TIMESTAMP(last_updated) AS date, question AS title, hits, CONCAT('/faq/index.php?id=',id) AS url ";
    $sql .= "FROM {$_TABLES['faq_questions']} ";
    $sql .= "WHERE published=1 ";

    $search = new SearchCriteria('faq', 'FAQ');
    $columns = array('answer');
    $sql .= $search->getDateRangeSQL('AND', '(UNIX_TIMESTAMP(last_updated))', $datestart, $dateend);
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}


/**
* Return information for a FAQ
*
* @param    string  $id         file ID or '*'
* @param    string  $what       comma-separated list of properties
* @param    int     $uid        user ID or 0 = current user
* @param    array   $options    (reserved for future extensions)
* @return   mixed               string or array of strings with the information
*
*/
function plugin_getiteminfo_faq($id, $what, $uid = 0, $options = array())
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $LANG_FAQ;

    $properties = explode(',', $what);
    $fields = array();
    foreach ($properties as $p) {
        switch ($p) {
            case 'date' :
            case 'date-modified':
            case 'date-created' :
                $fields[] = 'UNIX_TIMESTAMP(last_updated) AS unixdate';
                break;
            case 'description':
                $fields[] = 'question';
                $fields[] = 'answer';
                break;
            case 'excerpt':
                $fields[] = 'question';
                $fields[] = 'answer';
                break;
            case 'searchidx' :
                $fields[] = 'question';
                $fields[] = 'answer';
                break;
            case 'raw-description' :
                $fields[] = 'question';
                $fields[] = 'answer';
                break;
            case 'id':
                $fields[] = 'id';
                break;
            case 'title':
                $fields[] = 'question';
                break;
            case 'label':
            case 'url':
            case 'status' :
                $fields[] = 'id';
                $fields[] = 'published';
                break;
            case 'author' :
            case 'author_name' :
                $fields[] = 'owner_id';
                break;
            case 'hits' :
                $fields[] = 'hits';
                break;
            case 'perms' :

                break;
            default:
                break;
        }
    }

    $fields = array_unique($fields);

    if (count($fields) == 0) {
        $retval = array();

        return $retval;
    }

    if ($id == '*') {
        $where = '';
        $permOp = 'WHERE published=1 ';
    } else {
        $where = " WHERE id = '" . DB_escapeString($id) . "' ";
        $permOp = 'AND';
    }

    $sql = "SELECT " . implode(',', $fields)
            . " FROM {$_TABLES['faq_questions']} "
            . " " . $where;
    if ($id != '*') {
        $sql .= ' LIMIT 1';
    }

    $result = DB_query($sql);
    $numRows = DB_numRows($result);

    $retval = array();
    for ($i = 0; $i < $numRows; $i++) {
        $A = DB_fetchArray($result);

        $props = array();
        foreach ($properties as $p) {
            switch ($p) {
                case 'date' :
                case 'date-created' :
                case 'date-modified':
                    $props[$p] = $A['unixdate'];
                    break;
                case 'description':
                    $props[$p] = nl2br($A['question']);
                    break;
                case 'excerpt':
                    $props[$p] = nl2br(FAQ_truncate($A['question'],300,'...'));
                    break;
                case 'searchidx' :
                    $props[$p] = $A['question'].' '.$A['answer'];
                    break;
                case 'raw-description':
                    $props[$p] = $A['answer'];
                    break;
                case 'id':
                    $props['id'] = $A['id'];
                    break;
                case 'title':
                    $props['title'] = $A['question'];
                    break;
                case 'url':
                    if (empty($A['id'])) {
                        $props['url'] = COM_buildURL($_CONF['site_url'].'/faq/index.php?id=' . $id);
                    } else {
                        $props['url'] = COM_buildURL($_CONF['site_url'].'/faq/index.php?id=' . $A['id']);
                    }
                    break;
                case 'label':
                    $props['label'] = $LANG_FAQ['plugin_name'];
                    break;
                case 'status':
                    $props['status'] = $A['published'] == 1 ? 1 : 0;
                    break;
                case 'author' :
                    $props['author'] = $A['owner_id'];
                    break;
                case 'author_name' :
                    $props['author_name'] = $A['owner_id'];
                    break;
                case 'hits' :
                    $props['hits'] = $A['hits'];
                    break;
                case 'perms' :
                    $p_anon = 0;
                    $p_member = 0;
                    $props['perms'] = array(
                        'owner_id' => 2,
                        'group_id' => 3,
                        'perm_owner' => 3,
                        'perm_group' => 2,
                        'perm_members' => 2,
                        'perm_anon' => 2,
                    );
                    break;
                default:
                    $props[$p] = '';
                    break;
            }
        }

        $mapped = array();

        foreach ($props as $key => $value) {
            if ($id == '*') {
                if ($value != '') {
                    $mapped[$key] = $value;
                }
            } else {
                $mapped[$key] = $value;
            }
        }

        if ($id == '*') {
            $retval[] = $mapped;
        } else {
            $retval = $mapped;
            break;
        }
    }

    if (($id != '*') && (count($retval) == 1)) {
        $tRet = array_values($retval);
        $retval = $tRet[0];
    }

    return $retval;
}

/**
* Called by the plugin Editor to run the SQL Update for a plugin update
*/
function plugin_upgrade_faq() {
    global $_CONF, $_TABLES,$_FAQ_CONF;

    require_once $_CONF['path'] . 'plugins/faq/upgrade.php';

    return faq_upgrade();
}


/**
*   Get the currently installed version.
*
*   @return string  version number
*/
function plugin_chkVersion_faq()
{
    global $_FAQ_CONF;

    return $_FAQ_CONF['pi_version'];
}

/**
*   Determine if current user is a moderator
*
*   @return string  version number
*/
function plugin_ismoderator_faq()
{
    global $_CONF, $_USER;

    if ( SEC_hasRights('faq.admin') ) return true;
    return false;
}


/**
* Called when plugin is enabled / disabled via the plugin administration
* screen.
*
* @param   boolean  $state - true if enabling, false if disabling
*/
function plugin_enablestatechange_faq( $enable )
{
    global $_TABLES;

    if ( $enable == FALSE ) {
        DB_query("UPDATE {$_TABLES['blocks']} SET is_enabled=0 WHERE name='block_faq'",1);
    } else {
        DB_query("UPDATE {$_TABLES['blocks']} SET is_enabled=1 WHERE name='block_faq'",1);
    }

    return 1;
}

/*
	Truncated text to the nearest word based on a character count - substr()
	http://www.beliefmedia.com/php-truncate-functions
	preg-match()
	http://php.net/manual/en/function.preg-match.php
*/
function FAQ_truncate($string, $length, $trimmarker = '') {
    $strlen = strlen($string);
    /* mb_substr forces a break at $length if no word (space) boundary */
    $string = trim(utf8_substr($string, 0, $strlen));
    if ($strlen > $length) {
        preg_match('/^.{1,' . ($length - strlen($trimmarker)) . '}\b/su', $string, $match);
        $string = trim($match['0']) . $trimmarker;
    } else {
        $string = trim($string);
    }
    return $string;
}
?>