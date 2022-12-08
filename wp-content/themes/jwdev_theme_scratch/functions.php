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
			<a href="/lessons/week-1" style="background-image: url('/wp-content/uploads/2022/11/lessons.jpg')" class="callout">
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


function course_overview($atts) {
	ob_start();
	$data = shortcode_atts(array(
            'banner_img' => '/wp-content/uploads/2022/11/CoreDigitalStudiobanner.gif',
		'headline' => "Welcome to Core: Digital Studio!",
		'intro_1' => "I look forward to working with you this term. To be successful in this course, please plan to log in to participate on several days each week.", 
            'intro_2' => "Core: Digital Studio is a hybrid course. We will often meet in person in the computer lab and remotely via Zoom. There will be ongoing learning activities in Canvas that require your participation. This course is designed to include student-content, student-student, and student-instructor interaction.",
            'intro_3' => "Please be sure to read the syllabus and other information linked below, as these essential documents contain the answers to many frequently asked questions.",
            'technical_header' => "Technical Assistance",
            'technical_body' => "If you experience computer difficulties, need help downloading a browser or plug-in, assistance logging into the course, or if you experience any errors or problems while in your online course, contact the OSU Help Desk for assistance. You can call (541) 737-3474, email osuhelpdesk@oregonstate.edu or visit the OSU Computer Helpdesk online.",
	),$atts);
	?>
	<div class="course-overview-main-container">
		<div id="course-overview-intro-container">
                  <img id="course-overview-image" src="<?php echo $data['banner_img'] ?>" alt="">
			<h2 id="course-overview-headline"><?php echo $data['headline']?></h2>
                  <p id="course-overview-text"><?php echo $data['intro_1'] ?></p>
                  <p id="course-overview-text"><?php echo $data['intro_2'] ?></p>
                  <p id="course-overview-text"><?php echo $data['intro_3'] ?></p>

                  <div class="overview-button-container">
                        <a href='https://docs.google.com/document/d/1f7vO6D3-BhW7rCC6Xl-iU4O3hPgfvw39Td98QfT4jq0/edit?usp=sharing' target='_blank' class="btn" role="button" aria-disabled="true">Syllabus</a>
                        <a href="https://docs.google.com/document/d/1wp38X7InjTG0rNGzKmpTwKVIQprT7kh97GR2IGHsddE/edit?usp=sharing" class="btn" role="button" aria-disabled="true">Schedule</a>
                        <a href="/course/topics" class="btn" role="button" aria-disabled="true">Topics</a>
                        <a href="/course/policies" class="btn" role="button" aria-disabled="true">Policies</a>
                        <a href="/course/services" class="btn" role="button" aria-disabled="true">Services</a>
                  </div>

                  <p id="technical_header"><?php echo $data['technical_header'] ?></p>
                  <p id="technical_body"><?php echo $data['technical_body'] ?></p>
            </div>
		<div id="course-overview">


			</div>
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('course_overview','course_overview');


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
            'bio-1' => "John Whitten (he/him) is a visual artist, designer, web developer, and educator. His artistic practice is rooted in both traditional drawing, sculpture, and digital media. His artwork aims to excavate the philosophical significance of what it means to wander through the signals and noise enveloping our world. He has recently exhibited in New York City, Los Angeles, and Portland, and held gallery representation at Charles Hartman Fine Art in Portland from 2017 until the gallery closed in 2022. He is a co-founding director of the Thunderstruck Collective artist residency. He also co-founded the Portland-based artist-collective galleries Carnation Contemporary in 2018 and Well Well Projects in 2021. He earned his MFA in Painting and Drawing from the University of Oregon in Eugene, Oregon and his BFA in Studio Fine Art from Watkins College of Art in Nashville, Tennessee.",
            'bio-2' => "His 15 years experience in education includes 7 years as a Full-time Instructor of Digital Art & Design, Experimental Animation and Video, and Media Aesthetics at Oregon State University teaching over 100 term-long courses both in-person and online, 2 years teaching Drawing I at Linn-Benton Community College, 1 year teaching Digital Photography at Clark College, 3 years teaching undergraduate courses in Drawing and Time-Based  Media as a Graduate Teaching Fellow at the University of Oregon, 15 years teaching arts-oriented adult  continuing education and youth pre-college programs, summer camps, and workshops, and 5 years as  an admissions recruiter, academic counselor, and portfolio reviewer for a NASAD accredited college of  art.",
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
	      <div class="philosophy-container">
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
            'week-6-img' => '/wp-content/uploads/2022/11/Week-6.png',

            'week-6-header' => "Week 6 - Overview",
            'week-6-intro-header' => 'HOMEBODIES',
            'week-6-intro-body' => "This week we will wrap up Project 2: Video Remix. Additionally, this week we introduce and contextualize Project 3: 3-Dimensional Forms. We will look at artists using the site of home as a space for inspiration and artistic production. We will watch videos introducing artists Rachel Whiteread and Do Ho Suh. We will begin working with Google SketchUp to create 3D forms in a virtual space. Through discussion posts, we will also analyze and reflect on personal connections to materials and textures, and offer feedback to our peers.",

            'week-6-learning-head' => 'Weekly Learning Objectives',
            'week-6-learning-body' => "After successful completion of this week, you will be able to:",
            'week-6-learning-objective-1' => "Recognize artists working with the space of home as a site for inspiration and artistic production.",
            'week-6-learning-objective-2' => "Begin familiarizing ourselves with SketchUp in order to create unique 3D objects in a virtual space.",
            'week-6-learning-objective-3' => "Reflect, analyze and describe personal connections with various materials and textures through discussion posts.",
            

            'week-6-task-head' => "Task List",
            'week-6-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-6-task-1' => "Work through the material on the Week 6 – Learning Content page.",
            'week-6-task-2' => "Turn in Project 2 - Video Remix.",
            'week-6-task-3' => "Participate in the Peer Review Discussion: Video Remix.",
            'week-6-task-4' => "Participate in the Discussion: Digital SketchUp Model.",
            'week-6-task-5' => "Complete the Week 6 - Homework + Quiz.",
            'week-6-task-6' => "Begin working on Project 3 – 3-Dimensional Forms (Due Week 8)",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-6-img"  src="<?php echo $data['week-6-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-6-header'] ?></h1><br>
                        <h2><?php echo $data['week-6-intro-header'] ?></h2>
                              <p><?php echo $data['week-6-intro-body'] ?></p>              
                  <h3><?php echo $data['week-6-learning-head'] ?></h3>
                        <p><?php echo $data['week-6-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-6-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-6-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-6-learning-objective-3'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-6-task-head'] ?></h3>
                        <p><?php echo $data['week-6-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-6-task-1'] ?></li>
                                    <li><?php echo $data['week-6-task-2'] ?></li>
                                    <li><?php echo $data['week-6-task-3'] ?></li>
                                    <li><?php echo $data['week-6-task-4'] ?></li>
                                    <li><?php echo $data['week-6-task-5'] ?></li>
                                    <li><?php echo $data['week-6-task-6'] ?></li>
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
            'week-7-img' => '/wp-content/uploads/2022/11/Week-7.png',

            'week-7-header' => "Week 7 - Overview",
            'week-7-intro-header' => 'DIGITAL TECHNOLOGIES AS A MEDIUM',
            'week-7-intro-body' => "This week we will go over how digital technologies as a medium, which implies that from the production to the experience, the digital medium is explicitly explored and utilised in the artmaking process. We will be introduced to various form of digital art, such as Film, video, and animation, Internet art and networked art, Software art, Virtual reality, and Sound and Music. We will continue to look at artists working with different methods of digital process with an emphasis on Golan Levin, Jennifer Steinkamp, and Jacolby Satterwhite.",

            'week-7-learning-head' => 'Weekly Learning Objectives',
            'week-7-learning-body' => "After successful completion of this week, you will be able to:",
            'week-7-learning-objective-1' => "Identify various artists working with and through the digital medium.",
            'week-7-learning-objective-2' => "Recognize various digital art forms, such as film, video, animation, internet work and networked art, software art, virtual reality, and sound and music.",

            'week-7-task-head' => "Task List",
            'week-7-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-7-task-1' => "Work through the material on the Week 7 – Learning Content page.",
            'week-7-task-2' => "Comment on two classmates' posts in the Week 6 - Discussion: Digital SketchUp Model.",
            'week-7-task-3' => "Turn in the Practice Exercise: Google SketchUp Practice.",
            'week-7-task-4' => "Begin Project 3 – 3-Dimensional Forms (Due Week 8).",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-7-img"  src="<?php echo $data['week-7-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-7-header'] ?></h1><br>
                        <h2><?php echo $data['week-7-intro-header'] ?></h2>
                              <p><?php echo $data['week-7-intro-body'] ?></p>              
                  <h3><?php echo $data['week-7-learning-head'] ?></h3>
                        <p><?php echo $data['week-7-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-7-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-7-learning-objective-2'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-7-task-head'] ?></h3>
                        <p><?php echo $data['week-7-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-7-task-1'] ?></li>
                                    <li><?php echo $data['week-7-task-2'] ?></li>
                                    <li><?php echo $data['week-7-task-3'] ?></li>
                                    <li><?php echo $data['week-7-task-4'] ?></li>
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
            'week-8-img' => '/wp-content/uploads/2022/11/Week-8.png',

            'week-8-header' => "Week 8 - Overview",
            'week-8-intro-header' => 'WTF? TEXT AS ART',
            'week-8-intro-body' => "This week we will be researching “Text as Art” and “The History of Zines” in order to inform and inspire our last project, an Artist Zine. There are many different ways artists approach using text in their artwork. Some artists explore original texts they create, some artists use found or appropriated text as quotation, others use text in a purely formal manner that isn’t about what the words are saying at all. We will be exploring the use of text and language in art, while simultaneously considering the role zines have played in the history of art as protest.",

            'week-8-learning-head' => 'Weekly Learning Objectives',
            'week-8-learning-body' => "After successful completion of this week, you will be able to:",
            'week-8-learning-objective-1' => "Identify various artists working with and through the the use of text.",
            'week-8-learning-objective-2' => "Recognize various text art forms, such as erasure poetry, zines, animation, video, text as material, graffiti, and found text.",

            'week-8-task-head' => "Task List",
            'week-8-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-8-task-1' => "Work through the material on the Week 8 – Learning Content page.",
            'week-8-task-2' => "Turn in Project 3 - 3-Dimensional Forms.",
            'week-8-task-3' => "Participate in the Peer Review Discussion: 3-Dimensional Forms.",
            'week-8-task-4' => " Complete Week 8 - Homework + Quiz.",

	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-8-img"  src="<?php echo $data['week-8-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-8-header'] ?></h1><br>
                        <h2><?php echo $data['week-8-intro-header'] ?></h2>
                              <p><?php echo $data['week-8-intro-body'] ?></p>              
                  <h3><?php echo $data['week-8-learning-head'] ?></h3>
                        <p><?php echo $data['week-8-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-8-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-8-learning-objective-2'] ?></li>

                              </ol>            
                  <h3><?php echo $data['week-8-task-head'] ?></h3>
                        <p><?php echo $data['week-8-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-8-task-1'] ?></li>
                                    <li><?php echo $data['week-8-task-2'] ?></li>
                                    <li><?php echo $data['week-8-task-3'] ?></li>
                                    <li><?php echo $data['week-8-task-4'] ?></li>

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
            'week-9-img' => '/wp-content/uploads/2022/11/Week-9.png',

            'week-9-header' => "Week 9 - Overview",
            'week-9-intro-header' => 'PRESSPAUSEPLAY',
            'week-9-intro-body' => "Optional Extra Credit due at the end of this week.",

            'week-9-learning-head' => 'Weekly Learning Objectives',
            'week-9-learning-body' => "After successful completion of this week, you will be able to:",
            'week-9-learning-objective-1' => " ",
            'week-9-learning-objective-2' => " ",
            'week-9-learning-objective-3' => " ",
            'week-9-learning-objective-4' => " ",

            'week-9-task-head' => "Task List",
            'week-9-task-body' => "In order to achieve these learning outcomes, please make sure to complete the following in our Canvas LMS:",
            'week-9-task-1' => "Work through the material on the Week 9 – Learning Content page.",
            'week-9-task-2' => "Comment on two classmates' posts in the Peer Review Discussion: 3-Dimensional Forms.",
            'week-9-task-3' => "Participate in the Discussion: Found Text.",
            'week-9-task-4' => "Complete the Week 9 - Homework + Quiz.",
            'week-9-task-5' => "Begin working on Project 4 – Artist Zine.",
            'week-9-task-6' => "Optional Assignment: Extra Credit.",
	),$atts);
	?>
<div class="page">
      <section class="row">
            <div class="page-image">
                  <img id="week-9-img"  src="<?php echo $data['week-9-img'] ?> " alt="">
            </div>
            <div class="page-text">
                  <h1><?php echo $data['week-9-header'] ?></h1><br>
                        <h2><?php echo $data['week-9-intro-header'] ?></h2>
                              <p><?php echo $data['week-9-intro-body'] ?></p>              
                  <h3><?php echo $data['week-9-learning-head'] ?></h3>
                        <p><?php echo $data['week-9-learning-body'] ?></p>
                              <ol>
                                    <li><?php echo $data['week-9-learning-objective-1'] ?></li>
                                    <li><?php echo $data['week-9-learning-objective-2'] ?></li>
                                    <li><?php echo $data['week-9-learning-objective-3'] ?></li>
                                    <li><?php echo $data['week-9-learning-objective-4'] ?></li>
                              </ol>            
                  <h3><?php echo $data['week-9-task-head'] ?></h3>
                        <p><?php echo $data['week-9-task-body'] ?></p>
                              <ul>
                                    <li><?php echo $data['week-9-task-1'] ?></li>
                                    <li><?php echo $data['week-9-task-2'] ?></li>
                                    <li><?php echo $data['week-9-task-3'] ?></li>
                                    <li><?php echo $data['week-9-task-4'] ?></li>
                                    <li><?php echo $data['week-9-task-5'] ?></li>
                                    <li><?php echo $data['week-9-task-6'] ?></li>

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



function projects_overview($atts) {
	ob_start();
	$data = shortcode_atts(array(
            'headline' => "4 Major Projects",
            'projects_intro' => "Cumulatively worth 80% of course grade",
            
            'project_1_img' => '/wp-content/uploads/2022/11/Project_1.png',
		'project_1_headline' => "Project 1 - Culture Jam",
		'project_1_motivation' => "Culture jamming is a tactic used by many anti-consumerist activists whereby existing corporate media such as logos, billboards, bus-ads, posters, commercials, and other advertisements are disrupted, subverted, and re-configured to produce ironic or satirical commentary about the corporation or product itself, commonly using the original medium's communication method.", 

            'project_2_img' => '/wp-content/uploads/2022/11/Project_2.gif',
		'project_2_headline' => "Project 2 - Video Remix",
		'project_2_motivation' => "Artists consistently challenge the idea that meaning ascribed to objects is permanently fixed. In today’s digital world it’s easier than ever to copy, paste, mash-up, remix, download and publish content. Other people’s writing, artwork, images and videos can be inspiring, but they’re also easy to take without thinking twice or with any regard to critical consideration or the weight of their cultural content.", 

            'project_3_img' => '/wp-content/uploads/2022/11/Project_3.gif',
		'project_3_headline' => "Project 3 - 3 Dimensional Forms",
		'project_3_motivation' => "The domestic setting has been a crucial site (and recurring subject) of artistic production—a parallel track and occasionally a counterpoint to more commonly celebrated contexts such as the artist’s studio and the public sphere. In fact, many artists, for personal or financial reasons, work at home, and for those artists, the home often becomes the subject and source of their artwork.", 

            'project_4_img' => '/wp-content/uploads/2022/11/Project_4.gif',
		'project_4_headline' => "Project 4 - Artist Zine",
		'project_4_motivation' => "A zine (\ˈzēn\ ZEEN; an abbreviation of fanzine or magazine) is a cheaply-made, cheaply-priced, small circulation self-published printed and bound (usually with staples) book form work of original or appropriated texts and images often in black and white and usually reproduced via photocopier. Zines are noncommercial often homemade or online publications usually devoted to specialized and often unconventional subject matter.", 


	),$atts);
	?>
	<div class="projects-overview-main-container">

            <div id="projects-intro-container">
                  <h1 id="projects-overview-text"><b><?php echo $data['headline'] ?></b></h1>
                  <h3 id="projects-overview-text"><i><?php echo $data['projects_intro'] ?></i></h3>
            </div>
            
            <div id="projects-overview-intro-container">      
                  <a href='/assignments/projects/project-1' class="project-card"><img id="projects-overview-image" src="<?php echo $data['project_1_img'] ?>" alt="">
                        <h3 id="projects-overview-text"><?php echo $data['project_1_headline'] ?></h3>
                        <p id="projects-overview-text"><?php echo $data['project_1_motivation'] ?></p>
                        <p id="projects-overview-text">Click to learn more about this project</p>
                  </a>
            
                  <a href='/assignments/projects/project-2' class="project-card"><img id="projects-overview-image" src="<?php echo $data['project_2_img'] ?>" alt="">
                        <h3 id="projects-overview-text"><?php echo $data['project_2_headline'] ?></h3>
                        <p id="projects-overview-text"><?php echo $data['project_2_motivation'] ?></p>
                        <p id="projects-overview-text">Click to learn more about this project</p>
                  </a>

                  <a href='/assignments/projects/project-3' class="project-card"><img id="projects-overview-image" src="<?php echo $data['project_3_img'] ?>" alt="">
                        <h3 id="projects-overview-text"><?php echo $data['project_3_headline'] ?></h3>
                        <p id="projects-overview-text"><?php echo $data['project_3_motivation'] ?></p>
                        <p id="projects-overview-text">Click to learn more about this project</p>
                  </a>

                  <a href='/assignments/projects/project-4' class="project-card"><img id="projects-overview-image" src="<?php echo $data['project_4_img'] ?>" alt="">
                        <h3 id="projects-overview-text"><?php echo $data['project_4_headline'] ?></h3>
                        <p id="projects-overview-text"><?php echo $data['project_4_motivation'] ?></p>
                        <p id="projects-overview-text">Click to learn more about this project</p>
                  </a>
            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('projects_overview','projects_overview');


function project_one($atts) {
	ob_start();
	$data = shortcode_atts(array(
            'headline' => "Project 1 - Culture Jam",
            'projects_intro' => "Worth 25 points / 20% of course grade",
            
            'project_1_img' => '/wp-content/uploads/2022/11/Project_1.png',

            'project_1_motivation_headline' => "MOTIVATION",
		'project_1_motivation' => "Culture jamming is a tactic used by many anti-consumerist activists whereby existing corporate media such as logos, billboards, bus-ads, posters, commercials, and other advertisements are disrupted, subverted, and re-configured to produce ironic or satirical commentary about the corporation or product itself, commonly using the original medium's communication method.", 

            'project_1_deadline' => "Due WEDNESDAY of Week 3 by 11:59pm PST",

            'project_1_instructions_headline' => "INSTRUCTIONS",
            'project_1_instructions_text' => "For this project you will generate and execute a Culture Jam using Adobe Photoshop that fits within the parameters listed in the links below.",
            'project_1_guidelines' => 'https://docs.google.com/document/d/1AMkazUb1FnsSMPo_Ms4GP9caZhTNH0IY0oP4PeM98tg/edit?usp=sharing',
            'grading_ribric' => 'https://docs.google.com/document/d/1agaebIl0luT2UdwWRvMPm8YVYnNr1CgJeayTDQ0xMV0/edit?usp=sharing',

            'project_1_important_headline' => "IMPORTANT",
            'project_1_review_text' => "After following the instructions for the Project 1 - Culture Jam Guidelines and submitting your finished work (PSD only) on the Canvas Assignment page, you must also embed your finished image (JPEG only) to the Peer Review Discussion: Culture Jam and comment on at least two classmates' work to complete this Project.",

            'project_1_examples_headline' => "PAST STUDENT EXAMPLES",
            'project_1_student_examples' => 'https://drive.google.com/drive/folders/10vuraKpa2KXKPHrf3-kEP3zbj0HINe4n?usp=sharing',


	),$atts);
	?>
	<div class="project-main-container">

            <div id="project-intro-container">
                  <h1 id="project-text"><b><?php echo $data['headline'] ?></b></h1>
                  <h3 id="project-text"><i><?php echo $data['projects_intro'] ?></i></h3>
            </div>
            <div id="project-text-container">
                  <div class="project-left">
                        <img id="project-img"  src="<?php echo $data['project_1_img'] ?> " alt="">
                  </div>

                  <div class="project-right">
                        <h4 id="project-text"><b><?php echo $data['project_1_motivation_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_1_motivation'] ?> </p>
                        <h5 id="project-text"><b><?php echo $data['project_1_deadline'] ?></b></h5>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_1_instructions_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_1_instructions_text'] ?></p>
                        <ul>
                              <li><a href=<?php echo $data['project_1_guidelines'] ?> target="_blank" rel="noopener">Project 1 - Culture Jam Guidelines</a></li>
                              <li><a href=<?php echo $data['grading_rubric'] ?> target="_blank" rel="noopener">Major Project - Grading Rubric</a></li>
                        </ul>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_1_important_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_1_review_text'] ?></p>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_1_examples_headline'] ?></b></h4>
                        <a href=<?php echo $data['project_1_student_examples'] ?> target="_blank" rel="noopener">Project 1 - Culture Jam Student Examples</a>
                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('project_one','project_one');


function project_two($atts) {
	ob_start();
	$data = shortcode_atts(array(
            'headline' => "Project 2 - Video Remix",
            'projects_intro' => "Worth 25 points / 20% of course grade",
            
            'project_2_img' => '/wp-content/uploads/2022/11/Project_2.gif',

            'project_2_motivation_headline' => "MOTIVATION",
		'project_2_motivation' => "From the ideas of Duchamp and the ready-made, the use of found footage has been part of the video medium since its inception. Artists consistently challenge the idea that meaning ascribed to objects is permanently fixed. In today’s digital world it’s easier than ever to copy, paste, mash-up, remix, download and publish content. Other people’s writing, artwork, images and videos can be inspiring, but they’re also easy to take without thinking twice or with any regard to critical consideration or the weight of their cultural content.", 

            'project_2_deadline' => "Due WEDNESDAY of Week 6 by 11:59pm PST",

            'project_2_instructions_headline' => "INSTRUCTIONS",
            'project_2_instructions_text' => "For this project you will generate and execute a Video Remix using Adobe Premiere that fits within the parameters listed in the links below.",
            'project_2_guidelines' => 'https://docs.google.com/document/d/1Zxf5c5XMmLOomlpn6yIlbwZLFJ19CECO4FvwVCDlZks/edit?usp=sharing',
            'grading_rubric' => 'https://docs.google.com/document/d/1agaebIl0luT2UdwWRvMPm8YVYnNr1CgJeayTDQ0xMV0/edit?usp=sharing',

            'project_2_important_headline' => "IMPORTANT",
            'project_2_review_text' => "After following the instructions for the Project 2 - Video Remix and submitting your finished work on the Canvas Assignment page, you must also post your finished video (.mov, .mpeg, .mp4) to the Peer Review Discussion: Video Remix to complete this Project.",

            'project_2_examples_headline' => "PAST STUDENT EXAMPLES",
            'project_2_student_examples' => 'https://drive.google.com/drive/folders/15otXctIPORioDcbsfKbaaqywrOSPHi0J?usp=sharing',


	),$atts);
	?>
	<div class="project-main-container">

            <div id="project-intro-container">
                  <h1 id="project-text"><b><?php echo $data['headline'] ?></b></h1>
                  <h3 id="project-text"><i><?php echo $data['projects_intro'] ?></i></h3>
            </div>
            <div id="project-text-container">
                  <div class="project-left">
                        <img id="project-img"  src="<?php echo $data['project_2_img'] ?> " alt="">
                  </div>

                  <div class="project-right">
                        <h4 id="project-text"><b><?php echo $data['project_2_motivation_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_2_motivation'] ?> </p>
                        <h5 id="project-text"><b><?php echo $data['project_2_deadline'] ?></b></h5>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_2_instructions_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_2_instructions_text'] ?></p>
                        <ul>
                              <li><a href=<?php echo $data['project_2_guidelines'] ?> target="_blank" rel="noopener">Project 2 - Video Remix Guidelines</a></li>
                              <li><a href=<?php echo $data['grading_rubric'] ?> target="_blank" rel="noopener">Major Project - Grading Rubric</a></li>
                        </ul>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_2_important_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_2_review_text'] ?></p>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_2_examples_headline'] ?></b></h4>
                        <a href=<?php echo $data['project_2_student_examples'] ?> target="_blank" rel="noopener">Project 2 - Video Remix Student Examples</a>
                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('project_two','project_two');


function project_three($atts) {
	ob_start();
	$data = shortcode_atts(array(
            'headline' => "Project 3 - 3-Dimensional Forms",
            'projects_intro' => "Worth 25 points / 20% of course grade",
            
            'project_3_img' => '/wp-content/uploads/2022/11/Project_3.gif',

            'project_3_motivation_headline' => "MOTIVATION",
		'project_3_motivation' => "The domestic setting has been a crucial site (and recurring subject) of artistic production—a parallel track and occasionally a counterpoint to more commonly celebrated contexts such as the artist’s studio and the public sphere. In fact, many artists, for personal or financial reasons, work at home, and for those artists, the home often becomes the subject and source of their artwork.", 

            'project_3_deadline' => "Due SATURDAY of Week 8 by 11:59pm PST",

            'project_3_instructions_headline' => "INSTRUCTIONS",
            'project_3_instructions_text' => "For this project, you will generate a 3D digital rendering of 'home' using SketchUp Pro. You may use either the Free 30-day Trial of SketchUp Pro Desktop app or the Free SketchUp Web-based application Links to an external site. Use the parameters listed in the instructions through the links below.",
            'project_3_guidelines' => 'https://docs.google.com/document/d/1FGZmXwXlh7Ji3oMf29wQOLFMfhZvqWWrne8yu908KP0/edit?usp=sharing',
            'grading_rubric' => 'https://docs.google.com/document/d/1agaebIl0luT2UdwWRvMPm8YVYnNr1CgJeayTDQ0xMV0/edit?usp=sharing',

            'project_3_important_headline' => "IMPORTANT",
            'project_3_review_text' => "After following the instructions for Project 3 - 3-Dimensional Forms and submitting your finished work on the Canvas Assignment page, you must also post your finished video or animated GIF to the Peer Review Discussion: 3-Dimensional Forms to complete this Project.",

            'project_3_examples_headline' => "PAST STUDENT EXAMPLES",
            'project_3_student_examples' => 'https://drive.google.com/drive/folders/18Yp1NHltrijMQBuNCYXBgXSVe_AvPgkh?usp=sharing',


	),$atts);
	?>
	<div class="project-main-container">

            <div id="project-intro-container">
                  <h1 id="project-text"><b><?php echo $data['headline'] ?></b></h1>
                  <h3 id="project-text"><i><?php echo $data['projects_intro'] ?></i></h3>
            </div>
            <div id="project-text-container">
                  <div class="project-left">
                        <img id="project-img"  src="<?php echo $data['project_3_img'] ?> " alt="">
                  </div>

                  <div class="project-right">
                        <h4 id="project-text"><b><?php echo $data['project_3_motivation_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_3_motivation'] ?> </p>
                        <h5 id="project-text"><b><?php echo $data['project_3_deadline'] ?></b></h5>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_3_instructions_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_3_instructions_text'] ?></p>
                        <ul>
                              <li><a href=<?php echo $data['project_3_guidelines'] ?> target="_blank" rel="noopener">Project 3 - 3-Dimensional Forms Guidelines</a></li>
                              <li><a href=<?php echo $data['grading_rubric'] ?> target="_blank" rel="noopener">Major Project - Grading Rubric</a></li>
                        </ul>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_3_important_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_3_review_text'] ?></p>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_3_examples_headline'] ?></b></h4>
                        <a href=<?php echo $data['project_3_student_examples'] ?> target="_blank" rel="noopener">Project 3 - 3-Dimensional Forms Student Examples</a>
                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('project_three','project_three');



function project_four($atts) {
	ob_start();
	$data = shortcode_atts(array(
            'headline' => "Project 4 - Artist Zine",
            'projects_intro' => "Worth 25 points / 20% of course grade",
            
            'project_4_img' => '/wp-content/uploads/2022/11/Project_4.gif',

            'project_4_motivation_headline' => "MOTIVATION",
		'project_4_motivation' => "A zine (\ˈzēn\ ZEEN; an abbreviation of fanzine or magazine) is a cheaply-made, cheaply-priced, small circulation self-published printed and bound (usually with staples) book form work of original or appropriated texts and images often in black and white and usually reproduced via photocopier. A popular definition includes that circulation must be 1,000 or fewer, although in practice the majority are produced in editions of fewer than 100, and profit is not the primary intent of publication. Zines are noncommercial often homemade or online publication usually devoted to specialized and often unconventional subject matter.", 

            'project_4_deadline' => "Due WEDNESDAY of Week 11 by 11:59pm PST",

            'project_4_instructions_headline' => "INSTRUCTIONS",
            'project_4_instructions_text' => "For this project you will create a Zine using Adobe InDesign following the parameters listed in the instructions through the links below.",
            'project_4_guidelines' => 'https://docs.google.com/document/d/1SbTL6LNJUu_XuvMPduEOpCLZKzi0mwXqv3Q4bN_9V00/edit?usp=sharing',
            'grading_rubric' => 'https://docs.google.com/document/d/1agaebIl0luT2UdwWRvMPm8YVYnNr1CgJeayTDQ0xMV0/edit?usp=sharing',

            'project_4_important_headline' => "IMPORTANT",
            'project_4_review_text' => "After following the instructions for Project 4 - Artist Zine and submitting your finished work to the Canvas Assignment page, you must also post your finished Zine (PDF only) to the Share Your Artist Zine With Your Classmates to complete this Project. No Peer Review will take place for this project.",

            'project_4_examples_headline' => "PAST STUDENT EXAMPLES",
            'project_4_student_examples' => 'https://drive.google.com/drive/folders/1HAxTwvheFgLwefpzK9HdNqSgn4RRoZu1?usp=sharing',


	),$atts);
	?>
	<div class="project-main-container">

            <div id="project-intro-container">
                  <h1 id="project-text"><b><?php echo $data['headline'] ?></b></h1>
                  <h3 id="project-text"><i><?php echo $data['projects_intro'] ?></i></h3>
            </div>
            <div id="project-text-container">
                  <div class="project-left">
                        <img id="project-img"  src="<?php echo $data['project_4_img'] ?> " alt="">
                  </div>

                  <div class="project-right">
                        <h4 id="project-text"><b><?php echo $data['project_4_motivation_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_4_motivation'] ?> </p>
                        <h5 id="project-text"><b><?php echo $data['project_4_deadline'] ?></b></h5>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_4_instructions_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_4_instructions_text'] ?></p>
                        <ul>
                              <li><a href=<?php echo $data['project_4_guidelines'] ?> target="_blank" rel="noopener">Project 4 - Artist Zine Guidelines</a></li>
                              <li><a href=<?php echo $data['grading_rubric'] ?> target="_blank" rel="noopener">Major Project - Grading Rubric</a></li>
                        </ul>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_4_important_headline'] ?></b></h4>
                        <p id="project-text"><?php echo $data['project_4_review_text'] ?></p>
                        <hr></hr>
                        <h4 id="project-text"><b><?php echo $data['project_4_examples_headline'] ?></b></h4>
                        <a href=<?php echo $data['project_3_student_examples'] ?> target="_blank" rel="noopener">Project 4 - Artist Zine Student Examples</a>
                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('project_four','project_four');


function appointment() {
      ob_start();
	$data = shortcode_atts(array(
            'headline' => "Book an appoinment during office hours",
            'instructions' => "Select a date on the calendar to begin booking an appointment.",
            'issues' => "If you experience any technical issues booking an appointment, please reach out to John directly via email.",
            'email' => "johnwhitten.studio@gmail.com",
	),$atts);
	?>
	<div class="appointment-parent-container">

            <div class="appointment-headline">
            </div>
            <div class="appointment-child-container">
                  <div class="appointment-text">
                              
                              <h4 id="appointment-headline"><b><?php echo $data['headline'] ?></b></h4>
                              <hr id="appointment-hr"></hr>
                              <br>
                              <h5 id="appointment-instructions"><?php echo $data['instructions'] ?></h5>
                              <h5 id="appointment-issues"><?php echo $data['issues'] ?></h5>
                              <h5 id="appointment-email"><a href="mailto:johnwhitten.studio@gmail.com"><?php echo $data['email'] ?></a></h5>
                  </div>

                  <div class="appointment-calendly">
                        <!-- Calendly inline widget begin -->
                        <div class="calendly-inline-widget" data-url="https://calendly.com/johnwhitten-studio/20min?primary_color=1c97d0"></div>
                        <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
                        <!-- Calendly inline widget end -->
                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('appointment','appointment');




function policies() {
      ob_start();
	$data = shortcode_atts(array(
            'headline' => "Book an appoinment during office hours",
            'instructions' => "Select a date on the calendar to begin booking an appointment.",
            'issues' => "If you experience any technical issues booking an appointment, please reach out to John directly via email.",
            'email' => "johnwhitten.studio@gmail.com",
	),$atts);
	?>
	<div class="appointment-parent-container">

            <div class="appointment-headline">
            </div>
            <div class="appointment-child-container">
                  <div class="appointment-text">
                              
                              <h4 id="appointment-headline"><b><?php echo $data['headline'] ?></b></h4>
                              <hr id="appointment-hr"></hr>
                              <br>
                              <h5 id="appointment-instructions"><?php echo $data['instructions'] ?></h5>
                              <h5 id="appointment-issues"><?php echo $data['issues'] ?></h5>
                              <h5 id="appointment-email"><a href="mailto:johnwhitten.studio@gmail.com"><?php echo $data['email'] ?></a></h5>
                  </div>

                  <div class="appointment-calendly">
                        <!-- Calendly inline widget begin -->
                        <div class="calendly-inline-widget" data-url="https://calendly.com/johnwhitten-studio/20min?primary_color=1c97d0"></div>
                        <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
                        <!-- Calendly inline widget end -->
                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('policies','policies');




function services() {
      ob_start();
	$data = shortcode_atts(array(
            'services-text' => "From crisis support to academic coaching to recreational programming, there are loads of support services and opportunities to engage at OSU. Please review the provided websites or contact departments and offices directly for questions regarding services.",
            'phone' => "/wp-content/uploads/2022/12/phone.png",
            'web' => "/wp-content/uploads/2022/12/web.png",

            'services-headline-crisis' => "24-Hour Crisis Hotline and Services",
            'services-phone-crisis' => "1-800-273-8255",
            'services-web-crisis' => "http://www.ulifeline.org/get_help_now",
            'services-description-crisis' => "If you are considering suicide or feeling depressed, talk to someone. Reach out to a friend or family member, call or stop by CAPS, contact the 24-hour crisis hotline or visit the CAPS website for multiple resources.",

            'services-headline-asc' => "Academic Success Center (ASC)",
            'services-phone-asc' => "541-737-2272",
            'services-web-asc' => "https://success.oregonstate.edu/",
            'services-description-asc' => "The Academic Success Center (ASC) provides support and services to help all students achieve their academic goals. ASC programs help students develop learning strategies and time management skills to excel in their coursework and stay on track to graduate.",

            'services-headline-athlete' => "Academics for Student Athletes",
            'services-phone-athlete' => "541-737-1000",
            'services-web-athlete' => "https://studentathlete.oregonstate.edu/",
            'services-description-athlete' => "The Academics for Student Athletes office provides academic and personal support to all student athletes at Oregon State University. We strive to create a collaborative environment with other campus departments that will help you achieve your potential for intellectual, social and personal development. Services include sport-specific academic counselors and priority class registration for student athletes, with guidance and advising from major department advisors as well as ASA academic counselors.",

            'services-headline-nurse' => "After-Hours Medical Advice Nurse Line",
            'services-phone-nurse' => "541-737-9355",
            'services-web-nurse' => "https://studenthealth.oregonstate.edu/clinical-services/medical-advice-nurse-line",
            'services-description-nurse' => "Medical advice is available by phone 24 hours per day for OSU students eligible for care at Student Health Services. If you have medical concerns after clinic hours, you can obtain a toll-free number for a medical call center by calling 541-737-9355.",

            'services-headline-apcc' => "Asian & Pacific Cultural Center",
            'services-phone-apcc' => "541-737-6361",
            'services-web-apcc' => "https://dce.oregonstate.edu/apcc",
            'services-description-apcc' => "Celebrating the cultures of Asian and Pacific Island nations, the Asian & Pacific Cultural Center provides a meditation room, engagement opportunities, and space for students to mingle and experience community.",

            'services-headline-bnc' => "Basic Needs Center",
            'services-phone-bnc' => "541-737-3747",
            'services-web-bnc' => "https://studentlife.oregonstate.edu/bnc",
            'services-description-bnc' => "The Basic Needs Center is here to support you. We provide textbook access, laptop and calculator lending and food assistance. Contact the center for peer support to help you identify and apply to resources.",

            'services-headline-birf' => "Bias Incident Report Form",
            'services-phone-birf' => "541-737-1063",
            'services-web-birf' => "https://diversity.oregonstate.edu/bias-incident-response-1",
            'services-description-birf' => "Have you experienced or witnessed an incident of bias at Oregon State? In the university's ongoing efforts to create a safe and inclusive environment for all students, the Office of Institutional Diversity collaborates with campus partners to respond to incidents of bias. This team takes seriously all reports of bias and depending on the circumstances may follow up with campus safety, referrals to university resources, or community outreach. Use this form to report discrimination, bullying or other incidents of bias; reports may be made anonymously.",

            'services-headline-canvas' => "Canvas Support",
            'services-phone-canvas' => "1-844-329-3084",
            'services-description-canvas' => "Students can reach 24/7 Canvas support by clicking help in the main Canvas menu (lower left side of screen). Students can chat with a Canvas representative or call the Canvas support hotline at 1-844-329-3084.",

            'services-headline-cdc' => "Career Development Center",
            'services-phone-cdc' => "541-737-4085",
            'services-web-cdc' => "https://career.oregonstate.edu/",
            'services-description-cdc' => "The Career Development Center works to empower, support and nurture OSU students in their exploration and pursuit of lifelong career success and meaningful employment.",

            'services-headline-cccc' => "Centro Cultural César Chávez",
            'services-phone-cccc' => "541-737-3790",
            'services-web-cccc' => "https://dce.oregonstate.edu/cccc",
            'services-description-cccc' => "Celebrating Chicano/Latino/Hispanic cultures, Centro Cultural César Chávez has an event space, areas for students to study, and a comfortable learning atmosphere.",

            'services-headline-cbsd' => "Corvallis-Based Service Desk",
            'services-phone-cbsd' => "541-737-8787",
            'services-web-cbsd' => "https://oregonstate.teamdynamix.com/TDClient/1935/Portal/Home/",
            'services-description-cbsd' => "Whether you're having trouble installing software, connecting to the WiFi network or getting rid of a pesky virus on your computer, the Service Desk has qualified staff to assist you with your computer needs. ",

            'services-headline-caps' => "Counseling & Psychological Services (CAPS)",
            'services-phone-caps' => "541-737-2131",
            'services-web-caps' => "https://counseling.oregonstate.edu/",
            'services-description-caps' => "Committed to improving students’ lives through counseling, resources for managing stress and improving self-esteem, and mindfulness practices, Counseling & Psychological Services (CAPS) is here to help you flourish. CAPS offers many online wellness resources and remote services, including on-going individual counseling and the Single Session Clinic for one-time appointments focused on a particular concern or problem. "
	),$atts);
	?>
	<div class="services-parent-container">

            <div class="services-child-container">
                  
                  <div class="services-text"> 
                        <p id="services-text"><?php echo $data['services-text'] ?></p>
                  </div>

                  <div class="services">

                        <h4 id="services-headline"><?php echo $data['services-headline-crisis'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-crisis'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-crisis'] ?> target="_blank" rel="noopener">24-Hour Crisis Hotline and Services</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-crisis'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-asc'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-asc'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-asc'] ?> target="_blank" rel="noopener">Academic Success Center (ASC)</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-asc'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-athlete'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-athlete'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-athlete'] ?> target="_blank" rel="noopener">Academics for Student Athletes</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-athlete'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-apcc'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-apcc'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-apcc'] ?> target="_blank" rel="noopener">Asian & Pacific Cultural Center</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-apcc'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-bnc'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-bnc'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-bnc'] ?> target="_blank" rel="noopener">Basic Needs Center</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-bnc'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-birf'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-birf'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-birf'] ?> target="_blank" rel="noopener">Bias Incident Report Form</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-birf'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-canvas'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-canvas'] ?></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-canvas'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-cdc'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-cdc'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-cdc'] ?> target="_blank" rel="noopener">Career Development Center</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-cdc'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-cccc'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-cccc'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-cccc'] ?> target="_blank" rel="noopener">Centro Cultural César Chávez</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-cccc'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-cbsd'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-cbsd'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-cbsd'] ?> target="_blank" rel="noopener">Corvallis-Based Service Desk</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-cbsd'] ?></p>
                        </div>

                        <h4 id="services-headline"><?php echo $data['services-headline-caps'] ?></h4>
                        <div class="services-card">
                              <div class="services-card-row">
                                    <img id="services-phone-icon" src="<?php echo $data['phone'] ?>" alt="">
                                    <p id="services-phone"><?php echo $data['services-phone-caps'] ?></p>
                              </div>
                              <div class="services-card-row">
                                    <img id="services-web-icon" src="<?php echo $data['web'] ?>" alt="">
                                    <p id="services-web"><a href=<?php echo $data['services-web-caps'] ?> target="_blank" rel="noopener">Counseling & Psychological Services (CAPS)</a></p>
                              </div>
                              <p id="services-description"><?php echo $data['services-description-caps'] ?></p>
                        </div>


                  </div>

            </div>
      
      </div>
	<?php
	return ob_get_clean();
}
add_shortcode('services','services');