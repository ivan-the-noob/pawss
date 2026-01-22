document.addEventListener('DOMContentLoaded', function () {
    const section = document.querySelector('#services');
    const cards = section.querySelectorAll('.card');
  
    cards.forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)'; 
    });
  
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          cards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)'; 
          });
        } else {
          cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)'; 
          });
        }
      });
    }, { threshold: 0.1 });
  
    observer.observe(section);
  });

  