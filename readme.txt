=== Coil Web Monetization ===
Author URI: https://coil.com
Plugin URI: https://coil.com
Contributors: coil, obox, pragmaticagency
Tags: coil, content, monetization, payment, interledger, ilp
Requires at least: 5.0+
Requires PHP: 7.2
Tested up to: 6.1
Stable tag: 2.0.2
License: Apache-2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0.txt

Coil makes it easy for you to get paid for your online content.

== Description ==

Coil's official Web Monetization plugin makes it easy for you to get paid instantly while Coil members browse your content. All you need to do is add your payment pointer, then you’ll receive a stream of micropayments from Coil for every second that a Coil member views your content.

### How it works

1. Coil members pay a monthly fee.
2. You create an account with an ILP-enabled digital wallet and receive a payment pointer.
3. You install the WordPress plugin.
4. Coil streams payments to your wallet while Coil members enjoy your content.

To learn more about Coil, visit us at [coil.com](https://coil.com/).

For user documentation, visit our [help center](https://help.coil.com/docs/monetize/content/wp-overview/).

== Installation ==

1. Install the official Coil Web Monetization plugin.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Select 'Coil' in the Admin menu.
4. Select 'Add Payment Pointer' which will take you to the 'General Settings' tab where you can enter your payment pointer.
5. Click 'Save Changes'.

== Features ==

Our plugin lets you add Web Monetization to all or some of your content.

* When monetization is enabled, you get paid when Coil members visit your content
* When monetization is disabled, you don't get paid when people visit that content, even if they are Coil members

When content is monetized it can:

* Remain visible to everyone (default) - Allow all visitors to see the content, get paid when your visitor is a Coil member
* Be made exclusive - Only allow Coil members to see the content and get paid when they do. People who are not Coil members will see a paywall instead. Use the Coil Exclusive Content Divider to specify exactly how much content on a single post should be public and from what point it should become exclusive.

The plugin is supported with the Block/Gutenberg editor. Monetization and visibility settings can be assigned globally to post types, to individual pages and posts, and to tags and categories.

== Frequently Asked Questions ==

= How do I start using the Coil Web Monetization plugin? =

Simply install the plugin. Once activated, you must enter your payment pointer in the plugin's 'General Settings' tab. The defaults will monetize your content while keeping it visible for everyone to see.
If your exclusive content is displaying incorrectly, check your CSS selector. There is a button in the Exclusive Content tab that will attempt to detect your CSS selector automatically for you.

= What's a payment pointer? =

A payment pointer is like an email address for your digital wallet. It's how we know where to stream your payments. Your payment pointer is assigned to you by your ILP wallet provider.

= What's an ILP-enabled digital wallet? =

The Interledger Protocol (ILP) is an open protocol that Coil uses to stream payments. A digital wallet provider must support this protocol to receive payments from us. For more information about supported digital wallets, see [Digital Wallets](https://webmonetization.org/docs/ilp-wallets).

= Do I have to sign up for a Coil membership? =

Nope. You'll receive payments from Coil members regardless of whether you have an account with us.

= Where can I report bugs or contribute to the project? =

Reach out to us on the [Coil Web Monetization support forum](https://wordpress.org/support/plugin/coil-web-monetization/).

= Is Coil Web Monetization translatable? =

Yes! Translations can be contributed at [translate.wordpress.org](https://translate.wordpress.org/).

= Where can I ask for help? =

If you get stuck, check out our [help center](https://help.coil.com/docs/monetize/content/wp-overview/) or post a question to the [Coil Web Monetization support forum](https://wordpress.org/support/plugin/coil-web-monetization/).

== Screenshots ==

1. Simple setup
2. Easy customization
3. Exclusive content
4. Great experience
5. Thank supporters

== Changelog ==

= v2.0.2 =

= 15 November 2022 =

* Enhancement - Added a size selection for exclusive post icons to better match your site's font.
* Enhancement - Added a warning for the Exclusive Content Divider (ECD) in the post editor when an ECD is added to a post but the post is set to be public. In the case where the post is set to be public the ECD will have no effect.

* Tweak - Added compatibility for the new Web Monetization standard.
* Tweak - Added an automated build process to Circle CI so that a zip file is created each time a version is tagged.
* Tweak - Updated dev dependencies.

= v2.0.1 =

= 1 June 2022 =

* Fix - Custom menu labels were being incorrectly displayed.

= v2.0.0 =

= 18 May 2022 =

* Enhancement - Coil’s new Exclusive Content Divider (ECD) Block has been added to the editor to indicate the start of exclusive content. When placed in a post, all content above the ECD will be visible to everyone and all content below it will be exclusive to Coil members. Type /Coil, and select the ECD from the Block Inserter menu.
* Enhancement - The ECD replaces the Split Content functionality. If you previously used Split Content, upgrading to v2.0.0 will cause the ECD to be inserted before the first exclusive block on each applicable post.
* Enhancement - We’ve added a button that can automatically detect your theme’s CSS selectors for you.
* Enhancement - Web Monetization and content visibility settings have been separated, making it easier to manage either setting.
* Enhancement - All exclusive content settings are now in the Exclusive Content tab, where you can enable or disable exclusive content globally.
* Enhancement - The paywall that appears for visitors without a Coil membership can now be managed and customized in the Exclusive Content tab. Customizations can be seen with a live preview.
* Enhancement - Encourage your audience to support you through Coil by adding the Coil-branded streaming support widget to selected post types. With this customizable, dismissable widget, paying Coil members will be thanked for their contribution, while non-paying viewers will be prompted to become Coil members to support your amazing content.
* Enhancement - We’ve added more customization options for the icon that appears next to an exclusive post’s title. Choose from four icons, including the padlock, and select to place the icon before or after the post’s title.

* Tweak - The padlock icon no longer appears next to post titles in menus.
* Tweak - When excerpts are enabled for exclusive posts they will be visible during the pending phase (while the monetization state is being determined) so that users can start reading immediately.
* Tweak - The user-facing message that appears while the browser determines a visitor’s Coil membership status can no longer be customized.
* Tweak - The CSS selector default has been adjusted to support the new Twenty Twenty-Two theme.
* Tweak - We no longer support the Classic Editor.

= v1.9.0 =

= 15 July 2021 =

* Fix - Fixed embedded videos that were set to Coil Members Only so that they now display correctly once monetization has begun.
* Fix - The Coil Promotion Bar message no longer displays for Coil members.

* Enhancement - The Coil settings panel's first tab is now the Global Settings tab where the payment pointers and CSS selectors are saved.
* Enhancement - A sidebar has been added throughout the settings panel with help, FAQ and documentation links.
* Enhancement - All customization features have been consolidated into the Coil settings panel (including message, button text and link, and padlock and Coil Promotion Bar display customization).
* Enhancement - The wording explaining message customizations has been simplified for easier reading.
* Enhancement - The wording describing monetization settings has been adjusted. The new description distinguishes between monetization being enabled or disabled. If it is enabled users can select who the content is visible to: everyone (previously Monetized and Public), Coil members only or split.
* Enhancement - When creating posts / pages the monetization options have been made easier to understand. A simple Enabled / Disabled selector has been added with extra options for fine tuning the monetization level.
* Enhancement - When creating posts / pages the default monetization setting is listed next to "Default" so there is no need to remember what it was set to.
* Enhancement - The new default monetization setting for pages and posts is now enabled and visible to everyone instead of having monetization disabled by default.

* Tweak - Certain messages have been deprecated. These include the fully and partially gated excerpt messages, as well as the unable to verify message which has been merged with the unsupported message.
* Tweak - Code has been linted.
* Tweak - Eslint is the new linting tool being used instead of JSHint.
* Tweak - The Grunt watch task now also includes minifying the relevant CSS files as well to reflect SCSS changes made in real time during development.

= v1.8.0 =

= 08 Mar 2021 =

* Fix - Resolved issue that prevented users from adding content to custom pages.
* Fix - Solved the incompatibility issue with the Co-Authors Plus plugin and the CoverNews Pro theme.
* Fix - Fixed compatibility error with LearnDash when browser did not load.

* Enhancement - Updated handling of excerpts for monetized posts in the post archive. The default excerpt is now blank unless a custom excerpt has been written.
* Enhancement - A warning pop up has been added when a user saves their Global Settings and the payment pointer field is empty.
* Enhancement - The padlock icon has been completely removed from split content page / post titles and will only appear when entire pages / posts have monetization enabled and are only visible to Coil members.

* Tweak - Minimum supported PHP version is now version 7.2.
* Tweak - The gradient effect on the CTA boxes that block exclusive content for users without an active Coil membership has been replaced with a solid border instead.
* Tweak - Updated Coil header on the settings panel.
* Tweak - Removed the “Configure the plugin” button in the settings panel (simply took users to the Global Settings tab).
* Tweak - All buttons that point to external links now open in a new tab.

* Security - Added extra checks on the Theme Options partial loader logic.

= v1.7.0 =

First release.

== Upgrade Notice ==

= What to expect when updating to version 2 =
Please be aware that Split content is no longer supported. We're replacing Split content with the Exclusive Content Divider (ECD). If you had posts that were using Split content we will insert the ECD above the first instance of a block that is set to Coil Members Only. Everything above the ECD will be publicly visible and everything below it will be exclusive for Coil members.
There is no longer an option to hide content from Coil members.
Please don’t hesitate to be in touch with us about the plugin or the new release. You can reach us on the [Coil Web Monetization support forum](https://wordpress.org/support/plugin/coil-web-monetization/).
