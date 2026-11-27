<?php
if (! defined('ABSPATH')) exit;
get_header();
?>
<main class="dealer-content"><div class="dealer-container">
<?php while (have_posts()) : the_post(); ?>
<article <?php post_class('dealer-post-content'); ?>>
<p class="dealer-news-meta"><?php echo esc_html(get_the_date()); ?> · <?php the_category(', '); ?></p>
<h1 class="dealer-page-title"><?php the_title(); ?></h1>
<?php if (has_post_thumbnail()) : ?><figure><?php the_post_thumbnail('large'); ?></figure><?php endif; ?>
<div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
