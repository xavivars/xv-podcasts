<?php
/**
 * @package XVPodcasts
 */

/**
 * Class PodcastFeed
 *
 * Enables and renders podcast feeds
 */
class PodcastFeed
{
    public function __construct()
    {
        add_action('init', array( $this, 'register_feed'));
    }

    public function register_feed(){
        add_feed('podcast', array($this, 'render_feed'));
    }

    public function render_feed() {

        if (!$this->in_podcast_category()) {
            status_header( 404 );
            return;
        }

        // Timber::render( XV_PODCASTS_PATH . '/templates/rss.twig', array( 'feed' => $feed ) );
    }

    public function in_podcast_category() {
        $podcast_category = get_query_var( 'podcast-category' );
        return $podcast_category && is_archive();
    }
}