<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* Search Page
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

if (!isset($_FAQ_CONF['enable_search']) || $_FAQ_CONF['enable_search'] != true) {
    COM_refresh($_CONF['site_url'].'/faq/index.php');
}

if (!isset($_GET['q']) || empty($_GET['q'])) {
    COM_setMsg('No search item entered','error',false);
    COM_refresh($_CONF['site_url'].'/faq/index.php');
    exit;
}

include $_CONF['path'].'plugins/faq/include/wordstemmer.php';

$styleSheet = faq_getStylesheet();
$outputHandle = outputHandler::getInstance();
$outputHandle->addLinkStyle($styleSheet);


$T = new Template ($_CONF['path'] . 'plugins/faq/templates');
$T->set_file('page','faq-search-results.thtml');

$query = $_GET['q'];

$urlQuery = urlencode($query);
SESS_setVar('faq_search_field',$urlQuery);

$query_string = $query;
$search_results = searchFAQ($query,$T);
$searchResultCount = count($search_results);

$T->set_var('query',$query);

$T->set_block('page','searchresults','sr');

if ($searchResultCount > 0 ) {
    foreach($search_results AS $row) {
        $T->set_var(array(
            'search_question' => $row['question'],
            'search_answer'   => _shortenText('', $row['answer'], 50),
            'faq_article_url' => $_CONF['site_url'].'/faq/index.php?src=sr&amp;id='.(int) $row['id'],
            'relevance' => $row['relevance'],
        ));
        $T->parse('sr','searchresults',true);
    }
} else {
    $T->set_var('no_results_found',true);
    $T->set_var('lang_no_results_found',$LANG_FAQ['no_results_found']);
}

$T->set_var(array(
    'lang_search_results'   => $LANG_FAQ['search_results'],
    'lang_search_the'       => $LANG_FAQ['search_the'],
    'lang_back_to_home'     => $LANG_FAQ['back_to_home'],
    'return_url'            => $_CONF['site_url'].'/faq/index.php'
));

if (isset($_FAQ_CONF['faq_title']) && $_FAQ_CONF['faq_title'] != '') {
    $T->set_var('faq_title',$_FAQ_CONF['faq_title']);
} else {
    $T->set_var('faq_title',$LANG_FAQ['faq_title']);
}

$T->parse('output', 'page');
$page = $T->finish($T->get_var('output'));

$display = COM_siteHeader($_FAQ_CONF['menu'],$LANG_FAQ['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();

echo $display;
exit;

// Remove unnecessary words from the search term and return them as an array
function filterSearchKeys($query)
{
    $query = trim(preg_replace("/(\s+)+/", " ", $query));
    $words = array();
    // expand this list with your words.
    $list = array("in","it","a","the","of","or","I","you","he","me","us","they","she","to","but","that","this","those","then", "and", "or");
    $c = 0;
    foreach (explode(" ", $query) as $key) {
        if (in_array($key, $list)) {
            continue;
        }
        $words[] = $key;
        if ($c >= 15) {
            break;
        }
        $c++;
    }
    return $words;
}

// limit words number of characters
function limitChars($query, $limit = 200)
{
    return substr($query, 0,$limit);
}

function searchFAQ($query, $T = null)
{
    global $_TABLES, $_USER;

    $query = trim(rtrim($query));

    if (utf8_strlen($query) === 0) {
        return false;
    }
    $query = limitChars($query);

    // Weighing scores
    $scoreFullQuestion = 6;
    $scoreFullAnswer   = 5;

    $scoreQuestionKeyword = 4;
    $scoreAnswerKeyword = 3;
    $keywords = filterSearchKeys($query);
    $escQuery = DB_escapeString($query);

    $questionSQL = array();
    $answerSQL = array();

    $titleSQL = array();
    $sumSQL = array();
    $docSQL = array();
    $categorySQL = array();
    $urlSQL = array();

    $stemmer = new Libs_WordStemmer();

    /** Matching full occurences **/
    if (count($keywords) > 1 && !empty($escQuery)) {
        $questionSQL[] = "if (question LIKE '%".$escQuery."%',{$scoreFullQuestion},0)";
        $answerSQL[] = "if (answer LIKE '%".$escQuery."%',{$scoreFullAnswer},0)";
    }

    /** Matching Keywords **/
    foreach($keywords as $key) {
        if ( !empty($key)) {
            $key = $stemmer->stem($key);
            $questionSQL[] = "if (question LIKE '%".DB_escapeString($key)."%',{$scoreQuestionKeyword},0)";
            $answerSQL[] = "if (answer LIKE '%".DB_escapeString($key)."%',{$scoreAnswerKeyword},0)";
        }
    }

    // Just incase it's empty, add 0
    if (empty($questionSQL)) {
        $questionSQL[] = 0;
    }
    if (empty($answerSQL)) {
        $answerSQL[] = 0;
    }

    $where = COM_getPermSQL( 'AND', $_USER['uid'], 2, 'c');

    $sql = "SELECT *,
            (
                (-- Question score
                ".implode(" + ", $questionSQL)."
                )+
                (-- Answer score
                ".implode(" + ", $answerSQL)."
                )
            ) as relevance
            FROM {$_TABLES['faq_questions']} AS f LEFT JOIN {$_TABLES['faq_categories']} AS c ON f.cat_id=c.cat_id
            WHERE draft = 0 " . $where . "
            HAVING relevance > 0
            ORDER BY relevance DESC,hits DESC
            LIMIT 25";

    if ( $T != null && defined('DVLP_DEBUG')) {
        $T->set_var('query_string',htmlspecialchars($sql));
    }

    $results = DB_query($sql);

    $items = DB_fetchAll($results);
    return $items;
}

function _shortenText($keyword, $text, $num_words = 7)
{
    $text = COM_getTextContent($text);

    // parse some general bbcode / auto tags
    $bbcode = array(
        "/\[b\](.*?)\[\/b\]/is" => "$1",
        "/\[u\](.*?)\[\/u\]/is" => "$1",
        "/\[i\](.*?)\[\/i\]/is" => "$1",
        "/\[quote\](.*?)\[\/quote\]/is" => "$1",
        "/\[code\](.*?)\[\/code\]/is" => " $1 ",
        "/\[p\](.*?)\[\/p\]/is" => " $1 ",
        "/\[url\=(.*?)\](.*?)\[\/url\]/is" => "$2",
        "/\[wiki:(.*?) (.*?)[\]]/is" => "$2"
    );
    $text = @preg_replace(array_keys($bbcode), array_values($bbcode), $text);

    $words = explode(' ', $text);
    $word_count = count($words);
    if ($word_count <= $num_words) {
        return COM_highlightQuery($text, $keyword, 'b');
    }

    $rt = '';
    $pos = stripos($text, $keyword);
    if ($pos !== false) {
        $pos_space = utf8_strpos($text, ' ', $pos);
        if (empty($pos_space)) {
            // Keyword at the end of text
            $key = $word_count - 1;
            $start = 0 - $num_words;
            $end = 0;
            $rt = '<b>...</b> ';
        } else {
            $str = utf8_substr($text, $pos, $pos_space - $pos);
            $m = (int) (($num_words - 1) / 2);
            $key = _arraySearch($keyword, $words);
            if ($key === false) {
                // Keyword(s) not found - show start of text
                $key = 0;
                $start = 0;
                $end = $num_words - 1;
            } elseif ($key <= $m) {
                // Keyword at the start of text
                $start = 0 - $key;
                $end = $num_words - 1;
                $end = ($key + $m <= $word_count - 1)
                     ? $key : $word_count - $m - 1;
                $abs_length = abs($start) + abs($end) + 1;
                if ($abs_length < $num_words) {
                    $end += ($num_words - $abs_length);
                }
            } else {
                // Keyword in the middle of text
                $start = 0 - $m;
                $end = ($key + $m <= $word_count - 1)
                     ? $m : $word_count - $key - 1;
                $abs_length = abs($start) + abs($end) + 1;
                if ($abs_length < $num_words) {
                    $start -= ($num_words - $abs_length);
                }
                $rt = '<b>...</b> ';
            }
        }
    } else {
        $key = 0;
        $start = 0;
        $end = $num_words - 1;
    }

    for ($i = $start; $i <= $end; $i++) {
        $rt .= $words[$key + $i] . ' ';
    }
    if ($key + $i != $word_count) {
        $rt .= ' <b>...</b>';
    }
    return COM_highlightQuery($rt, $keyword, 'b');
}


function _arraySearch($needle, $haystack)
{
    $keywords = explode(' ', $needle);
    $num_keywords = count($keywords);

    foreach ($haystack as $key => $value) {
        if (stripos($value, $keywords[0]) !== false) {
            if ($num_keywords == 1) {
                return $key;
            } else {
                $matched_all = true;
                for ($i = 1; $i < $num_keywords; $i++) {
                    if (stripos($haystack[$key + $i], $keywords[$i]) === false) {
                        $matched_all = false;
                        break;
                    }
                }
                if ($matched_all) {
                    return $key;
                }
            }
        }
    }

    return false;
}
?>