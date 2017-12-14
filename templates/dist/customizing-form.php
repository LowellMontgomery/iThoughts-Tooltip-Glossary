<?php
/**
 * This file is used to edit custom styles
 *
 * @file Template file for the style editor
 *
 * @author Gerkin
 * @copyright 2016 GerkinDevelopment
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package ithoughts-tooltip-glossary
 *
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	status_header( 403 );
	wp_die( 'Forbidden' );// Exit if accessed directly.
}


$url;
switch ( substr( get_locale(), 0, 2 ) ) {
	case 'fr':
		$url = 'https://www.gerkindevelopment.net/portfolio/ithoughts-tooltip-glossary/';
		break;

	default:
		$url = 'https://www.gerkindevelopment.net/en/portfolio/ithoughts-tooltip-glossary/';
		break;
}
?> <div class="wrap"><div id="ithoughts-tooltip-glossary-options" class="meta-box meta-box-50 metabox-holder"><div class="meta-box-inside admin-help"><div class="icon32" id="icon-options-general"><br></div><h2><?php esc_html_e( 'Theme editor', 'ithoughts-tooltip-glossary' ); ?></h2><div id="dashboard-widgets-wrap"><div class="dashboard-widgets"><p style="font-size:17px"><em><?php
						printf(
							wp_kses(
								/* translators: %s is the url to the documentation in current language (if available) */
								__(
									'Need help? Check out the full plugin manual at <a href="%s">GerkinDevelopment.net</a>.', 'ithoughts-tooltip-glossary'
								),
								array(
									'a' => array(
										'href' => array(),
									),
								)
							),
							esc_url( $url )
						); ?></em></p><div style="display:flex;flex-direction:row;flex-wrap:wrap"><div id="normal-sortables" class="" style="flex:1 1 auto"><div class="postbox"><h3 class="hndle"><span><?php esc_html_e( 'Load a theme', 'ithoughts-tooltip-glossary' ); ?></span></h3><div class="inside"><form id="ithoughts_loadtheme" method="get"><input type="hidden" name="page" value="ithoughts-tooltip-glossary-themes"> <?php wp_nonce_field( 'ithoughts_tt_gl-loadtheme' ); ?> <label for="theme_select"><?php esc_html_e( 'Theme to load', 'ithoughts-tooltip-glossary' ); ?></label> <?php
										echo wp_kses($inputs['theme_select'], array(
											'select' => array(
												'name' => true,
												'id' => true,
												'autocomplete' => true,
											),
											'option' => array(
												'title' => true,
												'value' => true,
												'selected' => true,
												'disabled' => true,
											),
										)); ?> <button type="submit" name="action" class="button button-primary" value="load"><?php esc_html_e( 'Load', 'ithoughts-tooltip-glossary' ); ?></button> <button type="submit" class="button button-secondary" name="action" value="delete" onclick="var themename=gei('themename');return ((themename&&themename.value&&(themename=themename.value))?confirm('<?php
														 /* translators: %s is the url to the documentation in current language (if available) */
														 esc_attr_e( 'Are you sure you want to delete the theme %s?', 'ithoughts-tooltip-glossary' ); ?>'.replace('%s', themename)):false);"><?php esc_html_e( 'Delete', 'ithoughts-tooltip-glossary' ); ?></button></form></div></div><form method="get"><input type="hidden" name="page" value="ithoughts-tooltip-glossary-themes"> <?php wp_nonce_field( 'ithoughts_tt_gl-recompile_themes' ); ?> <button type="submit" name="action" class="button button-secondary floatright" value="recompile" style="width:100%;margin:0 auto 25px;padding: 25px;line-height: 0"><?php esc_html_e( 'Recompile all stylesheets', 'ithoughts-tooltip-glossary' ); ?></button></form><div class="postbox" id="ithoughts-tt-gl-lesseditor"><h3 class="hndle"><span><?php esc_html_e( 'LESS editor', 'ithoughts-tooltip-glossary' ); ?></span></h3><div class="inside"><form id="LESS-form" class="less-form simpleajaxform" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" data-target="update-response"><input type="hidden" name="action" id="action"> <?php wp_nonce_field( 'ithoughts_tt_gl-theme_editor' ); ?> <?php
										echo wp_kses($inputs['splittedHead'], array(
											'input' => array(
												'id' => true,
												'name' => true,
												'type' => true,
												'value' => true,
												'autocomplete' => true,
											),
										));
										echo wp_kses($inputs['file'], array(
											'input' => array(
												'id' => true,
												'name' => true,
												'type' => true,
												'value' => true,
												'autocomplete' => true,
											),
										)); ?> <table class="form-table stripped"><tr><th><?php esc_html_e( 'Theme name', 'ithoughts-tooltip-glossary' ); ?></th><td><?php
													echo wp_kses($inputs['themename'], array(
														'input' => array(
															'id' => true,
															'name' => true,
															'type' => true,
															'value' => true,
															'autocomplete' => true,
														),
													)); ?></td></tr><tr><th><?php esc_html_e( 'Theme content', 'ithoughts-tooltip-glossary' ); ?></th><td><?php
													echo wp_kses($inputs['content'], array(
														'textarea' => array(
															'id' => true,
															'name' => true,
															'disabled' => true,
															'class' => true,
															'data-lang' => true,
														),
													)); ?></td></tr><tr><td colspan="2"><div><button name="actionB" value="ithoughts_tt_gl_theme_save" id="compilecss" class="alignleft button button-primary" style="display:inline-block;width:50%;text-align:center"><?php esc_html_e( 'Save theme', 'ithoughts-tooltip-glossary' ); ?></button> <button name="actionB" value="ithoughts_tt_gl_theme_preview" id="previewcss" class="alignleft button" style="display:inline-block;width:50%;text-align:center"><?php esc_html_e( 'Preview', 'ithoughts-tooltip-glossary' ); ?></button></div></td></tr><tr><td colspan="2"><div id="update-response" class="clear confweb-update"></div></td></tr></table></form></div></div></div><div style="flex:1 1 auto;position:relative"><div id="floater" style="display:flex;flex-direction:row;width:100%"><p style="flex:1 1 auto;text-align:center"><span class="itg-tooltip" data-tip-autoshow="true" data-qtipstyle="<?php echo esc_attr( $themename ); ?>" data-tip-id="exampleStyle" data-tip-nosolo="true" data-tip-nohide="true" data-tooltip-content="<?php esc_attr_e( 'This is an example tooltip, with content such as <a>a tag for link</a>, <em>em tag for emphasis</em>, <b>b tag for bold</b> and <i>i tag for italic</i>', 'ithoughts-tooltip-glossary' ); ?>"><a href="javascript:void(0)" title=""><?php esc_html_e( 'Example Tooltip', 'ithoughts-tooltip-glossary' ); ?></a></span></p></div></div></div></div></div></div></div></div>