<?php if( have_posts() ): while( have_posts() ): the_post();?>



  <?php the_content();?>

  <?php 
  $fname = get_the_author_meta('first_name');
  $lname = get_the_author_meta('last_name');
  ?>

  <p>Posted by <span><i><?php echo $fname;?> <?php echo $lname;?></i></span></p>

  <?php
  $tags = get_the_tags();
  if($tags):
  foreach($tags as $tag):?>

        <a href="<?php echo get_tag_link($tag->term_id);?>" class="btn btn-primary">
            <?php echo $tag->name;?>
        </a>

  <?php endforeach; endif;?>




  <?php
  $categories = get_the_category();
  foreach($categories as $cat):?>

          <a href="<?php echo get_category_link($cat->term_id);?>">
              <?php echo $cat->name;?>
          </a>

  <?php endforeach;?>



  <?php // comments_template();?>

  <p><?php echo get_the_date('l F jS, Y h:i:s');?></p>



<?php endwhile; else: endif;?>