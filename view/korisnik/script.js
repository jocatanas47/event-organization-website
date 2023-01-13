var smer = "asc";
function sortirajRadionice(num) {
    let tabela, redovi, i, x, y, zameni;
    
    tabela = document.getElementById("radionice_tabela");
    let flag = true;
    while (flag) {
        flag = false;
        redovi = tabela.rows;
        for (i = 1; i < (redovi.length - 1); i++) {
            zameni = false;
            x = redovi[i].getElementsByTagName("TD")[num];
            y = redovi[i + 1].getElementsByTagName("TD")[num];
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() && smer === "asc") {
                zameni = true;
                break;
            }
            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase() && smer === "dsc") {
                zameni = true;
                break;
            }
        }
        if (zameni) {
            redovi[i].parentNode.insertBefore(redovi[i + 1], redovi[i]);
            flag = true;
        }
    }
    if (smer === "asc") {
        smer = "dsc";
    } else {
        smer = "asc";
    }
}

function togglePodRadAk(num) {
    let podaci = document.getElementById("podaci_div");
    let radionice = document.getElementById("radionice_div");
    let akcije = document.getElementById("akcije_div");
    switch (num) {
        case 0:
            podaci.style.display = "block";
            radionice.style.display = "none";
            akcije.style.display = "none";
            break;
        case 1:
            podaci.style.display = "none";
            radionice.style.display = "block";
            akcije.style.display = "none";
            break;
        case 2:
            podaci.style.display = "none";
            radionice.style.display = "none";
            akcije.style.display = "block";
            break;
    }
}

function togglePrijavljeneSve(num) {
    let prijavljene = document.getElementById("prijavljene_div");
    let sve = document.getElementById("sve_div");
    switch (num) {
        case 0:
            prijavljene.style.display = "block";
            sve.style.display = "none";
            break;
        case 1:
            prijavljene.style.display = "none";
            sve.style.display = "block";
            break;
    }
}