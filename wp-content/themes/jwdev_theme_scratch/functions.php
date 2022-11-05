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
			<a href="/about/veterinarians/" style="background-image: url('/wp-content/uploads/2022/11/course-overview.jpg')" class="callout">
				<div class="callout-text">
					<h3>Guiding and Informative</h3>
					<h2>Course<br>Overview</h2>
				</div>
			</a>
			<a href="/services/" style="background-image: url('/wp-content/uploads/2022/11/lessons.jpg')" class="callout">
				<div class="callout-text">
					<h3>Inspiring and Instructive</h3>
					<h2>Weekly<br>Lessons</h2>
				</div>
			</a>
			<a href="/appointment/" style="background-image: url('/wp-content/uploads/2022/11/projects.jpg')" class="callout">
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
		'button-link' => "/course-topics",
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
		'text' => "Digital Art has expanded the definition of art and increased the accessibility of art to the world. It is about the tools used to create it, as well as the vision, message, and context of its creation. This course aims to survey technology’s influence on art and culture from a historical and contemporary perspective. Below are the topics we cover in this course.",
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


