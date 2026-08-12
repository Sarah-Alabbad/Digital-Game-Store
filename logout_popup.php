<!-- log out Pop-up - Developed by Zahra AL-mari -->

<!-- Logout popup container
     It starts hidden and appears when the user clicks a logout link -->
<div id="logoutPopup" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background-color:rgba(0,0,0,0.65);
    justify-content:center;
    align-items:center;
    z-index:9999;
">

    <!-- Inner popup box -->
    <div style="
        background-color:#2e2c39;
        color:#d1d1e0;
        border:2px solid #4a475e;
        border-radius:15px;
        padding:30px;
        width:360px;
        text-align:center;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
    ">

        <!-- Popup title -->
        <h2 style="color:#bfa67a; margin-top:0;">Logout?</h2>

        <!-- Popup message -->
        <p style="font-size:16px; margin:20px 0;">
            Are you sure you want to log out?
        </p>

        <!-- Popup buttons -->
        <div style="display:flex; justify-content:center; gap:15px;">

            <!-- Cancel button closes the popup -->
            <button id="cancelLogout" class="btn">Cancel</button>

            <!-- Logout button confirms logout and sends the user to logout.php -->
            <button id="confirmLogout" class="btn" style="background-color:#e05252;">Logout</button>
        </div>
    </div>

</div>

<script>
// Get all logout links from the page
const logoutLinks = document.querySelectorAll(".logout-link");

// Get the logout popup and its buttons
const logoutPopup = document.getElementById("logoutPopup");
const cancelLogout = document.getElementById("cancelLogout");
const confirmLogout = document.getElementById("confirmLogout");

// This variable stores the logout link URL temporarily
let logoutUrl = "";

// Add a click event to every logout link
logoutLinks.forEach(function(link) {

    // This runs when the user clicks a logout link
    link.addEventListener("click", function(event) {

        // Stop the link from going to logout.php immediately
        event.preventDefault();

        // Save the logout URL from the clicked link
        logoutUrl = this.href;

        // Show the logout confirmation popup
        logoutPopup.style.display = "flex";
    });
});

// If the user clicks Cancel, close the popup and clear the saved logout URL
cancelLogout.addEventListener("click", function() {
    logoutPopup.style.display = "none";
    logoutUrl = "";
});

// If the user clicks Logout, send them to the saved logout URL
confirmLogout.addEventListener("click", function() {
    window.location.href = logoutUrl;
});

// Close the popup if the user clicks the dark background outside the popup box
logoutPopup.addEventListener("click", function(event) {
    if (event.target === logoutPopup) {
        logoutPopup.style.display = "none";
        logoutUrl = "";
    }
});
</script>