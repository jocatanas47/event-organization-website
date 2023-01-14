<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="view/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="view/script.js"></script>
        <script src="view/korisnik/script.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 header j-light-purple">
                    <div class="row p-2">
                        <a class="col-2 j-purple j-font-white btn" href="routes.php?akcija=profil&kontroler=korisnik">profil</a>

                    </div>
                    <div class="row p-2">
                        <a class="col-2 j-purple j-font-white btn" href="routes.php?akcija=radionice&kontroler=korisnik">radionice</a>
                    </div>
                    <div class="row p-2">
                        <a class="col-2 j-purple j-font-white btn" href="routes.php?akcija=predlog_radionice&kontroler=korisnik">postani organizator</a>
                        <div class="col-9">
                            <a class="col-3 j-purple j-font-white btn" href="routes.php?akcija=izloguj_se&kontroler=gost">izloguj se</a>
                        </div>
                    </div>
                </div>
            </div>
