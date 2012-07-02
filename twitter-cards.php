<?php
/**
 * Plugin Name: Twitter Cards
 * Plugin URI: https://github.com/niallkennedy/twitter-cards
 * Description: Add Twitter Cards markup to individual posts.
 * Author: Niall Kennedy
 * Author URI: http://www.niallkennedy.com/
 * Version: 1.0.2
 */

if ( ! class_exists( 'Twitter_Cards' ) ):
/**
 * Add Twitter Card markup to document <head>
 *
 * @since 1.0
 * @version 1.0.2
 */
class Twitter_Cards {
	/**
	 * Attach Twitter cards markup to wp_head if single post view
	 *
	 * @since 1.0
	 */
	public static function init() {
		if ( is_single() )
			add_action( 'wp_head', 'Twitter_Cards::markup' );
	}

	/**
	 * Build a Twitter Card object. Possibly output markup
	 *
	 * @since 1.0
	 */
	public static function markup() {
		global $post;

		if ( ! class_exists( 'Twitter_Card_WP' ) )
			require_once( dirname(__FILE__) . '/class-twitter-card-wp.php' );

		$card = new Twitter_Card_WP();
		$card->setURL( apply_filters( 'rel_canonical', get_permalink() ) );
		$post_type = get_post_type();
		if ( post_type_supports( $post_type, 'title' ) )
			$card->setTitle( get_the_title() );
		if ( post_type_supports( $post_type, 'excerpt' ) ) {
			// one line, no HTML
			$card->setDescription( self::make_description( $post ) );
		}
		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( $post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
			$card->setImage( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height );
		}

		if ( apply_filters( 'twitter_cards_htmlxml', 'html' ) === 'xml' )
			echo $card->asXML();
		else
			echo $card->asHTML();
	}

	/**
	 * Create a description from post excerpt or content. Prep for Twitter display.
	 * Twitter will truncate the description at 200 characters. We will not enforce this character count to allow for a maximum character change or other consuming agents.
	 *
	 * @since 1.0
	 * @param stdClass $post WordPress post object
	 * @return string description string
	 */
	public static function make_description( $post ) {
		if ( ! isset( $post ) )
			return '';

		$text = '';

		// allow plugins to modify, prepend, and append content in excerpt or main content
		if ( ! empty( $post->post_excerpt ) ) {
			// the_content may be triggered when building an excerpt from nothing
			$filters = array( 'the_excerpt', 'the_content' );
			foreach ( $filters as $filter ) {
				remove_filter( $filter, 'wptexturize' );
			}
			$text = trim( apply_filters( 'the_excerpt', $post->post_excerpt ) );
			foreach ( $filters as $filter ) {
				add_filter( $filter, 'wptexturize' );
			}
			unset( $filters );
		} else if ( isset( $post->post_content ) ) {
			remove_filter( 'the_content', 'wptexturize' );
			$text = trim( apply_filters( 'the_content', $post->post_content ) );
			add_filter( 'the_content', 'wptexturize' );
		}

		if ( empty( $text ) )
			return '';

		// shortcodes should have been handled in the_content filter 11. if they are still present then strip
		$text = strip_shortcodes( $text );

		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = wp_strip_all_tags( $text );
		$text = str_replace( array( "\r\n", "\r", "\n" ), ' ', $text );

		$excerpt_more = apply_filters( 'excerpt_more', '[...]' );

		// prep for a pure string compare
		$excerpt_more = html_entity_decode( $excerpt_more, ENT_QUOTES, 'UTF-8' );
		$excerpt_more = trim( $excerpt_more );
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		$text = trim( $text );

		if ( $excerpt_more ) {
			$excerpt_more_length = strlen( $excerpt_more );
			// test if text ends with excerpt more. if so, remove it
			if ( strlen( $text ) > $excerpt_more_length && substr_compare( $text, $excerpt_more, $excerpt_more_length * -1, $excerpt_more_length ) === 0 ) {
				$text = trim( substr( $text, 0, $excerpt_more_length * -1 ) );
			}
		}

		// Twitter asks for 200 characters. look for 300 to provide a buffer, allow for change, and support possible uses by other consuming agents
		if ( strlen( $text ) > 300 ) {
			// assume ~4 characters per word, 75 words max if 300 characters
			$text = trim( wp_trim_words( $text, 75, '' ) );
		}

		return $text;
	}
}
add_action( 'wp', 'Twitter_Cards::init' );
endif;
?>