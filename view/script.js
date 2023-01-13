function toggleUceOrg(num) {
    let x = document.getElementById("org_opcioni");
    if (num == 0) {
        x.style.display = "none";
    } else {
        x.style.display = "block";
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

function dobraLozinka(lozinka) {
    if (lozinka.length < 8 || lozinka.length > 16) {
        return false;
    }
    if (!/^[a-zA-Z]/.test(lozinka)) {
        return false;
    }
    if (!/[A-Z]/.test(lozinka)) {
        return false;
    }
    if (!/\d/.test(lozinka)) {
        return false;
    }
    if (!/[^a-zA-Z\d]/.test(lozinka)) {
        return false;
    }
    return true;
}

function dohvatiDimenzije(imageFile) {
    return new Promise((resolve, reject) => {
        let img = new Image();
        img.onload = function() {
            let width = img.naturalWidth;
            let height = img.naturalHeight;
            resolve({ width: width, height: height });
        };
        img.onerror = function() {
            reject(new Error());
        };
        img.src = URL.createObjectURL(imageFile);
    });
}

async function dimenzijeSlike(slika) {
    let width;
    let height;
    try {
        const dimensions = await dohvatiDimenzije(slika.files[0]);
        return dimensions;
    } catch (error) {
        return { width: 0, height: 0 };
    }
}

async function validacijaSlike(slika) {
    dimenzije = await dimenzijeSlike(slika);
    width = dimenzije.width;
    height = dimenzije.height;
    if (width >= 100 && width <= 300 && height >= 100 && height <= 300) {
        return true;
    } else {
        return false;
    }
}

async function registracija() {
    let ime = document.getElementById("ime").value;
    let prezime = document.getElementById("prezime").value;
    let kor_ime = document.getElementById("kor_ime").value;
    let lozinka = document.getElementById("lozinka").value;
    let potvrda = document.getElementById("potvrda").value;
    let telefon = document.getElementById("telefon").value;
    let mejl = document.getElementById("mejl").value;
    let ucesnik = document.getElementById("ucesnik").checked;
    let organizator = document.getElementById("organizator").checked;
    let slika = document.getElementById("slika");
    let imaSlika;
    if(slika.files.length === 0) {
        imaSlika = "false";
    } else {
        imaSlika = "true";
    }
    
    if (ime == "" || prezime == "" || kor_ime == "" || lozinka == ""
            || potvrda == "" || telefon == "" || mejl == "") {
        alert("polja označena zvezdicom su obavezna!");
        return;
    }
    if (!dobraLozinka(lozinka)) {
        alert("lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom");
        return;
    }
    if (lozinka != potvrda) {
        alert("potvrda lozinke mora biti ista kao lozinka");
        return;
    }
    tmp = await validacijaSlike(slika);
    if (slika.value != "" && !tmp) {
        alert("slika mora biti dimenzija 100x100 do 300x300");
        return;
    }
    
    let formData = new FormData();
    formData.append("ime", ime);
    formData.append("prezime", prezime);
    formData.append("kor_ime", kor_ime);
    formData.append("lozinka", lozinka);
    formData.append("potvrda", lozinka);
    formData.append("telefon", telefon);
    formData.append("mejl", mejl);
    formData.append("slika", slika.files[0]);
    formData.append("ima_slika", imaSlika);
    
    if (ucesnik) {
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("greska").innerHTML = xmlhttp.responseText;
            }
        };
        formData.append("akcija", "registracija_ucesnika");
        formData.append("kontroler", "gost");
        xmlhttp.open("POST", "routes.php", true);
        xmlhttp.send(formData);
    } 
    else {
        let naziv = document.getElementById("naziv").value;
        let maticni_broj = document.getElementById("maticni_broj").value;
        let drzava = document.getElementById("drzava").value;
        let grad = document.getElementById("grad").value;
        let postanski_broj = document.getElementById("postanski_broj").value;
        let ulica = document.getElementById("ulica").value;
        let adresa_broj = document.getElementById("adresa_broj").value;

        formData.append("naziv", naziv);
        formData.append("maticni_broj", maticni_broj);
        formData.append("drzava", drzava);
        formData.append("grad", grad);
        formData.append("postanski_broj", postanski_broj);
        formData.append("ulica", ulica);
        formData.append("adresa_broj", adresa_broj);
        
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("greska").innerHTML = xmlhttp.responseText;
            }
        };
        formData.append("akcija", "registracija_organizatora");
        formData.append("kontroler", "gost");
        xmlhttp.open("POST", "routes.php", true);
        xmlhttp.send(formData);
    }
}

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