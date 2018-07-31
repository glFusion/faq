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
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

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
    'desc_faq'              => '[faq] auto tag creates a link to the FAQ entry',
    'description'           => 'Description',
    'display_after'         => 'Display After',
    'draft'                 => 'Draft',
    'edit'				    => 'Edit FAQ',
    'edit_cat'              => 'Edit Category',
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
    'last_updated'          => 'Last Updated',
    'no'                    => 'NO',
    'no_cat_or_faq'         => 'No Frequently Asked Questions are available at this time.',
    'no_cats'               => 'No Categories have been created yet',
    'no_cats_admin'         => 'No Categories exist - Please create a Category before creating a FAQ',
    'no_faq_found'          => 'The requested FAQ was not found. Please return to the FAQ Home and try your selection again.',
    'no_faqs'               => 'No FAQs for this Category',
    'number_of_questions'   => 'Questions',
    'owner'                 => 'Category Owner',
    'permissions'           => 'Permissions',
    'preview'               => 'Preview',
    'preview_help'          => 'Select the <strong>Preview button</strong> to refresh the preview display',
    'question'              => 'Question',
    'reset_stats'           => 'Reset Helpful Stats',
    'save'		            => 'Save',
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
);

$LANG_configsubgroups['faq'] = array(
    'sg_main' => 'FAQ Settings',
);

$LANG_fs['faq'] = array(
    'fs_main' => 'Display Options',
    'fs_whatsnew' => 'What\'s New Block',
    'fs_perm_defaults' => 'Permission Defaults',
);

$LANG_configselects['faq'] = array(
    0  => array('True' => 1, 'False' => 0 ),
    1  => array('Navigation Blocks' => 0, 'Footer Blocks' => 1, 'All Blocks' => 2, 'No Blocks' => 3),
    2  => array('Yes' => 1, 'No' => 0 ),
    3  => array('No Centerblock' => -1, 'Top of Page' => 1, 'After Featured Story' => 2, 'Bottom of Page' => 3),
    4  => array('Question' => 'question', 'Date' => 'last_updated'),
    5  => array('Descending' => 'DESC', 'Ascending' => 'ASC'),
    6  => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
    7  => array('Category in Columns' => 0, 'Single Category Column' => 1),
);
?>