<?php get_header();?>

<section class="page-wrap">
<div class="container">

<h1><?php the_title();?></h1>


      <?php if(has_post_thumbnail()):?>
          <div class="gallery">
            <a href="<?php the_post_thumbnail_url('blog-large');?>">
              <img src="<?php the_post_thumbnail_url('blog-large');?>" alt="<?php the_title();?>" class="img-fluid mb-3 img-thumbnail">
            </a>
          </div>
      <?php endif;?>



              <!-- With ACF Pro version to get image gallery to display -->

              <?php
              $gallery = get_field('gallery');
              if($gallery):?>

                  <div class="gallery mb-5">

                      <?php foreach($gallery as $image):?>

                      <a href="<?php echo $image['sizes']['blog-large'];?>">
                        <img src="<?php echo $image['sizes']['blog-small'];?>" class="img-fluid img-thumbnail">

                      <?php endforeach;?>

                  </div>
              
              <?php endif;?>

              <!-- With ACF Pro version to get image gallery to display -->



      <div class="row">

          <div class="col-lg-6">

                <?php get_template_part('includes/section', 'cars');?>
                <?php wp_link_pages();?>

          </div>

          <div class="col-lg-6">

              <?php get_template_part('includes/form', 'inquiry');?>


              <ul>
                
                <li>
                  <b>Price:</b> $<?php the_field('price');?>
                </li>
                <li>
                  Color: <?php the_field('color');?>
                </li>
                <li>
                  Registration: <?php the_field('registration');?>
                </li>
                
              
              </ul>



          </div>



      </div>


</div>
</section>


<?php get_footer();?>