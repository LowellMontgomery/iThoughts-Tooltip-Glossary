<?php

/**
 * @file Class files registering & containing comon filters
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

namespace ithoughts\tooltip_glossary;

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if(!class_exists(__NAMESPACE__."\\Filters")){
class Filters extends \ithoughts\v1_0\Singleton{
	public function __construct(){
		add_filter("ithoughts_tt_gl-term-excerpt", array(&$this, "getTermExcerpt"));
		add_filter("ithoughts-split-args", array(&$this, "splitArgs"), 10, 5);
		add_filter("ithoughts-join-args", array(&$this, "joinArgs"), 10, 1);
		add_filter("ithoughts_tt_gl-split-args", array(&$this, "ithoughts_tt_gl_splitArgs"), 10, 1);
	}

	public function getTermExcerpt(\WP_Post $term){
		if( strlen($term->post_excerpt) > 0 ){
			$content = wpautop( $term->post_excerpt );
		} else {
			$content = wp_trim_words($term->post_content, 25, '...');
		}
		return $content;
	}

	/**
	 * Return array(
	 * "handled"				=> array(),
	 * "attributes"			=> array(),
	 * "overridesServer"		=> array(),
	 * "overridesClient"		=> array()
	 * ). Put all given attributes in the appropriated category, and with prefix they need
	 * @author Gerkin
	 * @param  string[]   $attributes                           Attributes to dispatch in categories
	 * @param  string[]   [$handled = array()]                  Attributes name/regex to store into "handled" subcategory
	 * @param  string[]   [$overridableOptionsServer = array()] Attributes name/regex to store into "overridesServer" subcategory
	 * @param  string[]   [$overridableOptionsClient = array()] Attributes name/regex to store into "overridesClient" subcategory
	 * @param  boolean    $fuseClientSideWithArgs = true        If set to true, category overridesClient & Attributes will be merged
	 * @return string[][]                                       Sorted attributes
	 */
	public function splitArgs($attributes, array $handled = array(), array $overridableOptionsServer = array(), array $overridableOptionsClient = array(), $fuseClientSideWithArgs = true){
		$attrs = array(
			'abbr','accept-charset','accept','accesskey','action','align','alt','archive','axis',
			'border',
			'cellpadding','cellspacing','char','charoff','charset','checked','cite','class','classid','codebase','codetype','cols','colspan','content','coords',
			'data','datetime','declare','defer','dir','disabled',
			'enctype',
			'for','frame','frameborder',
			'headers','height','href','hreflang','http-equiv',
			'id','ismap',
			'label','lang','longdesc',
			'marginheight','marginwidth','maxlength','media','method','multiple',
			'name','nohref','noresize',
			'onblur','onchange','onclick','ondblclick','onfocus','onkeydown','onkeypress','onkeyup','onload','onmousedown','onmousemove','onmouseout','onmouseover','onmouseup','onreset','onselect','onsubmit','onunload', 
			'profile',
			'readonly','rel','rev','rows','rowspan','rules',
			'scheme','scope','scrolling','selected','shape','size','span','src','standby','style','summary',
			'tabindex','target','title','type',
			'usemap',
			'valign','value','valuetype',
			'width',
			"/^aria-/",
			"/^data-/"
		);
		$attsLength = count($attrs);
		$res = array(
			"handled"				=> array(),
			"attributes"			=> array(),
			"overridesServer"		=> array(),
			"overridesClient"		=> array()
		);
		if(is_array($attributes)){
			foreach($attributes as $key => $value){
				// $attributes is a single-level array containing key-value HTML tag potential attributes with $key and $value
				if(array_search($key, $handled) !== false){ // This is a know `handled` attribute
					$res["handled"][$key] = $value;
				} else if(array_search($key, $overridableOptionsServer) !== false){ // This is a know `overridable from server` attribute
					$res["overridesServer"][$key] = $value;
				} else if(array_search($key, $overridableOptionsClient) !== false){ // This is a know `overridable from client` attribute
					$res["overridesClient"][$key] = $value;
				} else { // It does not belongs to any special categories
					$i = -1;
					$match = false;
					while(++$i < $attsLength && !$match){ // Loop through known HTML attributes
						$attr = $attrs[$i];
						if(strlen($attr) > 1 && $attr[0] == "/" && $attr[count($attr) - 1] == "/"){ // If this tested HTML attribute is a regex
							if(preg_match($attrs[$i], $key)){ // If our `$key` match with the test attribute
								$res["attributes"][$key] = $value; // Add it without any prefix
								$match = true;
							}
						} else { // Else, this is a string
							if($key === $attrs[$i]){ // If our `$key` is the same as tested attribute
								$res["attributes"][$key] = $value; // Add it without any prefix
								$match = true;
							}
						}
					}
					if(!$match){ // This attribute does not match any known HTML standard attributes
						$res["attributes"]["data-".$key] = $value; // Assign it prefixed
					}
				}
			}
		}
		$ret;
		if($fuseClientSideWithArgs){
			$ret = array(
				"handled" => $res["handled"],
				"attributes" => array_merge($res["attributes"], $res["overridesClient"]),
				"overridesServer" => $res["overridesServer"],
			);
		} else {
			$ret = $res;
		}
		return $ret;
	}

	public function ithoughts_tt_gl_splitArgs($atts){
		$ret = array();

		$backbone = \ithoughts\tooltip_glossary\Backbone::get_instance();
		$datas = apply_filters("ithoughts-split-args", $atts, $backbone->get_handled_attributes(), $backbone->get_server_side_overridable(), $backbone->get_client_side_overridable(), false);

		$ret["options"] = apply_filters("ithoughts_tt_gl_get_overriden_opts", $datas["overridesServer"], false);

		$linkAttrs = array();
		foreach($datas["attributes"] as $key => $value){//Extract /^link-/ datas
			if(strpos($key, "data-link-") === 0){
				$linkAttrs[substr($key, 10)] = $value;
				unset($datas["attributes"][$key]);
			}
		}
		$ret["linkAttrs"] = apply_filters("ithoughts-split-args", $linkAttrs);
		$ret["linkAttrs"] = $ret["linkAttrs"]["attributes"];

		$overridesClient = apply_filters("ithoughts_tt_gl_get_overriden_opts", $datas["overridesClient"], true);
		$overridesDataPrefixed = array();
		foreach($overridesClient as $override => $value){
			$overridesDataPrefixed["data-".$override] = $value;
		};
		$ret["attributes"] = array_merge($datas["attributes"], $overridesDataPrefixed);

		$ret["handled"] = $datas["handled"];

		if(isset($ret['attributes']["href"]) && !isset($ret['linkAttrs']["href"])){
			$ret['linkAttrs']["href"] = $ret['attributes']["href"];
			unset($ret['attributes']["href"]);
		}

		/*/
		echo "Serverside: <pre>";
		var_dump(parent::$serversideOverridable);
		echo "</pre>";

		echo "Clientside: <pre>";
		var_dump(parent::$clientsideOverridable);
		echo "</pre>";

		echo "Options: <pre>";
		var_dump(parent::$options);
		echo "</pre>";

		echo "Datas: <pre>";
		var_dump($datas);
		echo "</pre>";

		echo "Overrides server: <pre>";
		var_dump($ret["options"]);
		echo "</pre>";

		echo "Overrides client; <pre>";
		var_dump($overridesClient);
		echo "</pre>";

		//
		echo "Returned: <pre>";
		var_dump($ret);
		echo "</pre>";
		/**/

		return $ret;
	}
}
}