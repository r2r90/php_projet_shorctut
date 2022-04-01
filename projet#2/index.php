<?php

// Is received shortcut

if(!empty($_GET['q'])) {

    $shortcut = htmlspecialchars($_GET['q']);

    //is a shortcut

    $bdd = new PDO('mysql:host=localhost;dbname=beetly;charset=utf8', 'root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');

    $req->execute(array($shortcut));

    while ($result = $req->fetch()) {
        if ($result['x'] != 1) {
            header('location: ./?error=true&message=Adresse URL non connue');
            exit();
        }
    }

    // REDIRECTION 

    $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while($result = $req->fetch()) {
        header('location: '.$result['url']);
        exit();
    }


}


if (!empty($_POST['url'])) {

    $url = $_POST['url'];

    // Verification si c'est bien un adresse url 
    if (!filter_var($url, FILTER_VALIDATE_URL)) {

        header('location:./?error=true&message=Adresse url non valide');
        exit();
    }

    // Shortcut 

    $shortcut = crypt($url, rand());


    // Has been already send ?

    try {
        $bdd = new PDO("mysql:host=localhost;dbname=beetly_projet;charset=utf8", 'root', '');
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();;
    }

    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');

    $req->execute(array($url));

    while ($result = $req->fetch()) {
        if ($result['x'] != 0) {
            header('location: ./?error=true&message=Adresse déjà raccourcie');
            exit();
        }
    }

    // Sending if all is OK

    $req = $bdd->prepare("INSERT INTO links(url, shortcut) VALUES(?, ?)") or die(print_r($bdd->errorInfo()));
    $req->execute(array($url, $shortcut));

    header('location: ./?short=' . $shortcut);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raccourcisseur d'url express</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,regular,500,600,700,800,900,100italic,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic" rel="stylesheet" />
    <link rel="icon" type="image/png" href="./img/favico.png">
    <link rel="stylesheet" href="./Design/default.css">
</head>

<body>

    <!-- Presentation -->

    <section id="hello">
        <div class="container">
            <header>
                <img src="./img/logo.png" alt="logo" id="logo">
            </header>
            <h1>Une url longue? Raccourcissez-là</h1>
            <h2>Largement meilleur et plus court que les autres.</h2>


            <form method="POST" action="./">
                <input type="url" name="url" placeholder="Collez votre lien à raccourcir">
                <input type="submit" value="Raccourcir">
            </form>

            <?php if (isset($_GET['error']) && isset($_GET['message'])) {
            ?>
                <div class="center">
                    <div id="result">
                        <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                    </div>
                </div>
            <?php  } else if (!empty($_GET['short'])) { ?>
                <div class="center">
                    <div id="result">
                        <b>URL RACCOURCIE</b>
                        http://localhost/?q=<?php  echo htmlspecialchars($_GET['short']);   ?>
                    </div>
                </div>

            <?php } ?>



        </div>
    </section>

    <section id="brands">
        <div class="container">
            <h3>
                Ces MArques nous font confiance
            </h3>
            <img src="./img/1.png" alt="" class="picture">
            <img src="./img/2.png" alt="" class="picture">
            <img src="./img/3.png" alt="" class="picture">
            <img src="./img/4.png" alt="" class="picture">
        </div>
    </section>

    <footer>
        <div class="container">
            <img src="./img/logo2.png" alt="" id="logo_footer"><br>
            2022 © Artur <br>
            <a href="#">Contact</a> - <a href="#">A Propos</a>
        </div>
    </footer>

</body>

</html>