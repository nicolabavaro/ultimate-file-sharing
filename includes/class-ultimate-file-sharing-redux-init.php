<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.nicolabavaro.it
 * @since      1.0.0
 *
 * @package    Ultimate_File_Sharing
 * @subpackage Ultimate_File_Sharing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ultimate_File_Sharing
 * @subpackage Ultimate_File_Sharing/public
 * @author     Nicola Bavaro <nicola.bavaro@gmail.com>
 */

class Ultimate_File_Sharing_Redux{

    private $opt_name = "ufs_options";

    private $plugin_dir;

    public function __construct($ufs_plugin_dir){

        if ( ! class_exists( 'Redux' ) ) {
            return;
        }

        // This is the path of the plugin
        $this->plugin_dir = $ufs_plugin_dir;

        // This is your option name where all the Redux data is stored.
        $opt_name = $this->opt_name;

        /**
         * ---> SET ARGUMENTS
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         * */

        //$theme = wp_get_theme(); // For use with some settings. Not necessary.

        $args = array(
            // TYPICAL -> Change these values as you need/desire
            'opt_name'             => $opt_name,
            // This is where your data is stored in the database and also becomes your global variable name.
            'display_name'         => __( 'Ultimate File Sharing', 'ultimate-file-sharing' ),
            // Name that appears at the top of your panel
            'display_version'      => '1.0.0',
            // Version that appears at the top of your panel
            'menu_type'            => 'menu',
            //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
            'allow_sub_menu'       => true,
            // Show the sections below the admin menu item or not
            'menu_title'           => __( 'UFS Options', 'ultimate-file-sharing' ),
            'page_title'           => __( 'UFS Options', 'ultimate-file-sharing' ),
            // You will need to generate a Google API key to use this feature.
            // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
            'google_api_key'       => '',
            // Set it you want google fonts to update weekly. A google_api_key value is required.
            'google_update_weekly' => false,
            // Must be defined to add google fonts to the typography module
            'async_typography'     => true,
            // Use a asynchronous font on the front end or font string
            //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
            'admin_bar'            => false,
            // Show the panel pages on the admin bar
            'admin_bar_icon'       => 'dashicons-portfolio',
            // Choose an icon for the admin bar menu
            'admin_bar_priority'   => 50,
            // Choose an priority for the admin bar menu
            'global_variable'      => '',
            // Set a different name for your global variable other than the opt_name
            'dev_mode'             => false,
            // Show the time the page took to load, etc
            'update_notice'        => false,
            // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
            'customizer'           => false,
            // Enable basic customizer support
            //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
            //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

            // OPTIONAL -> Give you extra features
            'page_priority'        => null,
            // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
            'page_parent'          => 'themes.php',
            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
            'page_permissions'     => 'manage_options',
            // Permissions needed to access the options panel.
            'menu_icon'            => '',
            // Specify a custom URL to an icon
            'last_tab'             => '',
            // Force your panel to always open to a specific tab (by id)
            'page_icon'            => 'icon-themes',
            // Icon displayed in the admin panel next to your menu_title
            'page_slug'            => '_ufs_options',
            // Page slug used to denote the panel
            'save_defaults'        => true,
            // On load save the defaults to DB before user clicks save or not
            'default_show'         => false,
            // If true, shows the default value next to each field that is not the default value.
            'default_mark'         => '',
            // What to print by the field's title if the value shown is default. Suggested: *
            'show_import_export'   => true,
            // Shows the Import/Export panel when not used as a field.

            // CAREFUL -> These options are for advanced use only
            'transient_time'       => 60 * MINUTE_IN_SECONDS,
            'output'               => true,
            // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
            'output_tag'           => true,
            // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
            // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

            // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
            'database'             => '',
            // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!

            'use_cdn'              => true,
            // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

            //'compiler'             => true,

            // HINTS
            'hints'                => array(
                'icon'          => 'el el-question-sign',
                'icon_position' => 'right',
                'icon_color'    => 'lightgray',
                'icon_size'     => 'normal',
                'tip_style'     => array(
                    'color'   => 'light',
                    'shadow'  => true,
                    'rounded' => false,
                    'style'   => '',
                ),
                'tip_position'  => array(
                    'my' => 'top left',
                    'at' => 'bottom right',
                ),
                'tip_effect'    => array(
                    'show' => array(
                        'effect'   => 'slide',
                        'duration' => '500',
                        'event'    => 'mouseover',
                    ),
                    'hide' => array(
                        'effect'   => 'slide',
                        'duration' => '500',
                        'event'    => 'click mouseleave',
                    ),
                ),
            )
        );

        Redux::setArgs( $opt_name, $args );

        /*
         * ---> END ARGUMENTS
         */

        /*
         *
         * ---> START SECTIONS
         *
         */

        // -> START Basic Fields
        Redux::setSection( $opt_name, array(
            'title'  => __( 'General Settings', 'ultimate-file-sharing' ),
            'id'     => 'ufs-general-section',
            'desc'   => __( 'Basic field with no subsections.', 'ultimate-file-sharing' ),
            'icon'   => 'el el-cog',
            'fields' => array(
                array(
                    'id'       => 'ufs-select-download-page',
                    'type'     => 'select',
                    'data'     => 'pages',
                    'title'    => __( 'Pages Select Option', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'No validation can be done on this field type', 'ultimate-file-sharing' ),
                    'desc'     => __( 'This is the description field, again good for additional info.', 'ultimate-file-sharing' ),
                ),
                array(
                    'id'       => 'ufs-general-date-format',
                    'type'     => 'text',
                    'title'    => __( 'Date Format', 'ultimate-file-sharing' ),
                    'desc'     => __( 'Select your date format', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'ex. d/M/Y', 'ultimate-file-sharing' ),
                    'validate' => 'no_html'
                ),
                array(
                    'id'       => 'ufs-general-public-access',
                    'type'     => 'switch',
                    'title'    => __( 'Allow show to all registered user', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Allow the option to show a download to all registered users', 'ultimate-file-sharing' ),
                    'desc'     => __( 'Se vuoi rendere disponibili alcuni files ad utenti non registrati attiva questo selettore.', 'ultimate-file-sharing' ),
                ),
                array(
                    'id'       => 'ufs-general-public-custom-title',
                    'type'     => 'switch',
                    'title'    => __( 'Customize the title', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Customize the title of the download area page', 'ultimate-file-sharing' ),
                    'desc'     => __( 'if you want to customize the page title enable this function.', 'ultimate-file-sharing' ),
                ),
                array(
                    'id'       => 'ufs-general-admin-show-download-column',
                    'type'     => 'switch',
                    'title'    => __( 'Show Download Counter Column', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Customize the title of the download area page', 'ultimate-file-sharing' ),
                    'desc'     => __( 'if you want to customize the page title enable this function.', 'ultimate-file-sharing' ),
                ),
                array(
                    'id'       => 'ufs-general-admin-show-groups-column',
                    'type'     => 'switch',
                    'title'    => __( 'Show Groups Column', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Customize the title of the download area page', 'ultimate-file-sharing' ),
                    'desc'     => __( 'if you want to customize the page title enable this function.', 'ultimate-file-sharing' ),
                ),
                array(
                    'id'       => 'ufs-general-admin-show-users-column',
                    'type'     => 'switch',
                    'title'    => __( 'Show Users Column', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Customize the title of the download area page', 'ultimate-file-sharing' ),
                    'desc'     => __( 'if you want to customize the page title enable this function.', 'ultimate-file-sharing' ),
                ),
            )
        ) );
        Redux::setSection( $opt_name, array(
            'title'  => __( 'Style Settings', 'ultimate-file-sharing' ),
            'id'     => 'ufs-style-section',
            'desc'   => __( 'Basic field with no subsections.', 'ultimate-file-sharing' ),
            'icon'   => 'el el-brush',
            'fields' => array(
                array(
                    'id'       => 'custom-css-enable',
                    'type'     => 'switch',
                    'title'    => __( 'Enable Custom CSS', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'You can customize the views', 'ultimate-file-sharing' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'opt-ace-editor-css',
                    'type'     => 'ace_editor',
                    'title'    => __( 'CSS Code', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Paste your CSS code here.', 'ultimate-file-sharing' ),
                    'mode'     => 'css',
                    'theme'    => 'chrome',
                    'compiler' => array(''),
                    'desc'     => __('You can customize the css of the public views.','ultimate-file-sharing'),
                    'default'  => $this->ufs_get_css_default(),
                ),
            )
        )
        );

        Redux::setSection( $opt_name, array(
            'title'  => __( 'Performance', 'ultimate-file-sharing' ),
            'id'     => 'ufs-performance-section',
            'desc'   => __( 'Basic field with no subsections.', 'ultimate-file-sharing' ),
            'icon'   => 'el el-globe',
            'fields' => array(
                array(
                    'id'       => 'ufs-js-cdn-enable',
                    'type'     => 'switch',
                    'title'    => __( 'Enable JS CDN', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'If enabled it load the Datatable JS from the official CDN, otherwise local version will be loaded.', 'ultimate-file-sharing' ),
                    'default'  => false,
                ),
            )
        ));
        Redux::setSection( $opt_name, array(
            'title'  => __( 'MIME Types', 'ultimate-file-sharing' ),
            'id'     => 'ufs-mime-section',
            'desc'   => __( 'Basic field with no subsections.', 'ultimate-file-sharing' ),
            'icon'   => 'el el-file',
            'fields' => array(
                array(
                    'id'       => 'ufs-general-mime-types',
                    'type'     => 'textarea',
                    'title'    => __( 'Supported Mime Types', 'ultimate-file-sharing' ),
                    'subtitle' => __( 'Add more Mime types.', 'ultimate-file-sharing' ),
                    'desc'     => __( 'Utilizza il seguente formato: ESTENSIONE = MIME TYPE. Puoi specificare più Mime Types, in tal caso ricordati di andare a capo per ogni mime che aggiungi', 'ultimate-file-sharing' ),
                ),
            )
        ));

        add_filter('redux/options/' . $opt_name . '/compiler', array($this,'compiler_action'), 10, 3);
    }

    /**
     * @param $options
     * @param $css
     * @param $changed_values
     */
    public function compiler_action($options, $css, $changed_values)
    {
        global $wp_filesystem;
        global $ufs_options;

        // Ottengo il css personalizzato
        $css = $ufs_options['opt-ace-editor-css'];

        $filename =  $this->plugin_dir . 'public/css/ufs-custom-style.css';

        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        if ($wp_filesystem) {
            $wp_filesystem->put_contents(
                $filename,
                $css,
                FS_CHMOD_FILE // predefined mode settings for WP files
            );
        }
    }

    /**
     * Get the CSS default valuews
     *
     * @return string
     */
    public function ufs_get_css_default(){
        $css = '';

        $file = dirname( __FILE__ ) .'/css-default.css';

        if(file_exists($file)){
            $css = file_get_contents($file);
        }

        return $css;
    }
}