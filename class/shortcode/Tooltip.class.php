<?php

/**
 * @file Class file for HTML tooltips shortcode
 *
 * @author Gerkin
 * @copyright 2016 GerkinDevelopment
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
 * @package ithoughts-tooltip-glossary
 *
 * @version 2.5.0
 */


/**
  * @copyright 2015-2016 iThoughts Informatique
  * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
  */

namespace ithoughts\tooltip_glossary\shortcode;

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}


if(!class_exists(__NAMESPACE__."\\Tooltip")){
	class Tooltip extends \ithoughts\v1_0\Singleton{
		public function __construct() {
			// Shortcode
			add_shortcode( "ithoughts_tooltip_glossary-tooltip", array(&$this, "tooltip_shortcode") );
			add_shortcode( "tooltip", array(&$this, "tooltip_shortcode") );

			// Help functions..
			add_action( 'wp_insert_post_data',  array(&$this, 'parse_pseudo_links_to_shortcode'));
			add_action( 'edit_post',  array(&$this, 'convert_shortcodes'));
		}

		public function parse_pseudo_links_to_shortcode( $data ){
			$data['post_content'] = preg_replace('/<a\s+?data-tooltip-content=\\\\"(.+?)\\\\".*>(.*?)<\/a>/', '[ithoughts_tooltip_glossary-tooltip content="$1"]$2[/ithoughts_tooltip_glossary-tooltip]', $data['post_content']);
			return $data;
		}

		public function convert_shortcodes($post_id){
			$post = get_post($post_id);
			$post->post_content = preg_replace('/\[ithoughts_tooltip_glossary-tooltip(.*?)(?: content="(.+?)")(.*?)\](.+?)\[\/ithoughts_tooltip_glossary-tooltip\]/', '<a data-tooltip-content="$2" $1 $3>$4</a>', $post->post_content);
			return $post;
		}

		/** */
		public function tooltip_shortcode( $atts, $text='' ){
			$datas = apply_filters("ithoughts_tt_gl-split-args", $atts);

			$content = (isset($datas["handled"]["tooltip-content"]) && $datas["handled"]["tooltip-content"]) ? $datas["handled"]["tooltip-content"] : "";

			// Set text to default to content. This allows syntax like: [glossary]Cheddar[/glossary]
			if( empty($content) ) $content = $text;

			$backbone = \ithoughts\tooltip_glossary\Backbone::get_instance();
			$backbone->add_script('qtip');

			// qtip jquery data

			if(!(isset($datas["linkAttrs"]["href"]) && $datas["linkAttrs"]["href"]))
				$datas["linkAttrs"]["href"] = 'javascript:void(0);';
			if(!(isset($datas["linkAttrs"]["title"]) && $datas["linkAttrs"]["title"]))
				$datas["linkAttrs"]["title"] = esc_attr($text);

			$linkArgs = \ithoughts\v1_2\Toolbox::concat_attrs( $datas["linkAttrs"]);
			$link   = '<a '.$linkArgs.'>' . $text . '</a>';
			// Span that qtip finds
			$datas["attributes"]["class"] = "ithoughts_tooltip_glossary-tooltip".((isset($datas["attributes"]["class"]) && $datas["attributes"]["class"]) ? " ".$datas["attributes"]["class"] : "");
			$args = \ithoughts\v1_2\Toolbox::concat_attrs( $datas["attributes"]);
			$span = '<span '.$args.' data-tooltip-content="'.do_shortcode($content).'">' . $link . '</span>';

			return $span;
		}
	}
}