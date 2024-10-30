<?php
namespace letterpress;
if ( ! defined( 'ABSPATH' ) ) exit;

class HTMLSanitizer {
	public static function sanitize($input) {
		$jsEvent = [
			'onerror',
			'onmouseover',
			'onclick',
			'onkeydown',
			'onload',
			'onunload',
			'onfocus',
			'onblur',
			'onchange',
			'onsubmit',
			'location.',
			'window.',
			'document.',
		];

		$sanitizedInput = lp_strip_tags( $input, '<script>', true );
		$sanitizedInput = str_replace( 'javascript:', '', $sanitizedInput );
		$sanitizedInput = htmlspecialchars( $sanitizedInput );

		// Remove any <script> tags and attributes that can execute JavaScript
		$sanitizedInput = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $sanitizedInput);

		// Remove any on* attributes
		$sanitizedInput = preg_replace('/(on\w+)=("[^"]*"|\'[^\']*\'|[^ >]+)/i', '', $sanitizedInput);

		// Remove any js event attributes
		$sanitizedInput = str_replace($jsEvent, '', $sanitizedInput);

		return $sanitizedInput;
	}
}
