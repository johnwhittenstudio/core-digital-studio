<?php get_header(); ?>
<main id="content">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'entry' ); ?>
<?php endwhile; endif; ?>
</main>
<?php echo do_shortcode('[appointment_area background_image="/wp-content/uploads/2022/01/Dalmatian-services-cta-bg-e1641246613649.png" margin_top="0px"]') ?>

<?php get_footer(); ?>
