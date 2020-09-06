<?php

/**
 * Class PodcastModel
 *
 * Represents the model of the podcast
 */
class PodcastModel
{
    private $_program;

    public function __construct($program, $term)
    {
        $this->_program = $program;
        $this->_term = $term;
    }

    public function valid() {
        return $this->_term == $this->_program;
    }

    public function episodes() {
        return $this->_term->post(-1);
    }

    public function title() {
        $this->_term->title;
    }

    public function description() {
        $this->_term->description;
    }
}

/*
 * "title": "",
    "link": "",
    "language": "",
    "copyright": "",
    "author": "",
    "description": "",
"itunes_type": "",
    "owner": { // admin purposes
    "name": "",
      "email": ""
    },
    "image": "", // URL
    "categories": [ // see https://help.apple.com/itc/podcasts_connect/#/itc9267a2f12
      {
          "category": "",
        "subcategories": ""
      }
    ],
    "explicit": false, // bool
 */