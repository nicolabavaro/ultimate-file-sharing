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

class UFS_Download_PostType{

    /**
     * @var string
     *
     * Set post type params
     */
    private $type               = 'ufs_download';
    private $slug               = 'ufs-downloads';
    private $name               = 'Downloads';
    private $singular_name      = 'Download';

    /**
     * Register post type
     */
    public function register() {
        $labels = array(
            'name'                  => $this->name,
            'singular_name'         => $this->singular_name,
            'add_new'               => __( 'Add New', 'ultimate-file-sharing' ),
            'add_new_item'          => __( 'Add New', 'ultimate-file-sharing' )   . $this->singular_name,
            'edit_item'             => __( 'Edit ', 'ultimate-file-sharing' )      . $this->singular_name,
            'new_item'              => __( 'New ', 'ultimate-file-sharing' )       . $this->singular_name,
            'all_items'             => __( 'All ', 'ultimate-file-sharing' )       . $this->name,
            'view_item'             => __( 'View', 'ultimate-file-sharing' )      . $this->name,
            'search_items'          => __( 'Search', 'ultimate-file-sharing' )    . $this->name,
            'not_found'             => __( 'No ', 'ultimate-file-sharing' )        . strtolower($this->name) . __( 'found', 'ultimate-file-sharing' ),
            'not_found_in_trash'    => __( 'No ', 'ultimate-file-sharing' )        . strtolower($this->name) . __( 'found in trash', 'ultimate-file-sharing' ),
            'parent_item_colon'     => '',
            'menu_name'             => $this->name
        );
        $args = array(
            'labels'                => $labels,
            'public'                => false,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => $this->slug ),
            'capability_type'       => 'post',
            'has_archive'           => false,
            'hierarchical'          => true,
            'menu_position'         => 8,
            'supports'              => array( 'title', 'author', 'thumbnail'),
            'yarpp_support'         => true
        );
        register_post_type( $this->type, $args );
    }
    /**
     * Imposto i titoli delle colonne nella visualizzazione elenco dei posttype nell'area admin
     *
     * @param $columns
     * @return mixed
     *
     * Choose the columns you want in
     * the admin table for this post
     */
    public function ufs_set_columns($columns) {
        global $ufs_options;

        // Set/unset post type table columns here ufs-general-admin-show-download-column
        if($ufs_options['ufs-general-admin-show-download-column']){
            $columns['ufs_total_downloads'] = __( 'Total Downloads', 'ultimate-file-sharing' );
        }
        if($ufs_options['ufs-general-admin-show-groups-column']){
            $columns['ufs_groups'] = __( 'Allowed Groups', 'ultimate-file-sharing' );
        }
        if($ufs_options['ufs-general-admin-show-users-column']){
            $columns['ufs_users'] = __( 'Allowed Users', 'ultimate-file-sharing' );
        }

        return $columns;
    }
    /**
     * Imposto i contenuti delle colonne
     *
     * @param $column
     * @param $post_id
     *
     * Edit the contents of each column in
     * the admin table for this post
     */
    public function ufs_edit_columns($column, $post_id) {
        // Post type table column content code here
        switch ( $column ) {
            case 'ufs_total_downloads' :
                echo get_post_meta( $post_id , '_ufs_download_counter' , true );
                break;
            case 'ufs_users' :
                $users_id_array = get_post_meta( $post_id , 'allowed_users' , false );
                $this->display_alloed_user_column_data($users_id_array);
                break;
            case 'ufs_groups' :
                $group_id_array = get_post_meta( $post_id , 'allowed_groups' , false );
                $this->display_allowed_groups_column_data($group_id_array);
                break;
        }
    }

    public function display_alloed_user_column_data($users_id_array){
        $k = count($users_id_array );
        $i = 0;
        ob_start();
        foreach($users_id_array  as $user){
            $i++;
            if($i == $k){
                $sep = '';
            }else{
                $sep = ',';
            }
            $user = get_userdata($user );
            echo '<span class="ufs-user-column">'.$user->display_name.$sep.'</span> ';
        }
        $out = ob_get_clean();
        echo $out;
    }

    public function display_allowed_groups_column_data($groups_id_array){
        $k = count($groups_id_array);
        $i = 0;
        ob_start();
        foreach($groups_id_array as $group){
            $i++;
            if($i == $k){
                $sep = '';
            }else{
                $sep = ',';
            }
            echo '<span class="ufs-groups-column"></span> ';
        }
        $out = ob_get_clean();
        echo $out;
    }
    /**
     *  Creo la tassiomia Categorie
     */
    public function ufs_create_download_taxonomies() {
        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name'              => _x( 'Categories', 'taxonomy general name', 'ultimate-file-sharing' ),
            'singular_name'     => _x( 'Category', 'taxonomy singular name', 'ultimate-file-sharing' ),
            'search_items'      => __( 'Search Category', 'ultimate-file-sharing' ),
            'all_items'         => __( 'All Categories', 'ultimate-file-sharing' ),
            'parent_item'       => __( 'Parent Category', 'ultimate-file-sharing' ),
            'parent_item_colon' => __( 'Parent Category:', 'ultimate-file-sharing' ),
            'edit_item'         => __( 'Edit Category', 'ultimate-file-sharing' ),
            'update_item'       => __( 'Update Category', 'ultimate-file-sharing' ),
            'add_new_item'      => __( 'Add New Category', 'ultimate-file-sharing' ),
            'new_item_name'     => __( 'New Category Name', 'ultimate-file-sharing' ),
            'menu_name'         => __( 'Category', 'ultimate-file-sharing' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'download-category' ),
        );

        register_taxonomy( 'ufs_category', array( 'ufs_download' ), $args );
    }

    /**
     * Event constructor.
     *
     * When class is instantiated
     */
    public function __construct() {
        // Register the post type
        add_action('init', array($this, 'register'));
        // Admin set post columns
        add_filter( 'manage_edit-'.$this->type.'_columns',        array($this, 'ufs_set_columns'), 10, 1) ;
        // Admin edit post columns
        add_action( 'manage_'.$this->type.'_posts_custom_column', array($this, 'ufs_edit_columns'), 10, 2 );
        // Create Categories for Download Post Type
        add_action( 'init', array($this,'ufs_create_download_taxonomies'), 0 );
    }
}