<?php

include(XV_PODCASTS_PATH . 'src/class.episode-model.php');

/**
 * Class XVPodcastModel
 *
 * Represents the model of the podcast
 */
class XVPodcastModel
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

    public function title() {
        return $this->_term->name;
    }

    public function language() {
        return get_locale();
    }

    public function description() {
        return $this->_term->description;
    }

    public function short_description() {
        return $this->_term->short_description();
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

    public function image_id() {
        return $this->_term->image['id'];
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

    public function episodes() {
        $all_posts = $this->_term->get_posts(-1);
        $episodes = [];
        foreach($all_posts as $p) {
            $episodes[] = new XVEpisodeModel($p);
        }

        return $episodes;
    }
}
