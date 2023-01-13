function toggleRadPri(num) {
    let radionica = document.getElementById("radionica_div");
    let prijave = document.getElementById("prijave_div");
    switch (num) {
        case 0:
            radionica.style.display = "block";
            prijave.style.display = "none";
            break;
        case 1:
            radionica.style.display = "none";
            prijave.style.display = "block";
            break;
    }
}