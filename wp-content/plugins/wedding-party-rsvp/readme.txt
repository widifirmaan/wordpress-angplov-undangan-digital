=== Wedding Party RSVP ===
Contributors: brelandr
Tags: wedding, rsvp, guests, party, invitation
Requires at least: 6.0
Tested up to: 6.9.1
Stable tag: 7.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple and secure Wedding RSVP management system. Manage unlimited guests and adult meal choices.

== Description ==

Wedding Party RSVP is a streamlined solution for managing wedding guest lists and RSVPs directly inside WordPress.

== Try It Live - Preview This Plugin Instantly ==

Experience Wedding Party RSVP without installation! Click the link below to open WordPress Playground with the plugin pre-installed and configured with sample data.

[Preview on WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/brelandr/wedding-party-rsvp/main/blueprint.json)

== Screenshots ==

1. This is the main page where a user enters the invite ID or Party ID (Styled using elementor)
2. This shows how the widget looks on the frontend after entering Party ID or Invite ID.
3. This is the Main Admin Page
4. Main Settings Page
5. Email Invite Page
6. Shortcode
7. SMS Invite page

Key Features (Free Version):

Guest Management: Add, edit, and delete unlimited guests.

Adult Menu Choices: Create and manage entrée options for your reception.

Dietary Restrictions: Guests can note allergies (Gluten Free, Vegan, etc.).

Dashboard Statistics: View real-time stats on accepted, declined, and pending RSVPs.

Mobile Friendly: Fully responsive Admin Dashboard.

Security: Built with WordPress best practices for data sanitization and escaping.

Pro Features:
Upgrade to the Pro version to unlock:

Child Management: Track children and assign specific child meals.

Full Menu Course: Add Appetizers and Hors d'oeuvres options.

Admin Notes & Table Numbers: Organize your seating chart and keep private notes.

Email & SMS Invites: Send invitations directly from the dashboard.

Customization: Toggle visibility of fields and customize colors/fonts.

== How to Purchase Pro ==

Go to https://landtechwebdesigns.com/wedding-party-rsvp-wordpress-plugin/

Purchase the license key to unlock the full suite of features.

== Installation ==

Upload the wedding-party-rsvp folder to the /wp-content/plugins/ directory.

Activate the plugin through the 'Plugins' menu in WordPress.

Create a new Page (e.g., "RSVP").

Add the shortcode [wedding_rsvp_form] to the page content.

Go to Wedding RSVP -> Settings and set the "RSVP Page URL" to the link of the page you just created.

== Frequently Asked Questions ==

= Can I use this for events other than weddings? =
Yes! While tailored for weddings, it works for any event requiring basic RSVP tracking.

= How do I reset the guest list? =
Go to Settings and scroll to the bottom "Danger Zone". Click "Reset Program to Default".

== Screenshots ==

Dashboard: View guest statistics and manage RSVPs.

Frontend Form: How the guests see the RSVP form.

Mobile View: Managing guests on a mobile device.

== Changelog ==

= 7.3.1 =

New: Review request notice after 7 days (Enjoying Wedding Party RSVP?) with Yes / No (Support) / Dismiss. AJAX dismissal, nonce-secured, shown only on plugin admin pages.

= 7.3 =

Security Update: Implemented late escaping for inline styles and rigorous variable escaping for output.

Cleanup: Removed unused external service references to comply with directory guidelines.

= 7.2 =

Security: Updated prefixes, nonce sanitization, and SQL preparation.

Architecture: Moved form processing to init hook for safer redirects.

= 7.1 =

Security Update: Fixed escaping and sanitization issues.

Mobile Responsiveness: Updated Admin Dashboard with "Card View".

Performance: Implemented Object Caching.

= 7.0 =

Major update with new UI.

= 1.0 =

Initial Release.
