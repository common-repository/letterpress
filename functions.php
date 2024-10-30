<?php

use letterpress\HTMLSanitizer;

if ( ! function_exists( 'lp_sanitize_html' ) ) {

	/**
	 * Sanitize HTML string from user input
	 *
	 *
	 * @since LetterPress 1.0.1
	 *
	 * @param  null  $text
	 *
	 * @return mixed|string|void
	 */

	function lp_sanitize_html( $text = null ) {
		if ( ! $text ) {
			return '';
		}

		$sanitizedText = HTMLSanitizer::sanitize($text);

		return apply_filters( 'lp_sanitize_html', $sanitizedText );
	}
}

if ( ! function_exists( 'lp_decode_text' ) ) {

	/**
	 * Decode text which encoded before inserting into the database.
	 *
	 *
	 * Example usage:
	 *
	 *     lp_decode_text( $text ); //clean HTML text
	 *
	 *
	 * @since LetterPress 1.0.0
	 *
	 *
	 * @param  null  $text
	 *
	 * @return mixed|string|void
	 */

	function lp_decode_text( $text = null ) {

		if ( ! $text ) {
			return '';
		}

		$text = stripslashes( htmlspecialchars_decode( $text ) );

		return apply_filters( 'lp_decode_text', $text );
	}
}

if ( ! function_exists( 'lp_strip_tags' ) ) {

	/**
	 * Removes the HTML tags along with their contents
	 *
	 * Supports invert, that means you can define allowable ang removable tags.
	 *
	 * Example usage:
	 *
	 * Sample text:
	 * $text = '<b>sample</b> text with <div>tags</div>';
	 *
	 * Result for strip_tags($text):
	 * sample text with tags
	 *
	 * Result for lp_strip_tags( $text ):
	 * text with
	 *
	 * Result for lp_strip_tags( $text, '<b>' ):
	 * <b>sample</b> text with
	 *
	 * Result for lp_strip_tags( $text, '<b>', true );
	 * text with <div>tags</div>
	 *
	 *
	 *
	 * @since LetterPress 1.0.0
	 *
	 * @see lp_strip_tags();
	 *
	 * @param string $text HTML string
	 * @param  string  $tags tag name
	 * @param  false  $invert if allowable
	 *
	 * @return string|string[]|null
	 */

	function lp_strip_tags( $text, $tags = '', $invert = false ) {

		preg_match_all( '/<(.+?)[\s]*\/?[\s]*>/si', trim( $tags ), $tags );
		$tags = array_unique( $tags[1] );

		if ( is_array( $tags ) and count( $tags ) > 0 ) {
			if ( $invert == false ) {
				return preg_replace( '@<(?!(?:' . implode( '|', $tags ) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text );
			} else {
				return preg_replace( '@<(' . implode( '|', $tags ) . ')\b.*?>.*?</\1>@si', '', $text );
			}
		} elseif ( $invert == false ) {
			return preg_replace( '@<(\w+)\b.*?>.*?</\1>@si', '', $text );
		}

		return $text;
	}

}