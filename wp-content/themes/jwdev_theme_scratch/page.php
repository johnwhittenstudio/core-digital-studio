<?php get_header();?>

<section class="page-wrap">
<div class="container">

      <div class="page">
            <section class="row">


                  <!-- <div class="col-lg-3">
                        <?php if( is_active_sidebar('page-sidebar') ):?>
                              <?php dynamic_sidebar('page-sidebar');?>
                        <?php endif;?>
                  </div> -->


                  <!-- <div class="col-lg-4"> -->
                        <!-- <h3><?php the_title();?></h3> -->
                        <!-- <div class="page-image"> -->
                              <?php if(has_post_thumbnail()):?>
                                    <img src="<?php the_post_thumbnail_url('blog-large');?>" alt="<?php the_title();?>" class="img-fluid img-thumbnail">
                              <?php endif;?>
                        <!-- </div> -->
                  <!-- </div> -->


                  <!-- <div class="col-lg-6 mt-4 mb-4"> -->
                        <!-- <div class="page-text"> -->
                              <?php get_template_part('includes/section', 'content');?>
                        <!-- </div> -->
                  <!-- </div> -->

            </section>
      </div>

</div>
</section>


<?php get_footer();?>