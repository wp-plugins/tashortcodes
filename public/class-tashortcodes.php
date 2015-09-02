<?php
/**
 * Plugin Name.
 *
 * @package   TAShortcodes
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-tashortcodes-admin.php`
 *
 *
 * @package TAShortcodes
 * @author  Your Name <email@example.com>
 */
class TAShortcodes {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ta-shortcodes';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        /* Define custom functionality.
         * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         *
         * add_action ( 'hook_name', 'your_function_name', [priority], [accepted_args] );
         *
         * add_filter ( 'hook_name', 'your_filter', [priority], [accepted_args] );
         */

        add_action( 'sync_hourly_event', array( $this, 'remote_sync' ) );
        add_action( 'wp' , array( $this, 'active_remote_sync'));

        /* Create shortcode "bookie"
         *
         * [bookie casa="value"]your text here[/bookie] => <a href="http://url_for_casa_value">your text here</a>
         */
        add_shortcode('bookie', array($this, 'create_link_handler'));

        /* Create shortcode "promo"
         *
         * [promo casa="value"]your text here[/promo] => <a href="http://url_for_casa_value">your text here</a>
         */
        add_shortcode('promo', array($this, 'create_link_handler'));

        /* Create shortcode "pinextra"
         *
         * [pinextra]your text here[/pinextra] => <a href="http://your-server/pin-gratis-paysafecard">your text here</a>
         * [pinextra id="ID_NUMBER"]your text here[/pinextra] => <a href="http://url_of_page_with_that_page_ID">your text here</a>
         * [pinextra slug="PAGE_SLUG"]your text here[/pinextra] => <a href="http://url_of_page_with_that_SLUG">your text here</a>
         * [pinextra text="your text here"] => <a href="http://your-server/pin-gratis-paysafecard">your text here</a>
         */
        add_shortcode('pinextra', array($this, 'create_pinextra_handler'));

        // allow shortcodes in widgets
        add_filter('widget_text', 'do_shortcode');
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		add_option('tashorcode_site_links', null);
        self::get_instance()->remote_sync();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		delete_option('tashorcode_site_links');

        remove_shortcode('bookie');
        remove_shortcode('promo');
        remove_shortcode('pinextra');
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

    public function active_remote_sync() {
        if ( !wp_next_scheduled( 'sync_hourly_event' ) ) {
            wp_schedule_event(time(), 'hourly', 'sync_hourly_event');
        }
    }

    public function remote_sync($d = null)
    {
        //TODO: refactorizar a solucion que obtiene informacion a traves de la api de servicios REST
        $url_sync_link = 'http://www.todoapuestas.org/listBlogsLinksJson.php';

        $domain = $d;
        if(is_null($d))
            $domain = $_SERVER["HTTP_HOST"];

        $url = $url_sync_link."?domain=". $domain;
        $list_site_links = trim(file_get_contents($url));
        $list_site_links = json_decode($list_site_links, true);

        if(is_null($d) && !empty($list_site_links)){
            update_option('tashorcode_site_links', $list_site_links);
        }

        if(!is_null($d))
            return $list_site_links;
    }

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

    public function create_link_handler($atts, $content = null)
    {
        $blog = shortcode_atts( array('casa' => false, 'domain' => $_SERVER["HTTP_HOST"]), $atts ) ;

        if(isset($blog['casa'])){
            $casa = $blog['casa'];
            $domain = $blog['domain'];

            $list_site_links = get_option('tashorcode_site_links',false);
            if(strcmp($domain, $_SERVER["HTTP_HOST"]) != 0){
                $list_site_links = $this->remote_sync($domain);
            }

            if(array_key_exists($casa, $list_site_links)){
                $link = $list_site_links[''.$casa.'']['url'];
                ob_start() ?>
                <a href="<?php echo esc_url($link);?>" rel="nofollow" target="_blank"><?php echo do_shortcode($content); ?></a><?php
                return ob_get_clean();
            }
        }
    }

    public function create_pinextra_handler($atts, $content = null)
    {
        $page = shortcode_atts( array('id' => false, 'slug' => 'pin-gratis-paysafecard', 'text' => false), $atts ) ;
        $page_id = $page['id'];
        $page_slug = $page['slug'];
        $link_text = $page['text'];

        $query = null;
        if(!is_null($content)){
            $query = 'pagename='.$page_slug;
            if(intval($page_id) != false)
                $query = 'page_id='.$page_id;

        }elseif(isset($link_text) && strcmp($page_slug, 'pin-gratis-paysafecard') == 0){
            $query = 'pagename='.$page_slug;
            $content = $link_text;
        }

        $result = new WP_Query( $query );
        if($result->have_posts()){
            while($result->have_posts()){
                $result->the_post();
                $p = get_post();
                ob_start(); ?>
                <a href="<?php echo esc_url(get_the_permalink($p->ID));?>"><?php echo do_shortcode($content); ?></a><?php
            }
            return ob_get_clean();
        }
    }
}
