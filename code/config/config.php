<?php
$rep = __DIR__ . '/../';
$erreur = '';

//BD
$login = 'root';
$mdp = "";
$dsn = 'mysql:host=localhost;port=3307;dbname=startyourproject;charset=utf8';


function XSTR_LOG($chaine)
{
    $fp = fopen("pageLog.log", "a+");
    fwrite($fp, $chaine . "\n");
    fclose($fp);
}

//Vues
$bootstrapCSS = 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css';
$bootstrapJS = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js';
$bootstrapJquery = 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js';
$bootstrapJava = 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js';

$images['imageAccueil1'] = 'vues/media/tache.PNG';
$images['imageAccueil2'] = 'vues/media/Ptut.PNG';
$images['logo'] = 'vues/media/ImageLogo.png';
$images['logo2'] = 'vues/media/logo2.png';
$images['mesProjets'] = 'vues/media/projet.png';
$images['carousel'] = 'vues/media/carousel.jpg';


$vues['pageErreur'] = 'vues/pageErreur.php';
$vues['pageAccueil'] = 'vues/pageAccueil.php';
$vues['pageConnexion'] = 'vues/pageConnexion.php';
$vues['pageMesProjets'] = 'vues/pageMesProjets.php';
$vues['pageProjet'] = 'vues/pageProjet.php';
$vues['pageCreationTache'] = 'vues/pageCreationTache.php';
$vues['pageCreationProjet'] = 'vues/pageCreationProjet.php';
$vues['pageMenu'] = 'vues/pageMenu.php';
$vues['inscription'] = 'vues/pageInscription.php';
$vues['messagerie'] = 'vues/pageMessagerie.php';

$body = ' <!DOCTYPE html>
<head>
    <meta charset="utf-8"/>
    <title>Start Your Project</title>
</head>
<body>
    <table width="100%" style="background-color: black;">
        <tbody>
            <tr>
                <td align="center">
                    <img src="ImageLogo.png" style="height: 50%;"/>                
                </td>
            </tr>
        </tbody>
    </table>
    <table width="100%" style="margin-top: 10px;">
        <tbody>
            <tr>
                <td align="center">
                    <span style="font-size: 30px; font-family: Arial Black;" >
                        <strong>Bienvenue !</strong>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="100%">
        <tbody>
            <tr>
                <td align="center">
                    <p style="font-family: Arial;">
    Vous êtes désormais inscris sur notre outil de gestion StartYourProject.<br>
Vous pouvez dès à présent créer votre projet ou en rejoindre un !<br>
                    </p>
                    <button style="background-color: grey; border-collapse: separate; border-radius: 5px; border: 1px solid grey; color: white; font-size: 15px; font-family: Arial Black" >
    Notre site
</button>

                </td>
            </tr>
        </tbody>
    </table>
</body>
</body>';
