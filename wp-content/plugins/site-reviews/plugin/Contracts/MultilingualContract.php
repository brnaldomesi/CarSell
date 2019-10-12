<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MultilingualContract
{
    /**
     * @param int|string $postId
     * @return \WP_Post|void|null
     */
    public function getPost($postId);

    /**
     * @return array
     */
    public function getPostIds(array $postIds);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return bool
     */
    public function isSupported();
}
