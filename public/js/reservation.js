document.addEventListener('DOMContentLoaded', function () {
    const reserveButton = document.getElementById("open-reservation-panel") // Bouton Réserver
    const panel = document.getElementById('reservation-panel'); // Panneau latéral
    const closeButton = document.getElementById('close-panel'); // Bouton de fermeture

    // Ouvrir le panneau lors du clic sur "Réserver"
    reserveButton.addEventListener('click', function () {
        panel.classList.add('active');
    });

    // Fermer le panneau lors du clic sur le bouton de fermeture
    closeButton.addEventListener('click', function () {
        panel.classList.remove('active');
    });

});

document.addEventListener("DOMContentLoaded", function () {


    function setupSearch(inputId, resultsId) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);

        input.addEventListener("input", function () {
            const searchValue = this.value.toLowerCase();
            results.innerHTML = ""; // Effacer les résultats précédents

            if (searchValue) {
                const filteredStations = stations.filter(station =>
                    station.name.toLowerCase().includes(searchValue)
                );

                filteredStations.forEach(station => {
                    const resultItem = document.createElement("div");
                    resultItem.className = "search-result-item";
                    resultItem.textContent = station.name;

                    // Lorsque l'utilisateur clique sur une station, le champ est mis à jour
                    resultItem.addEventListener("click", function () {
                        input.value = station.name;
                        results.innerHTML = ""; // Effacer les suggestions
                    });

                    results.appendChild(resultItem);
                });

                if (!filteredStations.length) {
                    results.innerHTML = "<div class='no-results'>Aucune station trouvée.</div>";
                }
            }
        });

        // Fermer les suggestions si on clique en dehors
        document.addEventListener("click", function (e) {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.innerHTML = "";
            }
        });
    }

    // Activer la recherche sur les champs
    setupSearch("departureSearch", "departureResults");
    setupSearch("destinationSearch", "destinationResults");
});



document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("reservation-form");

    form.addEventListener("submit", function (e) {
        const departure = document.getElementById("departureSearch").value.trim();
        const destination = document.getElementById("destinationSearch").value.trim();
        // .trim() permet de supprimer les espaces avant et après la chaîne de caractères

        if (departure === destination) {
            e.preventDefault(); // Bloque l'envoi uniquement si les stations sont identiques
            alert("La station de départ et la station de destination ne peuvent pas être les mêmes.");
        }

        /// Vérifie si la valeur saisie correspond exactement à un nom de station
        const isDepartureValid = stations.some(station =>
            station.name.toLowerCase() === departure.toLowerCase()
        );
        const isDestinationValid = stations.some(station =>
            station.name.toLowerCase() === destination.toLowerCase()
        );

        if (!isDepartureValid) {
            e.preventDefault();
            alert("La station de départ n'existe pas ou est invalide.");
        }

        if (!isDestinationValid) {
            e.preventDefault();
            alert("La station de destination n'existe pas ou est invalide.");
        }


    });
});

