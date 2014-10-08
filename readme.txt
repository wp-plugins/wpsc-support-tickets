=== wpsc Support Tickets  ===
Contributors: jquindlen
Donate link: http://indiedevbundle.com
Tags: support,tickets,supporttickets,support-tickets,client,clients,help,helpdesk,help-desk,wpstorecart
Requires at least: 3.5.0
Tested up to: 4.0
Stable tag: 4.8.8

== Description ==

[wpsc Support Tickets](http://wpscsupporttickets.com/wordpress-support-ticket-plugin/ "wpsc Support Tickets") is an open source Wordpress support ticket system.  
This isn't some port of another ticket system that's been hacked to run inside Wordpress.  This is a new plugin designed from the ground up specifically for Wordpress.

Support tickets are critical to most businesses that provide a product or service, 
and is often used for clients, customers, members, authors, sponsors, pre-sale questions and more.   

For full documentation, support, addons, and related tools, visit [our site](http://wpscsupporttickets.com/wordpress-support-ticket-plugin/ "our site")

While completely optional, wpsc Support Tickets can work in unison with [wpStoreCart](http://wordpress.org/extend/plugins/wpstorecart/ "wpStoreCart"), an open source ecommerce plugin for Wordpress.
This can optionally allow you to keep your support ticket system accessible only to paying customers and/or current members.

**Hightlighted Features:**

 * Users can create support tickets and reply to their own tickets
 * Guests can use tickets as well, using just their email address.  Disabled by default.
 * Admins, Super Admins, and any user granted the manage_wpsct_support_tickets capability, can reply to, close, or delete any ticket
 * Front end support ticket interface is done in jQuery, and utilizes Ajax ticket loading
 * Customizable departments, email messages, and CSS for custom solutions
 * You can hide support ticket capabilities from a user who has not purchased a specific product (requires [wpStoreCart](http://wordpress.org/extend/plugins/wpstorecart/ "wpStoreCart") 2.4.9 or higher)
 * Seamless integration with open source wpStoreCart ecommerce plugin, including a shared Guest system 
 * Admin dashboard widget shows all open tickets
 * Both the admin and frontend provides a WYSIWYG HTML editor for formatting
 * Available in 16 languages
 * i18n ready and compatible (POT file included in the /languages/ directory)

**Languages Included**

 * English
 * Swedish (Svenska) by Stefan Johansson
 * Norwegian (Norsk) by Rune Kristoffersen
 * French (le Français) by Hawaien88
 * Brazilian Portuguese (português brasileiro) by Thiago Bernardi
 * German (Deutsch) by Markus Scheffknecht
 * Dutch (Nederlands) by Jos Wolbers
 * Finnish (Suomi) by Mikko Ohtonen 
 * Russian (ру́сский язы́к, russkiy yazyk) by Login Roman
 * Romanian (limba Română) by Nutu Valea, updated by Richard Vencu at http://www.dentfix.ro/
 * Italian (Italiano) by Pino Cinelli
 * Spanish (Español) translation provided by Víctor Belgrano
 * Hebrew (עִבְרִית) translation provided by http://atar4u.com/
 * Serbian (српски) translation provided by WPdiscounts @ http://wpdiscounts.com
 * Arabic (العربية) translation provided by Ahmed Raslan @ http://www.nilecode.com/
 * Czech by Jan Drda

== Installation ==

The recommended way to install wpsc Support Tickets is to go into the Wordpress admin panel, and click on Add New under the 
Plugins menu.  Search for wpsc Support Tickets, and then click on Install, then click Install Now.  Once the installation 
completes, Activate the plugin

Or, if you want to install manually:

1. Download the wpsc-Support-Tickets.zip file
1. Extract the zip file to your hard drive, using a 7-zip or your archiver of choice.
1. Upload the `/wpsc-support-tickets/` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page, call it something like Support Tickets
1. Inside this page, place this shortcode only: [wpscSupportTickets]
1. Visit the wpsc Support Tickets admin page and select a "mainpage" for wpsc Support Tickets to use, like the Support Tickets page we told you to create

== Frequently Asked Questions ==

= I have questions, where can I find answers? =
For full documentation, support, addons, and related tools, visit [our site](http://wpscsupporttickets.com/wordpress-support-ticket-plugin/ "our site")

== Screenshots ==
 
1. Admin panel

2. Admin dashboard widget

3. How the front end looks by default in the 2010 theme

4. Starting a new ticket

5. The menu

6. The full options

== Changelog ==

= 5.0.0 =
* COMING SOON:
* Added: Assign specific support staff to a single or multiple departments
* Added: Option for the ticket creator to choose a specific person from the department admins while creating the ticket (admin must enable this feature, as it's disabled by default)
* Added: Ability to edit radio, checkboxes, and drop downs
* Added: Ability to define custom states such as Pending, Need More Info, or any other phrase to describe a ticket state
* Added: Tons of additional information is sent with each email, such as the user who opened the ticket
* Fixed: You no longer receive an email when you reply to your own ticket (implemented in 4.7.30)
* Updated: Get rid of delete_ticket.php, reply_ticket.php, and submit_ticket.php and move functionality into /php/publicajax.php (done as of 4.8.4)
* Added: The ability to have different custom fields display depending on the selected department while creating a ticket.
* Updated: New easier to use and less buggy permission system allows you to easily fine tune who can administrate what tickets (started in 4.7.32)

= 4.8.8 =
* Fixed: Patched a bug that was causing the admin CSS to load on all admin pages
* Added: New setting lets you disable all emails 

= 4.8.7 =
* Updated: charset tweak

= 4.8.6 =
* Added: New beta testing option being added

= 4.8.5 =
* Fixed: Patched a bug related to the changes in 4.8.4 and deleting tickets.

= 4.8.4 =
* Updated: delete_ticket.php has been deleted and correctly replaced with calls to admin-post.php instead

= 4.8.3 =
* Updated: reply_ticket.php and submit_ticket.php have been deleted and correctly replaced with calls to admin-post.php instead

= 4.8.2 =
* Fixed: utf-8 is now correctly set as the default email encoding

= 4.8.1 =
* Added: New option which allows you to hide guest emails on support tickets

= 4.8.0 =
* Added: Support for 7 charsets added for email encoding
* Updated: Email system refactored to increase abilities and fix bugs

= 4.7.34 =
* Updated: The .POT file has been updated to the latest version

= 4.7.33 =
* Fixed: Patched an issue with the new option from 4.7.31

= 4.7.32 =
* Fixed: Multisite Network Activation now works correctly
* Updated: A couple of performance enhancements through slight refactoring, the start of a new permission system, and some updated comments in the code

= 4.7.31 =
* Added: New option which allows you to hide guest emails from the list of tickets

= 4.7.30 =
* Fixed: You no longer receive an email when you reply to your own ticket

= 4.7.29 =
* Added: Automatically changes email address and email sent from name with new settings in the admin panel settings

= 4.7.28 =
* Updated: Switched the settings headers from H2 to H1 for better clarity
* Updated: More work on 5.0 implemented

= 4.7.27 =
* Added: Lots of the ground work for the version 5.0.0 features listed above were implemented

= 4.7.26 =
* Updated: Small aesthetic improvement to admin page

= 4.7.25 =
* Updated: Added the CSS ID #wpscSupportTicketsRegisterMessage so that registration message can be hidden or changed via CSS or Javascript

= 4.7.24 =
* Fixed: Sigh.  bbPress stopped working again, so I had to make all wpsc Support Ticket admins full bbPress admins to fix it
* Updated: Moved most admin ajax to /php/adminajax.php and some public ajax moved to /php/publicajax.php

= 4.7.23 =
* Updated: The .POT file has been updated to the latest version

= 4.7.22 =
* Fixed: Replaced date() function calls with date_i18n() calls instead.

= 4.7.21 =
* Fixed: Line breaks should now properly work on all platforms when creating or replying to a ticket.

= 4.7.20 =
* Fixed: Patched a couple of localization problems

= 4.7.19 =
* Added: Starting the process of adding 'department_admins', which will allow the admin to assign specific support staff to a single or multiple departments

= 4.7.18 =
* Updated: Minor point release to update the pot file to reflect the added text from 4.7.17

= 4.7.17 =
* Added: Option for the ticket admin to not send an email when replying

= 4.7.16 =
* Fixed: Patched an issue that caused a bunch of backslashes to get added in emails
* Updated: Latest .POT file to sync with the new text added in 4.7.15

= 4.7.15 =
* Added: New option allows admin to hide or show severity field during ticket creation

= 4.7.14 =
* Updated: Updated the POT file so as to include the changes in 4.7.12 and 4.7.13

= 4.7.13 =
* Updated: Countries are now translatable strings

= 4.7.12 =
* Updated: More translatable strings added

= 4.7.11 =
* Updated: Reverted to 4.7.5 

= 4.7.10 =
* Fixed: Patched another issue introduced in 4.7.8 which prevented new ticket creation :(

= 4.7.9 =
* Fixed: Patched a loading issue introduced in 4.7.8

= 4.7.8 =
* Added: When show all tickets to guests is enabled, will now show the ticket list without first needing an email address

= 4.7.7 =
* Updated: Removed outdated error message

= 4.7.6 =
* Updated: Working on Jetpack incompatibly 

= 4.7.5 =
* Updated: Updated the pot file to reflect the added translations from 4.7.2. 

= 4.7.4 =
* Updated: Made progress on allowing edit custom fields, also slightly redesigned part of the admin page for custom fields

= 4.7.3 =
* Fixed: Patched an issue that prevent BBPress from showing up in the admin panel 

= 4.7.2 =
* Updated: Updated the pot file
* Updated: More translatable strings converted for i18n

= 4.7.1 =
* Updated: Changed the readme.txt to remove excess tags

= 4.7.0 =
* Updated: Radio buttons and drop downs now enabled for custom user fields.  Checkboxes will be enabled in an update very soon, as well as the ability to edit radios, dropdowns, and checkboxes (right now you have to delete the old one and create a new one)

= 4.6.4 =
* Updated: Romanian (limba Română) translation updated by Richard Vencu at http://www.dentfix.ro/

= 4.6.3 =
* Updated: Updated the pot file

= 4.6.2 =
* Removed: Deleted bug reporting links since they were hardly being used

= 4.6.1 =
* Fixed: Patched an issue where Open and Closed statuses were not being translated.

= 4.6.0 =
* Added: Severity can be set when ticket submitted
* Added: Severity is now editable

= 4.5.1 =
* Updated: Changed the way emails are saved

= 4.5.0 =
* Added: 3 new custom field types: dropdown, checkbox, and radio input
* Updated: A little tlc given to the New Custom Field admin page
* Updated: Minimum supported Wordpress version bumped up to 3.5.0 to accurate reflect the current codebase

= 4.4.3 =
* Fixed:  Changed mail() calls to wp_mail() calls per this bug report: http://wpbugtracktor.com/bug-tracker/mail-in-reply_ticket-php/

= 4.4.2 =
* Updated: Merged the refactored code improvements suggested here: https://github.com/uncovery/wpsc-support-tickets/commit/da2f64a91541460dfaafe03846cb0cbe585d259d

= 4.4.1 =
* Updated: .POT language file updated to the latest version

= 4.4.0 =
* Added: Two new options allow you to include the ticket or replies content inside the emails that are sent on new tickets and new replies
* Added: New ticket and reply email subject lines now include the ticket's title
* Added: Added the Report a Bug, and Suggest a Feature links into the header.
* Updated: Settings page got a slight redesign

= 4.3.2 =
* Updated: .POT language file updated to the latest version

= 4.3.1 =
* Fixed: Patched a bug that was preventing guests from posting

= 4.3.0 =
* Added: Introduced the ability for admin's to create tickets on behalf of other users inside the admin panel

= 4.2.0 =
* Fixed: Bugfix where saving custom fields weren't saving or sorting, and other misc issues

= 4.1.2 =
* Fixed: Bugfix as reported here: https://github.com/wp-plugins/wpsc-support-tickets/pull/1

= 4.1.1 =
* Updated: .POT language file updated to the latest version

= 4.1.0 =
* Added: 2 new settings added so that you can control where your custom fields are displayed on ticket creation, and on the frontend. Options are before everything, before message, after message, or after attachment.
* Fixed: Corrected an unclosed P tag on the options page
* Fixed: Deleted an extra closing DIV on the options page

= 4.0.1 =
* Fixed: Patched a minor issue with the custom fields 

= 4.0.0 =
* Added: Custom fields added
* Fixed: Misc PHP warnings were fixed
* Fixed: Minor small tweaks and fixes

= 3.0.8 =
* Updated: .POT language file updated to the latest version

= 3.0.7 =
* Added: Czech translation added

= 3.0.6 =
* Removed: Unused script

= 3.0.5 =
* Updated: .POT language file updated to the latest version

= 3.0.4 =
* Fixed: Patched an issue where the script was using UTC time instead of the time set in Wordpress admin panel

= 3.0.3 =
* Fixed: Patched an issue where REPLYING to a ticket without an upload was causing issues on certain servers

= 3.0.2 =
* Fixed: Patched an issue where submitting a ticket without an upload was causing issues on certain servers

= 3.0.1 =
* Fixed: Patched a couple warnings and other small errors in the 3.0 release
* Updated: .POT language file updated to the latest version
* Fixed: Tried to fix a timeout issue on sites with lots of tickets when viewing stats

= 3.0.0 =
* Added: Open ticket time added to PRO
* Added: Time it took to resolve tickets added to PRO
* Added: Stats added to PRO
* Fixed: Patched a small issue with file attachments in PRO

= 2.2.1 =
* Fixed: Patched an issue where the new ticket button would accidently submit the ticket form prematurely

= 2.2.0 =
* Fixed: Patched an issue where submitting a ticket without an upload was causing issues on certain servers

= 2.1.2 =
* Updated: Changed the unauthorized message to something more direct

= 2.1.1 =
* Fixed: Patched a bug where if all users could view all tickets, clicking on most tickets incorrectly stated they were last updated by you

= 2.1.0 =
* Fixed: Resolved a plugin conflict between this and the AG Custom Admin plugin
* Fixed: HTML markup using nicEdit in the admin panel should now work correctly
* Fixed: Patched a bug where slashes were added to HTML emails
* Fixed: Patched a bug where the Update Settings button would be unclickable at times
* Fixed: Patched a bug where if all users could view all tickets, most tickets incorrectly stated they were last updated by you

= 2.0.5 =
* Fixed: Patched an issue where backslashes were being added to HTML emails

= 2.0.4 =
* Fixed: Hopefully patched some bugs affecting some users

= 2.0.3 =
* Minor update

= 2.0.2 =
* Fixed: Patched an issue where settings were not saving correctly

= 2.0.1 =
* Updated: Language .POT file updated for changes in 2.0

= 2.0.0 =
* Added: Tons of new features for PRO users
* Added: Created options page
* Added: Added many action hooks to extend functionality
* Updated: Removed depreciated functions 

= 1.9.1 =
* Fixed: For users with session issues, guest accounts should now work properly

= 1.9.0 =
* Updated: Admin can now see the guests email address

= 1.8.12 =
* Added: Arabic (العربية) translation provided by Ahmed Raslan @ http://www.nilecode.com/

= 1.8.11 =
* Added: Serbian (српски) translation provided by WPdiscounts @ http://wpdiscounts.com
* Updated: NL translation minor update

= 1.8.10 =
* Fixed: Bug fix during activation

= 1.8.9 =
* Fixed: Incompatibilities between wpscSupportTicket and Jetpack were resolved.

= 1.8.8 =
* Added: Spanish (Español) translation provided by Víctor Belgrano url: http://www.pymessoft.com email: info@pymessoft.com
* Added: Hebrew (עִבְרִית) translation provided by http://atar4u.com/

= 1.8.7 =
* Added: Italian (Italiano) translation provided by Pino Cinelli

= 1.8.6 =
* Added: Russian (ру́сский язы́к, russkiy yazyk) translation provided by Login Roman

= 1.8.5 =
* Added: Finnish (Suomi) translation provided by Mikko Ohtonen http://twitter.com/mopetti
* Added: Dutch (Nederlands) translation provided by Jos Wolbers

= 1.8.4 =
* Added: German translation provided by Markus Scheffknecht, contact at https://twitter.com/Scheffhauser

= 1.8.3 =
* Fixed: Corrected two strings that were not properly registered for translation

= 1.8.2 =
* Added: Brazilian Portuguese translation provided by Thiago Bernardi, e-mail address: thiagobernardi@outlook.com

= 1.8.1 =
* Fixed: Fixed a javascript error regression that emerged in the 1.8.0 release.

= 1.8.0 =
* Fixed: Theme compatibility greatly improved.  Some themes break inline javascript, so I removed inline javascript function definitions, and localized them for Wordpress.
* Updated: Updated the French translation

= 1.7.7 =
* Added: Norwegian translation provided by Rune Kristoffersen
* Added: Hawaien88 for the French translate. -> Merci à Hawaien88 pour la traduction Française.

= 1.7.6 =
* Updated: Added a few missing classes from buttons
* Updated: Updated the Swedish translation

= 1.7.5 =
* Updated: Updated the POT file in the /languages/ directory
* Updated: Updated the Swedish translation

= 1.7.4 =
* Updated: A few missed translation strings were caught and are now translatable 

= 1.7.3 =
* Updated: Updated the POT file in the /languages/ directory
* Updated: Updated the Swedish translation

= 1.7.2 =
* Updated: A few missed translation strings were caught and are now translatable 

= 1.7.1 =
* Updated: Added the POT file into the /languages/ directory
* Updated: Updated the Swedish translation

= 1.7.0 =
* Added: Swedish translation provided by Stefan Johansson
* Added: Languages folder added.
* Updated: Text domain of wpsc-support-tickets manually added to all translatable strings
* Updated: A few missed translation strings were caught and are now translatable 

= 1.6.1 =
* Fixed: Turned off error reporting

= 1.6.0 =
* Updated: Fixed issue with Wordpress 3.4 Beta 1

= 1.5.0 =
* Fixed: SSL support added.  Removed references to WP_PLUGIN_URL and replaced them with plugin_url() which supports SSL
* Updated: A few strings that were not previously translatable have now become so.  Expect a German translation soon.
* Info: Minimum required Wordpress version changed to 3.0

= 1.4.0 =
* Added: You can now see who posted last on a ticket, either a staff member or the ticket creator.  This is in the front end, the admin panel, and the admin dashboard widget.  Note that staff replies that were made before you updated to this version will not work correctly.
* Fixed: Patched issue where the register URL was not always correctly formatted when certain conditions were met.
* Updated: More of the admin is now ready for language translations.
* Fixed: Patched a few issues were the word Guest was not showing up for guests

= 1.3.0 =
* Fixed: Patched the issue with improperly escaped URLs and quotes in the plugin.  Thanks for the help Bren!

= 1.2.0 =
* Fixed: Patched an issue with the admin dashboard widget declaring all ticket openers as Guest

= 1.1.0 =
* Fixed: Proactively insures that only one instance of the "Create a New Ticket" is displayed
* Added: Option to allow guests to use the ticket system by simply entering in their email address.

= 1.0.0 =
* Fixed: Removed the ability for non-approved user to see the admin dashboard widget

= 0.9.5 =
* Fixed: Removed reference to a non-used method that was causing an error on some dashboards

= 0.9.4 =
* Added: New option available to disable the inline styles which are by default put into the elements

= 0.9.3 =
* Added: Admin dashboard widget will now display a "No open tickets" message when there are no open tickets

= 0.9.2 =
* Updated: the readme.txt

= 0.9.1 =
* Updated: the description to remove references to this being an arcade plugin

= 0.9.0 =
* Added: Everything has been finished for the first public beta

= 0.5.0 =
* Added: Initial release
