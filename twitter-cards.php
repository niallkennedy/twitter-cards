<?php
/**
 * Plugin Name: Twitter Cards
 * Plugin URI: https://github.com/niallkennedy/twitter-cards
 * Description: Add Twitter Cards markup to individual posts.
 * Author: Niall Kennedy
 * Author URI: http://www.niallkennedy.com/
 * Version: 1.0
 */

if ( ! class_exists( 'Twitter_Cards' ) ):
/**
 * Add Twitter Card markup to document <head>
 *
 * @since 1.0
 * @version 1.0
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
			$card->setDescription( self::clean_description( apply_filters( 'the_excerpt', get_the_excerpt() ) ) );
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

	public static function clean_description( $description ) {
		if ( ! ( is_string( $description ) && $description ) )
			return '';

		$description = wp_strip_all_tags( strip_shortcodes( $description ) );
		$description = trim( str_replace( array( "\r\n", "\r", "\n" ), ' ', $description ) );
		$excerpt_more = trim( wp_strip_all_tags( apply_filters('excerpt_more', '[...]') ) );
		if ( $excerpt_more ) {
			$excerpt_more_length = strlen( $excerpt_more );
			if ( strlen( $description ) > $excerpt_more_length && substr_compare( $description, $excerpt_more, $excerpt_more_length * -1, $excerpt_more_length ) === 0 ) {
				$description = trim( substr( $description, 0, $excerpt_more_length * -1 ) );
			}
		}
		return $description;
	}
}
add_action( 'wp', 'Twitter_Cards::init' );
endif;
?>