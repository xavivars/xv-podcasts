<?php

/**
 * Class XVEpisodeModel
 *
 * Represents the model of a single episode
 */
class XVEpisodeModel
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
        return $this->_post->thumbnail->src;
    }

    public function link() {
        return $this->_post->link;
    }

    public function guid() {
        return $this->_post->guid;
    }

    public function enclosure() {
        $enclosure = $this->_post->get_field('enclosure');
        return array(
            'filesize' => $enclosure['filesize'],
            'mime' => $enclosure['mime_type'],
            'url' => $enclosure['url']
        );
    }

    public function date() {
        $format = "D, d M Y H:i:s O";
        return $this->_post->date($format);
    }

    public function duration() {
        return $this->_post->duration;
    }

    public function explicit() {
        return $this->_post->explicit ? 'true' : 'false';
    }
}