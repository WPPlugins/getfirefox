=== GetFirefox ===
Contributors: habbylow
Donate link: http://www.firefox-jetzt.de
Tags: mozilla, badget, widget, firefox, browser, web, internet, sidebar, widgets, badgets, link, links
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 0.1

Spread firefox by displaying a [firefox download](http://www.firefox-jetzt.de "firefox download") button in the sidebar of your blog.

== Description ==
                                                     
This plugin helps to spread one of the greatest browser we have yet, by displaying a [firefox download](http://www.firefox-jetzt.de "firefox download") button in the sidebar of your blog or anywhere else.

Languages included are english and german so far. If you like to translate this plugin to including the images to a third language, please contact me. Thank you! 

You can place the badge via the old and the new wordpress widget interface. If your theme is not widdget ready, you can place this `<?php if(function_exists('get_firefox'))get_firefox(); ?>` codesnippet anywhere in your template file to display the [firefox download](http://www.firefox-jetzt.de "firefox download") button. 

== Installation ==

1. Unpack the zipfile getfirefox-X.y.zip
1. Upload folder `getfirefox` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to settings -> GetFirefox and configure colors etc.
1. Place `<?php if(function_exists('get_firefox'))get_firefox(); ?>` in your template or use the sidebar widgets.

== Frequently Asked Questions ==

= How often is the download counter updated? =

Once an hour. You can set the timeout on the plugins option page!

= Why can't I choose layout 4 and 5 via widget interface? =

Because those layouts appear in a "pagepeel like" way and need to be placed on absolute position.
Some themes use absolute positioning for there sidebar, therefore we inject the button ode in the paages footer.
Note: yoou template have to call wp_footer()

= Why not spread Google Chrome? =

Dunno!

== Screenshots ==

1. GetFirefox - Button 88x33
1. GetFirefox - Button 80x15 
1. GetFirefox - Button 30x28
1. GetFirefox - Pagepeel left top
1. GetFirefox - Pagepeel right top

== Change Log ==

* v0.1 09.03.2010 initial release

