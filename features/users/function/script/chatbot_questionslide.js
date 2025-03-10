const horizontalSliderContent = document.querySelector('.horizontal-slider-content');
let isMouseDragging = false; 
let initialX;
let initialScrollLeft; 

horizontalSliderContent.addEventListener('mousedown', (e) => {
    isMouseDragging = true;
    horizontalSliderContent.classList.add('active');
    initialX = e.pageX - horizontalSliderContent.offsetLeft; 
    initialScrollLeft = horizontalSliderContent.scrollLeft; 
});

horizontalSliderContent.addEventListener('mouseleave', () => {
    isMouseDragging = false;
    horizontalSliderContent.classList.remove('active');
});

horizontalSliderContent.addEventListener('mouseup', () => {
    isMouseDragging = false;
    horizontalSliderContent.classList.remove('active');
});

horizontalSliderContent.addEventListener('mousemove', (e) => {
    if (!isMouseDragging) return;
    e.preventDefault();
    const x = e.pageX - horizontalSliderContent.offsetLeft;
    const walk = (x - initialX) * 2; 
    horizontalSliderContent.scrollLeft = initialScrollLeft - walk; 
});

horizontalSliderContent.addEventListener('touchstart', (e) => {
    isMouseDragging = true;
    initialX = e.touches[0].pageX - horizontalSliderContent.offsetLeft; 
    initialScrollLeft = horizontalSliderContent.scrollLeft; 
});

horizontalSliderContent.addEventListener('touchend', () => {
    isMouseDragging = false;
});

horizontalSliderContent.addEventListener('touchmove', (e) => {
    if (!isMouseDragging) return;
    e.preventDefault();
    const x = e.touches[0].pageX - horizontalSliderContent.offsetLeft;
    const walk = (x - initialX) * 2;
    horizontalSliderContent.scrollLeft = initialScrollLeft - walk; 
});