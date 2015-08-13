<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   TAShortcodes
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <p>A set of useful shortcodes for todoapuestar.org sites's.</p>
	<h3>Bookie <small>- Create shortcode "bookie"</small></h3>
    <ul>
        <li>The shortcode <code>[bookie casa="value"]your text here[/bookie]</code> will generate the following html <code><?php echo esc_html('<a href="http://url_for_casa_value" rel="nofollow" target="_blank">your text here</a>') ?></code></li>
    </ul>

    <h3>Promo <small>- Create shortcode "promo"</small></h3>
    <ul>
        <li>The shortcode <code>[promo casa="value"]your text here[/promo]</code> will generate the following html <code><?php echo esc_html('<a href="http://url_for_casa_value" rel="nofollow" target="_blank">your text here</a>') ?></code></li>
    </ul>

    <h3>Pinextra <small>- Create shortcode "pinextra"</small></h3>
    <ul>
        <li>The shortcode <code>[pinextra]your text here[/pinextra]</code> will generate the following html <code><?php echo esc_html('<a href="http://your-server/pin-gratis-paysafecard">your text here</a>') ?></code></li>
        <li>The shortcode <code>[pinextra id="ID_NUMBER"]your text here[/pinextra]</code> will generate the following html <code><?php echo esc_html('<a href="http://url_of_page_with_that_page_ID">your text here</a>') ?></code></li>
        <li>The shortcode <code>[pinextra slug="PAGE_SLUG"]your text here[/pinextra]</code> will generate the following html <code><?php echo esc_html('<a href="http://url_of_page_with_that_SLUG">your text here</a>') ?></code></li>
        <li>The shortcode <code>[pinextra text="your text here"]</code> will generate the following html <code><?php echo esc_html('<a href="http://your-server/pin-gratis-paysafecard">your text here</a>') ?></code></li>
    </ul>
</div>
