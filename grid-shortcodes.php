<?php
/*
	Plugin Name: Grid Shortcodes
	Version: 1.2.2
	Description: Adds a collection of shortcodes for easy implementation of the responsive Bootstrap Grid!
	Author: Evan Mattson
	Author URI: http://pagelines.evanmattson.com
	Plugin URI: http://pagelines.evanmattson.com/plugins/grid-shortcodes
	Demo: http://pagelines.evanmattson.com/plugins/grid-shortcodes/demo
	PageLines: true
	V3: true
*/

class GridShortcodes {

	function __construct() {

		$this->add_shortcodes();

		add_filter( 'the_content', array(&$this, 'do_grid_shortcodes'), 7 );
	}


	function do_grid_shortcodes( $content ) {

		global $shortcode_tags;

		// backup
		$_shortcode_tags = $shortcode_tags;

		// clear
		remove_all_shortcodes();

		// add
		$this->add_shortcodes();

		// do
		$content = do_shortcode( $content );

		// restore
		$shortcode_tags = $_shortcode_tags;

		return $content;
	}

	private function add_shortcodes() {

		$tags = array(
			'row',
			'span1',
			'span2',
			'span3',
			'span4',
			'span5',
			'span6',
			'span7',
			'span8',
			'span9',
			'span10',
			'span11',
			'span12'
		);

		// now we're going to add a LOT of shortcodes (13*(26+1))... 351
		foreach ( $tags as $tag ) {
			add_shortcode( $tag, array(&$this, 'grid_shortcodes') );
			foreach ( $this->get_alphabet_array() as $x )
				add_shortcode( "{$tag}_$x", array(&$this, 'grid_shortcodes') );
		}
	}

	/**
	 * Master callback for all grid shortcodes
	 */
	function grid_shortcodes( $atts, $content, $tag ) {

		extract( shortcode_atts($this->default_atts(), $atts) );

		$grid_class = $this->get_grid_class( $tag );

		$content = trim( $content );

		// grid css targets spanX with > selector
		if ( 'row' != $grid_class )
			$content = $this->maybe_wrap_content( $atts, $content, $tag );

		return sprintf('<div %s class="%s%s">%s</div>',
			$id ? "id='$id'" : '',
			$grid_class,
			$class ? " $class" : '',
			do_shortcode( $content )
		);
	}

	function get_grid_class( $tag ) {
		if ( false !== strpos($tag, '_') ) {
			$_tag = explode('_', $tag);
			return $_tag[0];
		}
		else
			return $tag;
	}

	function maybe_wrap_content( $atts, $content, $tag ) {

		// prepending here so it is inside the pad if it will be wrapped
		$content = '<p style="display:none;"></p>'.$content; // wpautop fix

		if ( $this->to_wrap_or_not_to_wrap( $atts ) ) {

			// if the pad class is set use it, otherwise give it a default
			// pad="" will give the wrapping div an empty class
			return sprintf('<div class="%s">%s</div>',
				isset( $atts['pad'] ) 
					? esc_attr( $atts['pad'] )
					: sprintf('span-pad %s-pad', $this->get_grid_class( $tag ) ),
				$content
			);
		}
		else
			return $content;
	}

	function to_wrap_or_not_to_wrap( $atts ) {

		if ( ! is_array($atts) )
			return false;

		if ( isset( $atts['pad'] ) )
			return true;

		// check to see if it was used without an attribute: value-only
		foreach ( $atts as $key => $value )
			if ( is_int( $key ) && 'pad' == $value )
				return true;

		return false;
	}

	/**
	 * Returns array of default attributes for grid shortcodes
	 * @return array defaults
	 */
	function default_atts() {

		return array(
			'id'    => '',
			'class' => '',
		);
	}

	function get_alphabet_array() {
		$alpha = 'a-b-c-d-e-f-g-h-i-j-k-l-m-n-o-p-q-r-s-t-u-v-w-x-y-z';
		return explode('-', $alpha);
	}
} // GridShortcodes

new GridShortcodes;