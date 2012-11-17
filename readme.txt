=== Twitter Cards ===
Contributors: niallkennedy
Tags: twitter, twitter cards, semantic markup
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 1.0.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Twitter Card markup to individual posts. Supports summary, photo, and player types.

== Description ==

Generate inline content previews on Twitter.com and Twitter clients by including [Twitter Card](https://dev.twitter.com/docs/cards) HTML meta elements for individual posts.

All cards are a Twitter Card summary by default. Tap into the `twitter_card_properties` filter to reference the Twitter accounts of your site or author.

== Filters ==

* `twitter_cards_properties` - act on an array of properties before they are output to the page
* `twitter_cards_htmlxml` - override the default treatment of `html` with `xml`. XML will self-close the meta void element ( "<element />" vs. "<element>" )

== Frequently Asked Questions ==

= You forgot to include a trailing slash on meta elements =

The plugin outputs HTML-style void elements without a trailing slash by default. Add XML-style trailing slashes by returning a value of `xml` on the `twitter_cards_htmlxml` filter.

== Upgrade Notice ==

= 1.0.3 =
Simplify description generator. Allow no description for photo cards.

= 1.0.2 =
Change attribute from value to content to match current Twitter documentation.

= 1.0.1 =
Improved auto-generated description.

== Changelog ==

= 1.0.3 =
* Simplify the description generator. Based on the Open Graph protocol description generator in the Facebook plugin.
* Update bundled version of Twitter Cards PHP, fixing description property treated as a required property for photo card type.

= 1.0.2 =

* Change attribute from value to content to match current Twitter documentation.

= 1.0.1 =

* Improve automatic excerpt generator and scrubber

= 1.0 =
* Initial release