<?php
if (! defined('ABSPATH')) exit;
get_header();
?>
<main class="dealer-content"><div class="dealer-container">
<?php while (have_posts()) : the_post(); ?>
<article <?php post_class('dealer-page-content'); ?>>
<?php if (! is_front_page()) : ?><h1 class="dealer-page-title"><?php the_title(); ?></h1><?php endif; ?>
<div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
