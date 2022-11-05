<?php get_header();?>

<section class="page-wrap">
<div class="container">

      <h3><?php echo single_cat_title();?></h3>

      <?php get_template_part('includes/section', 'archive');?>


      <?php
      global $wp_query;

      $big = 999999999; // need an unlikely integer
      $translated = __( 'Page', 'mytextdomain' ); // Supply translatable string

      echo paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'before_page_number' => '<span class="screen-reader-text">'.$translated.' </span>'
      ) );
      ?>


</div>
</section>


<?php get_footer();?>