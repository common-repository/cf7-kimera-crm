=== Contact Form 7 - Kimera CRM Add-on ===
Contributors: kimera
Donate link: http://www.kimeranet.com/
Tags: contact form 7, cf7, kimeracrm, kimera, crm
Requires at least: 4.6
Tested up to: 5.8
Stable tag: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An add-on for Contact Form 7 that provides a way to capture leads and send contact form data to  Kimera CRM.

== Description ==

This plugin adds integration for Kimera CRM to contact form 7. With this plugin it is possible to submit a contact form to an external installation of Kimera CRM.

Activating this plugin, you will be able to enable Kimera CRM integration on a Contact Form module. 
Each module can be linked to a specific intallation of Kimera CRM and to a specific Entity. 
The contact form will be then submitted to the Kimera CRM api. 
The data structure of the form should match the data structure for the api.

== Installation ==

1. Install and activate Contact Form 7 version 4.6 or later
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Enable Kimera CRM on per form basis.


== Changelog ==

= 1.1.5 =
* Add compatibility with Contact Form 7 v5.5.3

= 1.1.4 =
* Add compatibility with Contact Form 5.1

= 1.1.3 =
* Fixed a bug in handling escaped values

= 1.1.2 =
* Added Help tab with dynamic field

= 1.1.1 =
* Added support for yearRange option in jQuery UI Datepicker (kdate)

= 1.1.0 =
* Removed html5 fallback setting (no longer required)
* Added new tag kdate using jquery datepicker for all browsers

= 1.0.9 =
* Added optional format in kdatasource placeholder

= 1.0.8 =
* Added compatibility with PHP 5.4
* Added html5 fallback setting

= 1.0.7 =
* Added compatibility with Contact Form 7 Multi-Step Forms  
* Added compatibility with Contact Form 7 - Success Page Redirects
* Options for SELECT tags can now be in key/value form
* Better error handling policies
* Solved a number of minor issues

= 1.0.6 =
* Fixed issue that occurred when CF7 version installed was not compatible

= 1.0.5 =
* Added filter property in custom selects to retrieve filtered data from CRM
* Added "Refresh event" for all tags
* Added conditional panel ("kPanels")
* Added refresh button ("kButton")
* Added "Hidden input" front-end
* Added "Input file for upload to CRM"
* Performs an update or an insert operation depending on the data received from post or get (ID)
* Code optimization

= 1.0.4 =
* Fix HTTP_REFERER problem

= 1.0.3 =
* Optimizations for enhanced performances

= 1.0.2 =
* Added custom selects, checkboxes and radios with data retrieval from CRM

= 1.0.1 =
* Added "checkbox" and "acceptance" data conversion for CRM

= 1.0.0 =
* Initial commit

