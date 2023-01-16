function toggleUceOrg(num) {
    let x = document.getElementById("org_opcioni");
    if (num == 0) {
        x.style.display = "none";
    } else {
        x.style.display = "block";
    }
}

function togglePriPre(num) {
    let prihvacene = document.getElementById("prihvacene_div");
    let predlozene = document.getElementById("predlozene_div");
    if (num == 0) {
        prihvacene.style.display = "block";
        predlozene.style.display = "none";
    } else {
        prihvacene.style.display = "none";
        predlozene.style.display = "block";
    }
}