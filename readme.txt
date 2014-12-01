=== Content Warning ===
Contributors: Phyrax
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U5M6JBDKGF3EJ
Tags: warning, message, lading page, front page, enter page, adult content, consent, age verification, validation
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 3.6.0

A plugin that provides a warning box with a ton more options completely re-written from the ground up.
== Description ==

> Major bugfix release.  Verified working with 3.9.1

= Support Requests =
All support requests should be on the new Github Repo.

= Want to Contribute? =
Make a [Pull Request](https://github.com/JayWood/content-warning-v3) to the Github's project development branch.

This plugin provides a neat little warning and denial box to warn your users of possibly offensive content. 

= v3 Is Here =
* Complete core rewrite, I threw everything out the window and wrote this from scratch.
* Use Colorbox for (hopefully) a responsive layout and neat transitions.
* Some new options as requested FREQUENTLY
* You just need to check this out, you really do...

[youtube http://www.youtube.com/watch?v=0_ZNojpYuwk]

*NOTE:* 

> If the exit link is left empty, users will be redirected to google.

== Installation ==

= Default Method =
1. Go to Settings > Plugins in your administrator panel.
1. Click `Add New`
1. Search for Content Warning v2
1. Click install.

= Easy Method =
1. Download the zip file.
1. Login to your `Dashboard`
1. Open your plugins bar and click `Add New`
1. Click the `upload tab`
1. Choose `content-warning-v2` from your downloads folder
1. Click `Install Now`
1. All done, now just activate the plugin
1. Go to CWv3 menu and configure
1. Save, and you're all good.

= Old Method = 
1. Upload `content-warning-v2` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

This plugin is tested and proven to work with WordPress 4.0

= Why aren't you answering support questions? =

All support questions should be directed to the github issues section, just [open a ticket](https://github.com/JayWood/content-warning-v3/issues).

** How to get support **
* Provide a detailed explination of what the issue is, `it's broken` tickets will be closed.
* Detail what you have done to try and fix the problem.
* Provide a detailed list of options that are set.
* Give a link to your site or the affected page.

= The plugin is broken, fix it. = 

Please do not contact me with questions like this.  If you cannot be descriptive with your problem I cannot help you.

= I'm still seeing the dialog even after I clicked enter/exit =

* If your browser does not have cookies enabled, the plugin will not store your response.
* Also, if you're using google chrome, this is a bug that unfortunately I have been unable to squash.  Hitting F5 or refresh will fix it.

== Screenshots ==

1. Preview of the dialog shown by colorbox, for more info check the youtube video.

== Changelog ==

= 3.6.0 =
* Split methods and hooks from main class file, will prevent overhead.
* Moved to use of cookie.js
* Created API file for methods.
* [stacyk](https://github.com/stacyk) - Made buttons visible on popup at all times.

= 3.5.5 =
* [jgraup](https://github.com/jgraup) - Fixed Menu Positioning
* BUGFIX - Fixed Enter/Exit URLs not being respected.
* Sniffed and updated core code to match WordPress rules and coding standards
* Removed a couple methods in favor of built-in WordPress methods.
* Modified dialog output code and escaped its output accordingly.

= 3.5.4 =
* [jgraup](https://github.com/jgraup) - Fixed indexing errors [issue #6](https://github.com/JayWood/content-warning-v3/issues/6)
* Output custom css, for some reason I forgot [fixes issue #3](https://github.com/JayWood/content-warning-v3/issues/3)
* Stopped using javascript:; for buttons, in some browsers that just didn't work well [fixes issue #2 and #1](https://github.com/JayWood/content-warning-v3/issues/2)
* Versioning update to WP 4.0, and tested.

= 3.5.3 =
* **HOTFIX** Cookie logic

= 3.5.2 =
* More PHP Cleanup to WP Standards
* Updated Colorbox to v1.5.10 [Github Changelog](https://github.com/jackmoore/colorbox#changelog)
* Fixed Colorbox popup to show like it is supposed to, broke it in 3.5.1, sorry!
* Updated JW Simple Options framework to latest.
* Updated stable tag to latest WP Version
* Updated tested tag to latest WP Version
* More little things I can't remember.


= 3.5.1 =
* PHP Cleanup
* Setup GRUNT for sass, jshint, and a few others
* Fixed Short tags, users should no long have issues with activating the plugin.
* Other fixes I can't remember.

= 3.48 =
* Fixed IE Bug by removing debug function in javascript.

= 3.47 =
* Fixed check_data function, thanks to [Tuxlog](http://wordpress.org/support/profile/tuxlog "Tux's Profile")

= 3.46 =
* Complete rewrite from the ground up.
* New UI
* New dialog handler
* New Category Options
* New Misc. Page options (archive/search)
* Tons of new features.

== Upgrade Notice ==
= 2.0 =
Adds a ton more features from v1 by rajeevan.  Along with a few security fixes.
