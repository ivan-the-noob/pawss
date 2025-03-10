const sliderWrapper = document.querySelector('.slider-wrapper');
        let isDragging = false;
        let startX;
        let scrollLeft;

        sliderWrapper.addEventListener('mousedown', (e) => {
            isDragging = true;
            sliderWrapper.classList.add('active');
            startX = e.pageX - sliderWrapper.offsetLeft;
            scrollLeft = sliderWrapper.scrollLeft;
        });

        sliderWrapper.addEventListener('mouseleave', () => {
            isDragging = false;
            sliderWrapper.classList.remove('active');
        });

        sliderWrapper.addEventListener('mouseup', () => {
            isDragging = false;
            sliderWrapper.classList.remove('active');
        });

        sliderWrapper.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - sliderWrapper.offsetLeft;
            const walk = (x - startX) * 2; 
            sliderWrapper.scrollLeft = scrollLeft - walk;
        });

        sliderWrapper.addEventListener('touchstart', (e) => {
            isDragging = true;
            startX = e.touches[0].pageX - sliderWrapper.offsetLeft;
            scrollLeft = sliderWrapper.scrollLeft;
        });

        sliderWrapper.addEventListener('touchend', () => {
            isDragging = false;
        });

        sliderWrapper.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.touches[0].pageX - sliderWrapper.offsetLeft;
            const walk = (x - startX) * 2; 
            sliderWrapper.scrollLeft = scrollLeft - walk;
        });