=== Twitter Cards ===
Contributors: niallkennedy
Tags: twitter, twitter cards, semantic markup
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 1.0.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Twitter Card markup to individual posts. Supports summary, photo, and player types.

== Description ==

Generate inline content previews on Twitter.com and Twitter clients by including [Twitter Card](https://dev.twitter.com/docs/cards) HTML meta elements for individual posts.

All cards are a Twitter Card summary by default. Tap into the `twitter_card_properties` filter to reference the Twitter accounts of your site or author.

== Frequently Asked Questions ==

= You forgot to include a trailing slash on meta elements =

The plugin outputs HTML-style void elements without a trailing slash by default. Add XML-style trailing slashes by returning a value of `xml` on the `twitter_cards_htmlxml` filter.

== Changelog ==

= 1.0.2 =

Change attribute from value to content to match current Twitter documentation.

= 1.0.1 =

* Improve automatic excerpt generator and scrubber

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0.2 =
Change attribute from value to content to match current Twitter documentation.

= 1.0.1 =
Improved auto-generated description.