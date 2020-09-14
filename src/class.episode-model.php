<?php

/**
 * Class EpisodeModel
 *
 * Represents the model of a single episode
 */
class EpisodeModel
{
    private $_post;

    public function __construct($post)
    {
        $this->_post = $post;
    }

    public function type() {
        return $this->_post->episode_type;
    }

    public function number() {
        return $this->_post->episode_number;
    }

    public function season() {
        return $this->_post->episode_season;
    }

    public function title() {
        return $this->_post->name;
    }

    public function description() {
        return $this->_post->content;
    }

    public function image() {
        var_dump($this->_post->image);
    }

    public function link() {
        return $this->_post->link;
    }

    public function guid() {
        return $this->_post->guid;
    }
}