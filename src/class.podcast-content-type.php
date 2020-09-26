<?php
/**
 * @package XVPodcasts
 */

/**
 * Class XVPodcastContentType
 *
 * Registers the Podcast post type & taxonomies
 */

define('XV_PODCAST_DOWNLOAD_ID', 'xv_podcast_download');
define('XV_PODCAST_FILENAME', 'xv_podcast_filename');

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

        add_filter('acf/settings/load_json', array($this, 'load_acf_fields'));
        add_action('admin_init', array($this, 'validate_dependencies'));

        add_action('init', array($this, 'add_download_path'));
        add_filter('query_vars', array($this, 'add_download_query_var'));
        add_action('pre_get_posts', array($this, 'dispatch_download_path'), 1);


        add_filter('upload_size_limit', array($this, 'filter_site_upload_size_limit'), 20);
    }

    public function add_download_path()
    {
        add_rewrite_rule(
            '^podcasts-download/([0-9]+)/([a-z0-9-]+)\.mp3$',
            'index.php?' . XV_PODCAST_DOWNLOAD_ID . '=$matches[1]&' . XV_PODCAST_FILENAME . '=$matches[2]',
            'top'
        );
    }

    public function add_download_query_var($qv)
    {
        $qv[] = XV_PODCAST_DOWNLOAD_ID;
        $qv[] = XV_PODCAST_FILENAME;
        return $qv;
    }

    public function dispatch_download_path($query)
    {

        if (!$query->is_main_query()) {
            return;
        }

        $xv_post_id = get_query_var(XV_PODCAST_DOWNLOAD_ID);

        if ($xv_post_id) {

            $enclosure = get_field('enclosure', $xv_post_id);

            if ($enclosure) {
                var_dump($enclosure);
                var_dump(get_query_var(XV_PODCAST_FILENAME));

                $log = apply_filters('xv_podcasts_log', false);

                if ($log) {
                    $entry = $this->get_log_entry(
                        $xv_post_id,
                        get_query_var(XV_PODCAST_FILENAME),
                        $enclosure['url']
                    );

                    file_put_contents($log, json_encode($entry), FILE_APPEND | LOCK_EX);
                }

                header("Location: " . $enclosure['url'], TRUE, 302);

                exit;
            }

            $query->set_404();
            status_header(404);
            return;
        }
    }

    private function get_log_entry($id, $filename, $url)
    {
        $date = new DateTime();
        return [
            'timestamp' => $date->format("y:m:d h:i:s"),
            'podcast_id' => $id,
            'podcast_filename' => $filename,
            'podcast_url' => $url,
            'ip' => $_SERVER['HTTP_X_REAL_IP'],
            'accept' => $_SERVER['HTTP_ACCEPT'],
            'encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'],
            'charset' => $_SERVER['HTTP_ACCEPT_CHARSET'],
            'language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            'referer' => $_SERVER['HTTP_REFERER'],
            'ua' => $_SERVER['HTTP_USER_AGENT'],
            'request' => $_SERVER['REQUEST_URI']
        ];
    }

    public function filter_site_upload_size_limit($size)
    {
        // 100 MB.
        $newSize = 100 * 1024 * 1024;
        if ($size < $newSize) {
            return $newSize;
        }
        return $size;
    }

    public function register_custom_post_type()
    {
        $this->register_podcast_post_type();
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
        add_filter('post_type_link', array($this, 'podcast_programa_link'), 10, 2);
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

    public function register_custom_taxonomies()
    {
        $this->register_podcast_programa();
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

    public function validate_dependencies()
    {
        if (is_admin() && (!function_exists('get_field') || !class_exists('Timber'))) {
            add_action('admin_notices', array($this, 'show_missing_dependencies_message'));
        }
    }

    function show_missing_dependencies_message()
    {
        ?>
        <div class="error"><p><?php
            echo __('Sorry, XV Podcasts requires AdvancedCustomFields and Timber Library', 'xv-podcasts');
            ?></p></div><?php
    }

    public function load_acf_fields($paths)
    {
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
}
