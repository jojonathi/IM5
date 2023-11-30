holeFilter();

//Games holen

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

function gamesAnzeigen(data) {

    let listeLeer = document.getElementById("list");

    listeLeer.innerHTML = "";

    data.forEach(game => {
        let gameContainer = document.createElement("div");
        gameContainer.innerHTML =
            `<img src="${game.game_bild}" alt="${game.name}" class="gameBild"/>
            <h3>${game.name}</h3>
            <p>${game.beschreibung}</p>
            <p>Fach: ${game.fach}</p>`;
    
            game.plattform_bild.forEach(plattformBild => {
                let img = document.createElement("img");
                img.src = plattformBild;
                img.alt = game.plattform;
                img.className = "iconPlattform";
                gameContainer.appendChild(img);
            });
    
        gameContainer.innerHTML += `<br/><a href="${game.linkURL}">Link zum Game</a>`;
        document.getElementById("list").appendChild(gameContainer);

    });
}

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
            
            // console.log(data);
            
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
            
                        `<input type="checkbox" id="${plattform.name}" name="plattform" value="${plattform.nameä}"/>
                        <label for="${plattform.name}"><img src="${plattform.bild}" alt="${plattform.name}" /> <span>${plattform.name} (${plattform.game_count})</span></label>
                        `;
            
                    document.getElementById("plattform-liste").appendChild(plattformContainer);
            
                });
            
        });

        // Event-Listener für Änderungen an den Checkboxen
        document.addEventListener('change', function(event) {
            if (event.target.matches('input[name="plattform"], input[name="fach"]')) {
                getFilteredGames();
            }
        });
    
}

// Filter

function getFilteredGames() {

    // console.log('getFilteredGames ausgeführt!');

    let selectedPlattform = Array.from(document.querySelectorAll('input[name="plattform"]:checked')).map(checkbox => checkbox.value);
    let selectedFach = Array.from(document.querySelectorAll('input[name="fach"]:checked')).map(checkbox => checkbox.value);

    let requestData = {
        plattform: selectedPlattform,
        fach: selectedFach
    };

    console.log(requestData);

    var url = 'php/holeFilteredGames.php';

    fetch(url, {
        body: JSON.stringify(requestData),
        method: 'post', 
    })
        .then(res => {

            if (res.ok) {

                return res.json();

            } else {

                throw new Error('Fehler beim Abrufen der Spiele.');

            }
        })
        .then(data => {

            gamesAnzeigen(data);
            console.log(data);

        })

    
}

