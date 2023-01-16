<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="view/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="view/script.js"></script>
        <script src="view/administrator/script.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 header j-light-purple">
                    <div class="row p-2">
                        <a class="col-6 col-md-2 j-purple j-font-white btn" href="routes.php?akcija=promena_lozinke&kontroler=administrator">promeni lozinku</a>

                    </div>
                    <div class="row p-2">
                        <a class="col-6 col-md-2 j-purple j-font-white btn" href="routes.php?akcija=korisnici&kontroler=administrator">korisnici</a>
                    
                    </div>
                    <div class="row p-2">
                        <a class="col-6 col-md-2 j-purple j-font-white btn" href="routes.php?akcija=radionice&kontroler=administrator">radionice</a>

                        <div class="col-6 col-md-9">
                            <a class="col-12 col-md-3 j-purple j-font-white btn" href="routes.php?akcija=izloguj_se&kontroler=gost">izloguj se</a>
                        </div>
                    </div>
                </div>
            </div>
