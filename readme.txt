=== Content Warning ===
Contributors: Phyrax
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U5M6JBDKGF3EJ
Tags: warning, message, lading page, front page, enter page, adult content, consent, age verification, validation
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 3.48

A plugin that provides a warning box with a ton more options completely re-written from the ground up.
== Description ==
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

This plugin is tested and proven to work with WordPress 3.4

= The plugin is broken, fix it. = 

Please do not contact me with questions like this.  If you cannot be descriptive with your problem I cannot help you.

= I'm still seeing the dialog even after I clicked enter/exit =

* If your browser does not have cookies enabled, the plugin will not store your response.
* Also, if you're using google chrome, this is a bug that unfortunately I have been unable to squash.  Hitting F5 or refresh will fix it.

== Screenshots ==

1. Preview of the dialog shown by colorbox, for more info check the youtube video.

== Changelog ==

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

= 2.4.17 =
* Fixed bug that would redirect users to google if they click enter.  This bug was due to the "enter" option in the admin panel being empty, which now defaults to a hash sign, and forces the thickbox to just close and let the user view the page.
* Added default value to exit link, now goes to google if left empty.

= 2.4.16 =
* Fixed Cookie Bug due to users wishing to enter/exit being redirected prior to the cookie being set.
* Removed "Enabled" option, no real reason to have it as it's understood that if you have the plugin active you want to use it anyhow.

= 2.4.15 =
* Fixed exit/enter issues.
* Minor id change to settings page.
* Properlly registered scripts and styles.

= 2.4.14 =
* Fixed the enter link issue.

= 2.4.13 =
* Removed the annoying debug code.
* This does not fix the bug where some users can't see the dialog.  That seems to only affect a few users.

= 2.4.12 =
* **FIX** Bug ignoring site-wide function.
* **ADD** Notes and tips in admin panel.

= 2.4.9 =
* **FIX** Bug showing dialog ignoring cookie and session data.
* **ADD** Added element ID's across admin panel to allow for easier jQuery work coming up.

= 2.4.7 =
* Major AJAX security fix. (Requires 3.3)
* Re-arranged dialog panel
* Added Enter/Exit text options again.
* Complete rewrite of HELP for 3.3

= 2.4.5 =
* Added YooThemes compatability.
* Added wp_head dpendancy again, it's necessary as YOOthemes overrides the previous get_header function for some reason.

= 2.4.4 =
* Added site-wide function back originally caused by improper tabulated elements with short-hand if statements.
* Added themeing tab (code not implimented yet)

= 2.4 =
* Shortened ajax timing to zero, though it's still not instant.
* Added denial function.  The denial option will show the dialog (with your customizations/message) to users that have previously been denied.
* Compressed admin and front-end css and javascript for a 40% increase in performance.


== Upgrade Notice ==
= 2.0 =
Adds a ton more features from v1 by rajeevan.  Along with a few security fixes.
