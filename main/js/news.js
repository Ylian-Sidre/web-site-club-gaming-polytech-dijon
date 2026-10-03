async function loadNews() {
    try {
        const response = await fetch('../../php/news/get_news.php');
        const data = await response.json();

        if (data.error) {
            console.error("Erreur DB:", data.error);
            return;
        }

        const newsContainer = document.querySelector('.News');
        renderNews(data.news, newsContainer);
        overlayingDiv();

    } catch (error) {
        console.error("Erreur lors de la récupération:", error);
    }
}

function renderNews(data, container) {
    
    data.forEach(news => {
        const newsDiv = document.createElement('div');
        newsDiv.className = 'intraNews';
        newsDiv.innerHTML = `
                <div>
                    <h2>${news.title}</h2>
                    <p>${news.description}</p>
                </div>
                <img src="../../${news.imageUrl}" width="300">
            
        `;
        container.appendChild(newsDiv);
    });
}

window.onload = loadNews;