<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.nicolabavaro.it
 * @since      1.0.0
 *
 * @package    Ultimate_File_Sharing
 * @subpackage Ultimate_File_Sharing/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ultimate_File_Sharing
 * @subpackage Ultimate_File_Sharing/includes
 * @author     Nicola Bavaro <nicola.bavaro@gmail.com>
 */
class Ultimate_File_Sharing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ultimate_File_Sharing_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $UFS_Metabox;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $Helper;

    protected $ufs_UpdateChecker;

    /**
     *
     */
    protected $ufs_plugin_dir;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ultimate-file-sharing';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ultimate_File_Sharing_Loader. Orchestrates the hooks of the plugin.
	 * - Ultimate_File_Sharing_i18n. Defines internationalization functionality.
	 * - Ultimate_File_Sharing_Admin. Defines all hooks for the admin area.
	 * - Ultimate_File_Sharing_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

	    $this->ufs_plugin_dir = plugin_dir_path( dirname( __FILE__ ) );

	    /**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ultimate-file-sharing-loader.php';

        /**
         *
         */
        require_once plugin_dir_path( dirname( __FILE__) ) .'includes/class-ultimate-file-sharing-helpers.php';

        /**
         * La classe che inizializza Redux
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ultimate-file-sharing-redux-init.php';

        new Ultimate_File_Sharing_Redux($this->ufs_plugin_dir);

        /**
         *
         */
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/plugin-update-checker/plugin-update-checker.php';

        //$this->ufs_UpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://www.informaticabattistini.it/update-api/ufs-filesharing.json', __FILE__,'ultimate-file-sharing');
        /**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ultimate-file-sharing-i18n.php';

        /**
         * Includo TGMPA
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/tgmpa/class-tgm-plugin-activation.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ultimate-file-sharing-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ultimate-file-sharing-public.php';

        /**
         * Register Download Post Type
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ultimate-file-sharing-download-pt.php';

		$this->loader = new Ultimate_File_Sharing_Loader();

		// Boilerplate is loaded here we go!

        // Load Metabox.io
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/metabox/meta-box.php';

        // Create Download Post type and Category taxonomy
        new UFS_Download_PostType();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ultimate_File_Sharing_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ultimate_File_Sharing_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ultimate_File_Sharing_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Registro TGMPA
        $this->loader->add_action('tgmpa_register',$plugin_admin,'ufs_register_required_plugins');

        // Genero l'hash durante il salvataggio del download
        $this->loader->add_action('save_post_ufs_download',$plugin_admin,'ufs_generate_hash_download_save');

        // Registro i metabox
		$this->loader->add_filter( 'rwmb_meta_boxes',$plugin_admin,'ufs_register_meta_boxes' );

		// Registro i mime types supportati
		$this->loader->add_filter( 'upload_mimes',$plugin_admin, 'ufs_register_mime_types' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ultimate_File_Sharing_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// URL REWRITE PER DOWNLOADS
		$this->loader->add_action( 'init', $plugin_public, 'userpage_rewrite_rule');
        $this->loader->add_action( 'template_redirect', $plugin_public, 'userpage_rewrite_catch');
        $this->loader->add_action( 'init', $plugin_public, 'memberpage_rewrite');
        $this->loader->add_filter( 'query_vars', $plugin_public, 'userpage_rewrite_add_var');

        $this->loader->add_filter( 'the_title', $plugin_public, 'ufs_change_page_title');

        $this->loader->add_filter( 'template_include',$plugin_public, 'ufs_download_page_template' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ultimate_File_Sharing_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
