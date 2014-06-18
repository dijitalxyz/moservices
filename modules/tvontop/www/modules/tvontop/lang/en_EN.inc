<?php
require_once 'interfaces/l10n.inc';

class Locale_en_EN extends Localization {
    function Locale_en_EN() {
        $this->lang ='en_EN';

        $this->messages = array(
            '_TEST_' => 'Test %3$d,%2$d, %1$d english',
            'EPG_TITLE' => ' - Programm for %1$s',
            'Default template' => 'Default template used only for development purposes!',
            'Our folders' => 'Our folders',
            'Available Services' => 'Available services',
            'All Categories' => 'All Categories',
            'Change account' => 'Change account',
            'Season' => 'Season',
            'Episode' => 'Episode',
            'All' => 'All',
            'Favorites' => 'Favorites',
            'Last added' => 'Last added',
            'Favorite added' => 'Favorite added',
            'Favorite not added' => 'Favorite not added',
            'Favorite removed' => 'Favorite removed',
            'Favorite not removed' => 'Favorite not removed',
            'Error' => 'Error',
            'Message' => 'Message',
            'Wrong password' => 'Wrong password',
            'Add to favorites' => 'Add to favorites',
            'Remove from favorites' => 'Remove from favorites',
            'Film list' => 'Film list',
            'Page' => 'Page',
            'To page' => 'To page',
            'Jump to page' => 'Jump to page %1$d/%2$d, items %3$d-%4$d/%5$d',
            'Search' => 'Search',
            'Genres' => 'Genres',
            'Authorization error' => 'Authorization error',
            'Quality' => 'Quality',
            'year' => 'Year',
            'length' => 'Length',
            'min' => 'min',
            'Lang1' => 'Lang1',
            'Lang2' => 'Lang2',
            'rating' => 'Rating',
            'genre' => 'Genre',
            'country' => 'Country',
            'director' => 'Director',
            'scenario' => 'Scenario',
            'actors' => 'Actors',
            'studio' => 'Studio',
            'awards' => 'Awards',
            'budget' => 'Budget',
            'images' => 'Images',
        	'new_version_title' => 'New version available.',
        	'new_version_msg' => 'Version %1$d is available on http://tvontop.com. Please update.',
        	'Best' => 'Best',
        	'Loading video...' => 'Loading video...',
        	'Watch' => 'Watch',
        	'Protected channel password:' => 'Protected channel password:' 
        );
    }
}
?>
