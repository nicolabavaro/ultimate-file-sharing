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
class Ultimate_File_Sharing_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        // Shortcode per la visualizzazione della lista dei file scaricabili dall'utente
        add_shortcode( 'ufs_user_download_list', array($this,'ufs_shortcode_display_user_download_list') );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
        global $ufs_options;
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

		//wp_enqueue_style( $this->plugin_name, '//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all' );

        if($ufs_options['custom-css-enable']==true){
            wp_enqueue_style( $this->plugin_name.'-views', plugin_dir_url( __FILE__ ) . 'css/ufs-custom-style.css',array() , $this->version, 'all' );
        } else {
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ultimate-file-sharing-public.css',array() , $this->version, 'all' );
        }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
        global $ufs_options;
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

        if($ufs_options['ufs-js-cdn-enable']==true){
            wp_enqueue_script( $this->plugin_name.'core', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, true );
        } else {
            wp_enqueue_script( $this->plugin_name.'core', plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery'), $this->version, true );
        }

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ultimate-file-sharing-public.js', array( $this->plugin_name.'core' ), $this->version, true );
	}

    /**
     * Funzione che genere l'output dello shortcode del plugin
     *
     * @param $atts
     * @return string
     */
	public function ufs_shortcode_display_user_download_list($atts){

        global $ufs_options;

        // Ottengo l'utente corrente
        $current_user = wp_get_current_user();


        if(!is_user_logged_in()){
            $args = array(
                'echo'           => true,
                'remember'       => true,
                //'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                'form_id'        => 'loginform',
                'id_username'    => 'user_login',
                'id_password'    => 'user_pass',
                'id_remember'    => 'rememberme',
                'id_submit'      => 'wp-submit',
                'label_username' => __( 'Username or Email Address', 'ultimate-file-sharing'),
                'label_password' => __( 'Password', 'ultimate-file-sharing'),
                'label_remember' => __( 'Remember Me', 'ultimate-file-sharing'),
                'label_log_in'   => __( 'Log In', 'ultimate-file-sharing'),
                'value_username' => '',
                'value_remember' => false
            );
            ob_start();
            echo esc_html(__('Autentication is required. Please login','ultimate-file-sharing'));
            wp_login_form($args);
            $html = ob_get_clean();
            return $html;
        }

	    $atts = shortcode_atts( array(
            'foo' => 'no foo',
            'baz' => 'default baz'
        ), $atts, 'bartag' );

        // Ottengo i gruppi alla quale l'utente appartiene
        if(class_exists('Groups_User')){
            $groups_user = new Groups_User( get_current_user_id() );

            // Ottengo l'array dei gruppi padri e figli alla quale l'utente appartiene
            $user_group_ids_deep = $groups_user->group_ids_deep;
        }else{
            $user_group_ids_deep = 0;
        }

        // Ottengo un array dei file della quale l'utente ha l'accesso
        $files_allowed = $this->ufs_get_user_allowed_file($current_user->ID,$user_group_ids_deep);

        $out ='<div class="table-wrapper"><h2>'.esc_html(__('Files Available','ultimate-file-sharing')).'</h2>';
        $out .='<table id="ufs_download_list" class="display">';
        $out .='<thead><tr><th>'.esc_html(__('Filename','ultimate-file-sharing')).'</th><th>'.esc_html(__('Created on','ultimate-file-sharing')).'</th><th></th></tr></thead><tbody>';

        // Per ogni file dell'array esco il relativo html
        foreach ($files_allowed as $file){
            ob_start();
            ?>
            <tr>
                <td><a href="<?php echo esc_url($file['url_secure']); ?>" target="_blank"><?php echo esc_html($file['title']); ?></a></td>
                <td><?php echo esc_html($file['created_on'])?></td>
                <td><a href="<?php echo esc_url($file['url_secure']); ?>" target="_blank"> <?php echo esc_html(__('Download','ultimate-file-sharing')); ?></a></td>
            </tr>
            <?php
            $out .= ob_get_clean();
        }
        $out .='</table></div>';
        // Ritorno il codice per visualizzare
        return $out;
    }

    /**
     * Ottengo l'array dei files consentiti per l'utente
     *
     * @param $userid
     * @param $groupid
     * @return mixed
     */
    public function ufs_get_user_allowed_file($userid, $groupid){
        global $ufs_options;

        if(!isset($ufs_options['ufs-general-date-format'])){
            $ufs_options['ufs-general-date-format'] = 'd/M/Y';
        }

        $rd_args = array(
            'post_type'=> 'ufs_download',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'allowed_users',
                    'value'   => $userid,
                ),
                array(
                    'key'     => 'allowed_groups',
                    'value'   => $groupid,
                ),
                array(
                    'key'     => 'enable_public',
                    'value'   => '1',
                    'compare' => 'LIKE',
                ),
            ),
        );

        $allowed_files_query = new WP_Query( $rd_args );

        $allowed_files_array = [];

        foreach($allowed_files_query->posts as $files){
            if(!is_null($files->ID)){
                $date = new DateTime($files->post_date);
                //$date->format('d-m-Y');
                $allowed_files_array[$files->ID] = array(
                    'title' => $files->post_title,
                    'url' => $this->ufs_get_download_url($files->ID),
                    'hash'=> $this->ufs_get_file_hash($files->ID),
                    'url_secure' => $this->ufs_get_file_hash_url($files->ID),
                    'created_on' =>$date->format($ufs_options['ufs-general-date-format']),
                );
            }
        }
        return $allowed_files_array;
    }

    /**
     * Get the value of ufs_file postmeta
     */
    public function ufs_get_download_url($id){
        return get_post_meta( $id, 'ufs_file', true );
    }

    /**
    * Get the value of ufs_file _ufs_file_hash
    */
    public function ufs_get_file_hash($id){
        return get_post_meta( $id, '_ufs_file_hash', true );
    }
    /**
     * Get the value of ufs_file _ufs_file_hash_url
     */
    public function ufs_get_file_hash_url($id){
        return get_post_meta( $id, '_ufs_file_hash_url', true );
    }

    // Create the query var so that WP catches the custom /ufs-endpoint/username url
    public function userpage_rewrite_add_var( $vars ) {
        $vars[] = 'ufs-endpoint';
        return $vars;
    }

    // Create the rewrites
    public function userpage_rewrite_rule() {
        add_rewrite_tag( '%ufs_endpoint%', '([^&]+)' );
        add_rewrite_rule(
            '^ufs_endpoint/([^/]*)/?',
            'index.php?ufs_endpoint=$matches[1]',
            'top'
        );
    }

    // Catch the URL and redirect it to a template file
    /**
     * Intercetto l'URL e ridireziono al template che desidero.
     *
     */
    public function userpage_rewrite_catch() {
        global $wp_query;

        //var_dump($wp_query);
        if ( array_key_exists( 'ufs_endpoint', $wp_query->query_vars ) ) {
            // Se manca il REFERER significa che l'accesso è diretto
            $this->ufs_block_direct_access();
            // Verifico che l'utente sia autenticato nel sito
            if(is_user_logged_in()){
                $this->get_download_by_hash($wp_query->query_vars['ufs_endpoint']);
            }else{
                // Se l'utente non è autorizzato lo ridireziono alla pagina di autenticazione,
                // una volta autenticato potrà procedere con il download
                wp_redirect(wp_login_url('//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
                exit;
            }
            exit;
        }
    }

    // Code needed to finish the ufs-endpoint page setup
    public function memberpage_rewrite() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    /**
     * Fornito l'hash ottengo il download e ridireziono l'utente
     *
     * @param $hash
     */
    public function get_download_by_hash($hash){
        $query = new WP_Query( "post_type=ufs_download&meta_key=_ufs_file_hash&meta_value=$hash&order=ASC" );

        $postid = $query->posts[0]->ID;

        $out = get_post_meta( $postid, $key = 'ufs_file', $single = false );

        $current_user = wp_get_current_user();

        if(!is_null($out[0])){
            if($this->ufs_verify_user_download_permission($postid,$current_user)){
                // Add one download to file stats
                $this->ufs_add_one_download_to_stats($postid);

                $this->ufs_add_last_user_download($postid,$current_user);
                // redirect to the file
                wp_redirect($out[0]);
            }else{
                echo esc_html_e('You are not authorized to access this file','ultimate-file-sharing');
                //do_shortcode(wppb-login);
            }
        }else{
            echo esc_html_e('File not found','ultimate-file-sharing');
            exit;
        }
    }

    /**
     * Verifica il permesso dell'utente per scaricare un download
     *
     * @param $postid
     * @return bool
     */
    public function ufs_verify_user_download_permission($postid,$current_user){
        $allowed_users = get_post_meta( $postid, $key = 'allowed_users', $single = false );
        $allowed_groups = get_post_meta( $postid, $key = 'allowed_groups', $single = false );

        // Se il download è pubblico permetto il download
        if(get_post_meta( $postid,'enable_public', true )){
            return true;
        }

        foreach ($allowed_groups as $group) {
            // Ottengo i gruppi alla quale l'utente è associato
            $groups_user = new Groups_User( $current_user->data->ID );
            $user_group_ids_deep = $groups_user->group_ids_deep;

            // Confronto gli id dei gruppi alla quale l'utente è assegnato e li confronto con gli id dei gruppi autorizzati
            foreach ($user_group_ids_deep as $groupid){
                if($groupid == $group){
                    return true;
                }
            }

        }

        $k = 0;
        foreach ($allowed_users as $user){
            if($user == $current_user->data->ID){
                $k =1;
            }
        }

        if($k==1){
            return true;
        }else{
            return false;
        }
    }

    /**
     *  Se la richiesta non ha il referer significa che è un accesso diretto e va bloccato.
     */
    public function ufs_block_direct_access(){
        if(is_null($_SERVER['HTTP_REFERER'])){
            echo esc_html(__('Direct Access is not allowed','ultimate-file-sharing'));
            exit;
        }
    }

    /**
     *  Aggiorno la statisca dei downloads
     */
    public function ufs_add_one_download_to_stats($postid){
        if(get_post_meta( $postid, '_ufs_download_counter', true )){
            $k = get_post_meta( $postid, '_ufs_download_counter', true );
            $prev_value = $k;
            $k++;
            update_post_meta( $postid, '_ufs_download_counter', $k, $prev_value );

        }else{
            add_post_meta( $postid, '_ufs_download_counter', '1', true );
        }
    }

    /**
     *  Aggiorno la lista degli utenti che hanno scaricato il file
     */
    public function ufs_add_last_user_download($postid,$current_user){

        $user_name = $current_user ->data->display_name;

        // Aggiungo l'elemento se esiste già il meta altrimenti lo inserisco ex novo
        if(get_post_meta( $postid, '_ufs_download_history', true )){

            $old_value = get_post_meta( $postid, '_ufs_download_history', true );

            $history = unserialize($old_value);

            $history[]= array(
                    'date' => current_time( "Y-m-d H:i:s" ),
                    'userDisplay' => $user_name
            );

            // Se l'array ha più di 5 elementi cancello il primo (che è il più vecchio)
            if(count($history) > 5){
                array_splice($history, 0,1);
            }
            // Aggiorno il postmeta
            update_post_meta( $postid, '_ufs_download_history', serialize($history), $old_value );

        }else{
            $history_array = [];
            $history_array[] = array(
                    'date' => date("Y-m-d H:i:s"),
                    'userDisplay' => $user_name
            );

            add_post_meta( $postid, '_ufs_download_history', serialize($history_array), true );
        }
    }

    /**
     * Modifico il titolo della pagina visualizzando un testo personalizzato per l'utente
     *
     * @param $title
     * @return string
     */
    public function ufs_change_page_title($title){
        global $ufs_options;

        // Modifico il titolo della pagina solo se mi trovo nel frontend, nel loop e se la funzionalità è attiva
        if($ufs_options['ufs-general-public-custom-title']&&!is_admin()&& in_the_loop()){
            $user = wp_get_current_user();
            return __('Documents Area of:','ultimate-file-sharing').' '.$user->display_name;
        }
        return $title;
    }

    /**
     * Intercetto il template, questo però non lo rende selezionabile dalla pagina e non sembra essere una soluzione compatibile con il tema, che spesso ha delle chiamate sue.
     *
     * @param $template_path
     * @return string
     */
    public function ufs_download_page_template($template_path){
        global $ufs_options;

        $currentID = get_the_ID();

        if($ufs_options['ufs-select-download-page']==$currentID){
            if ( $theme_file = locate_template( array ( 'single-movie_reviews.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-movie_reviews.php';
            }
            $template_path = plugin_dir_path( __FILE__ ).'partials/ultimate-file-sharing-download-page-template.php';
        }

        return $template_path;
    }
}
