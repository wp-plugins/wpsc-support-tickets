=== wpsc Support Tickets  ===
Contributors: jquindlen
Donate link: http://wpstorecart.com/
Tags: support,tickets,supporttickets,support-tickets,client,clients,help,helpdesk,help-desk,wpstorecart
Requires at least: 2.8.0
Tested up to: 3.2.1
Stable tag: 1.4.0

== Description ==

[wpsc Support Tickets](http://wpstorecart.com/wpsc-support-tickets/ "wpsc Support Tickets") is an open source Wordpress support ticket system.  
This isn't some port of another ticket system that's been hacked to run inside Wordpress.  This is a new plugin designed from the ground up specifically for Wordpress.

Support tickets are critical to most businesses that provide a product or service, 
and is often used for clients, customers, members, authors, sponsors, pre-sale questions and more.   

For full documentation, support, addons, and related tools, visit [our site](http://wpstorecart.com/wpsc-support-tickets/ "our site")

While completely optional, wpsc Support Tickets can work in unison with [wpStoreCart](http://wordpress.org/extend/plugins/wpstorecart/ "wpStoreCart"), an open source ecommerce plugin for Wordpress.
This can optionally allow you to keep your support ticket system accessible only to paying customers and/or current members.

**Hightlighted Features:**

 * Users can create support tickets and reply to their own tickets
 * Guests can use tickets as well, using just their email address.  Disabled by default.
 * Admins, and any user granted the manage_wpsc_support_tickets capability, can reply to, close, or delete any ticket
 * Front end support ticket interface is done in jQuery, and utilizes Ajax ticket loading
 * Customizable departments, email messages, and CSS for custom solutions
 * You can hide support ticket capabilities from a user who has not purchased a specific product (requires [wpStoreCart](http://wordpress.org/extend/plugins/wpstorecart/ "wpStoreCart") 2.4.9 or higher)
 * Seamless integration with open source wpStoreCart ecommerce plugin, including a shared Guest system 
 * Admin dashboard widget shows all open tickets
 * Both the admin and frontend provides a WYSIWYG HTML editor for formatting
 * i18n ready and compatible
 * 100% free and open source under the LGPL

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
For full documentation, support, addons, and related tools, visit [our site](http://wpstorecart.com/wpsc-support-tickets/ "our site")

== Screenshots ==
 
1. Admin panel

2. Admin dashboard widget

3. How the front end looks by default in the 2010 theme

4. Starting a new ticket

5. The menu

6. The full options

== Changelog ==

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
