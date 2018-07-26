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

if (isset($_GET['id'])) {
    $page = faqItem($_GET['id']);
} else {
    $page = faqIndex();
}

function faqItem($id)
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $page = '';

    $filter = sanitizer::getInstance();
    $AllowedElements = $filter->makeAllowedElements('div[class],h1,h2,h3,pre,br,p[style],b[style],s,strong[style],i[style],em[style],u[style],strike,a[id|name|style|href|title|target],ol[style|class],ul[style|class],li[style|class],hr[style],blockquote[style],img[style|alt|title|width|height|src|align],table[style|width|bgcolor|align|cellspacing|cellpadding|border],tr[style],td[style],th[style],tbody,thead,caption,col,colgroup,span[style|class],sup,sub');
    $filter->setAllowedelements($AllowedElements);
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(true);
    $filter->setCensorData(true);
    $filter->setPostmode('html');

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates');
    $T->set_file('page','faq-item.thtml');

    $id = (int) COM_applyFilter($id,true);

    $result = DB_query("SELECT * FROM {$_TABLES['faq_questions']} WHERE id=".(int) $id);
    if (DB_numRows($result) != 1) {
        return 'FAQ not found';
    }
    $faqRecord = DB_fetchArray($result);

    // check permission of the category that this FAQ belongs

    $sql = "SELECT * FROM {$_TABLES['faq_categories']} WHERE cat_id=".(int) $faqRecord['cat_id'];
    $result = DB_query($sql);
    if (DB_numRows($result) != 1) {
        return 'FAQ not found';
    }
    $categoryRecord = DB_fetchArray($result);

    $permission = COM_getEffectivePermission( $categoryRecord['owner_id'],
                                              $categoryRecord['group_id'],
                                              $categoryRecord['perm_owner'],
                                              $categoryRecord['perm_group'],
                                              $categoryRecord['perm_members'],
                                              $categoryRecord['perm_anon']
                                            );

    if ($permission == 0) {
        return 'FAQ not found';
    }

    DB_change($_TABLES['faq_questions'], 'hits', 'hits + 1', 'id', (int) $faqRecord['id'], '', true);

    $T->set_var(array(
        'id'        => $faqRecord['id'],
        'question'  => $faqRecord['question'],
        'answer'    => $filter->displayText($faqRecord['answer']),
        'last_updated' => $faqRecord['last_updated'],
        'lang_back_to_home' => $LANG_FAQ['back_to_home'],
        'lang_helpful'      => $LANG_FAQ['helpful'],
        'lang_yes'          => $LANG_FAQ['yes'],
        'lang_no'           => $LANG_FAQ['no'],
        'lang_last_updated' => $LANG_FAQ['last_updated'],
        'lang_edit'         => $LANG_FAQ['edit'],
    ));

    if ($permission == 3) {
        $T->set_var('edit_link',$_CONF['site_admin_url'].'/plugins/faq/index.php?faqedit=x&faqid='.$faqRecord['id'].'&src=faq');
    }

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($_CONF['site_url'].'/faq/style.css');
    $outputHandle->addLinkScript($_CONF['site_url'].'/faq/faq.js');
    return $page;
}



function faqIndex($category = 0) {
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $page = '';

    $filter = new \sanitizer();

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates');
    $T->set_file('page','faq-index.thtml');

    $permSQL = COM_getPermSql ();
    $sql = "SELECT cat_id,title,description FROM {$_TABLES['faq_categories']} " . $permSQL;
    $result = DB_query($sql);
    $categoryResults = DB_fetchAll($result);

    $filter = sanitizer::getInstance();
    $AllowedElements = $filter->makeAllowedElements('div[class],h1,h2,h3,pre,br,p[style],b[style],s,strong[style],i[style],em[style],u[style],strike,a[id|name|style|href|title|target],ol[style|class],ul[style|class],li[style|class],hr[style],blockquote[style],img[style|alt|title|width|height|src|align],table[style|width|bgcolor|align|cellspacing|cellpadding|border],tr[style],td[style],th[style],tbody,thead,caption,col,colgroup,span[style|class],sup,sub');
    $filter->setAllowedelements($AllowedElements);
    $filter->setNamespace('faq','answer');
    $filter->setReplaceTags(true);
    $filter->setCensorData(true);
    $filter->setPostmode('html');

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
        $result = DB_query("SELECT * FROM {$_TABLES['faq_questions']} WHERE cat_id=".(int) $category['cat_id']." AND published=1 ORDER BY question ASC");
        $faqResults = DB_fetchAll($result);

        $T->set_block('page','questions','qs');

        if (count($faqResults) > 0 ) {
            $T->unset_var('lang_no_faqs');
            foreach ($faqResults AS $faq) {
                $T->set_var(array(
                    'question' => $faq['question'],
                    'answer' => $filter->displayText($faq['answer']),
                    'last_updated' => $faq['last_updated'],
                    'id' => $faq['id'],
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

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($_CONF['site_url'].'/faq/style.css');

    return $page;
}

$display = COM_siteHeader(0,$LANG_FAQ['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();

echo $display;
?>