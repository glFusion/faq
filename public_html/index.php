<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Main Page
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../lib-common.php';

if (!in_array('faq', $_PLUGINS)) {
    COM_404();
    exit;
}

COM_setArgNames( array('id') );
$id = (int) COM_applyFilter(COM_getArgument( 'id' ),true);

if ($id != 0) {
    $page = faqItem($_GET['id']);
} else {
    $page = faqIndex();
}

function faqItem($id)
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $page = '';

    $filter = sanitizer::getInstance();
    $AllowedElements = $filter->makeAllowedElements($_FAQ_CONF['allowed_html']);
    $filter->setAllowedelements($AllowedElements);
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(true);
    $filter->setCensorData(true);
    $filter->setPostmode('html');

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates');
    $T->set_file('page','faq-item.thtml');

    // set it here - we'll clear it if we find a FAQ
    $T->set_var('not_found',$LANG_FAQ['no_faq_found']);

    $T->set_var('lang_back_to_home', $LANG_FAQ['back_to_home']);

    $id = (int) COM_applyFilter($id,true);

    $result = DB_query("SELECT * FROM {$_TABLES['faq_questions']} WHERE id=".(int) $id);
    if (DB_numRows($result) == 1) {
        $faqRecord = DB_fetchArray($result);

        // look up the category permissions for this FAQ
        $sql = "SELECT owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon FROM {$_TABLES['faq_categories']} WHERE cat_id=".(int) $faqRecord['cat_id'];
        $result = DB_query($sql);
        if (DB_numRows($result) == 1) {

            $categoryRecord = DB_fetchArray($result);

            $permission = COM_getEffectivePermission( $categoryRecord['owner_id'],
                                                      $categoryRecord['group_id'],
                                                      $categoryRecord['perm_owner'],
                                                      $categoryRecord['perm_group'],
                                                      $categoryRecord['perm_members'],
                                                      $categoryRecord['perm_anon']
                                                    );

            if ($permission != 0) {

                $outputHandle = outputHandler::getInstance();
                $outputHandle->addLinkStyle($_CONF['site_url'].'/faq/style.css');

                $dt = new \Date($faqRecord['last_updated'],$_USER['tzid']);

                if ( !COM_isAnonUser() ) {
                    if ( empty( $_USER['format'] )) {
                        $dateformat = $_CONF['date'];
                    } else {
                        $dateformat = $_USER['format'];
                    }
                } else {
                    $dateformat = $_CONF['date'];
                }

                DB_change($_TABLES['faq_questions'], 'hits', 'hits + 1', 'id', (int) $faqRecord['id'], '', true);

                $filter->setPostmode('text');
                $question = $filter->displayText($faqRecord['question']);

                $filter->setPostmode('html');
                $answer = $filter->displayText($filter->filterHTML($faqRecord['answer']));

                $T->set_var(array(
                    'id'                => $faqRecord['id'],
                    'question'          => $question,
                    'answer'            => $answer,
                    'last_updated'      => $dt->format($dateformat,true),
                    'lang_helpful'      => $LANG_FAQ['helpful'],
                    'lang_yes'          => $LANG_FAQ['yes'],
                    'lang_no'           => $LANG_FAQ['no'],
                    'lang_last_updated' => $LANG_FAQ['last_updated'],
                    'lang_edit'         => $LANG_FAQ['edit'],
                    'lang_thank_you'    => $LANG_FAQ['thank_you'],
                ));

                if ($permission == 3) {
                    $T->set_var('edit_link',$_CONF['site_admin_url'].'/plugins/faq/index.php?faqedit=x&faqid='.$faqRecord['id'].'&src=faq');
                }
                $T->unset_var('not_found');
            }
        }
    }

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    return $page;
}



function faqIndex($category = 0) {
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $page = '';

    $filter = new \sanitizer();

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates');
    $T->set_file('page','faq-index.thtml');

    $permSQL = COM_getPermSql ();
    $sql = "SELECT cat_id,title,description FROM {$_TABLES['faq_categories']} " . $permSQL . " ORDER BY sort_order ASC";
    $result = DB_query($sql);
    $categoryResults = DB_fetchAll($result);

    $filter = \sanitizer::getInstance();
    $AllowedElements = $filter->makeAllowedElements($_FAQ_CONF['allowed_html']);
    $filter->setAllowedelements($AllowedElements);
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(true);
    $filter->setCensorData(true);
    $filter->setPostmode('html');

    if (isset($_FAQ_CONF['faq_title']) && $_FAQ_CONF['faq_title'] != '') {
        $T->set_var('faq_title',$_FAQ_CONF['faq_title']);
    } else {
        $T->set_var('faq_title',$LANG_FAQ['faq_title']);
    }

    foreach ($categoryResults AS $category) {
        $T->set_block('page','category','cy');
        $T->set_var(array(
            'category_title' => $category['title'],
            'description'   => $category['description'],
            'lang_create_new_faq' => $LANG_FAQ['create_new_faq'],
        ));

        if(SEC_inGroup('faq Admin')) {
            $T->set_var('add_item_link',$_CONF['site_admin_url'].'/plugins/faq/index.php?faqedit=x&cat_id='.$category['cat_id'].'&src=faq');
        } else {
            $T->unset_var('add_item_link');
        }

        // pull all questions
        $faqOrderBy = " ORDER BY questions ASC ";
        if (isset($_FAQ_CONF['question_sort_field'])) {
            $faqOrderBy = " ORDER BY " . $_FAQ_CONF['question_sort_field'];
            if (isset($_FAQ_CONF['question_sort_dir'])) {
                $faqOrderBy .= " " . $_FAQ_CONF['question_sort_dir'] . " ";
            } else {
                $faqOrderBy .= " ASC ";
            }
        }

        $result = DB_query("SELECT * FROM {$_TABLES['faq_questions']} WHERE cat_id=".(int) $category['cat_id']." AND draft=0 " . $faqOrderBy);
        $faqResults = DB_fetchAll($result);

        $T->set_block('page','questions','qs');

        if (count($faqResults) > 0 ) {
            $T->unset_var('lang_no_faqs');
            foreach ($faqResults AS $faq) {
                $filter->setPostmode('text');
                $question = $filter->displayText($faq['question']);
                $filter->setPostmode('html');
                $answer = $filter->displayText($filter->filterHTML($faq['answer']));
                $T->set_var(array(
                    'question' => $question,
                    'answer' => $answer,
                    'last_updated' => $faq['last_updated'],
                    'id' => $faq['id'],
                    'faq_url' => COM_buildURL($_CONF['site_url'].'/faq/index.php?id=' . $faq['id']),
                ));
                $T->parse('qs','questions',true);
            }
        } else {
            $T->set_var('qs','');
            $T->unset_var(array('question','answer','last_updated','id'));
            $T->set_var(array('lang_no_faqs' => $LANG_FAQ['no_faqs']));
            $T->parse('qs','questions',true);
        }
        $T->parse('cy','category',true);
        $T->set_var('qs','');
    }

    if (count($categoryResults) == 0) {
        $T->set_var('lang_no_cat_or_faq',$LANG_FAQ['no_cat_or_faq']);
    }

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($_CONF['site_url'].'/faq/style.css');

    return $page;
}

$display = COM_siteHeader($_FAQ_CONF['menu'],$LANG_FAQ['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();

echo $display;
?>