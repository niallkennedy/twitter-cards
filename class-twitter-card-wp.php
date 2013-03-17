<?php

if ( ! class_exists( 'Twitter_Card' ) )
	require_once( dirname(__FILE__) . '/includes/twitter-cards-php/twitter-card.php' );

/**
 * Extend the Twitter Card PHP class with WordPress-specific functionality and site settings.
 * Pass attribute values through esc_attr()
 * Pass URLs through esc_url_raw()
 */
class Twitter_Card_WP extends Twitter_Card {

	/**
	 * Test if given URL is valid and matches allowed schemes
	 *
	 * @since 1.0
	 * @param string $url URL to test
	 * @param array $allowed_schemes one or both of http, https
	 * @return bool true if URL can be parsed and scheme allowed, else false
	 */
	public static function is_valid_url( $url, $allowed_schemes = null ) {
		if ( parent::is_valid_url( $url, $allowed_schemes ) && esc_url_raw( $url, $allowed_schemes ) )
			return true;
		return false;
	}

	/**
	 * Build a single <meta> element from a name and value
	 *
	 * @param string $name name attribute value
	 * @param string|int $value value attribute value
	 * @param bool $xml include a trailing slash for XML. encode attributes for XHTML in PHP 5.4+
	 * @return meta element or empty string if name or value not valid
	 */
	public static function build_meta_element( $name, $value, $xml = false ) {
		if ( ! ( is_string( $name ) && $name && ( is_string( $value ) || ( is_int( $value ) && $value > 0 ) ) ) )
			return '';
		return '<meta name="' . esc_attr( self::PREFIX . ':' . $name ) . '" content="' . esc_attr( $value ) . '"' . ( $xml === true ? ' />' : '>' ) . "\n";
	}

	/**
	 * Pass all URLs through esc_url_raw. Unset the property if URL rejected
	 *
	 * @since 1.0
	 * @uses esc_url_raw()
	 */
	private function filter_urls() {
		if ( isset( $this->url ) ) {
			$this->url = esc_url_raw( $this->url );
			if ( ! $this->url )
				unset( $this->url );
		}

		if ( isset( $this->image ) && isset( $this->image->url ) ) {
			$this->image->url = esc_url_raw( $this->image->url );
			if ( ! $this->image->url )
				unset( $this->image );
		}

		if ( isset( $this->video ) && isset( $this->video->url ) ) {
			$this->video->url = esc_url_raw( $this->video->url );
			if ( $this->video->url ) {
				if ( isset( $this->video->stream ) && isset( $this->video->stream->url ) )
					$this->video->stream->url = esc_url_raw( $this->video->stream->url );
					if ( ! $this->video->stream->url )
						unset( $this->video->stream );
			} else {
				unset( $this->video );
			}
		}
	}

	/**
	 * Build a string of <meta> elements representing the object
	 * Pass URLs through esc_url_raw to preserve site preferences
	 *
	 * @since 1.0
	 * @param string $style markup style. "xml" adds a trailing slash to the meta void element
	 * @return string <meta> elements or empty string if minimum requirements not met
	 */
	private function generate_markup( $style = 'html' ) {
		$xml = false;
		if ( $style === 'xml' )
			$xml = true;
		$this->filter_urls();
		$t = apply_filters( 'twitter_cards_properties', $this->toArray() );
		if ( ! is_array( $t ) || empty( $t ) )
			return '';
		$s = '';
		foreach ( $t as $name => $value ) {
			$s .= self::build_meta_element( $name, $value, $xml );
		}
		return $s;
	}

	/**
	 * Output object properties as HTML meta elements with name and value attributes
	 *
	 * @return string HTML <meta> elements or empty string if minimum requirements not met for card type
	 */
	public function asHTML() {
		return $this->generate_markup();
	}

	/**
	 * Output object properties as XML meta elements with name and value attributes
	 *
	 * @since 1.0
	 * @return string XML <meta> elements or empty string if minimum requirements not met for card type
	 */
	public function asXML() {
		return $this->generate_markup( 'xml' );
	}
}
?>
