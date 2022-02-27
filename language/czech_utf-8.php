<?php
/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* English Language - UTF-8
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

$LANG_FAQ = array (
    'plugin'            => 'často kladené dotazy (Faq)',
    'plugin_name'       => 'Často kladené dotazy (FAQ)',
    'plugin_admin'		=> 'Administrátor často kladených dotazů FAQ',
    'access_denied'     => 'Přístup odepřen',
    'access_denied_msg' => 'Nemáte oprávnění k zobrazení této stránky. Vaše uživatelské jméno a IP adresa byly zaznamenány.',
    'admin'		            => 'Administrátor často kladených dotazů FAQ',
    'admin_help'            => 'Správa často kladených dotazů (FAQ). Umožňuje vytvářet, upravovat a odstraňovat FAQ a kategorie FAQ.',
    'admin_help_cat_edit'   => 'Vytvořit / upravit kategorii často kladených dotazů (FAQ). Všechna pole jsou povinná',
    'admin_help_cat_list'   => 'Seznam kategorií FAQ. Odtud můžete upravit stávající kategorie nebo odstranit jednu nebo více kategorií. Pokud budou kategorie odstraněny, všechny často kladené dotazy( FAQ) v kategorii budou také odstraněny.',
    'admin_help_faq_edit'   => 'Vytvořte / upravte článek často kladených dotazů (FAQ). Můžete přepínat mezi WYSIWYG (Visual) nebo prostým HTML editorem.',
    'admin_help_faq_list'   => 'Seznam článků s častými dotazy. Můžete seřadit toto zobrazení na základě kategorie a) užitečné nebo b)není užitečné. Vyberte články s nejčastějšími dotazy, které chcete upravit nebo odstranit.',
    'answer'                => 'Odpověď',
    'back_to_home'          => 'Zpět na domovskou stránku často kladených dotazů  (FAQ)',
    'back_to_admin'         => 'Zpět na seznam správců často kladených dotazů FAQ',
    'cancel'			    => 'Zrušit',
    'cat_id'                => 'ID kategorie',
    'cat_list'              => 'Seznam kategorií',
    'category'              => 'Kategorie',
    'category_saved'        => 'Kategorie byla úspěšně uložena.',
    'create_new'            => 'Nový často kladený dotaz FAQ',
    'create_new_cat'        => 'Nová kategorie',
    'create_new_faq'        => 'Vytvořit nový často kladený dotaz (FAQ)',
    'delete'			    => 'Smazat',
    'delete_category_checked'    => 'Vymaž vybrané',
    'delete_category_confirm'    => 'Jste si jisti, že chcete odstranit zaškrtnuté kategorie? Odstraněním kategorie budou odstraněny i VŠECHNY FAQ V TÉTO KATEGORII!',
    'delete_checked'        => 'Vymaž vybrané',
    'delete_confirm'        => 'Jste si jisti, že chcete odstranit zvolené FAQ?',
    'delete_confirm_faq'    => 'Jste si jisti, že chcete odstranit tento často kladený dotaz FAQ?',
    'delete_confirm_cat'    => 'Jste si jisti, že chcete odstranit zaškrtnuté kategorie? Odstraněním kategorie budou odstraněny i VŠECHNY FAQ V TÉTO KATEGORII!',
    'desc_faq'              => '[faq] auto tag vytvoří odkaz na položku FAQ',
    'description'           => 'Popis',
    'display_after'         => 'Zobrazit po',
    'draft'                 => 'Koncept',
    'edit'				    => 'Editovat',
    'edit_cat'              => 'Edituj kategorii',
    'edit_faq'			    => 'Upravit často kladené dotazy FAQ',
    'edit_existing_cat'     => 'Editor kategorií',
    'error_invalid_catid'   => 'ID kategorie je neplatné',
    'error_invalid_faqid'   => 'FAQ ID bylo neplatné',
    'error_no_answer'       => 'Musíte zadat odpověď pro článek s nejčastějšími dotazy',
    'error_no_cat'          => 'Musíte vybrat kategorii pro článek s nejčastějšími dotazy',
    'error_no_description'  => 'Popis kategorie nemůže být prázdný.',
    'error_no_question'     => 'Musíte zadat otázku pro článek s nejčastějšími dotazy',
    'error_no_title'        => 'Název kategorie nemůže být prázdný.',
    'faq'                   => 'Otázka na často kladené dotazy / Editor odpovědí',
    'faq_admin_title'       => 'Často kladené otázky (FAQ)',
    'faq_editor'            => 'Editor často kladených dotazů FAQ',
    'faq_list'              => 'Seznam často kladených dotazů FAQ',
    'faq_saved'             => 'Často kladený dotaz FAQ byl úspěšně uložen.',
    'faq_title'             => 'Často kladené otázky (Faq)',
    'first_position'        => 'První pozice',
    'group'                 => 'Skupina kategorií',
    'helpful'               => 'Pomohl tento článek?',
    'helpful_no'            => 'Nepomohlo',
    'helpful_yes'           => 'Nápomocno',
    'html'                  => 'HTML',
    'id'                    => 'ID FAQ',
    'keywords'              => 'Klíčová slova',
    'last_updated'          => 'Naposledy aktualizováno',
    'no'                    => 'NE',
    'no_cat_or_faq'         => 'V tuto chvíli nejsou často kladené otázky k dispozici.',
    'no_cats'               => 'Ještě nebyly vytvořeny žádné kategorie',
    'no_cats_admin'         => 'Nemáte zatím žádné kategorie - vytvořte prosím kategorii před vytvořením FAQ',
    'no_faq_found'          => 'Požadované FAQ nebylo nalezeno. Vraťte se prosím na hlavní stránku FAQ a zkuste to znovu.',
    'no_faqs'               => 'Žádné FAQ pro tuto kategorii',
    'no_results_found'      => 'Nebyly nalezeny žádné výsledky',
    'number_of_questions'   => 'Otázky',
    'owner'                 => 'Vlastník kategorie',
    'permissions'           => 'Oprávnění',
    'preview'               => 'Náhled',
    'preview_help'          => 'Vyberte tlačítko <strong>Náhled</strong> pro načtení náhledu',
    'question'              => 'Otázka',
    'related_faqs'          => 'Související FAQ',
    'reset_stats'           => 'Resetovat pomocné statistiky',
    'save'		            => 'Uložit',
    'search_results'        => 'Výsledky hledání',
    'search_the'            => 'Hledat',
    'silent_edit'           => 'Editace na pozadí',
    'sort_order'            => 'Řazení',
    'thank_you'             => 'Děkujeme za názor!',
    'title'                 => 'Název',
    'unsaved_data'          => 'Změny neuloženy! Ujistěte se prosím, že jste svou práci uložili před opuštěním této stránky.',
    'views'                 => 'Zobrazení',
    'visual'                => 'Vzhled',
    'whatsnew_period'       => 'posledních %s dnů',
    'yes'                   => 'ANO',
);

$LANG_configsections['faq'] = array(
    'label' => 'Často kladené dotazy (FAQ)',
    'title' => 'Nastavení pluginu FAQ',
);

$LANG_confignames['faq'] = array(
    'allowed_html'        => 'Povolené HTML v odpovědích',
    'default_permissions_category' => 'Výchozí oprávnění kategorie',
    'displayblocks'       => 'Zobrazit bloky',
    'faq_title'           => 'Hlavní název FAQ',
    'layout'              => 'Rozložení indexů FAQ',
    'question_sort_dir'   => 'Směr řazení FAQ',
    'question_sort_field' => 'Pole řazení FAQ',
    'whatsnew_enabled'    => 'Zahrnout do bloku: Co je nového',
    'whatsnew_interval'   => 'Jaký je nový interval (dny)',
    'max_columns_category' => 'Maximální počet sloupců v zobrazení "Kategorie ve sloupcích"',
    'max_columns_question' => 'Maximální počet sloupců pro otázky při zobrazení Jediné kategorie"',
    'default_edit_mode'   => 'Výchozí editor',
    'enable_search'       => 'Povolit hledání',
);

$LANG_configsubgroups['faq'] = array(
    'sg_main' => 'Nastavení často kladených dotazů FAQ',
);

$LANG_fs['faq'] = array(
    'fs_main' => 'Zobraz volby',
    'fs_whatsnew' => 'Blok: Co je nového',
    'fs_perm_defaults' => 'Výchozí nastavení oprávnění',
);

$LANG_configSelect['faq'] = array(
    0  => array(1=>'Ano', 0=>'Ne'),
    1  => array(0=>'Navigační bloky', 1=>'Bloky v zápatí', 2=>'Všechny bloky', 3=>'Bez bloků'),
    2  => array(1=>'Ano', 0=>'Ne'),
    3  => array(-1=>'Žádný středový blok', 1=>'Horní část stránky', 2=>'Po zdůrazněném článku', 3=>'Dolní část stránky'),
    4  => array('question'=>'Dotaz', 'last_updated'=>'Datum'),
    5  => array('DESC'=>'Sestupně', 'ASC'=>'Vzestupně'),
    6  => array(0=>'Bez přístupu', 2=>'Pouze pro čtení', 3=>'Čtení a zápis'),
    7  => array(0=>'Kategorie ve sloupcích', 1=>'Sloupec pro jednu kategorii'),
    8  => array(4=>'4', 3=>'3', 2=>'2', 1=>'1'),
    9  => array('wysiwyg'=>'WYSIWYG', 'html'=>'HTML'),
);
?>