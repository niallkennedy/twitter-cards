=== Twitter Cards ===
Contributors: niallkennedy, wernull
Tags: twitter, twitter cards, semantic markup
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.0.6
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Twitter Card markup to individual posts. Supports summary, photo, and player types.

== Description ==

Generate inline content previews on Twitter.com and Twitter clients by including [Twitter Card](https://dev.twitter.com/docs/cards) HTML meta elements for individual posts.

All cards are a Twitter Card summary by default. Tap into the `twitter_card_properties` filter to reference the Twitter accounts of your site or author.

Test your site's Twitter Card display through [Twitter's card preview tool](https://dev.twitter.com/docs/cards/preview).

== Filters ==

* `twitter_cards_properties` - act on an array of properties before they are output to the page
* `twitter_cards_htmlxml` - override the default treatment of `html` with `xml`. XML will self-close the meta void element ( "<element />" vs. "<element>" )

== Frequently Asked Questions ==

= Why don't you support feature X? =

I wrote this plugin for my own site, [NiallKennedy.com](http://www.niallkennedy.com/ "Niall Kennedy"), added some slight flexibility through filters, and released the plugin on GitHub as [a PHP library](https://github.com/niallkennedy/twitter-cards-php "Twitter Cards generator PHP") with [a WordPress plugin wrapper](https://github.com/niallkennedy/twitter-cards "Twitter Cards WordPress plugin"). I use summary cards for my site's articles. If you would like to add better support for photos or videos exposed to Twitter you can fork my work and optionally send some code in a pull request.

= How do I add my Twitter account? =
Go to Settings / Twitter Cards
Card Type should be summary, photo, summary_large_image, product, player, or app
Site and Creator should start with @
Site:ID and Creator:ID should be a number

Since Twitter API 1.0 was shut down, it is more difficult to find your Twitter ID. A few options:
http://mytwitterid.com/
http://www.idfromuser.com/

Your Twitter screenname may change but your Twitter ID will remain the same. Grab both while you are setting up your site to provide Twitter with the best data.

= You forgot to include a trailing slash on meta elements =

The plugin outputs HTML-style void elements without a trailing slash by default. Add XML-style trailing slashes by returning a value of `xml` on the `twitter_cards_htmlxml` filter.

== Screenshots ==

1. Twitter Card display on desktop
2. Twitter Card display on mobile

== Upgrade Notice ==

= 1.0.6 =
Add WordPress admin menu for setting twitter card properties

= 1.0.4 =
Treat post format of image as a Twitter photo card.

= 1.0.3 =
Simplify description generator. Allow no description for photo cards.

= 1.0.2 =
Change attribute from value to content to match current Twitter documentation.

= 1.0.1 =
Improved auto-generated description.

== Changelog ==

= 1.0.4 =
* Treat post format of "image" as a Twitter photo card.

= 1.0.3 =
* Simplify the description generator. Based on the Open Graph protocol description generator in the Facebook plugin.
* Update bundled version of Twitter Cards PHP, fixing description property treated as a required property for photo card type.

= 1.0.2 =

* Change attribute from value to content to match current Twitter documentation.

= 1.0.1 =

* Improve automatic excerpt generator and scrubber

= 1.0 =
* Initial release