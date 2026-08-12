async function fetchGameInfo() {
    const titleInput = document.querySelector('input[name="title"]');
    const genreInput = document.querySelector('select[name="genre"]');
    const priceInput = document.querySelector('input[name="price"]');
    const imageInput = document.querySelector('input[name="image"]');
    const descriptionInput = document.querySelector('textarea[name="description"]');

    const title = titleInput.value.toLowerCase().trim();

    const localGames = {
        "zelda": {
            genre: "Adventure",
            price: "160",
            image: "zelda.jpg",
            description: "Explore a vast world full of puzzles and secrets."
        },
        "fifa": {
            genre: "Sports",
            price: "200",
            image: "football.jpg",
            description: "Play football matches and tournaments."
        },
        "resident evil": {
            genre: "Action",
            price: "140",
            image: "resident.jpg",
            description: "Fight enemies and survive dangerous missions."
        }
    };

    function checkDiscount(price) {
        var discountMessage = document.getElementById("discountMessage");

        if (parseFloat(price) < 50) {
            priceInput.style.color = "green";
            priceInput.style.fontWeight = "bold";

            if (discountMessage) {
                discountMessage.innerHTML = "Discount Available";
                discountMessage.style.color = "green";
                discountMessage.style.fontWeight = "bold";
            }
        } else {
            priceInput.style.color = "black";
            priceInput.style.fontWeight = "normal";

            if (discountMessage) {
                discountMessage.innerHTML = "";
            }
        }
    }

    if (title === "") {
        alert("Enter game title first");
        return;
    }

    try {
        const response = await fetch("https://www.cheapshark.com/api/1.0/games?title=" + encodeURIComponent(title));
        const data = await response.json();

        if (data.length > 0) {
            const game = data[0];

            priceInput.value = game.cheapest;
            imageInput.value = game.thumb;
            descriptionInput.value = "Fetched from API";

            checkDiscount(game.cheapest);

            alert("Loaded from API");
            return;
        }
    } catch (e) {
        console.log("API failed");
    }

    if (localGames[title]) {
        genreInput.value = localGames[title].genre;
        priceInput.value = localGames[title].price;
        imageInput.value = localGames[title].image;
        descriptionInput.value = localGames[title].description;

        checkDiscount(localGames[title].price);

        alert("Loaded from local data");
    } else {
        alert("Game not found");
    }
}