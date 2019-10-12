<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PaginationDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'before_page_number' => '<span class="meta-nav screen-reader-text">'.__('Page', 'site-reviews').' </span>',
            'format' => '?'.glsr()->constant('PAGED_QUERY_VAR').'=%#%',
            'mid_size' => 1,
            'next_text' => __('Next &rarr;', 'site-reviews'),
            'prev_text' => __('&larr; Previous', 'site-reviews'),
            'type' => 'plain',
        ];
    }
}
