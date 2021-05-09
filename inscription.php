<?php
//CODE
include 'inc/init.inc.php';


include 'inc/header.inc.php';
include 'inc/nav.inc.php';


$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';
$msg = '';


if (
  isset($_POST['pseudo']) &&
  isset($_POST['mdp']) &&
  isset($_POST['nom']) &&
  isset($_POST['prenom']) &&
  isset($_POST['email']) &&
  isset($_POST['civilite'])
) {

  $pseudo = trim($_POST['pseudo']);
  $mdp = trim($_POST['mdp']);
  $nom = trim($_POST['nom']);
  $prenom = trim($_POST['prenom']);
  $email = trim($_POST['email']);
  $civilite = trim($_POST['civilite']);

  if (
    empty($_POST['pseudo']) ||
    empty($_POST['mdp']) ||
    empty($_POST['nom']) ||
    empty($_POST['prenom']) ||
    empty($_POST['email']) ||
    empty($_POST['civilite'])
  ) {

    $msg  .= '<div class="alert alert-danger h1">Un des champs n\'est pas renseigné</div>';
  } else {


    // contrôle sur la taille du pseudo
    if (iconv_strlen($pseudo) < 4 ||  iconv_strlen($pseudo) > 14) {
      //cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">Le pseudo doit avoir entre 4 et 14 caractères inclus</div>';
    }

    // controle sur les caractères du pseudo : a-zA-Z0-9À-ÖØ-öø-ÿœŒ
    $verif_pseudo = preg_match('#[a-zA-ZÀ-ÖØ-öø-ÿœŒ]$#' , $pseudo);



    if (!$verif_pseudo) {
      //cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">Le pseudo n\'est pas valide, caractères autorisées : A-Z 0-9 À-Ö </div>';
    }

    // Contrôle sur la validité du mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">Attention,<br>L\'email n\'est pas valide, veuillez vérifier vos saisies</div>';
    }


    //Si $msg ets vide alor sil ny a pas eu d'erreur
    if (empty($msg)) {

      // contrôle si le pseudo est disponible car unique en BDD
      $pseudo_dispo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
      $pseudo_dispo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
      $pseudo_dispo->execute();

      // on vérifie le nombre de ligne obtenue, s'il y a au moins une ligne, alors le pseudo est déjà pris
      if ($pseudo_dispo->rowCount() > 0) {

        $msg  .= '<div class="alert alert-danger h1">Attention,<br> Pseudo indisponible, veuillez recommencer </div>';
      } else {
        // cryptage du mot de passe :
        $mdp  = password_hash($mdp, PASSWORD_DEFAULT);

        // requete d'enregistrement en BDD
        $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement)VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, 1, NOW())");

        $enregistrement->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(":mdp", $mdp, PDO::PARAM_STR);
        $enregistrement->bindParam(":nom", $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(":prenom", $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(":email", $email, PDO::PARAM_STR);
        $enregistrement->bindParam(":civilite", $civilite, PDO::PARAM_STR);
        $enregistrement->execute();

        $msg  .= '<div class="alert alert-danger h1">Votre profil a bien été crée<br> <a  href="'.  URL .'connexion.php">Connexion</a>  </div>';

      }
    }
  }
}
?>

<main role="main" class="container ">

  <div class="starter-template ">
    <h1>INSCRIPTION</h1>
    <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                    ?>
      <hr></p>
    <p class="lead"></p>
  </div>
  <div class="row">
    <div class="col-12">
      <form method="post" action="">
        <div class="row">
          <div class="col-6">
            <div class="form-group h3">
              <label for="pseudo" class="mt-1">Pseudo</label>
              <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo; ?>">
              <label for="mdp" class="mt-1">Mot de passe</label>
              <input type="text" name="mdp" id="mdp" class="form-control" value="">
              <label for="nom" class="mt-1">Nom</label>
              <input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom; ?>">
              
            </div>
          </div>
          <div class="col-6">
            <div class="form-group h3">
            <label for="prenom" class="mt-1">Prénom</label>
              <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom; ?>">
              <label for="email" class="mt-1">Email</label>
              <input type="text" name="email" id="email" class="form-control" value="<?php echo $email; ?>">
              <label for="civilite" class="mt-1">Civilité</label>
              <select name="civilite" class="form-control">
                <option value="m">Homme</option>
                <option value="f" <?php if ($sexe == 'f') {
                                    echo 'selected';
                                  } ?>>Femme</option>
              </select>
            </div>
            

          </div>
          <div class="form-group col-6 offset-3 ">
              <button type="submit" class="btn btn-light border-dark w-100 mt-2"><span class="h5 font-weight-bold"> Inscription</span></button>
            </div>
      </form>

    </div>

  </div>

</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>