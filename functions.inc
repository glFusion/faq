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
*  Copyright (C) 2017-2022 by the following authors:
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

if (version_compare(GVERSION,'2.0.0','lt')) {
    if (isset($LANG_configSelect['faq']) && !isset($LANG_configselects['faq'])) {
        foreach ($LANG_configSelect['faq'] AS $cfgItem) {
            $LANG_configselects['faq'][] = array_flip($cfgItem);
        }
    }
}

/*
* Include faqman config file
*/
require_once $_CONF['path'] . 'plugins/faq/faq.php';

if (!isset($_FAQ_CONF['layout'])) {
    $_FAQ_CONF['layout'] = 1;
}
if (!isset($_FAQ_CONF['show_question_icon'])) {
    $_FAQ_CONF['show_question_icon'] = 0;
}
if (!isset($_FAQ_CONF['allowed_html'])) {
    $_FAQ_CONF['allowed_html'] = 'div[class],h1,h2,h3,pre,br,p[style],b[style],s,strong[style],i[style],em[style],u[style],strike,a[id|name|style|href|title|target],ol[style|class],ul[style|class],li[style|class],hr[style],blockquote[style],img[style|alt|title|width|height|src|align],table[style|width|bgcolor|align|cellspacing|cellpadding|border],tr[style],td[style],th[style],tbody,thead,caption,col,colgroup,span[style|class],sup,sub';
}
if (!isset($_FAQ_CONF['max_columns_category'])) {
    $_FAQ_CONF['max_columns_category'] = 3;
}
if (!isset($_FAQ_CONF['max_columns_question'])) {
    $_FAQ_CONF['max_columns_question'] = 3;
}
if (!isset($_FAQ_CONF['default_edit_mode'])) {
    $_FAQ_CONF['default_edit_mode'] = 'wysiwyg';
}

if ( !isset($_FAQ_CONF['displayblocks'])) $_FAQ_CONF['displayblocks'] = 'menu';

switch( $_FAQ_CONF['displayblocks'] ) {
    case 0 : // left only
    case 2 :
        $_FAQ_CONF['menu'] = 'menu';
        break;
    case 1 : // right only
    case 3 :
        $_FAQ_CONF['menu'] = 'none';
        break;
    default :
        $_FAQ_CONF['menu'] = 'menu';
        break;
}

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
            DB_count($_TABLES['faq_questions'],'draft',0)
        );
    }
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

    $permSQL = COM_getPermSql('AND');

    $sql = " SELECT id AS id, answer AS description,answer,question,UNIX_TIMESTAMP(a.last_updated) AS date, question AS title, hits, owner_uid AS uid,CONCAT('/faq/index.php?id=',id) AS url ";
    $sql .= "FROM {$_TABLES['faq_questions']} AS a LEFT JOIN {$_TABLES['faq_categories']} AS c ON a.cat_id=c.cat_id ";
    $sql .= " WHERE draft=0 " . $permSQL;

    if (!empty ($author)) {
        $sql .= " AND (owner_uid = '".DB_escapeString($author)."') ";
    }

    $search = new SearchCriteria('faq', $LANG_FAQ['plugin_name']);
    $columns = array('question','answer');
    $sql .= $search->getDateRangeSQL('AND', '(UNIX_TIMESTAMP(last_updated))', $datestart, $dateend);
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);

    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}


/**
  * API function provides the content of our "What's New" feed
*/
function plugin_getwhatsnew_faq() {
    global $_TABLES, $_CONF, $_FAQ_CONF, $LANG_FAQ, $LANG01;

    $retval = '';

    if ($_FAQ_CONF['whatsnew_enabled'] == 0 ) {
        return $retval;
    }

    $filter = sanitizer::getInstance();
    $filter->setReplaceTags(false);
    $filter->setCensorData(true);
    $filter->setPostmode('text');

    $items = array();

    $sql =  "SELECT * FROM {$_TABLES['faq_questions']} AS a ";
    $sql .= "LEFT JOIN {$_TABLES['faq_categories']} AS c ON a.cat_id=c.cat_id ";
    $sql .= "WHERE a.last_updated >= DATE_SUB('".$_CONF['_now']->toMySQL(true)."', INTERVAL ".$_FAQ_CONF['whatsnew_interval']." DAY ) ";
    $sql .= COM_getPermSql ('AND');
    $sql .= "AND draft=0 ORDER BY a.last_updated DESC LIMIT 15 ";

    $result = DB_query($sql);
    $faqRecords = DB_fetchAll($result);
    foreach ($faqRecords AS $faq) {
        $question = $filter->displayText($faq['question']);

        $url = COM_buildURL($_CONF['site_url'].'/faq/index.php?id=' . $faq['id']);
        $item = '<a href="'.$url.'" title="'.htmlspecialchars($question).'">'.$question.'</a>';
        $items[] = $item;
    }
    if (count($items) != 0 ) {
        $retval = array($LANG_FAQ['plugin_name'],sprintf($LANG_FAQ['whatsnew_period'],$_FAQ_CONF['whatsnew_interval']),$items);
    }
    return $retval;
}

/**
  * API function provides faq: auto tag
*/
function plugin_autotags_faq($op, $content = '', $autotag = '')
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $LANG_FAQ;

    if ($op == 'tagname' ) {
        return array('faq');
    } else if ($op == 'tagusage') {
        $tagUsage = array(
        array('namespace' => 'faq','usage' => 'answer')
        );
        return $tagUsage;
    } else if ($op == 'desc' ) {
        switch ($content) {
            case 'faq' :
            return $LANG_FAQ['desc_faq'];
            break;
            default :
            return '';
            break;
        }
    } else if ($op == 'parse') {
        if (($autotag['tag'] == 'faq')) {
            $target = '';
            $target_window = '';
            $skip = 0;
            $link = '';

            $autotag['parm2'] = str_replace("&nbsp;",' ',$autotag['parm2']);

            $faq_id = (int) COM_applyFilter($autotag['parm1'],true);

            $faqData = plugin_getiteminfo_faq($faq_id, 'url,title');

            if (count($faqData) == 2) {
                $caption = $autotag['parm2'];

                $px = explode (' ', trim(rtrim($autotag['parm2'])));
                if (is_array ($px)) {
                    foreach ($px as $part) {
                        if (substr ($part, 0, 7) == 'target:') {
                            $a = explode (':', $part);
                            $target_window = $a[1];
                            $skip++;
                        } else {
                            break;
                        }
                    }
                    if ($skip != 0) {
                        if (count ($px) > $skip) {
                            for ($i = 0; $i < $skip; $i++) {
                                array_shift ($px);
                            }
                            $caption = trim (implode (' ', $px));
                        } else {
                            $caption = $faqData['title'];
                        }
                    }
                } else {
                    $caption = trim (rtrim($autotag['parm2']));
                }

                if ($target_window != '') {
                    $target = 'target="'.$target_window.'"';
                }

                if (trim(rtrim($caption)) == '') {
                    $caption = $faqData['title'];
                }
                $link = '<a href="' . $faqData['url'] . '" ' . $target . ' >' . $caption . '</a>';
            }
            $content = str_replace ($autotag['tagstr'], $link, $content);
            return $content;
        }
    }
    return;
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
                $fields[] = 'UNIX_TIMESTAMP(a.last_updated) AS unixdate';
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
                $fields[] = 'draft';
                break;
            case 'author' :
            case 'author_name' :
                $fields[] = 'a.owner_uid';
                break;
            case 'hits' :
                $fields[] = 'hits';
                break;
            case 'perms' :
                $fields[] = 'c.owner_id AS owner';
                $fields[] = 'c.group_id';
                $fields[] = 'c.perm_owner';
                $fields[] = 'c.perm_group';
                $fields[] = 'c.perm_members';
                $fields[] = 'c.perm_anon';
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

    $permSQL = COM_getPermSql('AND');

    if ($id == '*') {
        $where = '';
        $permOp = 'WHERE a.draft=0 ';
    } else {
        $where = " WHERE a.id = '" . DB_escapeString($id) . "' ";
        $permOp = '';
    }

    $where = $where . $permOp . $permSQL;

    $sql = "SELECT " . implode(',', $fields)
            . " FROM {$_TABLES['faq_questions']} AS a LEFT JOIN {$_TABLES['faq_categories']} AS c ON a.cat_id=c.cat_id "
            . " " . $where;
    if ($id != '*') {
        $sql .= ' LIMIT 1';
    }

    $result = DB_query($sql);
    $numRows = DB_numRows($result);

    $filter = \sanitizer::getInstance();
    $AllowedElements = $filter->makeAllowedElements($_FAQ_CONF['allowed_html']);
    $filter->setAllowedelements($AllowedElements);
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(false);
    $filter->setCensorData(true);
    $filter->setPostmode('html');

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
                    $filter->setPostmode('html');
                    $props[$p] = $filter->displayText($A['answer']);
                    break;
                case 'excerpt':
                    $filter->setPostmode('html');
                    $answer = $filter->filterHTML($A['question']);
                    $answer = $filter->displayText($answer);
                    $props[$p] = FAQ_truncate($answer,300,'...');
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
                    $filter->setPostmode('text');
                    $props['title'] = $filter->displayText($A['question']);
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
                    $props['status'] = $A['draft'] == 0 ? 1 : 0;
                    break;
                case 'author' :
                    $props['author'] = $A['owner_uid'];
                    break;
                case 'author_name' :
                    $props['author_name'] = COM_getDisplayName($A['owner_uid']);
                    break;
                case 'hits' :
                    $props['hits'] = $A['hits'];
                    break;
                case 'perms' :
                    $props['perms'] = array(
                        'owner_id' => $A['owner'],
                        'group_id' => $A['group_id'],
                        'perm_owner' => $A['perm_owner'],
                        'perm_group' => $A['perm_group'],
                        'perm_members' => $A['perm_members'],
                        'perm_anon' => $A['perm_anon'],
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

function plugin_templatesetvars_faq($ctype, &$template)
{
    global $_CONF;
    if ($ctype =='ckeditor') {
        $styleSheet = faq_getStyleSheet();
        $template->set_var('faq_css',$styleSheet);
    }
}

function faq_getStylesheet()
{
    global $_CONF;

    $styleSheet = $_CONF['site_url'].'/faq/css/style.css';

    if ( @file_exists($_CONF['path_layout'].'plugins/faq/style.css') ) {
        $styleSheet = $_CONF['layout_url'].'/plugins/faq/style.css';
    } else if ( @file_exists($_CONF['path_html'] . 'faq/css/custom/style.css') ) {
        $styleSheet = $_CONF['site_url'] . '/faq/css/custom/style.css';
    }

    return $styleSheet;
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

/*
 * FAQ parser for pre-glFusion v2 sites
 */
function faq_parse($str, $postmode='text')
{
    global $_CONF, $_FAQ_CONF;

    if (version_compare(GVERSION,'2.0.0','ge')) {
        $format = new glFusion\Formatter();
        $format->setNamespace('faq');
        $format->setAction('answer');
        $format->setAllowedHTML($_FAQ_CONF['allowed_html']);
        $format->setType($postmode);
        $format->setProcessBBCode(false);
        $format->setParseURLs(false);
        $format->setConvertPre(true);
        $format->setParseAutoTags(true);
        return $format->parse($str);
    }

    $bbcode = new \StringParser_BBCode();
    $bbcode->setGlobalCaseSensitive (false);
    $filter = \sanitizer::getInstance();

    $filter->setPostmode($postmode);

    if ($postmode == 'text') {
        $bbcode->addFilter(1, 'faq_htmlspecialchars');
    }

    if ($postmode == 'html') {
        $str = str_replace('<pre>','[code]',$str);
        $str = str_replace('</pre>','[/code]',$str);
    }

    if ($postmode != 'html') {
        $bbcode->addParser(array('block','inline','link','listitem'), 'faq_nl2br');
    }

    $bbcode->addCode ('code', 'usecontent?', 'do_faq_code', array ('usecontent_param' => 'default'),
                      'code', array('listitem', 'block', 'inline', 'quote'), array ('link'));

    if ($postmode == 'html') {
       $bbcode->addParser(array('block','inline','list','listitem'), 'faq_cleanHTML');
    }

    $str = $filter->censor($str);

    $str = $bbcode->parse ($str);

    unset($bbcode);

    $str = PLG_replaceTags($str,'faq','answer');

    return $str;
}

/**
 * Encode string
 * @param $text
 * @return string
 */
function faq_htmlspecialchars($text)
{
    return (@htmlspecialchars ($text,ENT_NOQUOTES, COM_getEncodingt(),true));
}


/**
 * Converts newline to <br>
 */
function faq_nl2br($str)
{
    return str_replace(array("\r\n", "\r", "\n"), "<br>", $str);
}

/**
* Cleans (filters) HTML - only allows HTML tags specified in the
* in allowedHTML string.  This function is designed to be called
* by the stringparser class to filter everything except [code] blocks.
*
* @param        string      $message        The topic post to filter
* @return       string      filtered HTML code
*/
function faq_cleanHTML($message)
{
    global $_CONF, $_FAQ_CONF;

    $filter = \sanitizer::getInstance();

    $AllowedElements = $filter->makeAllowedElements($_FAQ_CONF['allowed_html']);
    $filter->setAllowedelements($AllowedElements);
    $filter->setPostmode('html');
    return $filter->filterHTML($message);
}

/**
 * [code] tag
 */
function do_faq_code($action, $attributes, $content, $params, $node_object)
{
    global $_FAQ_CONF;

    if ($action == 'validate') {
        return true;
    }

    static $insideCode = 0;

    if ($insideCode > 0 ) return $content;

    $insideCode = 1;

    $content = @htmlspecialchars_decode($content,ENT_QUOTES);
    $content = preg_replace('/^\s*?\n|\s*?\n$/','',$content);
    $codeblock = '<div style="width:100%;" class="php"><pre class="codeblock">'  . @htmlspecialchars ($content,ENT_NOQUOTES, COM_getEncodingt(),true) . '</pre></div>';

    $codeblock = str_replace('{','&#123;',$codeblock);
    $codeblock = str_replace('}','&#125;',$codeblock);
    $codeblock = str_replace('[','&#91;',$codeblock);
    $codeblock = str_replace(']','&#93;',$codeblock);

    $insideCode = 0;
    return $codeblock;
}

?>