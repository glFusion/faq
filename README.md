## FAQ Plugin for glFusion

For the latest, and more detailed, documentation, please see the [FAQ Plugin Wiki Page](https://www.glfusion.org/wiki/glfusion:plugins:faq:start)

### Overview

Frequently asked questions (FAQ), are a set of common or recurring questions and answers. The FAQ plugin allows you to maintain a list of commonly asked questions, by category.

### Features

- Organize common questions / answers by category
- Collect user feedback on whether the FAQ entry was helpful or not
- WYSIWYG (What You See is What You Get) editor for FAQ answers. Allows for embedding additional content such as links, videos, images, etc.
- Full Permission controls by category
- FAQ auto tag to allow integration into other content areas
- Multiple layout options to suit your needs

### System Requirements

The FAQ Plugin has the following system requirements:

* PHP 5.3.3 and higher.
* glFusion v1.7.5 or newer
* Must be using a UIKIT based theme - will not work with Vintage or Nouveau themes

### Installation

The FAQ Plugin uses the glFusion automated plugin installer. Simply upload the distribution using the glFusion plugin installer located in the Plugin Administration page.

### Upgrading

The upgrade process is identical to the installation process, simply upload the distribution from the Plugin Administration page.

### Configuration


**FAQ Main Title**

The title that appears on the FAQ Index Page.

**Display Blocks**

Which glFusion blocks to display when viewing FAQs.

**FAQ Sort Field**

The field used to sort the FAQ Questions on the FAQ Index Page. Options are Question (alphabetically) or Last Updated.

**FAQ Sort Direction**

Determines if the FAQ questions are sorting in ascending order or descending order.

**FAQ Index Layout**

Select the FAQ Index page layout. Category Columns will use up to 3 columns to show the categories with the answers listed below each category. Single column category will show a Category on a line with the questions listed below in up to 3 columns.

**Max Category Columns in "Category in Columns" View**

This setting determines the maximum number of Category columns that will be used in the "Category in Columns" view.

**Max Question Columns "Single Category" View

This setting determines the maximum number of columns for questions in the "Single Category" view.

**Default Editor**

Determines which editor is the default for editing FAQ Articles. WYSIWYG will start the edit session using the What You See is What You Get editor. HTML will start the edit session with the plain HTML raw editor.

**Allowed HTML**

List of HTML tags allowed to be used in FAQ answers.

**Include in Whats New Block**

If set to True, new or updated FAQ questions will appear in the site's What's New Block.

**What's New Interval**

The number of days that a new / updated FAQ will appear in the What's New block.

**Default Category Permissions**

Sets the default permissions to use when creating a new category. Order is Owner -- Group -- Member -- Anonymous.

### License

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.
