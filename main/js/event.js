async function loadEvents() {
    try {
        const response = await fetch('../../php/event/get_events.php');
        const data = await response.json();

        if (data.error) {
            console.error("Erreur DB:", data.error);
            return;
        }

        const ongoingContainer = document.querySelector('.onGoingEventsOnly');
        renderEvents(data.ongoing, ongoingContainer);

        const pastContainer = document.querySelector('.pastEventsOnly');
        renderEvents(data.past, pastContainer);
        overlayingDiv();

    } catch (error) {
        console.error("Erreur lors de la récupération:", error);
    }
}

function renderEvents(events, container) {
    container.innerHTML = ``;

    events.forEach(event => {
        console.log(event.imageUrl)
        const eventDiv = document.createElement('div');
        eventDiv.className = 'Event';
        eventDiv.innerHTML = `
            <div>
                <h2>${event.title}</h2>
                <p>${event.description}</p>
            </div>
            <img src="../../${event.imageUrl}" width="300">
        `;
        container.appendChild(eventDiv);
    });
}

window.onload = loadEvents;