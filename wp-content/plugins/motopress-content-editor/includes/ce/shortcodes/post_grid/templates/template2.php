<?php /* Name: CEPostsGridTemplate2 */ ?>

<article <?php post_class(); ?>>

<?php
if (post_type_supports($post_type, 'title') && $title_tag != 'hide' ) {
    echo '<' . $title_tag . ' class="motopress-posts-grid-title"><a href="'. get_permalink() .'">';
        the_title();
    echo '</a></'. $title_tag . '>';
}

if ( post_type_supports($post_type, 'thumbnail') && has_post_thumbnail() && $show_featured_image == 'true') {
    echo '<a class="motopress-posts-grid-thumbnail" href="'. get_permalink() .'">';
        the_post_thumbnail($featured_image_size);
    echo '</a>';
}

if ($show_date_comments == 'true') { ?>
<div class="motopress-posts-grid-meta">
    <p class="motopress-posts-grid-date <?php if (!comments_open()) echo 'motopress-no-float'; ?>"><i><?php echo get_the_date('F jS, Y') ?></i></p>
    <?php if (comments_open()) { ?>
        <p class="comments-link"><?php comments_popup_link( __('Leave a comment'), __('1 Comment'), __('% Comments') ); ?></p>
    <?php } ?>
</div>
<?php } ?>

<?php
switch ($show_content) {
    case 'hide': {
        break;
    }
    case 'full': {
        echo '<div class="motopress-posts-grid-content">';
            the_content();
        echo '</div>';
        break;
    }
    case 'excerpt': {
        echo '<div class="motopress-posts-grid-content">';
            if (post_type_supports($post_type, 'excerpt') && has_excerpt()) {
                the_excerpt();
            } else {
                the_content();
            }
        echo '</div>';
        break;
    }
    case 'short': {
        echo '<div class="motopress-posts-grid-content">';
            $content = apply_filters( 'the_content', get_the_content() );
//            $content = preg_replace('/<(script|style)(.*?)>(.*?)<\/(script|style)>/is', '', $content);
            $content = wp_strip_all_tags($content);
            $content = wp_kses( $content, array() );
            if (strlen($content) > $short_content_length) {
                $content = extension_loaded('mbstring') ? mb_substr($content, 0, $short_content_length) . '...' : substr($content, 0, $short_content_length) . '...';
            }
            echo $content;
        echo '</div>';
        break;
    }
}
?>

<?php
if (!empty($read_more_text)) {
    echo '<p class="motopress-posts-grid-more"><a href="' . get_permalink() . '">' . $read_more_text . '</a></p>';
} ?>
</article>
