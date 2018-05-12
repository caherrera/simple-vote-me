=== Simple Vote Me ===
Plugin Name: Simple Vote Me
Plugin URI: https://github.com/caherrera/simple-vote-me
Author: Carlos Herrera
Author URI: https://github.com/caherrera/simple-vote-me
Contributors:
Tags: vote, poll,smileys, count vote,good,bad,neutral,custom vote, recount, custom poll
Version: 1.4.6
Requires at least: 3.0
Tested up to: 4.9.5
Stable tag: 1.4.6
Donate link:
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate a simple voting plugin with smileys in your Wordpress!


== Description ==

This plugin allows you to set a votation for the post and pages. This votation has three options (good, neutral and bad).

= Features =
* Auto insert in content (it can be disabled).
* Filter by post and page.
* Total counts and vote percentages.
* 3 Types of votes: Good, Neutral and Bad.
* See the votes on the admin panel.
* Widget to insert wherever you want.
* Widget to see the top rated post.
* Shortcode to insert whenever you want.
* Two styles: Horizontal and Vertical so you can put it wherever you want!.
* Capability to set custom images.
* You can set a title before the poll (if you want it).
* Select if you want to show the poll before or after the content (or both).

== ScreenShots ==
1. This screen shot shows the result in "Horizontal Mode" with auto insert on post and with the default CSS.
2. This screen shot shows 2 widgets. The first allows us to put the poll wherever we want, and the second shows the most voted.
3. This screen shot shows the admin panel.
4. Inside create/edit post/page/custom post you can see who vote this.

== Installation ==

This section describes how to install the plugin and get it working.

= Installing the Plugin =


*(using the Wordpress Admin Console)*

1. From your dashboard, click on "Plugins" in the left sidebar
2. Add a new plugin
3. Search for "Simple Vote me"
4. Install "Simple vote me".
5. In the settings page you can set a few options!


*(manually via FTP)*

1. Upload the 'gt-simple-vote-me' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the settings page you can set a few options!


== Frequently Asked Questions ==

= Can I set a custom CSS? =

Sure!, Go to the admin panel and in the options page for "Simple vote me" you can add your custom CSS.


= How to modify it if I want Horizontal or Vertical? =

You can set it using the Widget (in the options Widget) or with the shortcodes [simplevoteme type="v"] for vertical mode, or [simplevoteme type="h"] for horizontal mode. 


= What about validation errors, will it still maintain my WP theme? =

The plugin uses minimum CSS and inserts valid HTML when necessary.


== Changelog ==
= 1.4.6 =
* Change vote list to floating

= 1.4.5 =
* Hide scrollbar on votes

= 1.4.4 =
* Adding list of votes to Compliments

= 1.4.3 =
* Adding support to old votes

= 1.4.2 =
* When infinity votes allows, this plugin will replace previous vote instead of add.

= 1.4.1 =
* Minor tweaks in css

= 1.4 =
* Show Users when votes

= 1.3.1 =
* Minor update. Fixed an error on PHP-FPM.
* Checked on Wordpress 4.6.1.

= 1.3 =
* Now you can limit the votes by user!
* Columns for pages and custom posts added in admin panel
* You can see who votes but older votes from version 1.2 and less will appear like Annonymous :( (sorry the info wasn't saved before).
* Added support for custom post and pages. Now you can select which do you want activate.

= 1.2 =
* Addded an option to show the poll on Home page.
* Minor fixes.

= 1.1.1 =
* Capabilities to change color wraps in images (and borders).
* Added option to maintain the default CSS and add minor CSS changes.

= 1.1 =
* Added capability to set custom images.
* Added title before poll (if you want it).
* Added option to show the poll before or after the content (or both).

= 1.0.1 =
* Minor fixes

= 1.0 =
* Create the plugin and the basic options!