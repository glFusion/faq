<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Administration Page
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

// Only let admin users access this page
if (!SEC_hasRights('faq.admin')) {
    COM_errorLog("Someone has tried to access the FAQ Admin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_FAQ['access_denied']);
    $display .= $LANG_FAQ['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}
USES_lib_admin();

/*
 * Display admin list of all FAQs
*/
function listFaq()
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $LANG_ADMIN, $LANG_FAQ, $LANG09, $_IMAGE_TYPE;

    $retval = "";

    $header_arr = array(
        array('text' => $LANG_FAQ['edit'], 'field' => 'faq_id',     'sort' => false, 'align' => 'center'),
        array('text' => $LANG_FAQ['question'], 'field' => 'question',   'sort' => true, 'align' => 'left'),
        array('text' => $LANG_FAQ['category'], 'field' => 'category',   'sort' => true, 'align' => 'left'),
        array('text' => $LANG_FAQ['helpful_yes'], 'field' => 'helpful_yes', 'sort' => true, 'align' => 'center'),
        array('text' => $LANG_FAQ['helpful_no'], 'field' => 'helpful_no','sort' => true, 'align' => 'center'),
        array('text' => $LANG_FAQ['views'], 'field' => 'hits','sort' => true, 'align' => 'center'),
        array('text' => $LANG_FAQ['draft'], 'field' => 'draft',  'sort' => true, 'align' => 'center'),
    );

    $defsort_arr = array('field'     => $_FAQ_CONF['question_sort_field'],
                         'direction' => $_FAQ_CONF['question_sort_dir']);
    $text_arr = array(
            'form_url'      => $_CONF['site_admin_url'] . '/plugins/faq/index.php?faqlist=x',
            'help_url'      => '',
            'has_search'    => true,
            'has_limit'     => true,
            'has_paging'    => true,
            'no_data'       => $LANG_FAQ['no_faqs'],
    );

    if (!empty ($_GET['catfilter'])) {
        $current_category = COM_applyFilter($_GET['catfilter']);
    } elseif (!empty ($_POST['catfilter'])) {
        $current_category = COM_applyFilter($_POST['catfilter']);
    } else {
        if ( SESS_isSet('faq_admin_category') ) {
            $current_category = SESS_getVar('faq_admin_category');
        } else {
            $current_category = $LANG09[9];
        }
    }
    SESS_setVar('faq_admin_category',$current_category);

    if ($current_category == $LANG09[9]) {
        $categoryQuery = " ";
    } else {
        $categoryQuery = " AND cat_id=".(int) $current_category;
    }

    $category_select = '';

    // build category list
    $sql = "SELECT * FROM {$_TABLES['faq_categories']} ORDER BY title ASC";
    $result = DB_query($sql);
    $resultSet = DB_fetchAll($result);
    foreach ($resultSet AS $record) {
        $category_select .= '<option value="'.$record['cat_id'].'"';
        if ($current_category == $record['cat_id']) {
            $category_select .= ' selected="selected"';
        }
        $category_select .= '>' . $record['title'] . '</option>';
    }

    $allcategories = '<option value="' .$LANG09[9]. '"';
    if ($current_category == $LANG09[9]) {
        $allcategories .= ' selected="selected"';
    }
    $allcategories .= '>' .$LANG09[9]. '</option>';

    $filter = $LANG_FAQ['category']
        . ': <select name="catfilter" style="width: 125px" onchange="this.form.submit()">'
        . $allcategories . $category_select . '</select>';

    $sql = "SELECT id, id AS faq_id,cat_id AS category,question,answer,draft,helpful_yes,helpful_no,hits "
            . "FROM {$_TABLES['faq_questions']} WHERE 1=1 ";

    $query_arr = array('table' => 'faq_questions',
                        'sql' => $sql,
                        'query_fields' => array('question','cat_id'),
                        'default_filter' => $categoryQuery,
                        'group_by' => "");

    $actions = '<input name="delsel" type="image" src="'
            . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
            . '" style="vertical-align:bottom;" title="' . $LANG_FAQ['delete_checked']
            . '" onclick="return confirm(\'' . $LANG_FAQ['delete_confirm'] . '\');"'
            . ' value="x" '
            . '/>&nbsp;' . $LANG_FAQ['delete_checked'];

    $option_arr = array('chkselect' => true,
            'chkfield' => 'faq_id',
            'chkname' => 'faq_ids',
            'chkminimum' => 0,
            'chkall' => true,
            'chkactions' => $actions
    );

    $token = SEC_createToken();

    $formfields = '
        <input name="action" type="hidden" value="delete">
        <input type="hidden" name="' . CSRF_TOKEN . '" value="'. $token .'">
    ';

    $form_arr = array(
        'top' => $formfields
    );

    $retval .= ADMIN_list('taglist', 'FAQ_getListField', $header_arr,
    $text_arr, $query_arr, $defsort_arr, $filter, "", $option_arr, $form_arr);

    return $retval;
}

/*
 * Display admin list of all FAQs
*/
function listCategories()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_FAQ, $_IMAGE_TYPE;

    $retval = "";

    categoryReorder();

    $header_arr = array(
        array('text' => $LANG_FAQ['edit'], 'field' => 'cat_id', 'sort' => false, 'align' => 'center'),
        array('text' => $LANG_FAQ['title'], 'field' => 'title', 'sort' => true, 'align' => 'left'),
        array('text' => $LANG_FAQ['description'], 'field' => 'description', 'sort' => true, 'align' => 'left'),
        array('text' => $LANG_FAQ['number_of_questions'], 'field' => 'num_questions', 'sort' => true, 'align' => 'center'),
        array('text' => $LANG_FAQ['sort_order'], 'field' => 'sort_order', 'sort' => true, 'align' => 'center'),
    );
    $defsort_arr = array('field'     => 'sort_order',
                         'direction' => 'ASC');
    $text_arr = array(
            'form_url'      => $_CONF['site_admin_url'] . '/plugins/faq/index.php?catlist=x',
            'help_url'      => '',
            'has_search'    => true,
            'has_limit'     => true,
            'has_paging'    => true,
            'no_data'       => $LANG_FAQ['no_cats'],
    );

    $sql = "SELECT cat_id, cat_id AS id, cat_id AS num_questions, title,description,sort_order "
            . "FROM {$_TABLES['faq_categories']} ";

    $query_arr = array('table' => 'faq_categories',
                        'sql' => $sql,
                        'query_fields' => array('title'),
                        'default_filter' => " WHERE 1=1 ",
                        'group_by' => "");

    $filter = '';

    $actions = '<input name="delselcat" type="image" src="'
            . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
            . '" style="vertical-align:bottom;" title="' . $LANG_FAQ['delete_category_checked']
            . '" onclick="return confirm(\'' . $LANG_FAQ['delete_category_confirm'] . '\');"'
            . ' value="x" '
            . '/>&nbsp;' . $LANG_FAQ['delete_checked'];

    $option_arr = array('chkselect' => true,
            'chkfield' => 'cat_id',
            'chkname' => 'cat_ids',
            'chkminimum' => 0,
            'chkall' => true,
            'chkactions' => $actions
    );

    $token = SEC_createToken();

    $formfields = '
        <input name="action" type="hidden" value="delete">
        <input type="hidden" name="' . CSRF_TOKEN . '" value="'. $token .'">
    ';

    $form_arr = array(
        'top' => $formfields
    );

    $retval .= ADMIN_list('taglist', 'FAQ_getListField', $header_arr,
    $text_arr, $query_arr, $defsort_arr, $filter, "", $option_arr, $form_arr);

    return $retval;
}

function FAQ_getListField($fieldname, $fieldvalue, $A, $icon_arr, $token = "")
{
    global $_CONF, $_FAQ_CONF, $_USER, $_TABLES, $LANG_ADMIN, $LANG04, $LANG28, $_IMAGE_TYPE;

    $retval = '';

    static $catIndex = array();

    $filter = \sanitizer::getInstance();
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(false);
    $filter->setCensorData(true);

    switch ($fieldname) {
        case 'cat_id' :
            $url = $_CONF['site_admin_url'].'/plugins/faq/index.php?editcat=x&catid='.$A['cat_id'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'faq_id' :
            $url = $_CONF['site_admin_url'].'/plugins/faq/index.php?editfaq=x&faqid='.$A['faq_id'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'category' :
            if (!isset($catIndex[$fieldvalue])) {
                $filter->setPostMode('text');
                $cat_title = DB_getItem($_TABLES['faq_categories'],'title','cat_id='.(int) $fieldvalue);
                $catIndex[$fieldvalue] = $filter->displayText($cat_title);
            }
            $retval = $catIndex[$fieldvalue];
            break;

        case 'title' :
            $filter->setPostMode('text');
            $retval = $filter->displayText($fieldvalue);
            break;

        case 'description' :
            $filter->setPostMode('text');
            $retval = $filter->displayText($fieldvalue);
            break;

        case 'draft' :
            if ( (int) $fieldvalue == 1 ) {
                $retval = '<i class="uk-icon uk-icon-times uk-text-danger"></i>';
            } else {
                $retval = '';
            }
            break;

        case 'num_questions' :
            $retval = (int) DB_count($_TABLES['faq_questions'],'cat_id',(int) $A['cat_id']);
            break;

        case 'sort_order' :
            $retval = (int) $fieldvalue;
            break;

        case 'helpful_yes' :
        case 'helpful_no'  :
            if ((int) $fieldvalue == 0) {
                $retval = '-';
            } else {
                $retval = (int) $fieldvalue;
            }
            break;

        case 'question' :
            $filter->setPostMode('text');
            $title = $filter->displayText($fieldvalue);
            $link = COM_buildURL($_CONF['site_url'].'/faq/index.php?id=' . $A['id'].'&amp;src=adm');
            $retval = '<a href="'.$link.'">'.$title.'</a>';
//            $retval = $filter->displayText($fieldvalue);
            break;

        case 'hits' :
            $retval = (int) $fieldvalue;
            break;

        default :
           $retval = $fieldvalue;
           break;
    }

    return $retval;
}

function saveFaq()
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $dt = new \Date('now',$_CONF['timezone']);

    $faq_id     = (int) COM_applyFilter($_POST['id'],true);
    $cat_id     = (int) COM_applyFilter($_POST['cat_id'],true);
    $question   = $_POST['question'];
    $answer     = $_POST['answer'];
    $draft      = (isset($_POST['draft']) ? 1 : 0);

    $keywords   = $_POST['keywords'];

//var_dump($_POST);exit;

    $_POST['draft'] = $draft;

    // form validation
    if ($cat_id <= 0) {
        COM_setMsg($LANG_FAQ['error_no_cat'],'error',true);
        return editFaq($_POST,false);
    }
    if ($question == '') {
        COM_setMsg($LANG_FAQ['error_no_question'],'error',true);
        return editFaq($_POST,false);
    }
    if ($answer == '') {
        COM_setMsg($LANG_FAQ['error_no_answer'],'error',true);
        return editFaq($_POST,false);
    }

    $filter = new sanitizer();

    $last_updated = $dt->toMySQL(true);

    $owner_id = $_USER['uid'];

    if ( $faq_id == 0 ) {
        $sql = "INSERT INTO {$_TABLES['faq_questions']} (cat_id,draft,question,answer,owner_uid,last_updated,keywords) "
               ." VALUES ("
               . $filter->prepareForDB($cat_id).","
               . (int) $draft .","
               ."'".$filter->prepareForDB($question)."',"
               ."'".$filter->prepareForDB($answer)."',"
               . $filter->prepareForDB($owner_id).","
               ."'".$filter->prepareForDB($last_updated)."',"
               ."'".$filter->prepareForDB($keywords)."'"
               .");";
        $result = DB_query($sql);
        $faq_id = DB_insertId($result);
    } else {
        $sql = "UPDATE {$_TABLES['faq_questions']} SET "
               ."cat_id=".(int) $cat_id.","
               ."draft=".(int) $draft.","
               ."question='".$filter->prepareForDB($question)."',"
               ."answer='".$filter->prepareForDB($answer)."',"
               ."keywords='".$filter->prepareForDB($keywords)."'";
        if (!isset($_POST['silent_update'])) {
            $sql .= ",last_updated='".$filter->prepareForDB($last_updated)."'";
        }

        $sql .= " WHERE id=".(int) $faq_id;
        $result = DB_query($sql);
    }
    PLG_itemSaved($faq_id,'faq');

    COM_setMsg( $LANG_FAQ['faq_saved'], 'warning' );

    SEC_setCookie ($_CONF['cookie_name'].'adveditor', 'exired',
                    time() - 3600, $_CONF['cookie_path'],
                    $_CONF['cookiedomain'], $_CONF['cookiesecure'],false);

    $c = \glFusion\Cache::getInstance();
    $c->deleteItemsByTag('faq'.(int)$faq_id);
    $c->deleteItemsByTags(array('menu','whatsnew','faqindex'));

    if (isset($_POST['src']) && $_POST['src'] == 'faq') {
        echo COM_refresh($_CONF['site_url'].'/faq/index.php?id='.(int)$faq_id);
    }
    return listFaq();
}

function editFaq($data,$preview = false)
{
    global $_CONF, $_FAQ_CONF, $_USER, $_TABLES, $LANG_FAQ, $LANG_ADMIN;

    $retval = '';
    $display = '';

    $dt = new \Date('now',$_CONF['timezone']);

    $result = DB_query("SELECT COUNT(*) AS count FROM {$_TABLES['faq_categories']}");
    $catCountRec = DB_fetchArray($result);
    $categoryCount = $catCountRec['count'];

    if ($categoryCount == 0) {
        return $LANG_FAQ['no_cats_admin' ];
    }

    $styleSheet = faq_getStylesheet();
    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($styleSheet);

    // load tag editor
    $outputHandle->addLinkStyle($_CONF['site_admin_url'].'/plugins/faq/js/jquery.tagit.css');
    $outputHandle->addLinkStyle($_CONF['site_admin_url'].'/plugins/faq/js/tagit.ui-zendesk.css');
    $outputHandle->addLinkScript($_CONF['site_admin_url'].'/plugins/faq/js/tag-it.min.js');

    $A['id']            = $data['id'];
    $A['cat_id']        = $data['cat_id'];
    $A['draft']         = $data['draft'];
    $A['last_updated']  = isset($data['last_updated']) ? $data['last_updated'] : $dt->toMySQL(true);
    $A['question']      = $data['question'];
    $A['answer']        = $data['answer'];
    $A['keywords']      = $data['keywords'];
    $A['owner_uid']     = isset($data['owner_id']) ? $data['owner_uid'] : $_USER['uid'];
    $A['hits']          = isset($data['hits']) ? $data['hits'] : 0;
    $A['helpful_yes']   = isset($data['helpful_yes']) ? $data['helpful_yes'] : 0;
    $A['helpful_no']    = isset($data['helpful_no']) ? $data['helpful_no'] : 0;
    $A['silent_update'] = isset($data['silent_update']) ? 1 : 0;

    if (isset($_POST['editor'])) {
        $editMode = COM_applyFilter($_POST['editor']);
    } else {
        $editMode = $_FAQ_CONF['default_edit_mode'];
    }

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates/admin');
    $T->set_file ('form','edit_faq.thtml');

    if ($preview == true) {
        $previewTemplate = new Template ($_CONF['path'] . 'plugins/faq/templates');
        $previewTemplate->set_file('page','faq-article.thtml');

        $previewTemplate->set_var('preview',true);

        if (isset($_FAQ_CONF['faq_title']) && $_FAQ_CONF['faq_title'] != '') {
            $previewTemplate->set_var('faq_title',$_FAQ_CONF['faq_title']);
        } else {
            $previewTemplate->set_var('faq_title',$LANG_FAQ['faq_title']);
        }

        $dt = new \Date($A['last_updated'],$_USER['tzid']);

        if ( !COM_isAnonUser() ) {
            if ( empty( $_USER['format'] )) {
                $dateformat = $_CONF['date'];
            } else {
                $dateformat = $_USER['format'];
            }
        } else {
            $dateformat = $_CONF['date'];
        }

        $cat_title = DB_getItem($_TABLES['faq_categories'],'title','cat_id='.(int) $A['cat_id']);

        $question = faq_parse($A['question'],'text');
        $cat_title = faq_parse($cat_title,'text');
        $answer = faq_parse($A['answer'],'html');

        $previewTemplate->set_var(array(
            'id'                => $A['id'],
            'question'          => $question,
            'answer'            => $answer,
            'cat_title'         => $cat_title,
            'last_updated'      => $dt->format($dateformat,true),
            'lang_last_updated' => $LANG_FAQ['last_updated'],
        ));

        $previewTemplate->parse('output', 'page');
        $previewPage = $previewTemplate->finish($previewTemplate->get_var('output'));
        $T->set_var('show_preview',true);
    } else {
        $previewPage = '';
    }

    $T->set_var(array(
        'preview_page'      => $previewPage,
        'lang_faq'          => $LANG_FAQ['faq'],
        'lang_category'     => $LANG_FAQ['category'],
        'lang_title'        => $LANG_FAQ['title'],
        'lang_question'     => $LANG_FAQ['question'],
        'lang_answer'       => $LANG_FAQ['answer'],
        'lang_draft'        => $LANG_FAQ['draft'],
        'lang_save'         => $LANG_FAQ['save'],
        'lang_cancel'       => $LANG_FAQ['cancel'],
        'lang_delete'       => $LANG_FAQ['delete'],
        'lang_delete_confirm' => $LANG_FAQ['delete_confirm_faq'],
        'lang_timeout'      => $LANG_ADMIN['timeout_msg'],
        'lang_hits'         => $LANG_FAQ['views'],
        'lang_helpful_yes'  => $LANG_FAQ['helpful_yes'],
        'lang_helpful_no'   => $LANG_FAQ['helpful_no'],
        'lang_reset_stats'  => $LANG_FAQ['reset_stats'],
        'lang_unsaved'      => $LANG_FAQ['unsaved_data'],
        'lang_preview_help' => $LANG_FAQ['preview_help'],
        'lang_preview'      => $LANG_FAQ['preview'],
        'lang_faq_editor'   => $LANG_FAQ['faq_editor'],
        'lang_silent_edit'  => $LANG_FAQ['silent_edit'],
        'lang_keywords'     => $LANG_FAQ['keywords'],
        'visual_editor'     => $LANG_FAQ['visual'],
        'html_editor'       => $LANG_FAQ['html'],
        'faq_css'           => $styleSheet,
        'edit_mode'         => $editMode,
    ));

    $wysiwyg = PLG_requestEditor('faq', 'faq_editor', $_CONF['path'] . 'plugins/faq/templates/admin/faq_wysiwyg.thtml');

    if (isset($_GET['src']) && $_GET['src'] == 'faq') {
        $T->set_var('src','faq');
    } else if (isset($_POST['src']) && $_POST['src'] == 'faq' ) {
        $T->set_var('src','faq');
    } else {
        $T->set_var('src','admin');
    }
    if (isset($_POST['mod']) && $_POST['mod'] == '1') {
        $T->set_var('mod',1);
    } else {
        $T->set_var('mod',0);
    }

    if ($A['id'] == 0) {
        $T->set_var('new_faq',true);
    }

    $user_select= COM_optionList($_TABLES['users'], 'uid,username',$A['owner_uid']);

    $category_select = '';

    // build category list
    $sql = "SELECT * FROM {$_TABLES['faq_categories']} ORDER BY title ASC";
    $result = DB_query($sql);
    $resultSet = DB_fetchAll($result);
    foreach ($resultSet AS $record) {
        $category_select .= '<option value="'.$record['cat_id'].'"';
        if ($A['cat_id'] == $record['cat_id']) {
            $category_select .= ' selected="selected"';
        }
        $category_select .= '>' . $record['title'] . '</option>';
    }

    $draftChecked = '';
    if ( $A['draft'] ) {
        $draftChecked = ' checked="checked" ';
    }
    $silentChecked = '';
    if ($A['silent_update']) {
        $silentChecked = ' checked="checked" ';
    }

    $filter = \sanitizer::getInstance();
    $AllowedElements = $filter->makeAllowedElements($_FAQ_CONF['allowed_html']);
    $filter->setAllowedelements($AllowedElements);

    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(false);
    $filter->setCensorData(false);
    $filter->setPostmode('html');

    $question = $filter->editableText($A['question']);
    $answer   = $filter->editableText($A['answer']);
    $keywords = $filter->editableText($A['keywords']);

    $T->set_var(array(
        'row_id'            => $A['id'],
        'row_faqid'         => $A['id'],
        'row_cat_id'        => $A['cat_id'],
        'row_draft'         => $A['draft'],
        'row_lastupdated'   => $A['last_updated'],
        'row_question'      => $question,
        'row_answer'        => $answer,
        'row_keywords'      => $keywords,
        'row_hits'          => $A['hits'],
        'row_helpful_yes'   => $A['helpful_yes'],
        'row_helpful_no'    => $A['helpful_no'],
        'draft_checked'     => $draftChecked,
        'silent_checked'    => $silentChecked,
        'row_owner_uid'     => $A['owner_uid'],
        'user_select'       => $user_select,
        'category_select'   => $category_select,
        'sec_token_name'    => CSRF_TOKEN,
        'sec_token'         => SEC_createToken(),
    ));

    // build unique list of existing tags
    $keywordArray = array();
    $result = DB_query("SELECT keywords FROM {$_TABLES['faq_questions']}");
    while (($row = DB_fetchArray($result,false)) != null ) {
        if (!empty($row['keywords'])) {
            $keywordArray = array_merge($keywordArray,explode(',',$row['keywords']));
        }
    }
    $keywordArray = array_unique($keywordArray);
    array_walk_recursive($keywordArray, function(&$item, $key) {
        $item = addslashes($item);
    });
    $keywords = "'".implode("','",$keywordArray)."'";
    $T->set_var('keyword_lookup',$keywords);

    PLG_templateSetVars('faq_editor',$T);

    SEC_setCookie ($_CONF['cookie_name'].'adveditor', SEC_createTokenGeneral('advancededitor'),
                    time() + 1200, $_CONF['cookie_path'],
                    $_CONF['cookiedomain'], $_CONF['cookiesecure'],false);

    $T->parse('output', 'form');
    $retval .= $T->finish($T->get_var('output'));
    return $retval;
}

function editCategory($data)
{
    global $_CONF, $_FAQ_CONF, $_USER, $_TABLES, $LANG_FAQ;

    $retval = '';
    $display = '';

    $dt = new \Date('now',$_CONF['timezone']);

    $styleSheet = faq_getStylesheet();
    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($styleSheet);

    $A['cat_id']        = $data['cat_id'];
    $A['title']         = $data['title'];
    $A['description']   = $data['description'];
    $A['sort_order']    = $data['sort_order'];
    $A['owner_id']      = $data['owner_id'];
    $A['group_id']      = $data['group_id'];
    $A['perm_owner']    = $data['perm_owner'];
    $A['perm_group']    = $data['perm_group'];
    $A['perm_members']  = $data['perm_members'];
    $A['perm_anon']     = $data['perm_anon'];
    $A['last_updated']  = $dt->toMySQL(true);

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates/admin');
    $T->set_file ('form','edit_category.thtml');

    if ($A['cat_id'] == 0) {
        $T->set_var('new_cat',true);
    }

    $T->set_var(array(
        'lang_category'     => $LANG_FAQ['category'],
        'lang_title'        => $LANG_FAQ['title'],
        'lang_description'  => $LANG_FAQ['description'],
        'lang_display_after'=> $LANG_FAQ['display_after'],
        'lang_sort_order'   => $LANG_FAQ['sort_order'],
        'lang_owner'        => $LANG_FAQ['owner'],
        'lang_group'        => $LANG_FAQ['group'],
        'lang_permissions'  => $LANG_FAQ['permissions'],
        'lang_unsaved'      => $LANG_FAQ['unsaved_data'],
        'lang_save'         => $LANG_FAQ['save'],
        'lang_cancel'       => $LANG_FAQ['cancel'],
        'lang_delete'       => $LANG_FAQ['delete'],
        'lang_delete_confirm' => $LANG_FAQ['delete_confirm_cat'],
    ));

    $user_select  = COM_optionList($_TABLES['users'], 'uid,username',$A['owner_id']);
    $group_select = COM_optionList($_TABLES['groups'],'grp_id,grp_name',$A['group_id']);

    $sort_select = '<option value="0">' . $LANG_FAQ['first_position'] . '</option>';
    $result = DB_query("SELECT cat_id,title,sort_order FROM {$_TABLES['faq_categories']} ORDER BY sort_order ASC");
    $order = 10;
    while ($row = DB_fetchArray($result)) {
        if ( $A['sort_order'] != $order ) {
            $label = $row['title'];
            $test_order = $order + 5;
            $sort_select .= '<option value="' . $test_order . '"' . ($A['sort_order'] == $test_order ? ' selected="selected"' : '') . '>' . $label . '</option>';
        }
        $order += 10;
    }

    $filter = \sanitizer::getInstance();
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(false);
    $filter->setCensorData(false);
    $filter->setPostmode('html');

    $T->set_var(array(
        'row_cat_id'        => $A['cat_id'],
        'row_title'         => $filter->editableText($A['title']),
        'row_description'   => $filter->editableText($A['description']),
        'row_sort_order'    => $A['sort_order'],
        'user_select'       => $user_select,
        'group_select'      => $group_select,
        'sort_select'       => $sort_select,
        'sec_token_name'    => CSRF_TOKEN,
        'sec_token'         => SEC_createToken(),
        'permissions_editor' => SEC_getPermissionsHTML($A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon'])
    ));

    if (!empty($faq_id)) {
        $T->set_var ('cancel_option', '<input type="submit" value="' . $LANG_FAQ['cancel'] . '" name="mode">');
        $T->set_var('lang_cancel',$LANG_FAQ['cancel']);
    }

    $T->parse('output', 'form');
    $retval .= $T->finish($T->get_var('output'));
    return $retval;
}

function saveCategory()
{
    global $_CONF, $_TABLES, $LANG_FAQ;

    $cat_id      = isset($_POST['cat_id']) ? (int) COM_applyFilter($_POST['cat_id'],true) : -1;
    $title       = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $sort_order  = isset($_POST['sort_order']) ? (int) COM_applyFilter($_POST['sort_order'],true) : 0;
    $owner_id    = isset($_POST['owner_id']) ? (int) COM_applyFilter($_POST['owner_id'],true) : 0;
    $group_id    = isset($_POST['group_id']) ? (int) COM_applyFilter($_POST['group_id'],true) : 0;

    if (isset($_POST['perm_owner']) && isset($_POST['perm_group']) && isset($_POST['perm_members']) && isset($_POST['perm_anon'])) {
        list($perm_owner, $perm_group, $perm_members, $perm_anon) =
            SEC_getPermissionValues(    $_POST['perm_owner'],
                                        $_POST['perm_group'],
                                        $_POST['perm_members'],
                                        $_POST['perm_anon'] );

        $_POST['perm_owner'] = $perm_owner;
        $_POST['perm_group'] = $perm_group;
        $_POST['perm_members'] = $perm_members;
        $_POST['perm_anon'] = $perm_anon;
    } else {
        die('Invalid data');
    }
    if ($cat_id == -1) {
        die('Invalid data');
    }
    if ($owner_id == 0) {
        die('Invalid data');
    }
    if ($group_id == 0) {
        die('Invalid data');
    }
    if ($title == '') {
        COM_setMsg($LANG_FAQ['error_no_title'],'error',true);
        return editCategory($_POST);
    }
    if ($description == '') {
        COM_setMsg($LANG_FAQ['error_no_description'],'error',true);
        return editCategory($_POST);
    }

    $filter = new sanitizer();

    $filter->setPostmode('text');

    $dt = new Date('now',$_CONF['timezone']);
    $last_updated = $dt->toMySQL(true);
    if ( $cat_id == 0 ) {
        $sql = "INSERT INTO {$_TABLES['faq_categories']} (title,description,sort_order,last_updated,owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon) "
               ." VALUES ("
               ."'".$filter->prepareForDB($title)."',"
               ."'".$filter->prepareForDB($description)."',"
               ."'".(int) $sort_order."',"
               ."'".$filter->prepareForDB($last_updated)."',"
               . $filter->prepareForDB($owner_id).","
               . $filter->prepareForDB($group_id).","
               . $filter->prepareForDB($perm_owner).","
               . $filter->prepareForDB($perm_group).","
               . $filter->prepareForDB($perm_members).","
               . $filter->prepareForDB($perm_anon)
               .");";
        $result = DB_query($sql);
        $cat_id = DB_insertId($result);
    } else {
        $sql = "UPDATE {$_TABLES['faq_categories']} SET "
               ."title='".$filter->prepareForDB($title)."',"
               ."description='".$filter->prepareForDB($description)."',"
               ."sort_order=".(int) $sort_order.","
               ."last_updated='".$filter->prepareForDB($last_updated)."',"
               ."owner_id=".(int) $owner_id.","
               ."group_id=".(int) $group_id.","
               ."perm_owner=".(int) $perm_owner.","
               ."perm_group=".(int) $perm_group.","
               ."perm_members=".(int) $perm_members.","
               ."perm_anon=".(int) $perm_anon
               ." WHERE cat_id=".(int) $cat_id;
        $result = DB_query($sql);
    }
    PLG_itemSaved($cat_id,'faq');

    COM_setMsg( $LANG_FAQ['category_saved'], 'warning' );

    $c = \glFusion\Cache::getInstance();
    $c->deleteItemsByTags(array('faq','faqindex','menu','whatsnew'));

    return listCategories();
}

function deleteCategory()
{
    global $_CONF, $_TABLES;

    $c = \glFusion\Cache::getInstance();

    $del_ids = $_POST['cat_ids'];
    if ( is_array($del_ids) && count($del_ids) > 0 ) {
        foreach ($del_ids AS  $id ) {
            $delete_id = (int) COM_applyFilter($id,true);
            if ( $delete_id > 0 ) {
                // remove all FAQs associated with this category
                $faqResult = DB_query("SELECT id FROM {$_TABLES['faq_questions']} WHERE cat_id = " . (int) $delete_id);
                $faqRecords = DB_fetchAll($faqResult);
                foreach ($faqRecords AS $faq) {
                    DB_query("DELETE FROM {$_TABLES['faq_questions']} WHERE id=".(int) $faq['id']. " AND cat_id=".(int) $delete_id);
                    PLG_itemDeleted($faq['id'],'faq');
                    $c->deleteItemsByTag('faq'.$faq['id']);
                }
                // delete the category
                DB_query("DELETE FROM {$_TABLES['faq_categories']} WHERE cat_id=".(int) $delete_id);
            }
        }
    }
    $c->deleteItemsByTags(array('faqindex','menu','whatsnew'));

    return;
}

function deleteSingleCat()
{
    global $_CONF, $_TABLES, $LANG_FAQ;

    $c = \glFusion\Cache::getInstance();

    $cat_id = 0;

    if (!isset($_POST['cat_id'])) {
        COM_setMsg($LANG_FAQ['error_invalid_catid'],'error',true);
    } else {
        $cat_id = (int) COM_applyFilter($_POST['cat_id'],true);
        // pull full CAT record to ensure it exists
        $result = DB_query ("SELECT * FROM {$_TABLES['faq_categories']} WHERE cat_id = ".(int) $cat_id);
        if ((DB_numRows($result)) != 1 ) {
            COM_setMsg($LANG_FAQ['error_invalid_catid'],'error',true);
        } else {
            // remove all FAQs associated with this category
            $faqResult = DB_query("SELECT id FROM {$_TABLES['faq_questions']} WHERE cat_id = " . (int) $cat_id);
            $faqRecords = DB_fetchAll($faqResult);
            foreach ($faqRecords AS $faq) {
                DB_query("DELETE FROM {$_TABLES['faq_questions']} WHERE id=".(int) $faq['id']. " AND cat_id=".(int) $cat_id);
                PLG_itemDeleted($faq['id'],'faq');
                $c->deleteItemsByTag('faq'.$faq['id']);
            }
            // delete the category
            DB_query("DELETE FROM {$_TABLES['faq_categories']} WHERE cat_id=".(int) $cat_id);
            $c->deleteItemsByTags(array('faqindex','menu','whatsnew'));
        }
    }
    return;
}


function deleteFaq()
{
    global $_CONF, $_TABLES;

    $c = \glFusion\Cache::getInstance();

    $del_ids = $_POST['faq_ids'];
    if ( is_array($del_ids) && count($del_ids) > 0 ) {
        foreach ($del_ids AS  $id ) {
            $delete_id = (int) COM_applyFilter($id,true);
            if ( $delete_id > 0 ) {
                DB_query("DELETE FROM {$_TABLES['faq_questions']} WHERE id=".(int) $delete_id);
                PLG_itemDeleted($delete_id,'faq');
                $c->deleteItemsByTag('faq'.(int) $delete_id);
            }
        }
    }
    $c->deleteItemsByTags(array('faqindex','menu','whatsnew'));

    return;
}

function deleteSingleFaq()
{
    global $_CONF, $_TABLES, $LANG_FAQ;

    $c = \glFusion\Cache::getInstance();

    $faq_id = 0;

    if (!isset($_POST['id'])) {
        COM_setMsg($LANG_FAQ['error_invalid_faqid'],'error',true);
    } else {
        $faq_id = (int) COM_applyFilter($_POST['id'],true);
        // pull full FAQ record to ensure it exists
        $result = DB_query ("SELECT * FROM {$_TABLES['faq_questions']} WHERE id = ".(int) $faq_id);
        if ((DB_numRows($result)) != 1 ) {
            COM_setMsg($LANG_FAQ['error_invalid_faqid'],'error',true);
        } else {
            DB_query("DELETE FROM {$_TABLES['faq_questions']} WHERE id=".(int) $faq_id);
            PLG_itemDeleted($faq_id,'faq');
            $c->deleteItemsByTags(array('faq'.(int) $faq_id,'faqindex','menu','whatsnew'));
        }
    }

    if (isset($_POST['src']) && $_POST['src'] == 'faq') {
        echo COM_refresh($_CONF['site_url'].'/faq/index.php');
    }
}

function faq_admin_menu($action)
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $LANG_ADMIN, $LANG_FAQ;

    $retval = '';

    $menu_arr = array(
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?faqlist=x','text' => $LANG_FAQ['faq_list'],'active' => ($action == 'faqlist' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?catlist=x','text' => $LANG_FAQ['cat_list'],'active' => ($action == 'catlist' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?newfaq=x','text'=> ($action == 'editfaq' ? $LANG_FAQ['edit_faq'] : $LANG_FAQ['create_new']), 'active'=> ($action == 'editfaq' || $action == 'newfaq' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?newcat=x','text'=> ($action == 'editcat' ? $LANG_FAQ['edit_cat'] : $LANG_FAQ['create_new_cat']), 'active'=> ($action == 'editcat' || $action == 'newcat' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'], 'text' => $LANG_ADMIN['admin_home'])
    );

    switch($action) {
        case 'faqlist' :
            $panelTitle = $LANG_FAQ['faq_admin_title'];
            $helpText   = $LANG_FAQ['admin_help_faq_list'];
            break;
        case 'catlist' :
            $panelTitle = $LANG_FAQ['faq_admin_title'];
            $helpText   = $LANG_FAQ['admin_help_cat_list'];
            break;
        case 'editfaq' :
        case 'newfaq' :
            $panelTitle = $LANG_FAQ['edit_faq'];
            $helpText   = $LANG_FAQ['admin_help_faq_edit'];
            break;
        case 'editcat' :
        case 'newcat' :
            $panelTitle = $LANG_FAQ['edit_existing_cat'];
            $helpText   = $LANG_FAQ['admin_help_cat_edit'];
            break;
        default :
            $panelTitle = $LANG_FAQ['faq_admin_title'];
            $helpText   = $LANG_FAQ['admin_help'];
            break;
    }

    $retval = '<h2>'.$panelTitle.'</h2>';

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $helpText,
        $_CONF['site_url'] . '/faq/images/faq.png'
    );

    return $retval;
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
*
* Re-orders all blocks in steps of 10
*
*/
function categoryReorder()
{
    global $_TABLES;
    $sql = "SELECT * FROM {$_TABLES['faq_categories']} ORDER BY sort_order asc;";
    $result = DB_query($sql);
    $nrows = DB_numRows($result);
    $categoryOrd = 10;
    $stepNumber = 10;

    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);
        if ($A['sort_order'] != $categoryOrd) {  // only update incorrect ones
            $q = "UPDATE " . $_TABLES['faq_categories'] . " SET sort_order = '" .
                  $categoryOrd . "' WHERE cat_id = '" . $A['cat_id'] ."'";
            DB_query($q);
        }
        $categoryOrd += $stepNumber;
    }
    return true;
}

$page = '';
$display = '';
$cmd ='faqlist';

$expectedActions = array('faqlist','catlist','newfaq','editfaq','previewfaq','editcat','newcat','deletefaq','deletecat','save','delsel_x','delselcat_x');
foreach ( $expectedActions AS $action ) {
    if ( isset($_POST[$action])) {
        $cmd = $action;
    } elseif ( isset($_GET[$action])) {
        $cmd = $action;
    }
}
if ( isset($_POST['cancel'])) {
    $src = COM_applyFilter($_POST['cancel']);
    if (isset($_POST['src']) && $_POST['src'] == 'faq') {
        echo COM_refresh($_CONF['site_url'].'/faq/index.php');
    }

    switch ($_POST['type']) {
        case 'category' :
            $cmd = 'catlist';
            break;
        default :
            $cmd = 'faqlist';
            break;
    }
}

$dt = new \Date('now',$_CONF['timezone']);

switch ( $cmd ) {

    case 'newfaq' :
        if (isset($_GET['cat_id'])) {
            $cat_id = (int) COM_applyFilter($_GET['cat_id'],true);
        } else {
            $cat_id = 0;
        }
        $A['id']            = 0;
        $A['cat_id']        = $cat_id;
        $A['draft']         = 0;
        $A['last_updated']  = $dt->toMySQL(true);
        $A['question']      = '';
        $A['answer']        = '';
        $A['owner_uid']     = $_USER['uid'];
        $A['hits']          = 0;
        $A['helpful_yes']   = 0;
        $A['helpful_no']    = 0;
        $A['keywords']      = '';
        $page = editFaq($A,false);
        $pageTitle = $LANG_FAQ['edit_faq'];
        break;

    case 'editfaq' :
        if (!isset($_GET['faqid'])) {
            COM_setMsg($LANG_FAQ['error_invalid_faqid'],'error',true);
            $page = listFaq();
        } else {
            $faq_id = (int) COM_applyFilter($_GET['faqid'],true);
            $result = DB_query ("SELECT * FROM {$_TABLES['faq_questions']} WHERE id = ".(int) $faq_id);
            if ((DB_numRows($result)) == 1 ) {
                $A = DB_fetchArray($result);
                $page = editFaq($A,false); // no preview
                $pageTitle = $LANG_FAQ['edit_faq'];
            } else {
                COM_setMsg($LANG_FAQ['error_invalid_faqid'],'error',true);
                $page = listFaq();
            }
        }
        break;

    case 'previewfaq' :
        $A = $_POST;
        $A['draft'] = (isset($_POST['draft']) ? 1 : 0);
        if ($A['id'] == 0 ) {
            $cmd = 'newfaq';
        } else {
            $cmd = 'editfaq';
        }
        $page = editFaq($A,true);
        $pageTitle = $LANG_FAQ['edit_faq'];
        break;

    case 'newcat' :
        $sort_order = 10;
        $sql = "SELECT sort_order, MAX(sort_order) AS max FROM {$_TABLES['faq_categories']};";
        $result = DB_query($sql);
        if (DB_numRows($result) > 0 ) {
            $max = DB_fetchArray($result);
            $sort_order = (int) $max['max'] + 10;
        }
        $A['cat_id']        = 0;
        $A['title']         = '';
        $A['description']   = '';
        $A['sort_order']    = $sort_order;
        $A['owner_id']      = $_USER['uid'];
        $A['group_id']      = DB_getItem($_TABLES['groups'],'grp_id','grp_name = "FAQ Admin"');

        $A['perm_owner']    = $_FAQ_CONF['default_permissions_category'][0];
        $A['perm_group']    = $_FAQ_CONF['default_permissions_category'][1];
        $A['perm_members']  = $_FAQ_CONF['default_permissions_category'][2];
        $A['perm_anon']     = $_FAQ_CONF['default_permissions_category'][3];
        $A['last_updated']  = 0;

        $page = editCategory($A);
        $pageTitle = $LANG_FAQ['edit_cat'];
        break;

    case 'editcat' :
        if (!isset($_GET['catid'])) {
            COM_setMsg($LANG_FAQ['error_invalid_catid'],'error',true);
            $page = listCategories();
        } else {
            $cat_id = (int) COM_applyFilter($_GET['catid'],true);
            $result = DB_query ("SELECT * FROM {$_TABLES['faq_categories']} WHERE cat_id = ".(int) $cat_id);
            if ((DB_numRows($result)) == 1 ) {
                $A = DB_fetchArray($result);
                $page = editCategory($A);
                $pageTitle = $LANG_FAQ['edit_cat'];
            } else {
                COM_setMsg($LANG_FAQ['error_invalid_catid'],'error',true);
                $page = listCategories();
            }
        }
        break;

    case 'save' :
        if (isset($_POST['type']) && SEC_checkToken()) {
            switch ($_POST['type']) {
                case 'category' :
                    $page = saveCategory();
                    $cmd = 'catlist';
                    break;
                case 'faq' :
                    $page = saveFaq();
                    $cmd = 'faqlist';
                    break;
                default :
                    $page = listFaq();
                    $cmd = 'faqlist';
                    break;
            }
        } else {
            $page = listFaq();
        }
        break;

    case  'delselcat_x':
        if (SEC_checkToken()) {
            deleteCategory();
        }
        $cmd = 'catlist';
        $page = listCategories();
        break;

    case  'delsel_x':
        if (SEC_checkToken()) {
            deleteFaq();
        }
        $cmd = 'faqlist';
        $page = listFaq();
        break;

    case 'deletefaq' :
        if (SEC_checkToken()) {
            deleteSingleFaq();
        }
        $page = listFaq();
        $cmd = 'faqlist';
        break;

    case 'deletecat' :
        if (SEC_checkToken()) {
            deleteSingleCat();
        }
        $page = listCategories();
        $cmd = 'catlist';
        break;


    case 'catlist' :
        $page = listCategories();
        break;

    case 'faqlist' :
    default :
        $page = listFaq();
        break;
}

if (!isset($pageTitle)) $pageTitle = $LANG_FAQ['admin'];

$display  = COM_siteHeader ('menu', $pageTitle);
$display .= faq_admin_menu($cmd);
$display .= $page;
$display .= COM_siteFooter (false);
echo $display;

?>
