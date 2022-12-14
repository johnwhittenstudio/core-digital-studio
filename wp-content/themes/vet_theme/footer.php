</div>
<footer id="footer">
  <!-- <div id="top-angle"></div> -->
  <?php if ( is_front_page() ) { ?>
  <div class="inner-wrapper" id="homepage-footer">
    <div class="fifty-percent" id="hp-footer-logo">
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Homepage Footer: One") ) : ?>
      <?php endif;?>
    </div>
    <div class="fifty-percent" id="hp-visit-us">
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Homepage Footer: Two") ) : ?>
      <?php endif;?>
    </div>
  </div>
  <?php } else { ?>
  <div class="inner-wrapper" id="subpage-footers">
    <img id="sp-footer-logo" class="footer-logo" src="/wp-content/uploads/2020/03/footer-logo.png" alt="<?php echo get_bloginfo('name') ?> logo">
    <div id="subpage-footer-inner">
      <div id="sp-footer-left" class="subpage-footer-half">
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Subpage Footer: One") ) : ?>
        <?php endif;?>
      </div>
      <div id="sp-footer-right" class="subpage-footer-half">
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Subpage Footer: Two") ) : ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <?php } ?>
</footer>
<div id="copyright">
  <div class="inner-wrapper">
    <p id="copyright-text"><a href="/accessibility/">Accessibility</a> | © Copyright <?php echo date('Y') ?> - <?php echo get_bloginfo('name') ?>. <a href="https://ivet360.com/marketing/" target="_blank" rel="noreferrer">Veterinary Marketing</a> powered by <a href="https://ivet360.com/" target="_blank" rel="noreferrer"><span id="lowercase">i</span>VET360</a>.</p>
  </div>
</div>
</div>
<?php wp_footer(); ?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.typekit.net/irj0byf.css" media="none" onload="if(media!='all')media='all'"><noscript><link rel="stylesheet" href="https://use.typekit.net/irj0byf.css"></noscript>
<script type="text/javascript">
var _userway_config = {position: '5',size: 'small',account: '7d2ED9opQx', color: '#2F8CAD'};
</script>
<script type="text/javascript" src="https://cdn.userway.org/widget.js"></script>
</body>
</html>
