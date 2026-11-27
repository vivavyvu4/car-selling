<?php
/**
 * Theme fallback template.
 *
 * WordPress requires an index.php template for a classic theme.
 */
if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="dealer-content">
    <div class="dealer-container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <h1 class="dealer-page-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h1>
                    <?php if (has_post_thumbnail()) : ?>
                        <figure><?php the_post_thumbnail('large'); ?></figure>
                    <?php endif; ?>
                    <div class="entry-content"><?php the_content(); ?></div>
                </article>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <h1><?php esc_html_e('Chưa có nội dung.', 'dealer-starter'); ?></h1>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
