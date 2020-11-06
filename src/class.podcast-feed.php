<?php
/**
 * @package XVPodcasts
 */
include(XV_PODCASTS_PATH . 'src/class.podcast-model.php');

/**
 * Class PodcastFeed
 *
 * Enables and renders podcast feeds
 */
class XVPodcastFeed
{
    public function __construct()
    {
        add_action('init', array($this, 'register_feed'));
    }

    public function register_feed()
    {
        add_feed('podcast', array($this, 'render_feed'));
    }

    public function render_feed()
    {

        if (!$this->in_podcast_category()) {
            include(get_query_template('404'));
            status_header(404);
            exit;
        }

        $term = new TimberTerm();
        $model = new XVPodcastModel(get_query_var('podcast-programa'), $term);

        if (!$model->valid()) {
            include(get_query_template('404'));
            status_header(404);
            exit;
        }

        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

        Timber::render(XV_PODCASTS_PATH . '/templates/podcast.twig', array('podcast' => $model));
    }

    public function in_podcast_category()
    {
        $podcast_category = get_query_var('podcast-programa');
        return $podcast_category && is_archive();
    }
}