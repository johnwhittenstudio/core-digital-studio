<div class="entry-summary">
  <?php if ( has_post_thumbnail() ) : ?>
  <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
  <?php endif; ?>
  <h2 class="post-title"><?php the_title(); ?></h2>
  <div class="search-excerpt">
    <?php the_excerpt(); ?>
  </div>
  <div class="vist-page-box">
    <a class="vist-page" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">Visit Page</a>
  </div>
  <?php if ( is_search() ) { ?>
  <div class="entry-links"><?php wp_link_pages(); ?></div>
  <?php } ?>
</div>
