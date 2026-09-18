<?php
/**
 * Drop-in Name: Accordion
 * Description: Organize taxonomy posts into a nested accordion navigation widget.
 * Author: Alex, Kolakube
 * Author URI: https://marketersdelight.com/
 * Drop-in URI: https://marketersdelight.com/dropins/accordion/
 * Drop-in Slug: accordion
 * Text Domain: md-accordion
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 */

class md_accordion extends md_api {

	public $slug = 'accordion';

	/**
	 * Add Accordion styles to the MD stylesheet compiler.
	 *
	 * @since 1.0.0
	 */

	public function css( $templates ) {
		$templates[$this->slug] = md_css( 'dropins', "{$this->slug}/css", true );

		return $templates;
	}

	/**
	 * Register the Accordion Navigation widget.
	 *
	 * @since 1.0.0
	 */

	public function widgets() {
		require_once MD_INSTALLED_DROPINS . "/{$this->slug}/widget.php";

		register_widget( 'md_accordion_widget' );
	}

}

new md_accordion;
