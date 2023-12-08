// Up-to-top Button
window.onscroll = function() {
    scrollFunction()
};

function scrollFunction() {

    let mybutton = document.getElementById("myBtn");

    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        
        mybutton.style.display = "block";
  
    } else {
        
        mybutton.style.display = "none";
    
    }
}

// Klick auf Up-to-top Button
function topFunction() {

    document.body.scrollTop = 0; // Für Safari
    document.documentElement.scrollTop = 0; // Für Chrome, Firefox, IE and Opera

}

holeFilter();

// Games holen (ohne Filter)
holeGames();

function holeGames(){

    var url = 'php/holeGames.php'; 

    fetch(url)
        .then((res) => {

            if (res.status >= 200 && res.status < 300) {
            
                return res.json();

            } else {

                alert('Ein Fehler ist aufgetreten. Kein einziges Spiel gefunden. Sorry.');

            }   
        })
        .then((data) => {
        
            gamesAnzeigen(data);

        })
}

// Games anzeigen mit oder ohne Filter
function gamesAnzeigen(data) {

    let listeLeer = document.getElementById("list");
    listeLeer.innerHTML = "";

    data.forEach(game => {
        let gameContainer = document.createElement("section");
        gameContainer.innerHTML =
            `<div class="gameTitelBild"><img src="${game.game_bild}" alt="${game.name}" class="gameBild"/></div>
            <h2>${game.name}</h2>
            <p>${game.beschreibung}</p>
            <h3>Allgemeine Infos</h3>
            <div class="gameDetails">
                <p><strong>Fach:</strong> ${game.fach}</p>  
                <p class="detailsJahr"><strong>Jahr:</strong> ${game.jahr}</p>
                <p><strong>Genre:</strong> ${game.genre}</p>
            </div>
            <p><strong>Verfügbar für:</strong></p>`;
    
            for (let i = 0; i < game.plattform_bild.length; i++) {
                let img = document.createElement("img");
                img.src = game.plattform_bild[i];
                img.alt = game.plattform[i];
                img.className = "iconPlattform";    
                gameContainer.appendChild(img);
            }
    
        gameContainer.innerHTML += `<br/><a href="${game.linkURL}" target="_blank"><strong>Link zum Game</strong></a>`;
        document.getElementById("list").appendChild(gameContainer);

    });
}

// Filter holen und anzeigen
function holeFilter() {

    var url = 'php/holeFilter.php';

    fetch(url)
        .then(res => {

            if (res.status >= 200 && res.status < 300) {

                return res.json();

            } else {

                throw new Error('Fehler beim Holen der Filter.');

            }
        })
        .then(data => {
            
            // Erstellung der Checkboxen
            data.fach.forEach(fach => {

                let fachContainer = document.createElement("div");
                fachContainer.innerHTML =

                    `<input type="checkbox" id="${fach.name}" name="fach" value="${fach.name}"/>
                    <label for="${fach.name}">${fach.name} (${fach.game_count})</label>
                    `;

                document.getElementById("fach-liste").appendChild(fachContainer);
            });

            data.plattform.forEach(plattform => {
            
                    let plattformContainer = document.createElement("div");
                    plattformContainer.innerHTML =
            
                        `<input type="checkbox" id="${plattform.name}" name="plattform" value="${plattform.name}"/>
                        <label for="${plattform.name}"><img src="${plattform.bild}" alt="${plattform.name}" /> <span>${plattform.name} (${plattform.game_count})</span></label>
                        `;
            
                    document.getElementById("plattform-liste").appendChild(plattformContainer);
            
            });

            data.genre.forEach(genre => {
            
                let genreContainer = document.createElement("div");
                genreContainer.innerHTML =
        
                    `<input type="checkbox" id="${genre.name}" name="genre" value="${genre.name}"/>
                    <label for="${genre.name}">${genre.name} (${genre.game_count})</span></label>
                    `;
        
                document.getElementById("genre-liste").appendChild(genreContainer);
        
            });
            
        });

        // Event-Listener für Änderungen an den Checkboxen
        document.addEventListener('change', function(event) {
            if (event.target.matches('input[name="plattform"], input[name="fach"], input[name="genre"]')) {
                getFilteredGames();
            }
        });
    
}

// Filter anwenden

function getFilteredGames() {

    // console.log('getFilteredGames ausgeführt!');

    // Erhalten der ausgewählten Filter
    let selectedPlattform = Array.from(document.querySelectorAll('input[name="plattform"]:checked')).map(checkbox => checkbox.value);
    let selectedFach = Array.from(document.querySelectorAll('input[name="fach"]:checked')).map(checkbox => checkbox.value);
    let selectedGenre = Array.from(document.querySelectorAll('input[name="genre"]:checked')).map(checkbox => checkbox.value);

    // Kontrollieren, ob Filter vorhanden sind
    if (selectedFach.length == 0 && selectedPlattform.length == 0 && selectedGenre.length == 0) {
        holeGames();
        return;
    }

    let formData = new FormData();
    formData.append('plattform', selectedPlattform);
    formData.append('fach', selectedFach);
    formData.append('genre', selectedGenre);

    var url = 'php/holeFilteredGames.php';

    fetch(url, {
        body: formData,
        method: 'POST', 
    })
        .then(res => {

            if (res.status >= 200 && res.status < 300) {

                return res.json();

            } else {

                throw new Error('Fehler beim Abrufen der Spiele.');

            }
        })
        .then(data => {

            gamesAnzeigen(data);
            // console.log(data);
            
        })

}
