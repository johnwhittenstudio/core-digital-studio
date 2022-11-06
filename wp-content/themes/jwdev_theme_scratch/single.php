<?php get_header();?>

<section class="page-wrap">
<div class="single-page-container">
      <div class="single-page-card">

            <div class="single-page-row">

                  <div class="single-page-left">

                        <?php if(has_post_thumbnail()):?>

                        <img src="<?php the_post_thumbnail_url('blog-large');?>" alt="<?php the_title();?>" class="img-thumbnail">

                        <?php endif;?>

                  </div>


                  <div class="single-page-right">

                        <h2><?php the_title();?></h2>
                        <?php get_template_part('includes/section', 'blogcontent');?>

                  </div>

                  
                  
            </div>
            
      </div>
      <?php wp_link_pages();?>
      <div class="single-page-prev">
            <?php previous_post_link(); ?>    
      </div>
      <div class="single-page-next">
            <?php next_post_link(); ?>
      </div>
</div>
</section>


<?php get_footer();?>