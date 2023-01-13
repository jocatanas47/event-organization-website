var sortNaziv = "asc";
var sortDatum = "asc"

function sortirajTabelu(num) {
    let tabela, redovi, i, x, y, zameni;
    tabela = document.getElementById("tabela");
    let flag = true;
    let sort;
    if (num === 1) {
        sort = sortNaziv;
        if (sortNaziv === "asc") {
            sortNaziv = "dsc";
        } else {
            sortNaziv = "asc";
        }
    } else {
        sort = sortDatum;
        if (sortDatum === "asc") {
            sortDatum = "dsc";
        } else {
            sortDatum = "asc";
        }
    }
    while (flag) {
        flag = false;
        redovi = tabela.rows;
        for (i = 0; i < (redovi.length - 1); i++) {
            zameni = false;
            x = redovi[i].getElementsByTagName("TD")[num];
            y = redovi[i + 1].getElementsByTagName("TD")[num];
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() && sort === "asc") {
                zameni = true;
                break;
            }
            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase() && sort === "dsc") {
                zameni = true;
                break;
            }
        }
        if (zameni) {
            redovi[i].parentNode.insertBefore(redovi[i + 1], redovi[i]);
            flag = true;
        }
    }
}