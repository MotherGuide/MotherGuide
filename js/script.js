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