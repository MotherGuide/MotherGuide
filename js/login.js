fetch("login.php", {
    method: "POST",
    body: new FormData(document.getElementById("login-form"))
})
.then(res => res.json())
.then(data => {

    if (data.status === "success") {
        alert(data.message);
        window.location.href = "../index.php";
    } else {
        alert(data.message);
    }

});