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
    $page = faqItem($id);
} else {
    $page = faqIndex();
}

/**
 * Displays a FAQ question / answer
 *
 * @param  int   $id
 * @return string faq page
 */
function faqItem($id)
{
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $page = '';

    $styleSheet = faq_getStylesheet();
    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($styleSheet);

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates');
    $T->set_file('page','faq-article.thtml');

    // set it here - we'll clear it if we find a FAQ
    $T->set_var('not_found',$LANG_FAQ['no_faq_found']);

    $T->set_var('lang_back_to_home', $LANG_FAQ['back_to_home']);

    $id = (int) COM_applyFilter($id,true);

    $result = DB_query("SELECT * FROM {$_TABLES['faq_questions']} AS f LEFT JOIN {$_TABLES['faq_categories']} AS c ON f.cat_id=c.cat_id WHERE f.id = ".(int) $id);

    if (DB_numRows($result) == 1) {
        $faqRecord = DB_fetchArray($result);

        $permission = COM_getEffectivePermission( $faqRecord['owner_id'],
                                                  $faqRecord['group_id'],
                                                  $faqRecord['perm_owner'],
                                                  $faqRecord['perm_group'],
                                                  $faqRecord['perm_members'],
                                                  $faqRecord['perm_anon']
                                                );

        if ($permission != 0) {

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
            if ($faqRecord['owner_uid'] != $_USER['uid']) {
                DB_change($_TABLES['faq_questions'], 'hits', 'hits + 1', 'id', (int) $faqRecord['id'], '', true);
            }

            $question   = faq_parse($faqRecord['question'],'text');
            $cat_title  = faq_parse($faqRecord['title'],'text');
            $answer     = faq_parse($faqRecord['answer'],'html');

            $T->set_var(array(
                'id'                => $faqRecord['id'],
                'question'          => $question,
                'answer'            => $answer,
                'cat_title'         => $cat_title,
                'last_updated'      => $dt->format($dateformat,true),
                'lang_helpful'      => $LANG_FAQ['helpful'],
                'lang_yes'          => $LANG_FAQ['yes'],
                'lang_no'           => $LANG_FAQ['no'],
                'lang_last_updated' => $LANG_FAQ['last_updated'],
                'lang_edit'         => $LANG_FAQ['edit'],
                'lang_thank_you'    => $LANG_FAQ['thank_you'],
            ));

            if ($permission == 3) {
                $T->set_var('edit_link',$_CONF['site_admin_url'].'/plugins/faq/index.php?editfaq=x&faqid='.$faqRecord['id'].'&src=faq');
            }
            $T->unset_var('not_found');
        }
    }

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    return $page;
}

/**
 * Displays the main FAQ index page
 *
 * @param  int   $category
 * @return string faq index page
 */
function faqIndex($category = 0) {
    global $_CONF, $_FAQ_CONF, $_TABLES, $_USER, $LANG_FAQ;

    $page = '';

    $styleSheet = faq_getStylesheet();
    $outputHandle = outputHandler::getInstance();
    $outputHandle->addLinkStyle($styleSheet);

    $c = \glFusion\Cache::getInstance();
    $key = 'faqindex'.'_'.$c->securityHash(true,true);
    if ( $c->has($key)) {
        return $c->get($key);
    }

    $filter = new \sanitizer();

    $T = new Template ($_CONF['path'] . 'plugins/faq/templates');
    if (!isset($_FAQ_CONF['layout']) || $_FAQ_CONF['layout'] == 0) {
        $T->set_file('page','faq-index-cat-columns.thtml');
    } else {
        $T->set_file('page','faq-index-ques-columns.thtml');
    }
    if (!isset($_FAQ_CONF['show_question_icon']) || $_FAQ_CONF['show_question_icon'] == 0) {
        $T->unset_var('show_question_icon');
    } else {
        $T->set_var('show_question_icon',true);
    }

    $permSQL = COM_getPermSql ();
    $sql = "SELECT cat_id,title,description FROM {$_TABLES['faq_categories']} " . $permSQL . " ORDER BY sort_order ASC";
    $result = DB_query($sql);
    $categoryResults = DB_fetchAll($result);

    $filter = \sanitizer::getInstance();
    $filter->setNamespace('faq','category');
    $filter->setReplaceTags(false);
    $filter->setCensorData(true);
    $filter->setPostmode('text');

    if (isset($_FAQ_CONF['faq_title']) && $_FAQ_CONF['faq_title'] != '') {
        $T->set_var('faq_title',$_FAQ_CONF['faq_title']);
    } else {
        $T->set_var('faq_title',$LANG_FAQ['faq_title']);
    }

    $numCategory = count($categoryResults);

    if ($numCategory >= $_FAQ_CONF['max_columns_category']) {
        $T->set_var('cat_columns',$_FAQ_CONF['max_columns_category']);
    } else {
        $T->set_var('cat_columns',$numCategory);
    }

    foreach ($categoryResults AS $category) {
        $T->set_block('page','category','cy');

        $filter->setPostmode('text');
        $title       = $filter->displayText($category['title']);
        $description = $filter->displayText($category['description']);

        $T->set_var(array(
            'category_title'      => $title,
            'description'         => $description,
            'lang_create_new_faq' => $LANG_FAQ['create_new_faq'],
        ));

        if (SEC_inGroup('FAQ Admin')) {
            $T->set_var('add_item_link',$_CONF['site_admin_url'].'/plugins/faq/index.php?newfaq=x&cat_id='.$category['cat_id'].'&src=faq');
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
            // maximum questions across all categories
            $sql = "SELECT COUNT(id) `count` FROM {$_TABLES['faq_questions']} GROUP BY cat_id ORDER BY `count` DESC LIMIT 1";
            $maxQuesResult = DB_query($sql);
            if (DB_numRows($maxQuesResult) == 1) {
                $maxRow = DB_fetchArray($maxQuesResult);
                $numQuestions = $maxRow['count'];
            } else {
                $numQuestions = 3;
            }
            if ($numQuestions >= $_FAQ_CONF['max_columns_question']) {
                $T->set_var('ques_columns',$_FAQ_CONF['max_columns_question']);
            } else {
                $T->set_var('ques_columns',$numQuestions);
            }

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

    $c->set($key,$page,array('faq','faqindex'));

    return $page;
}

$display = COM_siteHeader($_FAQ_CONF['menu'],$LANG_FAQ['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();

echo $display;
?>