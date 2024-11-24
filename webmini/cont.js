// JavaScript to show contact info on scroll
window.addEventListener('scroll', function() {
    var contactInfo = document.querySelector('.contact-info');
    var position = contactInfo.getBoundingClientRect();
  
    // If the contact info section is in view
    if (position.top < window.innerHeight && position.bottom >= 0) {
      contactInfo.classList.add('show');
    } else {
      contactInfo.classList.remove('show');
    }
  });
