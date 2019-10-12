<?php defined('WPINC') || die; ?>

<p class="about-description">If you are using any Site Reviews code snippets or have built any custom integrations with Site Reviews, please make sure to read this upgrade guide.</p>
<div class="is-fullwidth">
    <div class="glsr-flex-row">
        <div class="glsr-column">
            <div class="glsr-card card is-fullwidth">
                <h3>Meta Keys</h3>
                <p><em>Likelihood Of Impact: Low</em></p>
                <p>The Meta Keys that Site Reviews 4.0 uses to store information to reviews are now protected (they begin with an underscore) so that they don't show up in the Custom Fields Meta Box on other pages. This change can potentially affect you if one of the following cases apply:</p>
                <ul>
                    <li>You are using the <code>WP_Query</code> class or the <code>get_posts</code> function to fetch reviews and are setting the <code>meta_key</code> or <code>meta_query</code> options</li>
                    <li>You are using the <code>pre_get_posts</code> WordPress filter to modify the Site Reviews <code>meta_query</code></li>
                    <li>You are using the <code>update_postmeta</code> WordPress filter with reviews and are checking the <code>meta_key</code> value</li>
                </ul>
                <p>If any of the above cases apply to you, please make sure that you prefix the Meta Keys with an underscore.</p>
                <p>Here is a <code>get_posts</code> example:</p>
                <pre><code class="php">// OLD CODE
$reviews = get_posts([
    'meta_query' => [['key' => 'assigned_to', 'value' => 123]],
    'post_type' => 'site-review',
]);

// NEW CODE:
$reviews = get_posts([
    'meta_query' => [['key' => '_assigned_to', 'value' => 123]], <-- here we changed the meta_key
    'post_type' => 'site-review',
]);
</code></pre>
                <p>Here is a <code>pre_get_posts</code> example:</p>
                <pre><code class="php">// OLD CODE
add_action('pre_get_posts', function ($query) {
    if ($query->get('post_type') == 'site-review') {
        $meta = (array) $query->get('meta_keyquery');
        $index = array_search('rating', array_column($meta, 'key'));
        // ...
    }
});

// NEW CODE
add_action('pre_get_posts', function ($query) {
    if ($query->get('post_type') == 'site-review') {
        $meta = (array) $query->get('meta_query');
        $index = array_search('_rating', array_column($meta, 'key')); <-- here we changed the meta_key
        // ...
    }
});</code></pre>
                <p>Here is a <code>update_postmeta</code> example:</p>
                <pre><code class="php">// OLD CODE
add_action('update_postmeta', function ($metaId, $postId, $metaKey) {
    $review = apply_filters('glsr_get_review', null, $postId);
    if (!empty($review->ID) && 'response' == $metaKey) {
        // ...
    }
}, 10, 3);

// NEW CODE
add_action('update_postmeta', function ($metaId, $postId, $metaKey) {
    $review = apply_filters('glsr_get_review', null, $postId);
    if (!empty($review->ID) && '_response' == $metaKey) { <-- here we changed the meta_key
        // ...
    }
}, 10, 3);</code></pre>
                <ul>
            </div>
        </div>
    </div>
</div>
