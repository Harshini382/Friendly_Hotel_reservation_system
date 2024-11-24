document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to room elements
    const rooms = document.querySelectorAll('.room');

    rooms.forEach(room => {
        const roomImg = room.querySelector('img');
        const roomDetails = room.querySelector('.room-details');

        // Show room details on hover
        room.addEventListener('mouseover', function() {
            roomImg.style.transform = 'scale(1.1)';
            roomDetails.style.transform = 'translateY(0)';
        });

        // Hide room details on mouseout
        room.addEventListener('mouseout', function() {
            roomImg.style.transform = 'scale(1)';
            roomDetails.style.transform = 'translateY(100%)';
        });
    });

    // Add smooth scrolling effect to navigation links
    const navLinks = document.querySelectorAll('.navbar-links a');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Check if the link is targeting a section on the same page
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                e.preventDefault();

                window.scrollTo({
                    top: targetElement.offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Handle opening about.html in a new tab
    const aboutLink = document.querySelector('.navbar-links a[href="about.html"]');
    
    if (aboutLink) {
        aboutLink.setAttribute('target', '_blank');
    }
});

