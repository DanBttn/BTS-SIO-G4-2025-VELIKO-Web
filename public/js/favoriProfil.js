function addToFavorites(stationId, stationName) {
    console.log("Station ID à ajouter/retirer :", stationId);
    console.log("Nom de la station :", stationName);

    fetch('/ajout/favori', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            station_id: stationId,  // Envoie l'ID de la station
            station_name: stationName  // Envoie le nom de la station
        })
    })
        .then(response => response.text())
        .then(data => {
            alert(data);

            // Supprimez dynamiquement la carte de la station
            const stationCard = document.querySelector(`[data-station-id="${stationId}"]`);
            if (stationCard) {
                stationCard.remove();
            }


        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
}