<?php


// Load Stylesheets
function load_css()
{

      wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), false, 'all' );
      wp_enqueue_style('bootstrap');

      wp_register_style('magnific', get_template_directory_uri() . '/css/magnific-popup.css', array(), false, 'all' );
      wp_enqueue_style('magnific');

      wp_register_style('main', get_template_directory_uri() . '/css/main.css', array(), false, 'all' );
      wp_enqueue_style('main');

}
add_action('wp_enqueue_scripts', 'load_css');



// Load Javascript
function load_js()
{
      wp_enqueue_script('jquery');

      wp_register_script('bootstrap',  get_template_directory_uri() . '/js/bootstrap.min.js', 'jquery', false, true);
      wp_enqueue_script('bootstrap');

      wp_register_script('magnific',  get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', 'jquery', false, true);
      wp_enqueue_script('magnific');

      wp_register_script('custom',  get_template_directory_uri() . '/js/custom.js', 'jquery', false, true);
      wp_enqueue_script('custom');

}
add_action('wp_enqueue_scripts', 'load_js');




// Theme Options
add_theme_support('menus');
add_theme_support('post-thumbnails');
add_theme_support('widgets');





// Menus
register_nav_menus(
      array(
            'top-menu' => 'Top Menu Location',
            'mobile-menu' => 'Mobile Menu Location',
            'footer-menu' => 'Footer Menu Location',
      )
);





// Custom Image Sizes
add_image_size('blog-large', 800, 600, false);
add_image_size('blog-small', 300, 200, true);



// Register Sidebars
function my_sidebars()
{

            register_sidebar(

                  array(
                        'name' => 'Page Sidebar',
                        'id' => 'page-sidebar',
                        'before_title' => '<h4 class="widget-title">',
                        'after_title' => '</h4>',
                  )
            );


            register_sidebar(

                  array(
                        'name' => 'Blog Sidebar',
                        'id' => 'blog-sidebar',
                        'before_title' => '<h4 class="widget-title">',
                        'after_title' => '</h4>',
                  )
            );


}
add_action('widgets_init', 'my_sidebars');




function my_first_post_type()
{

      $args = array(

            'labels' => array(

                  'name' => "Cars",
                  'singular_name' => 'Car',
            ),
            'hierarchical' => true,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-images-alt2',
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            // 'rewrite' => array('slug' => 'my-cars'),

      );

      register_post_type('cars', $args);

}
add_action('init', 'my_first_post_type');




function my_first_taxonomy()
{

            $args = array(

                  'labels' => array(
                        'name' => 'Brands',
                        'singular_name' => 'Brand',
                  ),

                  'public' => true,
                  'hierarchical' => false,
            );

            register_taxonomy('brands', array('cars'), $args);


}
add_action('init', 'my_first_taxonomy');




// Custom Forms

add_action('wp_ajax_inquiry', 'inquiry_form');
add_action('wp_ajax_nopriv_inquiry', 'inquiry_form');

function inquiry_form()
{


      if(  !wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) )
      {

            wp_send_json_error('Nonce is incorrect', 401);
            die();

      }


      $formdata = [];

      wp_parse_str($_POST['inquiry'], $formdata);

      // Admin email
      $admin_email = get_option('admin_email');

      // Email headers
      $headers[] = 'Content-Type: text/html; charset=UTF-8';
      $headers[] = 'From: My Website <' . $admin_email . '>';
      $headers[] = 'Reply-to:' . $formdata['email'];
      $headers[] = 'BCC: ' . $formdata['johnwhitten.studio@gmail.com'];

      // Who are we sending the email to?
      $send_to = $admin_email;

      // Subject
      $subject = "Inquiry from" . $formdata['fname'] . '' . $formdata['lname'];

      //Message
      $message = '';

      foreach($formdata as $index => $field)
      {
            $message .= '<strong>' . $index . '</strong>: ' . $field . '<br />';
      }


      try {

            if( wp_mail($send_to, $subject, $message, $headers) )
            {
                  wp_send_json_success('Email sent');
            }
            else {
                  wp_send_json_error('Email error');
            }
      
      } catch (Exception $e)
      {
                  wp_send_json_error($e->getMessage());
      }



      wp_send_json_success( $formdata['fname'] );
}




/**
 * Register Custom Navigation Walker
 */
function register_navwalker(){
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );
add_filter( 'nav_menu_link_attributes', 'bootstrap5_dropdown_fix' );
function bootstrap5_dropdown_fix( $atts ) {
     if ( array_key_exists( 'data-toggle', $atts ) ) {
         unset( $atts['data-toggle'] );
         $atts['data-bs-toggle'] = 'dropdown';
     }
     return $atts;
}

// ---------------------FIGURE OUT HOW TO GET DROPDOWNS TO OPEN ON HOVER--------------------- //


// bootstrap 5 wp_nav_menu walker
class bootstrap_5_wp_nav_menu_walker extends Walker_Nav_menu
{
  private $current_item;
  private $dropdown_menu_alignment_values = [
    'dropdown-menu-start',
    'dropdown-menu-end',
    'dropdown-menu-sm-start',
    'dropdown-menu-sm-end',
    'dropdown-menu-md-start',
    'dropdown-menu-md-end',
    'dropdown-menu-lg-start',
    'dropdown-menu-lg-end',
    'dropdown-menu-xl-start',
    'dropdown-menu-xl-end',
    'dropdown-menu-xxl-start',
    'dropdown-menu-xxl-end'
  ];

  function start_lvl(&$output, $depth = 0, $args = null)
  {
    $dropdown_menu_class[] = '';
    foreach($this->current_item->classes as $class) {
      if(in_array($class, $this->dropdown_menu_alignment_values)) {
        $dropdown_menu_class[] = $class;
      }
    }
    $indent = str_repeat("\t", $depth);
    $submenu = ($depth > 0) ? ' sub-menu' : '';
    $output .= "\n$indent<ul class=\"dropdown-menu$submenu " . esc_attr(implode(" ",$dropdown_menu_class)) . " depth_$depth\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
  {
    $this->current_item = $item;

    $indent = ($depth) ? str_repeat("\t", $depth) : '';

    $li_attributes = '';
    $class_names = $value = '';

    $classes = empty($item->classes) ? array() : (array) $item->classes;

    $classes[] = ($args->walker->has_children) ? 'dropdown' : '';
    $classes[] = 'nav-item';
    $classes[] = 'nav-item-' . $item->ID;
    if ($depth && $args->walker->has_children) {
      $classes[] = 'dropdown-menu dropdown-menu-end';
    }

    $class_names =  join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
    $class_names = ' class="' . esc_attr($class_names) . '"';

    $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
    $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

    $output .= $indent . '<li ' . $id . $value . $class_names . $li_attributes . '>';

    $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
    $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
    $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
    $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

    $active_class = ($item->current || $item->current_item_ancestor || in_array("current_page_parent", $item->classes, true) || in_array("current-post-ancestor", $item->classes, true)) ? 'active' : '';
    $nav_link_class = ( $depth > 0 ) ? 'dropdown-item ' : 'nav-link ';
    $attributes .= ( $args->walker->has_children ) ? ' class="'. $nav_link_class . $active_class . ' dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : ' class="'. $nav_link_class . $active_class . '"';

    $item_output = $args->before;
    $item_output .= '<a' . $attributes . '>';
    $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
}
// register a new menu
register_nav_menu('main-menu', 'Main menu');

// ---------------------FIGURE OUT HOW TO GET DROPDOWNS TO OPEN ON HOVER--------------------- //





// Override WordPress Mailer to send to personal email and not WordPress Server
// Or Try the "WP Mail SMTP Plugin"


// add_action('phpmailer_init', 'custom_mailer');
// function custom_mailer( PHPMailer $phpmailer )
// {

//       $phpmailer->SetFrom('johnwhitten.studio@gmail.com', 'John Whitten');
//       $phpmailer->Host = 'email-smtp.us-west-2.amazonaws.com';
//       $phpmailer->Port = 587;
//       $phpmailer->SMTPAuth = true;
//       $phpmailer->SMTPSecure = 'tls';
//       $phpmailer->Username = SMTP_LOGIN;
//       $phpmailer->Password = SMTP_PASSWORD;
//       $phpmailer->IsSMTP();

// }


function my_shortcode($atts, $content = null, $tag = '')
{

      print_r($atts);

      ob_start();
      
      get_template_part('includes/latest', 'cars');
      
      return ob_get_clean();

}
add_shortcode('latest_cars', 'my_shortcode');








// ---------------------CUSTOM FUNCTIONS BELOW---------------------


function welcome_section($atts) {
	ob_start();
	$data = shortcode_atts(array(
		'video_on_off' => 'on'
	),$atts);
	?>
  <div id="hero-container" <?php if ($data['video_on_off'] == 'off') { ?> style="background-image: url('/wp-content/uploads/2022/10/46.2496°-N-122.1369°-W_sm.jpg');" <?php } ?>>
    <?php if ($data['video_on_off'] == 'on') { ?>
      <div id="video-text">
        <h1 class="video-thin-text">Welcome</h1>
        <br>
        <h1 class="video-bold-text">to</h1>
        <br>
        <h1 id="video-italics-text">CORE</h1>
        <h1 id="video-italics-text"><span class="video-underline">Digital</span> Studio</span></h1>
      </div>
      <div style="width: 100vw" class="wp-video">
        <div id="video-overlay"></div>
        <video muted autoplay="autoplay" loop class="wp-video-shortcode" id="video-697-2" width="100%" height="100%" preload="metadata"><source type="video/mp4" src="/wp-content/uploads/2022/11/stock-video-hd.mp4" /><a href="/wp-content/uploads/2022/11/stock-video-hd.mp4">/wp-content/uploads/2022/11/stock-video-hd.mp4</a></video>
      </div>
    <?php } ?>
	</div>
	<div id="welcome-section">
		<div id="three-callouts">
			<a href="/course/overview" style="background-image: url('/wp-content/uploads/2022/11/course-overview.jpg')" class="callout">
				<div class="callout-text">
					<h3>Guiding and Informative</h3>
					<h2>Course<br>Content</h2>
				</div>
			</a>
			<a href="/lessons" style="background-image: url('/wp-content/uploads/2022/11/lessons.jpg')" class="callout">
				<div class="callout-text">
					<h3>Inspiring and Instructive</h3>
					<h2>Weekly<br>Lessons</h2>
				</div>
			</a>
			<a href="/assignments/projects" style="background-image: url('/wp-content/uploads/2022/11/projects.jpg')" class="callout">
				<div class="callout-text">
					<h3>Exciting and Challenging</h3>
					<h2>Creative<br>Projects</h2>
				</div>
			</a>
		</div>
		<div id="about-area">
			<div class="inner-wrapper">
				<div class="about-section" id="first-about-section">
					<div id="about-intro" class="about-animation-text">
						<h3>Welcome to</h3>
						<h2>CORE<br>Digital Studio</h2>
						<!-- <h2><?php echo get_bloginfo('name') ?></h2> -->
						<h3>An Artistic Exploration<br>of Digital Tools</h3>
                                    <i class="fas fa-lg fa-photo-film" style="color: #95d9f1"></i>
                                    <i class="fas fa-lg fa-cube" style="color: #95d9f1"></i>
                                    <i class="fas fa-lg  fa-vr-cardboard" style="color: #95d9f1"></i>
                                    <i class="fas fa-lg fa-video" style="color: #95d9f1"></i>

					</div>
					<!-- <img id="icons-image" src="/wp-content/uploads/2020/04/animal-icons.png" alt="Animal Icons"> -->
                              <div id="about-body" class="about-animation-text">
                                    <p class="about-section-text about-animation-text">An introductory studio art class using a variety of software to create expressive artworks. Project-based exploration of screen-based design, digital imaging and image manipulation, screen and print-based layout design, 3-D rendering and prototype modeling, and video and audio editing. Examination of the impact of digital technology on the visual arts from contemporary and historical perspectives.</p>
                              </div>
				</div>
				<!-- <div class="about-section">
					<p class="about-section-text about-animation-text">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div> -->
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('welcome_section','welcome_section');


function homepage_services_jw($atts) {
	ob_start();
	$data = shortcode_atts(array(
		'headline' => "Media Literacy & Critical Concepts",
		'text' => "When we learn to consume media critically, we learn how to research a particular subject and form our own opinions about that subject, which we can use to make informed decisions and solve problems. A main emphasis of this course is learning how to develop the skills to be both critical consumers and responsible creators of digital media. Throughout this course, we explore a variety of related topics. Here's a sample. Click below to see more.",
		'img' => '/wp-content/uploads/2022/11/pixel-hand.jpg',
		'button-text' => 'View all course topics',
		'button-link' => "/topics",
	),$atts);
	?>
	<div id="hp-j-services">
		<div class="jserv-inner-wrapper">
			<div class="hp-jserv-fifty-percent-left">
				<div id="hp-jservices-intro">
					<div id="hp-jservices-intro-container">
						<h2 id="hp-jservices-headline"><?php echo $data['headline']?></h2>
					</div>
					<p id="hp-jservices-text"><?php echo $data['text'] ?></p>
					<a href=<?php echo $data['button-link'] ?>>
						<div class="pink-button">
							<?php echo $data['button-text'] ?>
						</div>
					</a>
					<img id="jserv-image" src="<?php echo $data['img'] ?>" alt="">
                        </div>
			</div>
				<div id="jserv-services">
					<?php
					$args = array(
						'category_name' => 'Services',
						'posts_per_page' => '8'
					);
					$posts_array = new WP_Query($args);
					if ( $posts_array -> have_posts() ) :
						while ( $posts_array -> have_posts() ) :
							$posts_array -> the_post();
							$id = get_the_ID();
							$img = get_the_post_thumbnail_url($id);
							$link = get_permalink();
							$title = get_the_title();
						?>
						<a href="<?php echo $link ?>" class="hp-j-service-box">
							<div class="hp-j-services-container">
								<div class="hp-j-inner-service-box">
                                                      <div class="jservice-ribbon"></div>
                                                      <div class="hp-j-inner-service-box-overlay">
                                                            <img class="service-image" src="<?php echo $img ?>" alt="<?php echo $title ?>">
                                                      </div>
									<div class="j-service-title-box">
									<!-- <i class=" fas fa-paw"></i> -->
										<h2><?php echo $title ?></h2>
									</div>
								</div>
							</div>	
						</a>
					<?php endwhile; endif; ?>
				</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('homepage_services_jw','homepage_services_jw');


function subpage_services_jw($atts) {
	ob_start();
	$data = shortcode_atts(array(
		'headline' => "Course Topics",
		'text' => "Digital Art has expanded the definition of art and increased the accessibility of art to the world. It is about the tools used to create it, as well as the vision, message, and context of its creation. This course aims to survey technology’s influence on art and culture from a historical and contemporary perspective. Below are the topics covered in this course.",
	),$atts);
	?>
	<div class="sp-j-services-main-container">
		<div id="sp-j-services-intro-container">
			<h2 id="sp-j-services-headline"><?php echo $data['headline']?></h2>
		</div>
		<p id="sp-j-services-text"><?php echo $data['text'] ?></p>
		<div id="sp-j-services">
			<?php
				$args = array(
					'category_name' => 'Topics',
					'posts_per_page' => '-1'
				);
				$posts_array = new WP_Query($args);
				if ( $posts_array -> have_posts() ) : while ( $posts_array -> have_posts() ) :$posts_array -> the_post();
						$id = get_the_ID();
						$img = get_the_post_thumbnail_url($id);
						$link = get_permalink();
						$title = get_the_title();
						$content = wp_trim_words(get_the_content(), 15);
				?>
				<a href="<?php echo $link ?>" class="sp-j-service-box">
					<div class="sp-j-services-container">
						<div class="sp-j-inner-service-box">
							<div class="service-ribbon"></div>
							<img class="service-image" src="<?php echo $img ?>" alt="<?php echo $title ?>">
							<div class="sp-j-service-content">
								<p><?php echo $content ?></p>
								<p><strong>READ MORE</strong></p>
							</div>
						</div>
					</div>
					<div class="j-service-title-box">
						<h2><?php echo $title ?></h2>
					</div>
				</a>
			<?php endwhile; endif; ?>
			</div>
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('subpage_services_jw','subpage_services_jw');

function contact_us($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'phone' => "tel:615-887-3367",
            'email' => "mailto:johnwhitten.studio@gmail.com",
            'instagram' => "https://www.instagram.com/john.whitten/?hl=en",
            'facebook' => "https://www.facebook.com/johnwhittenstudio/",
            'linkedin' => "https://www.linkedin.com/in/johnwhittenstudio/",
            'github' => "https://github.com/johnwhittenstudio",
      ),$atts);
	?>
	<div id="contact-us">
		<div id="contact-map">
			<iframe title="Map of <?php echo get_bloginfo('name') ?>" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d86416.51154363637!2d-122.71404574429876!3d45.51512922054555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54950b0b7da97427%3A0x1c36b9e6f6d18591!2sPortland%2C%20OR!5e0!3m2!1sen!2sus!4v1668028196386!5m2!1sen!2sus" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
		</div>
		<div id="contact-info">
			<div class="visit-us">
                        <h1>John Whitten</h1>
                        <!-- <h2><?php echo get_bloginfo('name') ?></h2> -->
                        <h3>Portland, Oregon</h3>
                        <br>
                        <p>I'd love to hear from you</p>
                        <!-- <br> -->
                        <p><i class="fa-solid fa-phone"></i>&nbsp;&nbsp;
                              <a href="tel:615-887-3367">615-887-3367 </a><br>
                              <i class="fa-solid fa-envelope"></i>&nbsp;&nbsp;
                              <a href="mailto:johnwhitten.studio@gmail.com">johnwhitten.studio@gmail.com</a></p>
                        <div class="newsletter">
                              <!-- <?php echo do_shortcode('[gravityform id="3" title="false" description="false"]')?> -->
                        </div>
                        <div class="social-icons">
                              <a href=<?php echo $data['facebook'] ?> target="_blank" rel="noopener">
                                    <i class="fab fa-facebook-f"></i>
                              </a>
                              <a href=<?php echo $data['instagram'] ?>target="_blank" rel="noopener">
                                    <i class="fab fa-instagram fa-lg"></i>
                              </a>
                              <a href=<?php echo $data['linkedin'] ?> target="_blank" rel="noopener">
                                    <i class="fab fa-linkedin fa-lg"></i>
                              </a>
                              <a href=<?php echo $data['github'] ?> target="_blank" rel="noopener">
                                    <i class="fab fa-github fa-lg"></i>
                              </a>
                        </div>
                  </div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('contact_us','contact_us');

function appointment_area($atts) {
	ob_start();
	$data = shortcode_atts(array(
		'background_image' => '/wp-content/uploads/2022/11/vr-crowd.jpeg',
		'margin_top' => '-5%',
	),$atts);
	?>
	<div class="appointment-area" style="background-image: url('<?php echo $data['background_image']?>'); margin-top: <?php echo $data['margin_top']?>;">
		<div class="inner-wrapper">
			<div class="appointment-container" <?php if ( is_front_page() ) { ?> id="hp-appointment-container" <?php } else { ?> id="sp-appointment-container" <?php } ?>>
				<h2>Make an appointment<br>with us today!</h2>
				<div class="appointment-box">
					<a href="tel:<?php the_author_meta( 'phone' )?>" class="blue-button-container">
						<div class="dark-blue-button">
							Call us! <?php the_author_meta( 'phone' )?>
						</div>
					</a>
					<a href="/appointment/" class="blue-button-container">
						<div class="dark-blue-button">
							Book online today
						</div>
					</a>
				</div>
			</div>
		</div>
    <svg version="1.1" id="Layer_1" class="appt-bottom-divider" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
       viewBox="0 0 1600 82" style="enable-background:new 0 0 1600 82;" xml:space="preserve">
    <path d="M0,1.5L1595.6,82H-2L0,1.5z"/>
    </svg>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('appointment_area','appointment_area');

function about_page($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'bio-pic' => '/wp-content/uploads/2022/11/John-and-Katherine-DSC_4311-web.jpg',
            'bio-1' => "John Whitten (he/him) is a visual artist, designer, web developer, and educator. His artist practice is rooted in both traditional drawing, sculpture, and digital media. His artwork aims to excavate the philosophical significance of what it means to wander through the signals and noise enveloping our world. He has recently exhibited in New York City, Los Angeles, and Portland, and held gallery representation at Charles Hartman Fine Art in Portland from 2017 until the gallery closed in 2022. He is a co-founding director of the Thunderstruck Collective artist residency. He also co-founded the Portland-based artist-collective galleries Carnation Contemporary in 2018 and Well Well Projects in 2021. He earned his MFA in Painting and Drawing from the University of Oregon in Eugene, Oregon and his BFA in Studio Fine Art from Watkins College of Art in Nashville, Tennessee.",
            'bio-2' => "His experience in education includes 7 years as a Full-time Instructor of Digital Art & Design, Experimental Animation and Video, and Media Aesthetics at Oregon State University teaching over 100 term-long courses both in-person and online, 2 years teaching Drawing I at Linn-Benton Community College, 1 year teaching Digital Photography at Clark College, 3 years teaching undergraduate courses in Drawing and Time-Based  Media as a Graduate Teaching Fellow at the University of Oregon, 15 years teaching arts-oriented adult  continuing education and youth pre-college programs, summer camps, and workshops, and 5 years as  an admissions recruiter, academic counselor, and portfolio reviewer for a NASAD accredited college of  art.",
            'bio-3' => "John is a husband, cat parent, storyteller, coffee lover, and cook. His home and studio are located in Portland, Oregon.",
	),$atts);
	?>
	<div id="about-page">
	      <div class="about-container<?php if ($remainder == 0) { ?> odd-background<?php } ?>">
                  <div class="about-container-inner">
                        <div class="about-photo">
                        <img id="about-bio-pic" src="<?php echo $data['bio-pic'] ?>" alt="">
                        </div>
          <div class="about-text">
          <br>
            <p><?php echo $data['bio-1'] ?></p><br>
            <p><?php echo $data['bio-2'] ?></p><br>
            <p><?php echo $data['bio-3'] ?>
            His visual artwork can be viewed at <a href="https://www.johnwhitten.com/" class="visual-art-website">www.johnwhitten.com</a><span>.</span></p>
            
          </div>
        </div>
	</div>
	
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('about_page','about_page');

function philosophy_page($atts) {
	ob_start();
      $data = shortcode_atts(array(
            // 'bio-pic' => '/wp-content/uploads/2022/11/John-and-Katherine-DSC_4311-web.jpg',
            'philosophy-1-img' => '/wp-content/uploads/2022/11/accessibility-icon-p.png',

            'philosophy-1' => "John Whitten wants to excite students to be engaged creators of visual culture in the world instead of passive  consumers. He believes in the power of nurturing individual creativity to ignite self-expression and transform  the way people engage in their lives, families, and communities. It is his viewpoint that everyone should  have equal access to education and opportunities for self-advancement. His technical instruction emphasizes intention and experimentation and covers an array of cutting-edge, industry-level as well as free, open-source software to ensure students learn the tools needed to be competitive in today's market while maintaining a sustainable, agile set of skills.",

            'philosophy-2-img' => '/wp-content/uploads/2022/11/diversity-icon-p.png',

            'philosophy-2' => "Diversity and difference are not  only anticipated in his classroom, but are cherished. An inclusive culture of respect that honors the rights,  safety, dignity, and worth of every individual is essential to the success of any learning environment.  John recognizes and strives to be conscious of his privilege, and is committed to creating a culturally-responsive, anti-racist, and accessible space that is  free of discrimination and bias. His classroom is a laboratory for experimentation and a platform for  expression, which offers a space of open dialogue wherein he emphasizes the importance of learning to  read, grapple with, and critically discuss an idea, image or object rather than coming to an easy judgment.",

            'philosophy-3-img' => '/wp-content/uploads/2022/11/strategy-icon-p.png',

            'philosophy-3' => "His goal is to  teach students strategies of thinking and engaging with ideas, materials, and processes so they may  formulate conceptual objectives and apply critical analysis to future work. His hope is that students gain  both skills and confidence by being afforded the opportunity to experiment and push the parameters of  an assignment in a setting that values failure as part of the learning process. Ideally, students will create  a portfolio that excites an exploration of ideas and interests, serves as a means of personal expression,  and stimulates a larger ongoing artistic practice. As an exhibiting artist and gallery director, he aims to showcase the many ways artists connect with their community.",
	),$atts);
	?>
	<div id="philosophy-page">
	      <div class="philosophy-container<?php if ($remainder == 0) { ?> odd-background<?php } ?>">
                  <div class="philosophy-container-inner">

                        <div class="philosophy-card">
                              <div class="philosophy-img">
                                    <img id="philosophy-access-pic" src="<?php echo $data['philosophy-1-img'] ?>" alt="">
                              </div>
                              <div class="philosophy-text">
                                    <p><?php echo $data['philosophy-1'] ?></p>
                              </div>
                        </div>

                        <div class="philosophy-card">
                              <div class="philosophy-img">
                                    <img id="philosophy-diversity-pic" src="<?php echo $data['philosophy-2-img'] ?>" alt="">
                              </div>
                              <div class="philosophy-text">
                                    <p><?php echo $data['philosophy-2'] ?></p>
                              </div>
                        </div>


                        <div class="philosophy-card">
                              <div class="philosophy-img" >
                                    <img id="philosophy-strategy-pic"  src="<?php echo $data['philosophy-3-img'] ?> " alt="">
                              </div>
                              <div class="philosophy-text">
                                    <p><?php echo $data['philosophy-3'] ?></p>
                              </div>
                        </div>

                  </div>
	      </div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('philosophy_page','philosophy_page');




function weekly_lesson_page_1($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => 'Brief Intro to Digital Art • Product Placement',
            'week-1-intro-body' => "This week we will go over course policies, procedures, and resources relevant to taking this course, such as participation and project submission. Additionally, we will begin researching and reflecting critically and theoretically on aspects of media’s impact on our current daily lives. Personal and classmates’ ideation will be supported through peer feedback. We will also begin to identify and relate theoretical concepts connecting digital art with art historical movements such as Dada, Conceptual Art, and Fluxus.",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => "Identify ART121 course policies, procedures, and resources relevant to taking this course, such as participation and project submission.",
            'week-1-learning-objective-2' => "Recognize and reflect critically and theoretically on aspects of media’s impact on our current daily lives.",
            'week-1-learning-objective-3' => "Identify and relate theoretical concepts connecting digital art with art historical movements such as Dada, Conceptual Art, and Fluxus.",
            'week-1-learning-objective-4' => "Begin acquiring skills necessary using the computer as a tool to create conceptually interesting digital-based projects. Tools include: idea generation, time planning, review, storage, manipulation, commitment, failure, revision, and patience.",
            
            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Meet your classmates in the Introduction Discussion.",
            'week-1-task-2' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-3' => "Read the Culture Jam Introduction.",
            'week-1-task-4' => "Participate in the Discussion: Product Placement.",
            'week-1-task-5' => "Complete the Practice Exercise: Cute Puppies Glitch.",
            'week-1-task-6' => "Finish the Week 1 - Homework & Quiz.",
            'week-1-task-7' => "Complete the Syllabus Quiz.",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_1','weekly_lesson_page_1');




function weekly_lesson_page_2($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-2-img' => '/wp-content/uploads/2022/11/Week-2.png',

            'week-2-header' => "Week 2 - Overview",
            'week-2-intro-header' => 'DIGITAL TECHNOLOGIES AS A TOOL',
            'week-2-intro-body' => "This week we will go over how digital technologies can be used as a tool in the artmaking process. We will continue to look at artists working with different methods of culture jamming with a focus on Hank Willis Thomas.",

            'week-2-learning-head' => 'Weekly Learning Objectives',
            'week-2-learning-body' => "After successful completion of this week, you will be able to:",
            'week-2-learning-objective-1' => "Recognize and reflect critically and theoretically on aspects of the History of Logos and the ways Digital Technologies can be used as a Tool.",
            'week-2-learning-objective-2' => "Identify and relate theoretical concepts connecting digital art with the history of technology as well as contemporary artists using digital technologies in various stages in the development of their art.",
            'week-2-learning-objective-3' => "Continue building and refining skills necessary using the computer as a tool to create conceptually interesting digital-based projects. Tools include: idea generation, time planning, review, storage, manipulation, commitment, failure, revision, and patience.",

            'week-2-task-head' => "Task List",
            'week-2-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-2-task-1' => "Work through the material on the Week 2 – Learning Content page.",
            'week-2-task-2' => "Complete the Assignment: Working with Layers.",
            'week-2-task-3' => "Participate in the Discussion: Culture Jam Research and Ideation.",
            'week-2-task-4' => "Finish the Week 2 – Homework + Quiz.",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-2-img"  src="<?php echo $data['week-2-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-2-header'] ?></h1><br>
                        <h2><?php echo $data['week-2-intro-header'] ?></h2>
                              <p><?php echo $data['week-2-intro-body'] ?></p>              
                  <h3><?php echo $data['week-2-learning-head'] ?></h3>
                        <p><?php echo $data['week-2-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-2-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-2-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-2-learning-objective-3'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-2-task-head'] ?></h3>
                        <p><?php echo $data['week-2-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-2-task-1'] ?></li>
                                    <li><?php echo $data['week-2-task-2'] ?></li>
                                    <li><?php echo $data['week-2-task-3'] ?></li>
                                    <li><?php echo $data['week-2-task-4'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_2','weekly_lesson_page_2');




function weekly_lesson_page_3($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-3-img' => '/wp-content/uploads/2022/11/Week-3.png',

            'week-3-header' => "Week 3 - Overview",
            'week-3-intro-header' => 'FINISH PROJECT 1: CULTURE JAM • BEGIN PROJECT 2: VIDEO REMIX',
            'week-3-intro-body' => "This week we will wrap up Project 1: Culture Jam, and introduce Project 2: Video Remix. We will use the video Everything is a Remix in order to introduce ourselves to the history of remixing, as well as dissect the fundamental building blocks of creativity: copy, combine, and transform. We will also begin to look at a variety of artists who are using a wide array of methods to remix found content.",

            'week-3-learning-head' => 'Weekly Learning Objectives',
            'week-3-learning-body' => "After successful completion of this week, you will be able to:",
            'week-3-learning-objective-1' => "Recognize and reflect critically and theoretically on aspects of the fundamental building blocks of creativity: copy, combine, and transform",
            'week-3-learning-objective-2' => "Identify and relate theoretical concepts connecting remixing with its impact on popular culture, media, and artmaking.",
            'week-3-learning-objective-3' => "Acquire and refine skills necessary using the computer as a tool to create conceptually interesting digital-based projects. Tools include: saving photoshop project using multiple formats, video editing, digital file organization, idea generation, time planning, review, storage, manipulation, commitment, failure, revision, and patience.",

            'week-3-task-head' => "Task List",
            'week-3-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-3-task-1' => "Work through the material on the Week 3 – Learning Content page.",
            'week-3-task-2' => "Complete Project 1 – Culture Jam.",
            'week-3-task-3' => "Participate in the Peer Review Discussion: Culture Jam.",
            'week-3-task-4' => "Participate in the Discussion: Video Remix.",
            'week-3-task-5' => "Finish the Week 3 – Homework + Quiz.",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-3-img"  src="<?php echo $data['week-3-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-3-header'] ?></h1><br>
                        <h2><?php echo $data['week-3-intro-header'] ?></h2>
                              <p><?php echo $data['week-3-intro-body'] ?></p>              
                  <h3><?php echo $data['week-3-learning-head'] ?></h3>
                        <p><?php echo $data['week-3-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-3-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-3-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-3-learning-objective-3'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-3-task-head'] ?></h3>
                        <p><?php echo $data['week-3-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-3-task-1'] ?></li>
                                    <li><?php echo $data['week-3-task-2'] ?></li>
                                    <li><?php echo $data['week-3-task-3'] ?></li>
                                    <li><?php echo $data['week-3-task-4'] ?></li>
                                    <li><?php echo $data['week-3-task-5'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_3','weekly_lesson_page_3');




function weekly_lesson_page_4($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-4-img' => '/wp-content/uploads/2022/11/Week-4.png',

            'week-4-header' => "Week 4 - Overview",
            'week-4-intro-header' => 'GLITCH',
            'week-4-intro-body' => "This week we will continue to look at examples of video remixes from an array of sources. We will also focus on glitch art, which can be intentionally instigated or a found disruption. We will also complete a quick exercise on how to glitch various files using various software.",

            'week-4-learning-head' => 'Weekly Learning Objectives',
            'week-4-learning-body' => "After successful completion of this week, you will be able to:",
            'week-4-learning-objective-1' => "Glitch an image file using Audacity",
            'week-4-learning-objective-2' => "Glitch a video file using Audacity",
            'week-4-learning-objective-3' => "Recognize and reflect critically and theoretically on aspects of glitch art.",
            'week-4-learning-objective-4' => "Identify and relate theoretical concepts connecting digital art with the history of technology as well as contemporary artists using digital technologies in various stages in the development of their art.",
            'week-4-learning-objective-5' => "Continue building and refining skills necessary using the computer as a tool to create conceptually interesting digital-based projects. Tools include: corrupting data code, video editing, idea generation, time planning, review, storage, manipulation, commitment, failure, revision, and patience.",

            'week-4-task-head' => "Task List",
            'week-4-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-4-task-1' => "Work through the material on the Week 4 – Learning Content page.",
            'week-4-task-2' => "Participate in the Week 4 – Video Remix Research & Ideation Discussion.",
            'week-4-task-3' => "Complete the Practice Exercise: Glitch Photo & Video.",
            'week-4-task-4' => "Finish the Week 4 - Homework & Quiz.",
            'week-4-task-5' => "Continue working on Project 2 – Video Remix (Due Week 5)",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-4-img"  src="<?php echo $data['week-4-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-4-header'] ?></h1><br>
                        <h2><?php echo $data['week-4-intro-header'] ?></h2>
                              <p><?php echo $data['week-4-intro-body'] ?></p>              
                  <h3><?php echo $data['week-4-learning-head'] ?></h3>
                        <p><?php echo $data['week-4-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-4-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-4-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-4-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-4-learning-objective-4'] ?></li>
                                    <li><?php echo $data['week-4-learning-objective-5'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-4-task-head'] ?></h3>
                        <p><?php echo $data['week-4-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-4-task-1'] ?></li>
                                    <li><?php echo $data['week-4-task-2'] ?></li>
                                    <li><?php echo $data['week-4-task-3'] ?></li>
                                    <li><?php echo $data['week-4-task-4'] ?></li>
                                    <li><?php echo $data['week-4-task-5'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_4','weekly_lesson_page_4');




function weekly_lesson_page_5($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-5-img' => '/wp-content/uploads/2022/11/Week-5.png',

            'week-5-header' => "Week 5 - Overview",
            'week-5-intro-header' => 'APPROPRIATION',
            'week-5-intro-body' => "This week we will consider Appropriation as an artistic tool and its place in the creative process. We will look at artists such as John Baldessari, Brian Jungen, and Richard Prince in order to consider both the positive and negative impacts appropriation for art’s sake has on our culture. There will be a discussion post asking you to consider your own thoughts on the topics of Appropriation vs Originality.",

            'week-5-learning-head' => 'Weekly Learning Objectives',
            'week-5-learning-body' => "After successful completion of this week, you will be able to:",
            'week-5-learning-objective-1' => "Identify artists working with appropriation as an artistic tool, and reflect on how appropriation impacts our culture and various artistic practices.",
            'week-5-learning-objective-2' => "Successfully complete and export a video using Adobe Premiere.",


            'week-5-task-head' => "Task List",
            'week-5-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-5-task-1' => "Work through the material on the Week 5 – Learning Content page.",
            'week-5-task-2' => "Participate in the Discussion: Appropriation vs Originality.",

	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-5-img"  src="<?php echo $data['week-5-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-5-header'] ?></h1><br>
                        <h2><?php echo $data['week-5-intro-header'] ?></h2>
                              <p><?php echo $data['week-5-intro-body'] ?></p>              
                  <h3><?php echo $data['week-5-learning-head'] ?></h3>
                        <p><?php echo $data['week-5-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-5-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-5-learning-objective-2'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-5-task-head'] ?></h3>
                        <p><?php echo $data['week-5-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-5-task-1'] ?></li>
                                    <li><?php echo $data['week-5-task-2'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_5','weekly_lesson_page_5');




function weekly_lesson_page_6($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => ' ',
            'week-1-intro-body' => " ",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => " ",
            'week-1-learning-objective-2' => " ",
            'week-1-learning-objective-3' => " ",
            'week-1-learning-objective-4' => " ",

            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-2' => " ",
            'week-1-task-3' => " ",
            'week-1-task-4' => " ",
            'week-1-task-5' => " ",
            'week-1-task-6' => " ",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_6','weekly_lesson_page_6');




function weekly_lesson_page_7($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => ' ',
            'week-1-intro-body' => " ",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => " ",
            'week-1-learning-objective-2' => " ",
            'week-1-learning-objective-3' => " ",
            'week-1-learning-objective-4' => " ",

            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-2' => " ",
            'week-1-task-3' => " ",
            'week-1-task-4' => " ",
            'week-1-task-5' => " ",
            'week-1-task-6' => " ",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_7','weekly_lesson_page_7');





function weekly_lesson_page_8($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => ' ',
            'week-1-intro-body' => " ",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => " ",
            'week-1-learning-objective-2' => " ",
            'week-1-learning-objective-3' => " ",
            'week-1-learning-objective-4' => " ",

            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-2' => " ",
            'week-1-task-3' => " ",
            'week-1-task-4' => " ",
            'week-1-task-5' => " ",
            'week-1-task-6' => " ",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_8','weekly_lesson_page_8');





function weekly_lesson_page_9($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => ' ',
            'week-1-intro-body' => " ",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => " ",
            'week-1-learning-objective-2' => " ",
            'week-1-learning-objective-3' => " ",
            'week-1-learning-objective-4' => " ",

            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-2' => " ",
            'week-1-task-3' => " ",
            'week-1-task-4' => " ",
            'week-1-task-5' => " ",
            'week-1-task-6' => " ",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_9','weekly_lesson_page_9');




function weekly_lesson_page_10($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => ' ',
            'week-1-intro-body' => " ",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => " ",
            'week-1-learning-objective-2' => " ",
            'week-1-learning-objective-3' => " ",
            'week-1-learning-objective-4' => " ",

            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-2' => " ",
            'week-1-task-3' => " ",
            'week-1-task-4' => " ",
            'week-1-task-5' => " ",
            'week-1-task-6' => " ",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_10','weekly_lesson_page_10');




function weekly_lesson_page_11($atts) {
	ob_start();
      $data = shortcode_atts(array(
            'week-1-img' => '/wp-content/uploads/2022/11/Week-1.png',

            'week-1-header' => "Week 1 - Overview",
            'week-1-intro-header' => ' ',
            'week-1-intro-body' => " ",

            'week-1-learning-head' => 'Weekly Learning Objectives',
            'week-1-learning-body' => "After successful completion of this week, you will be able to:",
            'week-1-learning-objective-1' => " ",
            'week-1-learning-objective-2' => " ",
            'week-1-learning-objective-3' => " ",
            'week-1-learning-objective-4' => " ",

            'week-1-task-head' => "Task List",
            'week-1-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-1-task-1' => "Work through the material on the Week 1 – Learning Content page.",
            'week-1-task-2' => " ",
            'week-1-task-3' => " ",
            'week-1-task-4' => " ",
            'week-1-task-5' => " ",
            'week-1-task-6' => " ",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-1-img"  src="<?php echo $data['week-1-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-1-header'] ?></h1><br>
                        <h2><?php echo $data['week-1-intro-header'] ?></h2>
                              <p><?php echo $data['week-1-intro-body'] ?></p>              
                  <h3><?php echo $data['week-1-learning-head'] ?></h3>
                        <p><?php echo $data['week-1-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-1-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-1-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-1-task-head'] ?></h3>
                        <p><?php echo $data['week-1-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-1-task-1'] ?></li>
                                    <li><?php echo $data['week-1-task-2'] ?></li>
                                    <li><?php echo $data['week-1-task-3'] ?></li>
                                    <li><?php echo $data['week-1-task-4'] ?></li>
                                    <li><?php echo $data['week-1-task-5'] ?></li>
                                    <li><?php echo $data['week-1-task-6'] ?></li>
                                    <li><?php echo $data['week-1-task-7'] ?></li>
                              </ul>
            </div>
      </section>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('weekly_lesson_page_11','weekly_lesson_page_11');