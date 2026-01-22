document.addEventListener('DOMContentLoaded', () => {
  const section = document.querySelector('#choose-us');
  const reviewBoxes = section.querySelectorAll('.review-box');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
      } else {
        entry.target.classList.remove('show');
      }
    });
  }, { threshold: 0.1 });

  reviewBoxes.forEach(box => {
    observer.observe(box);
  });
});
