<?php

/**
 * Class PodcastModel
 *
 * Represents the model of the podcast
 */
class PodcastModel
{
    private $_program;

    public function __construct($program)
    {
        $this->_program = $program;
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