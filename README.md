# Content Warning v3
[![Repo Status](http://plugish.com/svg_images/passing.svg)](https://wordpress.org/plugins/content-warning-v2/)
[![Download Count](http://plugish.com/svg_images/cwv3_downloads.svg)](https://wordpress.org/plugins/content-warning-v2/)
[![License](http://plugish.com/svg_images/license.svg)](https://wordpress.org/plugins/content-warning-v2/)

A plugin that provides a warning box with a ton more options completely re-written from the ground up.

[Report an issue](https://github.com/JayWood/content-warning-v3/issues) | [Make a pull request](https://github.com/JayWood/content-warning-v3/pulls)

* Complete core rewrite, I threw everything out the window and wrote this from scratch.
* Use Colorbox for (hopefully) a responsive layout and neat transitions.
* Some new options as requested FREQUENTLY
* You just need to check this out, you really do...

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

## Changelog

### 3.5.5
* [jgraup](https://github.com/jgraup) - Fixed Menu Positioning
* BUGFIX - Fixed Enter/Exit URL's not being respected.
* Sniffed and updated core code to match WordPress rules and coding standards
* Removed a couple methods in favor of built-in WordPress methods.
* Modified dialog output code and escaped its output accordingly.


### 3.5.4
* [jgraup](https://github.com/jgraup) - Fixed indexing errors [issue #6](https://github.com/JayWood/content-warning-v3/issues/6)
* Output custom css, for some reason I forgot [fixes issue #3](https://github.com/JayWood/content-warning-v3/issues/3)
* Stopped using javascript:; for buttons, in some browsers that just didn't work well [fixes issue #2 and #1](https://github.com/JayWood/content-warning-v3/issues/2)
* Versioning update to WP 4.0, and tested.

### 3.5.3
* HOTFIX - Fixes cookie logic

### 3.5.2
* More PHP Cleanup to WP Standards
* Updated Colorbox to v1.5.10 [Github Changelog](https://github.com/jackmoore/colorbox#changelog)
* Fixed Colorbox popup to show like it is supposed to, broke it in 3.5.1, sorry!
* Updated JW Simple Options framework to latest.
* Updated stable tag to latest WP Version
* Updated tested tag to latest WP Version
* More little things I can't remember.

### 3.5.1
* PHP Cleanup
* Setup GRUNT for sass, jshint, and a few others
* Fixed Short tags, users should no longer have issues with activating the plugin.
* Other fixes I can't remember.

### 3.48
* Fixed IE Bug by removing debug function in javascript.