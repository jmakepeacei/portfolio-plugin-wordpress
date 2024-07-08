document.addEventListener('DOMContentLoaded', function() {
    const portfolioItems = document.querySelectorAll('.portfolio-item');
  
    portfolioItems.forEach(item => {
      item.addEventListener('touchstart', function(e) {
        if (this.classList.contains('mobile-hover')) {
          this.classList.remove('mobile-hover');
        } else {
          portfolioItems.forEach(otherItem => {
            otherItem.classList.remove('mobile-hover');
          });
          this.classList.add('mobile-hover');
        }
        e.stopPropagation();
      });
    });
  
    document.addEventListener('touchstart', function() {
      portfolioItems.forEach(item => {
        item.classList.remove('mobile-hover');
      });
    });
  });
  