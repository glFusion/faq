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
    'plugin'            => 'faq',
    'plugin_name'       => 'FAQ',
    'plugin_admin'		=> 'FAQ Admin',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'You are not authorized to view this Page.  Your user name and IP have been recorded.',
    'admin'		            => 'FAQ Admin',
    'admin_help'            => 'FAQ administration. Allows you to create, edit and delete FAQs and FAQ Categories.',
    'admin_help_cat_edit'   => 'Create / Edit a FAQ Category. All fields are required',
    'admin_help_cat_list'   => 'FAQ Category list. From here you can edit existing categories or delete one or more categories. When categoreis are deleted, All FAQ article in the Category will also be deleted.',
    'admin_help_faq_edit'   => 'Create / Edit a FAQ Article. You can toggle between the WYSIWYG (Visual) or Plain HTML editor as needed.',
    'admin_help_faq_list'   => 'FAQ Article list. You can sort this view based on category or helpful or not helpful. Select FAQ Articles to edit or delete.',
    'answer'                => 'Answer',
    'back_to_home'          => 'Back to FAQ Home',
    'back_to_admin'         => 'Back to FAQ Admin List',
    'cancel'			    => 'Cancel',
    'cat_id'                => 'Category ID',
    'cat_list'              => 'Category List',
    'category'              => 'Category',
    'category_saved'        => 'Category was successfully saved.',
    'create_new'            => 'New FAQ',
    'create_new_cat'        => 'New Category',
    'create_new_faq'        => 'Create New FAQ Question',
    'delete'			    => 'Delete',
    'delete_category_checked'    => 'Delete Checked',
    'delete_category_confirm'    => 'Are you sure you want to delete the checked Categories? ALL FAQs IN THE CATEGORY WILL ALSO BE DELETED!',
    'delete_checked'        => 'Delete Checked',
    'delete_confirm'        => 'Are you sure you want to delete the checked FAQs?',
    'delete_confirm_faq'    => 'Are you sure you want to delete this FAQ?',
    'delete_confirm_cat'    => 'Are you sure you want to delete this Category? ALL FAQs IN THE CATEGORY WILL ALSO BE DELETED!',
    'desc_faq'              => '[faq] auto tag creates a link to the FAQ entry',
    'description'           => 'Description',
    'display_after'         => 'Display After',
    'draft'                 => 'Draft',
    'edit'				    => 'Edit',
    'edit_cat'              => 'Edit Category',
    'edit_faq'			    => 'Edit FAQ',
    'edit_existing_cat'     => 'Category Editor',
    'error_invalid_catid'   => 'The Category ID was not valid',
    'error_invalid_faqid'   => 'The FAQ ID was not valid',
    'error_no_answer'       => 'You must enter an Answer for the FAQ article',
    'error_no_cat'          => 'You must select a Category for the FAQ article',
    'error_no_description'  => 'Category Description cannot be blank.',
    'error_no_question'     => 'You must enter a Question for the FAQ article',
    'error_no_title'        => 'Category Title cannot be blank.',
    'faq'                   => 'FAQ Question / Answer Editor',
    'faq_admin_title'       => 'Frequently Asked Questions (FAQ)',
    'faq_editor'            => 'FAQ Editor',
    'faq_list'              => 'FAQ List',
    'faq_saved'             => 'FAQ was successfully saved.',
    'faq_title'             => 'Frequently Asked Questions',
    'first_position'        => 'First Position',
    'group'                 => 'Category Group',
    'helpful'               => 'Was this article helpful?',
    'helpful_no'            => 'Not Helpful',
    'helpful_yes'           => 'Helpful',
    'html'                  => 'HTML',
    'id'                    => 'FAQ Id',
    'keywords'              => 'Keywords',
    'last_updated'          => 'Last Updated',
    'no'                    => 'NO',
    'no_cat_or_faq'         => 'No Frequently Asked Questions are available at this time.',
    'no_cats'               => 'No Categories have been created yet',
    'no_cats_admin'         => 'No Categories exist - Please create a Category before creating a FAQ',
    'no_faq_found'          => 'The requested FAQ was not found. Please return to the FAQ Home and try your selection again.',
    'no_faqs'               => 'No FAQs for this Category',
    'no_results_found'      => 'No Results Found',
    'number_of_questions'   => 'Questions',
    'owner'                 => 'Category Owner',
    'permissions'           => 'Permissions',
    'preview'               => 'Preview',
    'preview_help'          => 'Select the <strong>Preview button</strong> to refresh the preview display',
    'question'              => 'Question',
    'related_faqs'          => 'Related FAQs',
    'reset_stats'           => 'Reset Helpful Stats',
    'save'		            => 'Save',
    'search_results'        => 'Search Results',
    'search_the'            => 'Search the',
    'silent_edit'           => 'Silent Edit',
    'sort_order'            => 'Order',
    'thank_you'             => 'Thank you for your feedback!',
    'title'                 => 'Title',
    'unsaved_data'          => 'Unsaved changes! Please make sure you save your work before leaving this page.',
    'views'                 => 'Views',
    'visual'                => 'Visual',
    'whatsnew_period'       => 'last %s days',
    'yes'                   => 'YES',
);

$LANG_configsections['faq'] = array(
    'label' => 'FAQ',
    'title' => 'FAQ Plugin Configuration',
);

$LANG_confignames['faq'] = array(
    'allowed_html'        => 'Allowed HTML in Answers',
    'default_permissions_category' => 'Default Category Permissions',
    'displayblocks'       => 'Display Blocks',
    'faq_title'           => 'FAQ Main Title',
    'layout'              => 'FAQ Index Layout',
    'question_sort_dir'   => 'FAQ Sort Direction',
    'question_sort_field' => 'FAQ Sort Field',
    'whatsnew_enabled'    => 'Include in What\'s New Block',
    'whatsnew_interval'   => 'What\'s New Interval (Days)',
    'max_columns_category' => 'Max Category Columns in "Category in Columns" View',
    'max_columns_question' => 'Max Question Columns "Single Category" View',
    'default_edit_mode'   => 'Default Editor',
    'enable_search'       => 'Enable Search',
);

$LANG_configsubgroups['faq'] = array(
    'sg_main' => 'FAQ Settings',
);

$LANG_fs['faq'] = array(
    'fs_main' => 'Display Options',
    'fs_whatsnew' => 'What\'s New Block',
    'fs_perm_defaults' => 'Permission Defaults',
);

$LANG_configSelect['faq'] = array(
    0  => array(1 => 'True', 0 => 'False'),
    1  => array(0 => 'Navigation Blocks', 1 => 'Footer Blocks', 2 => 'All Blocks', 3 => 'No Blocks'),
    2  => array(1 => 'Yes', 0 => 'No'),
    3  => array(-1 => 'No Centerblock', 1 => 'Top of Page', 2 => 'After Featured Story', 3 => 'Bottom of Page'),
    4  => array('question' => 'Question', 'last_updated' => 'Date'),
    5  => array('DESC' => 'Descending', 'ASC' => 'Ascending'),
    6  => array(0 => 'No access', 2 => 'Read-Only', 3 => 'Read-Write'),
    7  => array(0 => 'Category in Columns', 1 => 'Single Category Column'),
    8  => array(4 => '4', 3 => '3', 2 => '2', 1 => '1'),
    9  => array('wysiwyg' => 'WYSIWYG', 'html' => 'HTML'),
);
?>