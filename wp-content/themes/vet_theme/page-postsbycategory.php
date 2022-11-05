<?php get_header(); ?>
<?php if (!is_front_page()) { ?>
<header class="header">
  <div class="inner-header">
    <h1 class="entry-title">
      <?php
      $title = get_the_title();
      $title_array = explode(' ', $title);
      if (count($title_array) > 1) {
        $first_word = $title_array[0]; ?>
        <span class="first-word"><?php echo $first_word; ?></span>
        <?php echo substr(strstr($title," "), 1); ?>
      <?php } else { ?>
        <?php echo $title ?>
      <?php } ?>
    </h1>
  </div>
</header>
<?php } ?>
<main id="content">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
      <?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
      <?php the_content(); ?>
      <div class="entry-links"><?php wp_link_pages(); ?></div>
    </div>
  </article>
  <?php endwhile; endif; ?>
</main>
<?php if (!is_front_page()) { ?>
    <?php echo do_shortcode('[appointment_area background_image="/wp-content/uploads/2022/01/Dalmatian-services-cta-bg-e1641246613649.png" margin_top="0px"]') ?>
<?php } ?>
<?php get_footer(); ?>
