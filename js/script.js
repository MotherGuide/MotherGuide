window.addEventListener("scroll", function() {
    const navbar = document.getElementById("navbar");
    const navleft1 = document.getElementById("nav-left-1");

    if (window.scrollY > 40) {
        navbar.classList.add("scrolled");
        // navleft1.classList.remove('hidden');
    } else {
        navbar.classList.remove("scrolled");
        // navleft1.classList.add('hidden');
    }
});
// Handle Like/Dislike[cite: 11]
function handleInteraction(tipId, type) {
    const formData = new FormData();
    formData.append('tip_id', tipId);
    formData.append('type', type);

    fetch("api/interact.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            location.reload(); // Refresh to see updated counts
        }
    });
}

// Example: Attach to your stat divs[cite: 16, 17]
// Note: These need to be attached to actual elements in the HTML
// This is a template - you'll need to add proper IDs or classes to your HTML elements