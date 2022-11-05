<div class="post-container">
  <div class="entry-content post-content">
  <?php if ( has_post_thumbnail() ) : ?>
  <div class="post-img-container" href="<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full', false ); echo esc_url( $src[0] ); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></div>
  <?php endif; ?>
  <h2 class="post-title"><?php the_title(); ?></h2>
  <?php the_content(); ?>
  <div class="entry-links"><?php wp_link_pages(); ?></div>
  </div>
</div>
