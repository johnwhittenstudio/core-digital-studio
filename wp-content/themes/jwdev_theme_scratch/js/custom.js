jQuery('.gallery').each(function() { // the containers for all your galleries
  jQuery(this).magnificPopup({
      delegate: 'a', // the selector for gallery item
      type: 'image',
      gallery: {
        enabled:true
      }
  });
});


$ = jQuery;

function isScrolledIntoView(el) {
  const rect = el.getBoundingClientRect();
  const elemTop = rect.top;
  const elemBottom = rect.bottom;

  // Only completely visible elements return true:
  const isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
  // Partially visible elements return true:
  //isVisible = elemTop < window.innerHeight && elemBottom >= 0;
  return isVisible;
}

function animateElement(e, className, duration) {
  const element = document.getElementsByClassName(e);

  for (let i = 0; i < element.length; i++) {
    setTimeout(function timer() {
        element[i].classList.add(className);
    }, i * parseInt(duration));
  }
}

// FRONTEND CODE

function hamburgerMenu() {
  const hamburger = document.getElementById('hamburger-jw');
  const mobileMenu = document.getElementById('jw-mobile-menu-container');
  const subMenus = document.getElementsByClassName('sub-menu');
  let hamburgerOpen = false;
  hamburger.addEventListener('click', () => {
    if(!hamburgerOpen) {
      let subMenuTotals = 0;
      for (var i = 0; i < subMenus.length; i++) {
        let scrollHeight = parseInt(subMenus[i].scrollHeight);
        subMenuTotals = scrollHeight + subMenuTotals;
      }
      hamburger.classList.add('open');
      hamburgerOpen = true;
      mobileMenu.style.maxHeight = (mobileMenu.scrollHeight + subMenuTotals) + "px";
    } else {
      hamburger.classList.remove('open');
      hamburgerOpen = false;
      mobileMenu.style.maxHeight = null;
    }
  });
}



window.onload = function() {

  const menuItems = document.getElementsByClassName('menu-item');
  for (let i = 0; i < menuItems.length; i++) {
    if (menuItems[i].children[0].href === `${window.location.href}#`) {
      menuItems[i].children[0].addEventListener('click', function() {
        event.preventDefault();
      });
    }
  }

  const navMenuItems = document.getElementById('jw-mobile-menu').children[0].children[0].children;
  const headerHeight = document.getElementById('header').offsetHeight;

  document.getElementById('search-box').style.height = `${headerHeight}px`;
  document.getElementById('search-box').style.transform = `translateY(-${headerHeight}px)`;

  hamburgerMenu();
  animateElement('hero-fade', 'fade-in', 250);

  setTimeout(function() { animateElement('video-underline', 'hundid', 500); }, 2000);

  const subpageImage = document.getElementsByClassName('post-img-container')[0];
  const subpageTitle = document.getElementsByClassName('entry-title')[0];
  const subMenus = document.getElementById('jw-mobile-menu-container').children[0].children[0].children

  if (subpageImage) {
    subpageImage.classList.add('fade-in');
  }

  if (subpageTitle) {
    subpageTitle.classList.add('fade-in');
  }

  for (let i = 0; i < subMenus.length; i++) {
    if (subMenus[i].classList.contains("menu-item-has-children")) {
      const fontAwesome = document.createElement('i');
      fontAwesome.className = 'fas fa-chevron-right';
      subMenus[i].append(fontAwesome);
      subMenus[i].addEventListener('click', function() {
        if (this.classList.contains('sub-menu-open') === false) {
          this.children[2].classList.add('rotate');
          this.classList.add('sub-menu-open');
          this.children[1].style.maxHeight = this.children[1].scrollHeight + "px";
        } else {
          this.children[2].classList.remove('rotate');
          this.classList.remove('sub-menu-open');
          this.children[1].style.maxHeight = null;
        }
      });
    }
  }

  document.getElementById('open-search').addEventListener('click', function() {
    document.getElementById('search-box').classList.add('slide-down');
    document.getElementById('opacity-effect').style.display = 'block';
  });

  document.getElementById('exit-search-btn').addEventListener('click', function() {
    document.getElementById('search-box').classList.remove('slide-down');
    document.getElementById('opacity-effect').style.display = 'none';
  });
  if($('.accordion').length){
    $('.accordion .body').hide();
    $('.accordion .head').click(function(){
      $(this).next().slideToggle();
    });
  }

}

window.onscroll = function() {

  const scrollTop = window.pageYOffset;

  if (scrollTop >= 200) {
    document.getElementById('sticky-header').classList.add('slide-down');
  } else {
    document.getElementById('sticky-header').classList.remove('slide-down');
  }

  // HOMEPAGE ELEMENTS
  const threeCallouts = document.getElementsByClassName('callout-text')[0];
  const firsAboutAnimation = document.getElementsByClassName('about-animation-text')[0];
  const serviceTitle = document.getElementById('services-title');
  const appointmentContainer = document.getElementById('hp-appointment-container');
  const homepageServicesTitle = document.getElementById('hp-services-title');
  const homepageServicesText = document.getElementById('hp-services-text');
  const footerLogo = document.getElementById('hp-footer-logo');
  const footerVisitUs = document.querySelectorAll('#hp-visit-us > div');

  // SUBPAGE ELEMENTS
  const spAppointmentContainer = document.getElementById('sp-appointment-container');
  const spFooterLogo = document.getElementById('sp-footer-logo');
  const spFooterLeft = document.getElementById('sp-footer-left');
  const spFooterRight = document.getElementById('sp-footer-right');

  if (document.getElementsByClassName('home')[0] != null) {

    if (isScrolledIntoView(threeCallouts) === true) {
      animateElement('callout-text', 'fade-in', 300);
    }

    if (isScrolledIntoView(firsAboutAnimation) === true) {
      animateElement('about-animation-text', 'fade-in', 300);
    }

    if (isScrolledIntoView(serviceTitle) === true) {
      animateElement('service', 'fade-in', 300);
    }

    if (isScrolledIntoView(appointmentContainer) === true) {
      appointmentContainer.classList.add("fade-in");
    }

    if (isScrolledIntoView(homepageServicesTitle) === true) {
      homepageServicesTitle.classList.add("fade-in");
    }

    if (isScrolledIntoView(homepageServicesText) === true) {
      homepageServicesText.classList.add("fade-in");
    }

    if (isScrolledIntoView(footerLogo) === true) {
      footerLogo.classList.add("fade-in");
      // footerVisitUs.classList.add("fade-in");
      footerVisitUs.forEach(function(visitUsDiv) {
        visitUsDiv.classList.add("fade-in");

      });
    }

    if (scrollTop >= 1600) {
      document.getElementsByClassName('light-gray-line-one')[0].classList.add('animate-gray-line');
      document.getElementsByClassName('light-gray-line-one')[1].classList.add('animate-gray-line');
      document.getElementById('gray-line-one').classList.add('animate-gray-line');
    }

  } else {

    if (spAppointmentContainer != null && isScrolledIntoView(spAppointmentContainer) === true) {
      spAppointmentContainer.classList.add("fade-in");
    }

    if (isScrolledIntoView(spFooterLogo) === true) {
      spFooterLogo.classList.add("fade-in");
    }

    if (isScrolledIntoView(spFooterLeft) === true) {
      spFooterLeft.classList.add("fade-in");
    }

    if (isScrolledIntoView(spFooterRight) === true) {
      spFooterRight.classList.add("fade-in");
    }

  }

}
