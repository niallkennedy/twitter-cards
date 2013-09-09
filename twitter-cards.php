<?php
/**
 * Plugin Name: Twitter Cards
 * Plugin URI: https://github.com/niallkennedy/twitter-cards
 * Description: Add Twitter Cards markup to individual posts.
 * Author: Niall Kennedy
 * Author URI: http://www.niallkennedy.com/
 * Version: 1.0.6
 */

if ( ! class_exists( 'Twitter_Cards' ) ):
/**
 * Add Twitter Card markup to document <head>
 *
 * @since 1.0
 * @version 1.0.4
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
		if ( ! ( isset( $post ) && isset( $post->ID ) ) )
			return;

		setup_postdata( $post );

		$post_id = absint( $post->ID );
		if ( ! $post_id )
			return;

		$post_type = get_post_type( $post );
		if ( ! $post_type )
			return;
		$post_format = get_post_format( $post_id );

		if ( ! class_exists( 'Twitter_Card_WP' ) )
			require_once( dirname(__FILE__) . '/class-twitter-card-wp.php' );

		// does current post type and the current theme support post thumbnails?
		$post_thumbnail_url = false;
		if ( post_type_supports( $post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() )
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

		// treat as photo card if post type image
		// why not also check for post format video? No real easy way in WordPress Core to ask for an iframe version of the video embedded in your post, meaning you'll have to tap into a filter and may as well set the card value alongside required attributes such as player
		if ( $post_format === 'image' && $post_thumbnail_url && $post_thumbnail_width >= 280 && $post_thumbnail_height >= 150 )
			$card = new Twitter_Card_WP( 'photo' );
		else
			$card = new Twitter_Card_WP();
		$card->setURL( get_permalink( $post_id ) );
		if ( post_type_supports( $post_type, 'title' ) )
			$card->setTitle( get_the_title() );
		if ( post_type_supports( $post_type, 'excerpt' ) ) {
			// one line, no HTML
			$description = self::make_description( $post );
			if ( $description )
				$card->setDescription( $description );
			unset( $description );
		}

		if ( $post_thumbnail_url )
			$card->setImage( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height );

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
		if ( ! ( isset( $post ) && isset( $post->post_excerpt ) && isset( $post->post_content ) ) )
			return '';

		// did the publisher specify a custom excerpt? use it
		if ( ! empty( $post->post_excerpt ) )
			$description = apply_filters( 'get_the_excerpt', $post->post_excerpt );
		else
			$description = $post->post_content;

		$description = trim( $description );
		if ( ! $description )
			return '';

		// basic filters from wp_trim_excerpt() that should apply to both excerpt and content
		// note: the_content filter is so polluted with extra third-party stuff we purposely avoid to better represent page content
		$description = strip_shortcodes( $description ); // remove any shortcodes that may exist, such as an image macro
		$description = str_replace( ']]>', ']]&gt;', $description );
		$description = trim( $description );

		if ( ! $description )
			return '';

		// use the built-in WordPress function for localized support of words vs. characters
		// Twitter asks for 200 characters. look for 300 to provide a buffer, allow for change, and support possible uses by other consuming agents
		// assume ~4 characters per word, 75 words max if 300 characters
		$description = wp_trim_words( $description, 75, '' );

		if ( $description )
			return $description;

		return '';
	}

	/**
	 * Register settings for Twitter Cards and set validation callback
	 *
	 * @since 1.0.6
	 */
	public static function admin_init() {
		register_setting( 'twitter-card', 'twitter_card', 'Twitter_Cards::settings_validate' );
	}

	/**
	 * TODO Validate user input from admin menu
	 *
	 * @since 1.0.6
	 * @param $input form input data
	 * @return validated form data
	 */
	public static function settings_validate( $input ) { 
		return $input; 
	}

	/**
	 * Register admin menu page
	 *
	 * @since 1.0.6
	 */
	public static function admin_menu() {
		add_options_page( 'Twitter Cards', 'Twitter Cards', 'manage_options', 'twitter-card', 'Twitter_Cards::admin_options' );
	}

	/**
	 * Display admin menu outer html
	 *
	 * @since 1.0.6
	 */
	public static function admin_options() {
		?>
    	<div class="wrap">
    		<div id="icon-options-general" class="icon32"></div>
			<h2>Twitter Card Settings</h2>
			<form action="options.php" method="post">
				<?php 
					settings_fields('twitter-card');
					$options = self::get_admin_options();
		
			        echo '<br/><input type="hidden" name="twitter_card[card]" value="0" />
			        <label>Card Type: <input type="text" name="twitter_card[card]" value="'. $options['card'] . '" /> 
			        </label><br /><br />';
			        
				?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
			</form>
		</div>
    	<?php
	}

	/**
	 * Get options and/or set defaults
	 *
	 * @since 1.0.6
	 * @return array with options
	 */
	public static function get_admin_options(){
		$option = get_option('twitter_card');
		
		if(!is_array($option)) {
			$option = array();
		} 

		$option_default = array();
		$option_default['card'] = 'summary';
		$option_default['site'] = null;
		$option_default['site:id'] = null;
		$option_default['creator'] = '@twitter';
		$option_default['creator:id'] = null;

		$option = array_merge($option_default, $option);

		return $option;
	}

}
add_action( 'wp', 'Twitter_Cards::init' );
add_action( 'admin_init', 'Twitter_Cards::admin_init' );
add_action( 'admin_menu', 'Twitter_Cards::admin_menu' );
endif;
?>
