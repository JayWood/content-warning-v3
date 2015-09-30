# Content Warning v3
[![Rating](https://img.shields.io/wordpress/plugin/r/content-warning-v2.svg)](https://wordpress.org/plugins/content-warning-v2/)
[![Plugin Version](https://img.shields.io/wordpress/plugin/v/content-warning-v2.svg)](https://wordpress.org/plugins/content-warning-v2/)
[![Tested Version](https://img.shields.io/wordpress/v/content-warning-v2.svg)](http://wordpress.org/)
[![Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/content-warning-v2.svg)](https://wordpress.org/plugins/content-warning-v2/)
![License](https://img.shields.io/badge/License-GPLv2-orange.svg)
[![Issues](https://img.shields.io/github/issues/JayWood/content-warning-v3.svg)](https://github.com/JayWood/content-warning-v3/issues)

**Current Version:** 3.6.6   
**Tested Up To:** 4.3  
**Author:** [Jay Wood](http://github.com/JayWood)   
**Author URI:** http://plugish.com   
**License:** GPLv2+   

A WordPress Plugin to allow site owners to display an acceptance dialog to their users and have that follow them throughout the site.  This plugin allows you to do redirect users if they decline,
and show popups only on single posts, pages, or categories.  ie. if a user accepts on `Post A`, but no `Post B`, you can force them to accept on a per-post/page/category basis.  You can also set
the popup to site-wide, meaning the first time they accept, they will not see it again.

This plugin allows you to do the following:

* Gate Individual Posts
* Gate Individual Pages
* Gate Individual Categories
* Specify cookie time ( in days ) - or just for the browser session
* Block or redirect users who previously denied your terms.
* Customize enter & exit links
* Customize the message, enter, and exit text
* Customize the CSS in the settings page
* Customize the background color via a simple color selector, along with it's opacity
* Blanket protect misc. pages like search and archives


[Report an issue](https://github.com/JayWood/content-warning-v3/issues) | [Make a pull request](https://github.com/JayWood/content-warning-v3/pulls)   
[Check the Youtube Video](https://www.youtube.com/watch?v=0_ZNojpYuwk) | [Download from Official Wordpress.org](http://wordpress.org/plugins/content-warning-v2)

**NOTE:**

> If the exit link is left empty, users will be redirected to google.

## Installation

### Easy Method

1. Download the zip file.
1. Login to your `Dashboard`
1. Open your plugins bar and click `Add New`
1. Click the `upload tab`
1. Choose `content-warning-v2` from your downloads folder
1. Click `Install Now`
1. All done, now just activate the plugin
1. Go to CWv3 menu and configure
1. Save, and you're all good.

### Old Method
1. Upload `content-warning-v2` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions

This plugin is tested and proven to work with WordPress 3.4

### The plugin is broken, fix it.

Please do not contact me with questions like this.  If you cannot be descriptive with your problem I cannot help you.

### I'm still seeing the dialog even after I clicked enter/exit

* If your browser does not have cookies enabled, the plugin will not store your response.
* Also, if you're using google chrome, this is a bug that unfortunately I have been unable to squash.  Hitting F5 or refresh will fix it.

## TODO
- [ ] Update Admin post column to properly reflect if the post is protected or not when in a category.
- [x] Localization [#10](https://github.com/JayWood/content-warning-v3/issues/10)
- [x] Make use of jquery.cookie [#11](https://github.com/JayWood/content-warning-v3/issues/11)
- [ ] Include API / Template Tags [#12](https://github.com/JayWood/content-warning-v3/issues/12)
	- [ ] Document API / Template Tags [#13](https://github.com/JayWood/content-warning-v3/issues/13)
- [x] Custom popup, remove colorbox dependency [#14](https://github.com/JayWood/content-warning-v3/issues/14)


## Changelog

### 3.6.6
* Fixed CSS issues for background images and css overrides

### 3.6.5
* Zero day ( 0 ) cookies should use sessions instead of NOT setting the cookie. [Issue #29](https://github.com/JayWood/content-warning-v3/issues/29)
* New filter for display condition - [See Wiki](https://github.com/JayWood/content-warning-v3/wiki/Dev-Documentation#hide-the-dialog-on-certain-pages-regardless-of-cookies) - [Issue #26](https://github.com/JayWood/content-warning-v3/issues/26)

### 3.6.4
* Fixed denial redirects. [Issue #28](https://github.com/JayWood/content-warning-v3/issues/28)
* Fixed multiple undefined index errors on admin
* Changed yes/no on post columns to locked dash-icon, less clutter
* Clean up meta saving logic
* Added @since tags for future development
* Better PHP documentation
* Add /lang directory for I18n
* Update Tested Up To version
* [Development Documentation](https://github.com/JayWood/content-warning-v3/wiki/Dev-Documentation)
* Passified all PHPcs complaints

### 3.6.3
* Category fix, fixes [#18](https://github.com/JayWood/content-warning-v3/issues/18)
* Alphabetize method names, because why not!?
* Few docblock changes

### 3.6.2
* Dialog re-sizing fixes.

### 3.6.1
* Cookie HOTFIX

### 3.6.0
* Split methods and hooks from main class file, will prevent overhead, also separates admin from front-end.
* Moved to use of cookie.js
* Created API file for methods.
* New filters & actions for developers
* Began development of API file, currently only support JS outputs.
* **NEW** Filters for content outputs, see `inc/api.php` more to come.
* Switched CSS priority, to allow custom css to override bg image and opacity
* Converted sass file to nested sass and uses classes instead of IDs
* [stacyk](https://github.com/stacyk) - Made buttons visible on popup at all times.
* [stacyk](https://github.com/stacyk) - CSS Fixes for new popup.
* New Popup coding, dropped colorbox in favor of my own popup code. ( Less bloat )
* BIG THANKS to Stacy for helping me with some initial CSS issues.
