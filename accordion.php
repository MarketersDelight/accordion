<?php
/**
 * Drop-in Name: Accordion
 * Description: Organize taxonomy posts into a nested accordion navigation widget.
 * Author: Alex, Kolakube
 * AuthorURI: https://marketersdelight.com/
 * DropinURI: https://marketersdelight.com/dropins/accordion/
 * Slug: accordion
 * Version: 1.0.0
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
