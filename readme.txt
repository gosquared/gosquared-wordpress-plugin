=== Plugin Name ===
Contributors: GoSquared
Donate link: http://www.gosquared.com/
Tags: GoSquared, LiveStats, Real-Time, Analytics, Statistics, Real, Time
Requires at least: 2.6
Tested up to: 3.4.1
Stable tag: 0.4.1

The official GoSquared for Wordpress plugin to integrate the GoSquared Tracking Code and GoSquared widgets into your Wordpress blog.

== Description ==

This plugin allows site owners to easily integrate their GoSquared Tracking Code and GoSquared widgets into their Wordpress site without having to change any source code or theme files.

This enables you to monitor and share your Wordpress blog's traffic in real-time with GoSquared, the real-time analytics platform.

To use GoSquared on your blog, simply download this plugin and sign up for a free trial account at https://www.gosquared.com/join/

== Installation ==

1. Sign up to GoSquared for a free trial at https://www.gosquared.com/join/
2. Upload the directory called `gosquared` to `/wp-content/plugins/`
4. Go to the plugin's admin page (Settings -> GoSquared on the left sidebar) and enter your site token, which can be found in the tracking code tab in GoSquared Settings at http://www.gosquared.com/settings/

== Frequently Asked Questions ==

= Do I need a GoSquared account for this Plugin to work? =
You'll need to sign up for GoSquared at https://www.gosquared.com/join/ to make use of this plugin. If you're already using GoSquared, you'll simply need your Site Token (which can be found on the Tracking Code tab in GoSquared Settings http://www.gosquared.com/home/) and API Key (which can be found at https://www.gosquared.com/home/developer) to make use of this plugin.

= Why do I need to enter my API Key? =
Your API Key is required for the widgets to work.

= I don't have a site token. How do I get GoSquared? =
Check out GoSquared at http://www.gosquared.com/features/ and sign up for an account.

= Where can I find my Site Token? =
Sign in to GoSquared and go to Settings (which is accessible via the switch icon in the top navbar), then click on the "Tracking Code" tab for your site.

= Where can I find my API Key? =
Sign in to GoSquared and go to https://www.gosquared.com/settings/#Personal.

= I can't get this thing to work. Where can I contact you? =
Wing us an email at http://www.gosquared.com/contact/ or check out support documentation at http://www.gosquared.com/support/

== Changelog ==

= 0.4 =
* Complete rewrite of the backend.
* Added theming functions (live_visitors() and top_content() so far).
* Redesigned the admin interface. Old widgets have been removed, and looks cleaner now.

= 0.3.5 =
* Bugfix: use long tags.

= 0.3.4 =
* Bug fixes

= 0.3.0 =
* Re-designed admin interface.
* Widgets for sharing - display the number of current visitors on your site.

= 0.2.3 =
* Added option to prevent post preview pages from being tracked.
* Improved styling: New branding banner at the top and restyled success & error notices.
* Updated tracking code to latest version.
* Added & updated screenshots.

= 0.2.2 =
* Aaron Parker adds option to track admin pages and integrates visitor naming.
* Updated Tracking Code, completely re-architectured for speed & efficiency. Tracker is now included asynchronously and completely unobtrusive.

= 0.2.1 =
* Fixed a bug with site token validation.

= 0.1 =
* Created the plugin.

== Upgrade Notice ==
None
