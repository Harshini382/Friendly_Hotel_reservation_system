document.addEventListener('DOMContentLoaded', function() {
    // Simulate fetching number of occupied rooms and total rooms
    setTimeout(function() {
      const occupiedRooms = 50; // Replace with actual dynamic data fetching logic
      const totalRooms = 100; // Replace with actual total number of rooms
      const availableRooms = totalRooms - occupiedRooms;
  
      // Update displayed room numbers
      document.getElementById('occupiedRooms').textContent = occupiedRooms;
      document.getElementById('availableRooms').textContent = availableRooms;
  
      // Create occupancy visualization
      const occupancyVisualization = document.getElementById('occupancyVisualization');
      occupancyVisualization.innerHTML = ''; // Clear previous visualization
  
      for (let i = 0; i < totalRooms; i++) {
        const circle = document.createElement('div');
        circle.className = 'circle';
        if (i >= occupiedRooms) {
          circle.classList.add('unoccupied');
        }
        occupancyVisualization.appendChild(circle);
      }
  
      // Image carousel animation
      const images = document.querySelectorAll('.background-carousel img');
      let currentImageIndex = 0;
      
      setInterval(() => {
        // Hide current image
        images[currentImageIndex].classList.remove('active');
        
        // Move to next image
        currentImageIndex = (currentImageIndex + 1) % images.length;
        
        // Show next image
        images[currentImageIndex].classList.add('active');
      }, 4000); // Change image every 4 seconds (4000 milliseconds)
    }, 1000); // Simulate delay for fetching data
  });
