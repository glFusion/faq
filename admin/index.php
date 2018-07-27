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
    global $_CONF, $_FAQ_CONF, $_TABLES, $LANG_ADMIN, $LANG_FAQ, $_IMAGE_TYPE;

    $retval = "";

    $header_arr = array(
        array('text' => $LANG_FAQ['edit'],      'field' => 'faq_id',     'sort' => false, 'align' => 'center'),
        array('text' => $LANG_FAQ['question'],  'field' => 'question',   'sort' => true, 'align' => 'left'),
        array('text' => $LANG_FAQ['category'],  'field' => 'category',   'sort' => true, 'align' => 'left'),
        array('text' => $LANG_FAQ['helpful_yes'],  'field' => 'helpful_yes', 'sort' => true, 'align' => 'center'),
        array('text' => $LANG_FAQ['helpful_no'],  'field' => 'helpful_no','sort' => true, 'align' => 'center'),
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

    $sql = "SELECT id, id AS faq_id,cat_id AS category,question,answer,draft,helpful_yes,helpful_no "
            . "FROM {$_TABLES['faq_questions']} ";

    $query_arr = array('table' => 'faq_questions',
                        'sql' => $sql,
                        'query_fields' => array('question'),
                        'default_filter' => " WHERE 1=1 ",
                        'group_by' => "");

    $filter = '';

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
    global $_CONF, $_USER, $_TABLES, $LANG_ADMIN, $LANG04, $LANG28, $_IMAGE_TYPE;

    $retval = '';

    static $catIndex = array();

    switch ($fieldname) {
        case 'cat_id' :
            $url = $_CONF['site_admin_url'].'/plugins/faq/index.php?catedit=x&catid='.$A['cat_id'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'faq_id' :
            $url = $_CONF['site_admin_url'].'/plugins/faq/index.php?faqedit=x&faqid='.$A['faq_id'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'category' :
            if (!isset($catIndex[$fieldvalue])) {
                $catIndex[$fieldvalue] = DB_getItem($_TABLES['faq_categories'],'title','cat_id='.(int) $fieldvalue);
            }
            $retval = $catIndex[$fieldvalue];
            break;

        case 'draft' :
            if ( (int) $fieldvalue == 1 ) {
                $retval = '<i class="uk-icon uk-icon-times uk-text-danger"></i>';
            } else {
                $retval = '';
//                $retval = '<i class="uk-icon uk-icon-check-circle uk-text-success"></i>';
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

        default :
           $retval = $fieldvalue;
           break;
    }

    return $retval;
}

function saveFaq()
{
    global $_CONF, $_TABLES, $_USER, $LANG_FAQ;

    // we need to do some error checking here - make sure everything
    // is set and in proper format (such as date).

    $faq_id     = (int) COM_applyFilter($_POST['faq_id'],true);
    $cat_id     = (int) COM_applyFilter($_POST['cat_id'],true);
    $question   = $_POST['question'];
    $answer     = $_POST['answer'];
    $draft  = (isset($_POST['draft']) ? 1 : 0);

    $filter = new sanitizer();
    $filter->setPostmode('text');
    $question  = $filter->filterText($question);

    $filter->setAllowedElements('div[class],h1,h2,h3,pre,br,p[style],b[style],s,strong[style],i[style],em[style],u[style],strike,a[id|name|style|href|title|target],ol[style|class],ul[style|class],li[style|class],hr[style],blockquote[style],img[style|alt|title|width|height|src|align],table[style|width|bgcolor|align|cellspacing|cellpadding|border],tr[style],td[style],th[style],tbody,thead,caption,col,colgroup,span[style|class],sup,sub');

    $filter->setPostmode('html');
    $answer = $filter->filterHTML($answer);

    $dt = new Date('now',$_CONF['timezone']);
    $last_updated = $dt->toMySQL(true);

    $owner_id = $_USER['uid'];

    if ( $faq_id == 0 ) {
        $sql = "INSERT INTO {$_TABLES['faq_questions']} (cat_id,draft,question,answer,owner_uid,last_updated) "
               ." VALUES ("
               . $filter->prepareForDB($cat_id).","
               . (int) $draft .","
               ."'".$filter->prepareForDB($question)."',"
               ."'".$filter->prepareForDB($answer)."',"
               . $filter->prepareForDB($owner_id).","
               ."'".$filter->prepareForDB($last_updated)."'"
               .");";
        $result = DB_query($sql);
        $faq_id = DB_insertId($result);
    } else {
        $sql = "UPDATE {$_TABLES['faq_questions']} SET "
               ."cat_id=".(int) $cat_id.","
               ."draft=".(int) $draft.","
               ."question='".$filter->prepareForDB($question)."',"
               ."answer='".$filter->prepareForDB($answer)."',"
               ."last_updated='".$filter->prepareForDB($last_updated)."'"
               ." WHERE id=".(int) $faq_id;
        $result = DB_query($sql);
    }
    PLG_itemSaved($faq_id,'faq');

    COM_setMsg( $LANG_FAQ['faq_saved'], 'warning' );

    CACHE_remove_instance('menu');
    CACHE_remove_instance('whatsnew');

    if (isset($_POST['src']) && $_POST['src'] == 'faq') {
        echo COM_refresh($_CONF['site_url'].'/faq/index.php?id='.(int)$faq_id);
    }

    return listFaq();
}

function editFaq($mode,$faq_id='',$cat_id=0)
{
    global $_CONF, $_USER, $_TABLES, $LANG_FAQ;

    $retval = '';
    $display = '';

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates/admin');
    $T->set_file ('form','edit_faq.thtml');

    $T->set_var(array(
        'lang_faq'          => $LANG_FAQ['faq'],
        'lang_category'     => $LANG_FAQ['category'],
        'lang_title'        => $LANG_FAQ['title'],
        'lang_question'     => $LANG_FAQ['question'],
        'lang_answer'       => $LANG_FAQ['answer'],
        'lang_draft'        => $LANG_FAQ['draft'],
        'lang_save'         => $LANG_FAQ['save'],
        'lang_cancel'       => $LANG_FAQ['cancel'],
    ));

    if ( file_exists($_CONF['path_layout'].'ck_styles.js') ) {
        $T->set_var('styleset',"stylesSet: '".$_USER['theme'].":".$_CONF['layout_url']."/ck_styles.js',");
    }
    list($cacheFile,$cacheURL) = COM_getStyleCacheLocation();
    $T->set_var(array(
        'theme'     => $_USER['theme'],
        'path_html' => $_CONF['path_html'],
        'css_url'   => $cacheURL,
    ));

    if (isset($_GET['src']) && $_GET['src'] == 'faq') {
        $T->set_var('src','faq');
    }  else {
        $T->set_var('src','admin');
    }

    if ($mode == 'faqedit' && ($faq_id != "" || $faq_id != 0)) {
        $result = DB_query ("SELECT * FROM {$_TABLES['faq_questions']} WHERE id = ".(int) $faq_id);
        $A = DB_fetchArray($result);
    } else {
        $A['id'] = '';
        $A['cat_id'] = $cat_id;
        $A['draft'] = 0;
        $A['last_updated'] = date('Y-m-d');
        $A['question']= '';
        $A['answer'] = '';
        $A['owner_uid']  = $_USER['uid'];
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

    $T->set_var(array(
        'row_id'            => $A['id'],
        'row_faqid'         => $A['id'],
        'row_cat_id'        => $A['cat_id'],
        'row_draft'         => $A['draft'],
        'row_lastupdated'   => $A['last_updated'],
        'row_question'      => $A['question'],
        'row_answer'        => $A['answer'],
        'draft_checked'     => $draftChecked,
        'user_select'       => $user_select,
        'category_select'   => $category_select,
        'sec_token_name'    => CSRF_TOKEN,
        'sec_token'         => SEC_createToken(),
    ));

    if (!empty($faq_id)) {
        $T->set_var ('cancel_option', '<input type="submit" value="' . $LANG_FAQ['cancel'] . '" name="mode">');
        $T->set_var('lang_cancel',$LANG_FAQ['cancel']);
    }

    $T->parse('output', 'form');
    $retval .= $T->finish($T->get_var('output'));
    return $retval;
}


function editCategory($mode,$cat_id='')
{
    global $_CONF, $_FAQ_CONF, $_USER, $_TABLES, $LANG_FAQ;

    $retval = '';
    $display = '';

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates/admin');
    $T->set_file ('form','edit_category.thtml');

    $T->set_var(array(
        'lang_category'     => $LANG_FAQ['category'],
        'lang_title'        => $LANG_FAQ['title'],
        'lang_description'  => $LANG_FAQ['description'],
        'lang_display_after'=> $LANG_FAQ['display_after'],
        'lang_sort_order'   => $LANG_FAQ['sort_order'],
        'lang_owner'        => $LANG_FAQ['owner'],
        'lang_group'        => $LANG_FAQ['group'],
        'lang_permissions'  => $LANG_FAQ['permissions'],
        'lang_save'         => $LANG_FAQ['save'],
        'lang_cancel'       => $LANG_FAQ['cancel'],
    ));
    if ($mode == 'catedit' && ($cat_id != "" || $cat_id != 0)) {
        $result = DB_query ("SELECT * FROM {$_TABLES['faq_categories']} WHERE cat_id = ".(int) $cat_id);
        $A = DB_fetchArray($result);
    } else {
        $sort_order = 10;
        $sql = "SELECT sort_order, MAX(sort_order) FROM {$_TABLES['faq_categories']} GROUP BY cat_id;";
        $result = DB_query($sql);
        if (DB_numRows($result) > 0 ) {
            $max = DB_fetchArray($result);
            $sort_order = (int) $max['sort_order'] + 10;
        }

        $A['cat_id'] = '';
        $A['title'] = '';
        $A['description'] = '';
        $A['sort_order'] = $sort_order;
        $A['owner_id'] = $_USER['uid'];
        $A['group_id'] = DB_getItem($_TABLES['groups'],'grp_id','grp_name = "FAQ Admin"');
        $A['perm_owner'] = $_FAQ_CONF['default_permissions_category'][0];
        $A['perm_group'] = $_FAQ_CONF['default_permissions_category'][1];
        $A['perm_members'] = $_FAQ_CONF['default_permissions_category'][2];
        $A['perm_anon'] = $_FAQ_CONF['default_permissions_category'][3];
        $A['last_updated'] = 0;
    }

    $user_select  = COM_optionList($_TABLES['users'], 'uid,username',$A['owner_id']);
    $group_select = COM_optionList($_TABLES['groups'],'grp_id,grp_name',$A['group_id']);

    $sort_select = '<option value="0">' . $LANG_FAQ['first_position'] . '</option>';
    $result = DB_query("SELECT cat_id,title,sort_order FROM {$_TABLES['faq_categories']} ORDER BY sort_order ASC");
    $order = 10;
    while ($row = DB_fetchArray($result)) {
        if ( $A['sort_order'] != $order ) {
            $label = $row['title'];
            $test_order = $order + 10;
            $sort_select .= '<option value="' . $order . '"' . ($A['sort_order'] == $test_order ? ' selected="selected"' : '') . '>' . $label . '</option>';
        }
        $order += 10;
    }

    $T->set_var(array(
        'row_cat_id'        => $A['cat_id'],
        'row_title'         => $A['title'],
        'row_description'   => $A['description'],
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

    // we need to do some error checking here - make sure everything
    // is set and in proper format (such as date).

    $cat_id      = (int) COM_applyFilter($_POST['cat_id'],true);
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $sort_order  = (int) COM_applyFilter($_POST['sort_order'],true) + 1;
    $owner_id    = (int) COM_applyFilter($_POST['owner'],true);
    $group_id    = (int) COM_applyFilter($_POST['group'],true);

    list($perm_owner, $perm_group, $perm_members, $perm_anon) =
        SEC_getPermissionValues(    $_POST['perm_owner'],
                                    $_POST['perm_group'],
                                    $_POST['perm_members'],
                                    $_POST['perm_anon'] );

    $filter = new sanitizer();

    $filter->setPostmode('text');
    $title = $filter->filterText($title);
    $description = $filter->filterText($description);

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

    CACHE_remove_instance('menu');
    CACHE_remove_instance('whatsnew');

    return listCategories();
}

function deleteCategory()
{
    global $_CONF, $_TABLES;

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
                }
                // delete the category
                DB_query("DELETE FROM {$_TABLES['faq_categories']} WHERE cat_id=".(int) $delete_id);
            }
        }
    }
    CACHE_remove_instance('menu');
    CACHE_remove_instance('whatsnew');
    return;
}

function deleteFaq()
{
    global $_CONF, $_TABLES;

    $del_ids = $_POST['faq_ids'];
    if ( is_array($del_ids) && count($del_ids) > 0 ) {
        foreach ($del_ids AS  $id ) {
            $delete_id = (int) COM_applyFilter($id,true);
            if ( $delete_id > 0 ) {
                DB_query("DELETE FROM {$_TABLES['faq_questions']} WHERE id=".(int) $delete_id);
                PLG_itemDeleted($delete_id,'faq');
            }
        }
    }

    CACHE_remove_instance('menu');
    CACHE_remove_instance('whatsnew');

    return;
}

function faq_admin_menu($action)
{
    global $_CONF, $_FAQ_CONF, $LANG_ADMIN, $LANG_FAQ;

    $retval = '';

    $menu_arr = array(
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?faqlist=x','text' => $LANG_FAQ['faq_list'],'active' => ($action == 'faqlist' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?catlist=x','text' => $LANG_FAQ['cat_list'],'active' => ($action == 'catlist' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?faqedit=x','text'=> ($action == 'edit_existing' ? $LANG_FAQ['edit'] : $LANG_FAQ['create_new']), 'active'=> ($action == 'faqedit' || $action == 'edit_existing' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/faq/index.php?catedit=x','text'=> ($action == 'edit_existing_cat' ? $LANG_FAQ['edit_cat'] : $LANG_FAQ['create_new_cat']), 'active'=> ($action == 'catedit' || $action == 'edit_existing_cat' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'], 'text' => $LANG_ADMIN['admin_home'])
    );

    $retval = '<h2>'.$LANG_FAQ['faq_admin_title'].'</h2>';

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_FAQ['admin_help'],
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

$expectedActions = array('faqlist','catlist','faqedit','catedit','deletefaq','save','delsel_x','delselcat_x');
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

switch ( $cmd ) {
    case 'faqedit' :
        if (empty ($_GET['faqid'])) {
            if (isset($_GET['cat_id'])) {
                $cat_id = COM_applyFilter($_GET['cat_id'],true);
            } else {
                $cat_id = 0;
            }
            $page = editFaq ($cmd,0,$cat_id);
        } else {
            $page = editFaq ($cmd, (int) COM_applyFilter ($_GET['faqid']));
            $cmd = 'edit_existing';
        }
        break;

    case 'catedit' :
        if (empty ($_GET['catid'])) {
            $page = editCategory ($cmd);
        } else {
            $page = editCategory ($cmd, (int) COM_applyFilter ($_GET['catid']));
            $cmd = 'edit_existing_cat';
        }
        break;

    case 'save' :
        if (isset($_POST['type']) && SEC_checkToken()) {
            switch ($_POST['type']) {
                case 'category' :
                    $page = saveCategory();
                    break;
                case 'faq' :
                    $page = saveFaq();
                    break;
                default :
                    $page = listFaq();
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
        $page = listCategories();
        break;

    case  'delsel_x':
        if (SEC_checkToken()) {
            deleteFaq();
        }
        $page = listFaq();
        break;

    case 'delete' :
        $page = 'Not implemented yet';
        break;

    case 'catlist' :
        $page = listCategories();
        break;

    case 'faqlist' :
    default :
        $page = listFaq();
        break;
}

$display  = COM_siteHeader ('menu', $LANG_FAQ['admin']);
$display .= faq_admin_menu($cmd);
$display .= $page;
$display .= COM_siteFooter (false);
echo $display;

?>