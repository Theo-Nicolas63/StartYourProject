<?php


use PHPMailer\PHPMailer\PHPMailer;

class UtilisateurControleur
{
    function __construct()
    {
        global $rep, $vues, $erreur;
        try {
            if (isset($_REQUEST["action"])) {
                $action = $_REQUEST["action"];
            } else {
                $action = NULL;
            }
            switch ($action) {
                case "Deconnecter" :
                    $this->seDeconnecter();
                    break;
                case "AfficherPageMesProjets":
                    $this->afficherPageMesProjets();
                    break;
                case "AjouterProjet":
                    $this->ajouterProjet();
                    break;
                case "AfficherPageProjet":
                    $this->afficherPageProjet();
                    break;
                case "CreerTache":
                    $this->creerTache();
                    break;
                case "AffichePageCreationTache":
                    $this->affichePageCreationTache();
                    break;
                case "AfficherPageCreationProjet":
                    $this->afficherPageCreationProjet();
                    break;
                case "Messagerie":
                    $this->messagerie();
                    break;
                case "ChangerSemaine":
                    $this->ChangerSemaine();
                default:
                    throw new \Exception('Unexpected value');
            }
        } catch (PDOException $e) {
            $erreur = "Erreur PDO";
            require($rep . $vues['pageErreur']);
        } catch (Exception $e2) {
            $erreur = $e2->getMessage();
            require($rep . $vues['pageErreur']);
        }
    }

    /**
     * Permet de se déconnecter
     */
    private function seDeconnecter(): void
    {
        session_destroy();
        header('Refresh:0;url=index.php');
    }

    /**
     * Permet d'afficher la page contenant tous les projets d'un utilisateur
     */
    private function afficherPageMesProjets(): void
    {
        global $rep, $vues;
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
        } else $email = null;
        if ($email != null) {
            $tabProjets = $this->afficherPageProjetTrier($email);
            require($rep . $vues['pageMesProjets']);
        }
    }

    /**
     * Permet de retourner tous les projets triés dans un ordre voulu
     * @param string $email
     * @return array|mixed|mixed[]
     */
    private function afficherPageProjetTrier(string $email): array
    {
        $tri = $_REQUEST['triSelect'];
        if(!isset($tri)){
            $tri="";
        }
        return ModelProjet::getAllProjetsByMail($email, $tri);
    }

    /**
     * Permet de créer et d'ajouter un projet
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function ajouterProjet(): void
    {
        global $rep, $vues;

        $nomProjet = $_REQUEST['nomProjet'];
        $descriptionProjet = $_REQUEST['descriptionProjet'];
        $membresProjet = $_REQUEST['mailMembre'];
        $budgetProjet = $_REQUEST['budgetProjet'];
        $dateDebut = $_REQUEST['dateDebut'];
        $createur = $_SESSION['email'];

        if (!isset($budgetProjet) || empty($budgetProjet))
            $budgetProjet = 0;
        if (!isset($dateDebut) || empty($dateDebut))
            $dateDebut = date('Y-m-d');

        $idProjet = ModelProjet::ajouterProjet($nomProjet, $createur, $descriptionProjet, $budgetProjet, $dateDebut);

        foreach ($membresProjet as $key => $value) {
            if (isset($value) && !empty($value)) {
                if (ModelUtilisateur::rechercheUtilisateurByMail($value) != null) {
                    ModelProjet::ajouterMembre($value, $idProjet);

                } else {
                    $decoupage = explode("@", $value);
                    $nom = explode(".", $decoupage[0]);

                    if (isset($nom[1])) {
                        $prenom = $nom[1];
                    } else $prenom = $nom[0];
                    ModelUtilisateur::inscription($value, $nom[0], $prenom, '1234', false);
                    ModelProjet::ajouterMembre($value, $idProjet);

                    require_once "PHPMailer/PHPMailer.php";
                    require_once "PHPMailer/SMTP.php";
                    require_once "PHPMailer/Exception.php";


                    $mail = new PHPMailer();

                    //Paramétrage serveur SMTP
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = "startyourproject63@gmail.com"; //l'adresse email crée
                    $mail->Password = 'mdptest1'; //mot de passe de l'adresse mail
                    $mail->Port = 465;
                    $mail->SMTPSecure = "ssl";

                    //Paramétrage email
                    $mail->isHTML(true);
                    $mail->setFrom($value, "StartYourProject");
                    $mail->addAddress($value);
                    $mail->Subject = ("$value ,bonjour");
                    $mail->Body = "test mail";

                    if ($mail->send()) {
                        $status = "success";
                        $response = "Email is sent!";
                    } else {
                        $status = "failed";
                        $response = "Something is wrong: <br><br>" . $value->ErrorInfo;
                    }

                }
            }
        }
        self::afficherPageMesProjets();
    }

    /**
     * Permet d'afficher la page d'un projet
     */
    private function afficherPageProjet(): void
    {

        global $rep, $vues;
        $tabJours = array();
        $idProjet = $_REQUEST['idprojet'];
        $numeroSemaine = $_REQUEST['numeroSemaine'];
        if (!isset($numeroSemaine) || empty($numeroSemaine) || $numeroSemaine < 0){
            $numeroSemaine = 0;
        }
        else $numeroSemaine = intval($numeroSemaine);
        if (isset($idProjet)) {
            for($i =1; $i <= 5; $i++){
                $tabJours[$i] = ModelTache::gestionJoursSemaine($i, $numeroSemaine);
            }
            $isChefProjet = ModelChefProjet::isChefProjet($idProjet, $_SESSION['email']);
            $projet = ModelProjet::getProjetbyID($idProjet);
            $taches = ModelTache::getAllTachesByIdProjet($idProjet);
            $membres = ModelUtilisateur::getAllMembresProjet($idProjet);
            foreach($taches as $tache){
                $tache->setListeMembres(self::concatenerMembreProjet($tache->getIdTache()));
            }
            $projet->setListeTaches($taches);
            $projet->setListeUtilisateur($membres);
            require($rep . $vues['pageProjet']);
        }
    }

    /**
     * concatène tous les membres d'une tâche dans une chaine de caractère
     */
    private function concatenerMembreProjet(int $idTache) : string{
        $listeMembres = "";
        $i = 1;
        $membres = array();
        $membres = ModelUtilisateur::getAllUtilisateursByIdTache($idTache);
        foreach ($membres as $m){
            if($i < sizeof($membres)){
                $listeMembres .= $m->getPrenom() . " " . $m->getNom() . ", ";
            }
            else{
                $listeMembres .= $m->getPrenom() . " " . $m->getNom() . ". ";
            }
            $i++;
        }
        return $listeMembres;
    }

    /**
     * Fonction de création de tâches qui crée une tâche en récupèrant les données saisies par l'utilisateur
     */
    private function creerTache(): int
    {
        global $rep, $vues;
        XSTR_LOG("cccccccccccccccccccc" );


        $email= $_SESSION['email'];
        $idProjet = $_REQUEST['idProjet'];
        $nomTache = $_POST['nomtache'];
        $description = $_POST['description'];
        $cout = $_POST['cout'];
        $dateDebut = $_POST['datedebut'];
        $duree = $_POST['duree'];

        XSTR_LOG("avant tache");
        $idTache = ModelTache::ajouterTache($email, $idProjet, $nomTache, $description, $cout, $dateDebut, $duree);
        XSTR_LOG("apres tache");

        $emailMembre = $_POST['mailMembre'];

        foreach($emailMembre as $key ){

            $key = explode(",",$key);
            foreach ($key as $val){

                if(ModelUtilisateur::isMembreProjet($val,$idProjet)){
                    ModelTache::ajouterMembreTache($val, $idTache);
                  //  $projet = ModelTache::affichageDetailProjet($idProjet);

                    echo 1;

                }
                else {
                    echo 0;
                    require($rep . $vues["pageErreur"]);

                }
            }
        }
        //voir si le pelo existe
        //voir si le pelo existe dans la table est membre
        //self::afficherAccueil(); // a faire

        return 1;
    }
    /**
     * Permet d'afficher la page de création de tâche
     */
    private function affichePageCreationTache(): void
    {
        global $rep, $vues;
        $idProjet = $_REQUEST['idProjet'];
        $isUtilisateur = ModelUtilisateur::isUtilisateur();
        require($rep . $vues['pageCreationTache']);

    }

    /**
     * Permet d'afficher la page de création de projet
     */
    private function afficherPageCreationProjet(): void
    {
        global $rep, $vues;
        $isUtilisateur = ModelUtilisateur::isUtilisateur();
        require($rep . $vues['pageCreationProjet']);
    }

    /**
     * Permet d'afficher la page de messagerie
     */
    private function messagerie(): void
    {
        global $rep, $vues;
        $isUtilisateur = ModelUtilisateur::isUtilisateur();
        $utilisateur = ModelUtilisateur::rechercheUtilisateurByMail($_SESSION['email']);
        $projet = ModelProjet::getProjetbyID($_REQUEST['idProjet']);
        require($rep . $vues['messagerie']);
    }

}