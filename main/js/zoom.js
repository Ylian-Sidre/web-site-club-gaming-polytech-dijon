function newsLoaded() {
    const trigger = document.querySelectorAll(".inside-news, .Event, .intraNews")
    return trigger
}

const overlay = document.getElementById('overlay');

function overlayingDiv() {
    newsLoaded().forEach(image => {
    image.addEventListener('click', () => {
        overlay.innerHTML = '';
        const cloneImage = image.cloneNode(true);
        overlay.appendChild(cloneImage)
        overlay.style.display = 'flex'; 
        
        setTimeout(() => {
            overlay.classList.add('active');

        }, 10);
    });
});

}

overlay.addEventListener('click', () => {
    overlay.classList.remove('active');
    setTimeout(() => {
        overlay.style.display = 'none';
    }, 300); 
});