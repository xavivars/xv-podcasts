<?php
/**
 * @package XVPodcasts
 */

/**
 * Class XVPodcastContentType
 *
 * Registers the Podcast post type & taxonomies
 */
class XVPodcastContentType
{
    /**
     * @var string
     */
    public $singular;

    /**
     * @var string
     */
    public $plural;

    public function __construct()
    {
        $this->singular = __('Podcast', 'xv-podcasts');
        $this->plural = __('Podcasts', 'xv-podcasts');

        add_action('init', array($this, 'register_custom_taxonomies'));
        add_action('init', array($this, 'register_custom_post_type'));

        add_filter('manage_' . $this->singular . '_posts_columns', array($this, 'add_columns_to_admin'));
        add_action('manage_' . $this->singular . '_posts_custom_column', array($this, 'custom_columns'), 10, 2);

        add_filter( 'acf/settings/load_json', array( $this, 'load_acf_fields' ) );
        add_action( 'admin_init', array( $this, 'validate_dependencies' ));

        add_filter( 'upload_size_limit', array($this, 'filter_site_upload_size_limit'), 20 );
    }

    public function filter_site_upload_size_limit( $size ) {
        // 10 MB.
        return 10 * 1024 * 1024;
    }

    public function register_custom_post_type()
    {
        $this->register_podcast_post_type();
    }

    public function register_custom_taxonomies()
    {
        $this->register_podcast_programa();
    }

    public function validate_dependencies() {
        if ( is_admin() && (!function_exists('get_field') || !class_exists('Timber'))) {
            add_action( 'admin_notices', array( $this, 'show_missing_dependencies_message' ));
        }
    }

    function show_missing_dependencies_message(){
        ?><div class="error"><p><?php
        echo __('Sorry, XV Podcasts requires AdvancedCustomFields and Timber Library', 'xv-podcasts');
        ?></p></div><?php
    }

    public function load_acf_fields( $paths ) {
        $paths[] = XV_PODCASTS_PATH . 'conf/';

        return $paths;
    }

    public function custom_columns($column, $post_id)
    {
        switch ($column) {
            case 'episode':
                echo esc_url(get_post_meta($post_id, 'episode', true));
                break;

            case 'season':
                echo esc_url(get_post_meta($post_id, 'season', true));
                break;

            default:
                return;
        }
    }

    public function add_columns_to_admin($columns)
    {

        return array_merge(
            $columns,
            array(
                'episode' => __('Episode', 'xv-podcasts'),
                'season' => __('Season', 'xv-podcasts'),
            )
        );
    }

    private function register_podcast_post_type()
    {
        $labels = $this->get_ctp_labels(__('Podcasts', 'xv-podcasts'));

        $args = array(
            'label' => __('Podcast', 'xv-podcasts'),
            'description' => __('Podcasts', 'xv-podcasts'),
            'labels' => $labels,
            'hierarchical' => false,
            'supports' => ['title', 'editor', 'revisions', 'thumbnail', 'comments'],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-microphone',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => 'podcasts',
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'rewrite' => array(
                'slug' => 'podcasts/%podcast-programa%',
                'with_front' => false,
            ),
            'capability_type' => 'post',
            'show_in_rest' => true,
        );

        register_post_type('podcast', $args);
        add_filter('post_type_link', array( $this, 'podcast_programa_link'), 10, 2);
    }

    private function register_podcast_programa()
    {
        $labels = $this->get_taxonomy_labels(
            __('Podcasts programs', 'xv-podcasts'),
            __('Podcast program', 'xv-podcasts'),
            __('Podcast program', 'xv-podcasts')
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'rewrite' => array(
                'slug' => 'podcasts',
                'with_front' => false
            ),
            'show_in_rest' => false,
        );
        register_taxonomy('podcast-programa', array('podcast'), $args);
    }

    function podcast_programa_link($post_link, $id = 0)
    {
        $post = get_post($id);
        if (is_object($post)) {
            $terms = wp_get_object_terms($post->ID, 'podcast-programa');
            if ($terms) {
                return str_replace('%podcast-programa%', $terms[0]->slug, $post_link);
            }
        }
        return $post_link;
    }

    protected function get_ctp_labels($menu)
    {
        return array(
            'name' => $this->plural,
            'singular_name' => $this->singular,
            'menu_name' => $menu,
            'name_admin_bar' => $menu,
            'archives' => __('Item Archives', 'xv-podcasts'),
            'attributes' => __('Item Attributes', 'xv-podcasts'),
            'parent_item_colon' => __('Parent Item:', 'xv-podcasts'),
            'all_items' => __('All Items', 'xv-podcasts'),
            'add_new_item' => __('Add New Item', 'xv-podcasts'),
            'add_new' => __('Add New', 'xv-podcasts'),
            'new_item' => __('New Item', 'xv-podcasts'),
            'edit_item' => __('Edit Item', 'xv-podcasts'),
            'update_item' => __('Update Item', 'xv-podcasts'),
            'view_item' => __('View Item', 'xv-podcasts'),
            'view_items' => __('View Items', 'xv-podcasts'),
            'search_items' => __('Search Item', 'xv-podcasts'),
            'not_found' => __('Not found', 'xv-podcasts'),
            'not_found_in_trash' => __('Not found in Trash', 'xv-podcasts'),
            'featured_image' => __('Featured Image', 'xv-podcasts'),
            'set_featured_image' => __('Set featured image', 'xv-podcasts'),
            'remove_featured_image' => __('Remove featured image', 'xv-podcasts'),
            'use_featured_image' => __('Use as featured image', 'xv-podcasts'),
            'insert_into_item' => __('Insert into item', 'xv-podcasts'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'xv-podcasts'),
            'items_list' => __('Items list', 'xv-podcasts'),
            'items_list_navigation' => __('Items list navigation', 'xv-podcasts'),
            'filter_items_list' => __('Filter items list', 'xv-podcasts'),
        );

    }

    protected function get_taxonomy_labels($plural, $singular, $menu)
    {
        return array(
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $menu,
            'all_items' => __('All Items', 'xv-podcasts'),
            'parent_item' => __('Parent Item', 'xv-podcasts'),
            'parent_item_colon' => __('Parent Item:', 'xv-podcasts'),
            'new_item_name' => __('New Item Name', 'xv-podcasts'),
            'add_new_item' => __('Add New Item', 'xv-podcasts'),
            'edit_item' => __('Edit Item', 'xv-podcasts'),
            'update_item' => __('Update Item', 'xv-podcasts'),
            'view_item' => __('View Item', 'xv-podcasts'),
            'separate_items_with_commas' => __('Separate items with commas', 'xv-podcasts'),
            'add_or_remove_items' => __('Add or remove items', 'xv-podcasts'),
            'choose_from_most_used' => __('Choose from the most used', 'xv-podcasts'),
            'popular_items' => __('Popular Items', 'xv-podcasts'),
            'search_items' => __('Search Items', 'xv-podcasts'),
            'not_found' => __('Not Found', 'xv-podcasts'),
            'no_terms' => __('No items', 'xv-podcasts'),
            'items_list' => __('Items list', 'xv-podcasts'),
            'items_list_navigation' => __('Items list navigation', 'xv-podcasts'),
        );
    }
}
