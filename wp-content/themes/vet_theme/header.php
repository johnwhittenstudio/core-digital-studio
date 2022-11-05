<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <div id="wrapper" class="hfeed">
    <div id="opacity-effect" style="display: none;"></div>
    <header id="header">
      <div id="search-box">
        <div id="search-inner">
          <?php get_search_form(); ?>
          <div id="exit-search-btn">
            <i id="exit-search-icon" class="fas fa-times"></i>
          </div>
        </div>
      </div>
      <div id="sticky-header">
        <div class="sticky-section" id="sticky-one">
          <a class="sticky-button" href="/appointment/">
            <i class="fas fa-calendar-day"></i>
            Appointments
          </a>
        </div>
        <div class="sticky-section" id="sticky-two">
          <a class="sticky-button" href="tel:<?php the_author_meta( 'phone' )?>">
            <i style="transform: scaleX(-1);" class="fas fa-phone"></i>
            Call Now
          </a>
        </div>
        <div class="sticky-section" id="sticky-three">
          <a class="sticky-button" href="#" target="_blank" rel="noreferrer">
            <i class="fas fa-prescription-bottle-alt"></i>
            Pharmacy
          </a>
        </div>
      </div>
      <div id="main-header">
        <div id="header-left">
          <a href="/" id="logo-anchor"><img id="header-logo" src="/wp-content/uploads/2020/03/header-logo.png" alt="<?php echo get_bloginfo('name') ?>"></a>
        </div>
        <div id="header-right">
          <div id="header-top">
            <div id="header-top-inner">
              <a class="header-top-buttons" href="tel:<?php the_author_meta( 'phone' )?>"><?php the_author_meta( 'phone' )?></a>
              <a class="header-top-buttons" href="mailto:info@awesomevet.com">info@awesomevet.com</a>
              <a class="header-top-buttons" href="/contact/">Find Us</a>
              <a class="header-top-buttons" id="header-appointment" href="/appointment/">Appointment</a>
            </div>
          </div>
          <nav id="menu">
            <?php wp_nav_menu(
              array(
                'menu' => 'Main Menu',
                'theme_location' => 'Main Menu',
                'menu_id' => 'main-menu'
              )
            ); ?>
            <div id="open-search">
              <i class="fas fa-search"></i>
            </div>
            <div id="hamburger">
              <div class="burger-line"></div>
            </div>
          </nav>
        </div>
      </div>
      <nav id="mobile-menu-container">
        <?php wp_nav_menu(
          array(
            'menu' => 'Primary Menu',
            'theme_location' => 'Primary Menu',
            'menu_id' => 'mobile-menu'
          )
        ); ?>
      </nav>
      <!--<div id="header-social-box">
        <a class="header-social-links" href="#" target="_blank" rel="noopener" aria-label="Facebook logo"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
        <a class="header-social-links" href="#" target="_blank" rel="noopener" aria-label="Instagram logo"><i class="fab fa-instagram" aria-hidden="true"></i></a>
        <a class="header-social-links" href="#" target="_blank" rel="noopener" aria-label="Google logo"><i class="fab fa-google" aria-hidden="true"></i></a>
        <a class="header-social-links" href="#" target="_blank" rel="noopener" aria-label="Nextdoor icon"><i style="transform: scaleX(-1)" class="fas fa-home" aria-hidden="true"></i></a>
      </div>-->
    </header>
  <div id="container">
