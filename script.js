// Digital Game Store Script
// Modified by Roaa Alhaddad & Lama Alshaikah


// Store selected game URL for popup navigation
let selectedGameUrl = "";


// Show popup when user clicks View Details
function showGameDetailsPopup(name, url) {

    // Get popup elements
    const popup = document.getElementById("gameDetailsPopup");
    const text = document.getElementById("gameDetailsPopupText");

    // Save selected game URL
    selectedGameUrl = url;

    // Change popup text dynamically
    text.textContent = "Do you want to view details for " + name + "?";

    // Display popup
    popup.style.display = "flex";
}


// Close popup window
function closeGameDetailsPopup() {

    // Get popup element
    const popup = document.getElementById("gameDetailsPopup");

    // Hide popup
    popup.style.display = "none";

    // Clear selected URL
    selectedGameUrl = "";
}


// Open selected game page
function confirmGameDetailsPopup() {

    // Redirect to selected game details page
    window.location.href = selectedGameUrl;
}


// Enlarge game card on mouse hover
function enlargeCard(id) {

    document.getElementById(id).style.transform = "scale(1.05)";
}


// Return card to normal size
function normalCard(id) {

    document.getElementById(id).style.transform = "scale(1)";
}


// Show total number of categories
function showCategoryCount() {

    if (document.getElementById("categoryCount")) {

        document.getElementById("categoryCount").innerHTML =
            "Available Categories: " + categoryIds.length;
    }
}


// Show total number of games
function showGameCount() {

    if (document.getElementById("gameCount")) {

        // If only one game exists
        if (gameIds.length == 1) {

            document.getElementById("gameCount").innerHTML =
                "1 game available";

        } else {

            // Display total games
            document.getElementById("gameCount").innerHTML =
                gameIds.length + " games available";
        }
    }
}


// Load games from API using AJAX
function loadAPIData() {

    // Create AJAX request
    var request = new XMLHttpRequest();

    // Open API file
    request.open("GET", "api_games.php", true);

    // Run when response is loaded
    request.onload = function() {

        // If request successful
        if (request.status == 200) {

            // Convert JSON data
            var data = JSON.parse(request.responseText);

            // Display API status message
            if (document.getElementById("apiMessage")) {

                // Check for API error
                if (data.error) {

                    document.getElementById("apiMessage").innerHTML =
                        "API error";

                } else {

                    // Show loaded games count
                    document.getElementById("apiMessage").innerHTML =
                        "API loaded " + data.length + " games from database";
                }
            }

        } else {

            // If request failed
            if (document.getElementById("apiMessage")) {

                document.getElementById("apiMessage").innerHTML =
                    "API request failed";
            }
        }
    };

    // Send request
    request.send();
}


// Run all functions when page loads
window.onload = function() {

    // Display category count
    showCategoryCount();

    // Display game count
    showGameCount();

    // Load API data
    loadAPIData();
};