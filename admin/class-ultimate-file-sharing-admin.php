<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.nicolabavaro.it
 * @since      1.0.0
 *
 * @package    Ultimate_File_Sharing
 * @subpackage Ultimate_File_Sharing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ultimate_File_Sharing
 * @subpackage Ultimate_File_Sharing/admin
 * @author     Nicola Bavaro <nicola.bavaro@gmail.com>
 */
class Ultimate_File_Sharing_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ultimate_File_Sharing_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ultimate_File_Sharing_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        //wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ultimate-file-sharing-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ultimate_File_Sharing_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ultimate_File_Sharing_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ultimate-file-sharing-admin.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Registro mime types personalizzati
     *
     * @param $mime_types
     * @return mixed
     */
    public function ufs_register_mime_types($mime_types){

        $mime_types['doc']  = 'application/msword';
        $mime_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $mime_types['xls']  = 'application/excel, application/vnd.ms-excel, application/x-excel, application/x-msexcel';
        $mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $mime_types['ppt']  = 'application/mspowerpoint, application/powerpoint, application/vnd.ms-powerpoint, application/x-mspowerpoint';
        $mime_types['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $mime_types['psd']  = 'application/octet-stream';

        // return the array back to the function with our added mime type
        return $mime_types;
    }

    /**
     * Funzione responsabile della creazione dei metabox che visualizzo nel post type Downloads
     */
    public function ufs_register_meta_boxes($meta_boxes)
    {
        $prefix = 'ufs_';

        // Select2 Users
        $meta_boxes[] = array(
            'id' => 'ufs_allowed_users',
            'title' => 'Universal File Sharing',
            'post_types' => 'ufs_download',
            'context' => 'normal',
            'priority' => 'high',

            'fields' => array(
                array(
                    'id'        => 'enable_public',
                    'name'      => __('Show to all registered users','ultimate-file-sharing'),
                    'type'      => 'switch',

                    // Style: rounded (default) or square
                    'style'     => 'rounded',

                    // On label: can be any HTML
                    'on_label'  => __('Yes','ultimate-file-sharing'),

                    // Off label
                    'off_label' => __('No','ultimate-file-sharing'),
                ),
                array(
                    'name' => __('Authorized Users','ultimate-file-sharing'),
                    'id' => 'allowed_users',
                    'type' => 'select_advanced',
                    // Array of 'value' => 'Label' pairs
                    'options' => $this->get_users_list(),
                    // Allow to select multiple value?
                    'multiple' => true,
                    // Placeholder text
                    'placeholder' => __('Select one or more users','ultimate-file-sharing'),
                    // Display "Select All / None" button?
                    'select_all_none' => false,
                ),
                array(
                    'name' => __('Authorized Groups','ultimate-file-sharing'),
                    'id' => 'allowed_groups',
                    'type' => 'select_advanced',
                    // Array of 'value' => 'Label' pairs
                    'options' => $this->get_groups_list(),
                    // Allow to select multiple value?
                    'multiple' => true,
                    // Placeholder text
                    'placeholder' => __('Select one or more groups','ultimate-file-sharing'),
                    // Display "Select All / None" button?
                    'select_all_none' => false,

                ),
                array(
                    'name' => __('Upload file','ultimate-file-sharing'),
                    'desc' => __('Upload the file you want to share','ultimate-file-sharing'),
                    'id' => $prefix . 'file',
                    'type' => 'file_input',
                    'max_file_uploads' => 1,
                ),
            )
        );

        $meta_boxes[] = array(
            'id' => 'ufs_details',
            'title' => __('Download Details','ultimate-file-sharing'),
            'post_types' => 'ufs_download',
            'context' => 'normal',
            'priority' => 'high',

            'fields' => array(
                array(
                    'type' => 'heading',
                    'name' => __('Download History','ultimate-file-sharing'),
                    'desc' => $this->ufs_display_download_details(),
                ),
            )
        );

        return $meta_boxes;
    }

    /**
     * Get the user list and order it on array
     * @return array
     */
    public function get_users_list()
    {
        $args = array(
            'blog_id' => $GLOBALS['blog_id'],
            'role' => '',
            'role__in' => array(),
            'role__not_in' => array('administrator'),
            'meta_key' => '',
            'meta_value' => '',
            'meta_compare' => '',
            'meta_query' => array(),
            'date_query' => array(),
            'include' => array(),
            'exclude' => array(),
            'orderby' => '',
            'order' => 'ASC',
            'offset' => '',
            'search' => '',
            'number' => '',
            'count_total' => false,
            'fields' => array('ID', 'display_name'),
            'who' => '',
        );

        $user_array = get_users($args);

        $out = [];

        foreach ($user_array as $user) {
            $out[$user->ID] = $user->display_name;
        }

        return $out;
    }

    /**
     * Richiedo i gruppi presenti e li metto in un array
     *
     * @return array Array of groups
     */
    public function get_groups_list(){
        global $wpdb;

        // Controllo che la classe Groups sia presente
        if(class_exists('Groups_Group')){
            $groups_table = _groups_get_tablename( 'group' );

            $group_array =  $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY name" );

            $out = [];

            foreach ($group_array as $group) {
                $out[$group->group_id] = $group->name;
            }

            return $out;
        }
        return array();
    }

    /**
     * Register the required plugins for this theme.
     *
     *  <snip />
     *
     * This function is hooked into tgmpa_init, which is fired within the
     * TGM_Plugin_Activation class constructor.
     */
    public function ufs_register_required_plugins()
    {
        /*
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(

            // This is an example of how to include a plugin bundled with a theme.
            array(
                'name' => 'Redux Framework',
                'slug' => 'redux-framework',
                'required' => true,
            ),
            array(
                'name' => 'Groups',
                'slug' => 'groups',
                'required' => true,
            ),
            array(
                'name' => 'Adminimize',
                'slug' => 'adminimize',
                'required' => true,
            )
        );

        /*
         * Array of configuration settings. Amend each line as needed.
         *
         * TGMPA will start providing localized text strings soon. If you already have translations of our standard
         * strings available, please help us make TGMPA even better by giving us access to these translations or by
         * sending in a pull-request with .po file(s) with the translations.
         *
         * Only uncomment the strings in the config array if you want to customize the strings.
         */
        $config = array(
            'id' => 'tgmpa',                          // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                     // Default absolute path to bundled plugins.
            'menu' => 'tgmpa-install-plugins',        // Menu slug.
            'parent_slug' => 'themes.php',            // Parent menu slug.
            'capability' => 'edit_theme_options',     // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices' => true,                    // Show admin notices or not.
            'dismissable' => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg' => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,                   // Automatically activate plugins after installation or not.
            'message' => '',                          // Message to output right before the plugins table.
            /*
            'strings'      => array(
                'page_title'                      => __( 'Install Required Plugins', 'theme-slug' ),
                'menu_title'                      => __( 'Install Plugins', 'theme-slug' ),
                // <snip>...</snip>
                'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            )
            */
        );
        tgmpa( $plugins, $config );
    }

    /**
     * Quanto viene salvato un download genero l'hash e il suo download link
     * utilizzo add_post_meta così da creare i meta solo se mancano (quindi creazione ex novo di un download)
     * e non in fase di update del download.
     */
    public function ufs_generate_hash_download_save(){
        global $post;

        $ufs_hash = uniqid('ufs_',true );

        if(isset($post)){
            add_post_meta( $post->ID, '_ufs_file_hash', $ufs_hash, true );
            add_post_meta( $post->ID, '_ufs_file_hash_url', get_site_url().'/ufs_endpoint/'.$ufs_hash, true );
        }
    }

    /**
     * Questa funziona restituisce lo storico dei downloads
     *
     * @return string
     */
    public function ufs_display_download_details()
    {
        global $post;

        $html = '';

        if(isset($_GET['post'])){
            $history_array = unserialize(get_post_meta($post_id = $_GET['post'], '_ufs_download_history', true));
        }else{
            $history_array = array();
        }

        if (!empty($history_array)) {
            $history_array = array_reverse($history_array);
            foreach ($history_array as $event) {
                $html .= $event['date'].' ===> '.$event['userDisplay'].'<br>';
            }
            $html .= '<h4>'.__('Download Counter','ultimate-file-sharing').'</h4>';
            $html .= '<strong>' . __('Total Downloads', 'ultimate-file-sharing') . ':</strong> ' . get_post_meta($post_id = $_GET['post'], '_ufs_download_counter', true);
        }else{
            $html = __('No stats available','ultimate-file-sharing');
        }

        // Escape the HTML for security reason
        return $html;
    }

    /**
     * Questa funzione salva il valore ufscompany al momento dell'update dei meta utente
     *
     * @return bool
     */
    public function ufs_save_extra_user_page_fields($user_id){
        if ( !current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
        update_user_meta( $user_id, 'ufscompany', $_POST['ufscompany'] );
        return true;
    }

    public function ufs_disable_dashboard($redirect_to, $request, $user){
        //is there a user to check?
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url();
        }
    } else {
        return $redirect_to;
    }
    }
}
