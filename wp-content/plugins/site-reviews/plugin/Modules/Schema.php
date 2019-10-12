<?php

namespace GeminiLabs\SiteReviews\Modules;

use DateTime;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Schema\UnknownType;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class Schema
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $ratingCounts;

    /**
     * @return array
     */
    public function build(array $args = [])
    {
        $this->args = $args;
        $schema = $this->buildSummary($args);
        $reviews = [];
        foreach (glsr(ReviewManager::class)->get($this->args) as $review) {
            // Only include critic reviews that have been directly produced by your site, not reviews from third-party sites or syndicated reviews.
            // @see https://developers.google.com/search/docs/data-types/review
            if ('local' != $review->review_type) {
                continue;
            }
            $reviews[] = $this->buildReview($review);
        }
        if (!empty($reviews)) {
            array_walk($reviews, function (&$review) {
                unset($review['@context']);
                unset($review['itemReviewed']);
            });
            $schema['review'] = $reviews;
        }
        return $schema;
    }

    /**
     * @param array|null $args
     * @return array
     */
    public function buildSummary($args = null)
    {
        if (is_array($args)) {
            $this->args = $args;
        }
        $buildSummary = Helper::buildMethodName($this->getSchemaOptionValue('type'), 'buildSummaryFor');
        $count = array_sum($this->getRatingCounts());
        $schema = method_exists($this, $buildSummary)
            ? $this->$buildSummary()
            : $this->buildSummaryForCustom();
        if (!empty($count)) {
            $schema->aggregateRating(
                $this->getSchemaType('AggregateRating')
                    ->ratingValue($this->getRatingValue())
                    ->reviewCount($count)
                    ->bestRating(glsr()->constant('MAX_RATING', Rating::class))
                    ->worstRating(glsr()->constant('MIN_RATING', Rating::class))
            );
        }
        $schema = $schema->toArray();
        return apply_filters('site-reviews/schema/'.$schema['@type'], $schema, $args);
    }

    /**
     * @return void
     */
    public function render()
    {
        if (empty(glsr()->schemas)) {
            return;
        }
        printf('<script type="application/ld+json">%s</script>', json_encode(
            apply_filters('site-reviews/schema/all', glsr()->schemas),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));
    }

    /**
     * @return void
     */
    public function store(array $schema)
    {
        $schemas = glsr()->schemas;
        $schemas[] = $schema;
        glsr()->schemas = array_map('unserialize', array_unique(array_map('serialize', $schemas)));
    }

    /**
     * @param Review $review
     * @return array
     */
    protected function buildReview($review)
    {
        $schema = $this->getSchemaType('Review')
            ->doIf(!in_array('title', $this->args['hide']), function ($schema) use ($review) {
                $schema->name($review->title);
            })
            ->doIf(!in_array('excerpt', $this->args['hide']), function ($schema) use ($review) {
                $schema->reviewBody($review->content);
            })
            ->datePublished((new DateTime($review->date)))
            ->author($this->getSchemaType('Person')->name($review->author))
            ->itemReviewed($this->getSchemaType()->name($this->getSchemaOptionValue('name')));
        if (!empty($review->rating)) {
            $schema->reviewRating(
                $this->getSchemaType('Rating')
                    ->ratingValue($review->rating)
                    ->bestRating(glsr()->constant('MAX_RATING', Rating::class))
                    ->worstRating(glsr()->constant('MIN_RATING', Rating::class))
            );
        }
        return apply_filters('site-reviews/schema/review', $schema->toArray(), $review, $this->args);
    }

    /**
     * @param mixed $schema
     * @return mixed
     */
    protected function buildSchemaValues($schema, array $values = [])
    {
        foreach ($values as $value) {
            $option = $this->getSchemaOptionValue($value);
            if (empty($option)) {
                continue;
            }
            $schema->$value($option);
        }
        return $schema;
    }

    /**
     * @return mixed
     */
    protected function buildSummaryForCustom()
    {
        return $this->buildSchemaValues($this->getSchemaType(), [
            'description', 'image', 'name', 'url',
        ]);
    }

    /**
     * @return mixed
     */
    protected function buildSummaryForLocalBusiness()
    {
        return $this->buildSchemaValues($this->buildSummaryForCustom(), [
            'address', 'priceRange', 'telephone',
        ]);
    }

    /**
     * @return mixed
     */
    protected function buildSummaryForProduct()
    {
        $offerType = $this->getSchemaOption('offerType', 'AggregateOffer');
        $offers = $this->buildSchemaValues($this->getSchemaType($offerType), [
            'highPrice', 'lowPrice', 'price', 'priceCurrency',
        ]);
        return $this->buildSummaryForCustom()
            ->doIf(!empty($offers->getProperties()), function ($schema) use ($offers) {
                $schema->offers($offers);
            })
            ->setProperty('@id', $this->getSchemaOptionValue('url').'#product');
    }

    /**
     * @return array
     */
    protected function getRatingCounts()
    {
        if (!isset($this->ratingCounts)) {
            $this->ratingCounts = glsr(ReviewManager::class)->getRatingCounts($this->args);
        }
        return $this->ratingCounts;
    }

    /**
     * @return int|float
     */
    protected function getRatingValue()
    {
        return glsr(Rating::class)->getAverage($this->getRatingCounts());
    }

    /**
     * @param string $option
     * @param string $fallback
     * @return string
     */
    protected function getSchemaOption($option, $fallback)
    {
        $option = strtolower($option);
        if ($schemaOption = trim((string) get_post_meta(intval(get_the_ID()), 'schema_'.$option, true))) {
            return $schemaOption;
        }
        $setting = glsr(OptionManager::class)->get('settings.schema.'.$option);
        if (is_array($setting)) {
            return $this->getSchemaOptionDefault($setting, $fallback);
        }
        return !empty($setting)
            ? $setting
            : $fallback;
    }

    /**
     * @param string $fallback
     * @return string
     */
    protected function getSchemaOptionDefault(array $setting, $fallback)
    {
        $setting = wp_parse_args($setting, [
            'custom' => '',
            'default' => $fallback,
        ]);
        return 'custom' != $setting['default']
            ? $setting['default']
            : $setting['custom'];
    }

    /**
     * @param string $option
     * @param string $fallback
     * @return void|string
     */
    protected function getSchemaOptionValue($option, $fallback = 'post')
    {
        $value = $this->getSchemaOption($option, $fallback);
        if ($value != $fallback) {
            return $value;
        }
        if (!is_single() && !is_page()) {
            return;
        }
        $method = Helper::buildMethodName($option, 'getThing');
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    /**
     * @param string|null $type
     * @return mixed
     */
    protected function getSchemaType($type = null)
    {
        if (!is_string($type)) {
            $type = $this->getSchemaOption('type', 'LocalBusiness');
        }
        $className = Helper::buildClassName($type, 'Modules\Schema');
        return class_exists($className)
            ? new $className()
            : new UnknownType($type);
    }

    /**
     * @return string
     */
    protected function getThingDescription()
    {
        $description = strip_shortcodes(wp_strip_all_tags(get_the_excerpt()));
        return wp_trim_words($description, apply_filters('excerpt_length', 55));
    }

    /**
     * @return string
     */
    protected function getThingImage()
    {
        return (string) get_the_post_thumbnail_url(null, 'large');
    }

    /**
     * @return string
     */
    protected function getThingName()
    {
        return get_the_title();
    }

    /**
     * @return string
     */
    protected function getThingUrl()
    {
        return (string) get_the_permalink();
    }
}
