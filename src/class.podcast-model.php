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
        return $this->_term->slug == $this->_program;
    }

    public function episodes() {
        return $this->_term->post(-1);
    }

    public function title() {
        return $this->_term->name;
    }

    public function language() {
        return get_locale();
    }

    public function description() {
        return $this->_term->description;
    }
    public function link() {
        return $this->_term->link();
    }

    public function copyright() {
        return $this->_term->copyright;
    }

    public function author() {
        return $this->_term->author;
    }

    public function owner() {
        return array(
            'name' => $this->_term->owner_name,
            'email' => $this->_term->owner_email
        );
    }

    public function itunes_type() {
        return $this->_term->itunestype;
    }

    public function explicit() {
        return $this->_term->explicit ? 'true' : 'false';
    }

    public function image() {
        return $this->_term->image['url'];
    }

    public function categories() {
        $array = $this->_term->categories;
        sort($array);

        $c = [];
        foreach($array as $e) {
            $s = explode(' | ', $e);
            if(!array_key_exists($s[0], $c)) {
                $c[$s[0]] = ['category'=> $s[0], 'subcategories'=>[]];
            }
            if(count($s)>1) {
                $c[$s[0]]['subcategories'][] = $s[1];
            }
        }
        return $c;
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