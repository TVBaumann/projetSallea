<?php
//CODE
include '../inc/init.inc.php';
if(!user_is_admin()) { 
  header("location:../connexion.php");
  exit(); // bloque l'execution du code, page blanche . (sécurité )
}

include '../inc/header.inc.php';
include '../inc/nav.inc.php';

/*********************************
//*********************************
//***02 SUPPRESSIOn PRODUIT*****
//********************************
//*********************************/
if(isset($_GET['action']) && $_GET['action']  == 'supprimer'  && isset($_GET['id_membre']) && is_numeric($_GET['id_membre'])) {
  //si l'indice action existe dans GET et si sa valeur ets égale à supprimer MAIS aussi si id-article exsite dans GET et si c'est bien sous forme nuùmérique.
  $suppression_produit = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id");
  $suppression_produit->bindParam(':id', $_GET['id_membre'], PDO::PARAM_STR);
  $suppression_produit->execute();
  $msg .= '<div class="alert alert-success">L\'article n°'.$_GET['id_membre'] . ' a bien été supprimé</div';
  $_GET['action'] = 'affichage'; // on force l'affichage du tableau.
}


/*********************************
//*********************************
//***02 FIN SUPRESISON PRODUIT*****
//********************************
//*********************************/


/*********************************
//*********************************
//***01 DEBUT ENRGEISTREMENT PRODUIT*
//********************************
//*********************************/
$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';
$msg = '';
$statut = '';
$id_membre = ''; //pour la modif

/*********************************
//*********************************
//***03 RECUPERATION DES INFIRMATIONS 
******POUR LA MODIFIFICATION*******
//************&& 04 MODIFICATION*****
//*********************************/

if(isset($_GET['action']) && $_GET['action'] == 'modifier' && isset($_GET['id_membre']) && is_numeric($_GET['id_membre'])){

//requete pour récuperer les informations en BDD
$recup_info = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
$recup_info->bindParam(":id_membre", $_GET['id_membre'], PDO::PARAM_STR);
$recup_info ->execute();

$info_membre = $recup_info->fetch(PDO::FETCH_ASSOC);

$pseudo = $info_membre['pseudo'];
$nom = $info_membre['nom'];
$prenom = $info_membre['prenom'];
$email = $info_membre['email'];
$civilite = $info_membre['civilite'];
$statut = $info_membre['statut'];
$id_membre = $info_membre['id_membre']; //pour la modif
}


/*********************************
//*********************************
//***03 FIN  RECUPERATION DES INFIRMATIONS 
******POUR LA MODIFIFICATION******
//*******04- MODIFICATION**********
//*********************************/

if (
  isset($_POST['pseudo']) &&
  isset($_POST['mdp']) &&
  isset($_POST['nom']) &&
  isset($_POST['prenom']) &&
  isset($_POST['email']) &&
  isset($_POST['statut']) &&
  isset($_POST['id_membre']) &&
  isset($_POST['civilite'])
) {
  
  $pseudo = trim($_POST['pseudo']);
  $mdp = trim($_POST['mdp']);
  $nom = trim($_POST['nom']);
  $prenom = trim($_POST['prenom']);
  $email = trim($_POST['email']);
  $civilite = trim($_POST['civilite']);
  $statut = trim($_POST['statut']);
  $id_membre = trim($_POST['id_membre']);
 
 
  if (
    empty($_POST['pseudo']) ||
    //empty($_POST['mdp']) ||
    empty($_POST['nom']) ||
    empty($_POST['prenom']) ||
    empty($_POST['email']) ||
    empty($_POST['statut']) ||
    empty($_POST['civilite'])
  ) {

    $msg  .= '<div class="alert alert-danger h1">Un des champs n\'est pas renseigné</div>';
  } else {

    
    // contrôle sur la taille du pseudo
    if (iconv_strlen($pseudo) < 4 ||  iconv_strlen($pseudo) > 14) {
      //cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">Le pseudo doit avoir entre 4 et 14 caractères inclus</div>';
    }

    // controle sur les caractères du pseudo : a-z A-Z 0-9
    $verif_pseudo = preg_match('#^[a-zA-Z0-9_]+$#', $pseudo);



    if (!$verif_pseudo) {
      //cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">Le pseudo n\'est pas valide, caractères autorisées : A-Z 0-9</div>';
    }

    // Contrôle sur la validité du mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">Attention,<br>L\'email n\'est pas valide, veuillez vérifier vos saisies</div>';
    }


    //Si $msg ets vide alor sil ny a pas eu d'erreur
    if (empty($msg)) {

      if(empty($id_membre)){
      // contrôle si le pseudo est disponible car unique en BDD
      $pseudo_dispo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo"); 
      $pseudo_dispo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
      $pseudo_dispo->execute();
      }

     if(!empty($id_membre)){
        // contrôle si le pseudo est disponible car unique en BDD
        $pseudo_dispo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo AND id_membre != :id_membre"); 
        $pseudo_dispo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $pseudo_dispo->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
        $pseudo_dispo->execute();
        
        }

      // on vérifie le nombre de ligne obtenue, s'il y a au moins une ligne, alors le pseudo est déjà pris
      if ($pseudo_dispo->rowCount() > 0) {

        $msg  .= '<div class="alert alert-danger h1">Attention,<br> Pseudo indisponible, veuillez recommencer </div>';
      } else {
        // cryptage du mot de passe :
        $mdp  = password_hash($mdp, PASSWORD_DEFAULT);

        if(empty($id_membre)){
        // requete d'enregistrement en BDD
        $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement)VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut, NOW())");
        $enregistrement->bindParam(":mdp", $mdp, PDO::PARAM_STR);
      }
        
       else{
          //UPDATE=> modification
          // j'ai fait le choix de ne pas permettre de modifier le mot de passe depuis l'admin
        
          $enregistrement = $pdo->prepare("UPDATE membre set pseudo = :pseudo, nom = :nom, prenom = :prenom, email = :email, civilite = :civilite, statut = :statut WHERE id_membre = :id_membre ");
          $enregistrement->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
          
        }
        

        $enregistrement->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
        
        $enregistrement->bindParam(":nom", $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(":prenom", $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(":email", $email, PDO::PARAM_STR);
        $enregistrement->bindParam(":civilite", $civilite, PDO::PARAM_STR);
        $enregistrement->bindParam(":statut", $statut, PDO::PARAM_STR);
        $enregistrement->execute();
      }
    }
  }
}

?>

  <main role="main" class="container ">

      <div class="starter-template ">
           <h1>GESTION DES MEMBRES</h1>
            <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur ?><hr></p>
            <a href="?action=enregistrement" class="btn btn-outline-dark"><b>Enregistrement produits</b></a>
            <a href="?action=affichage" class="btn btn-outline-dark"><b>Affichage des produits</b></a>
      </div>
      <?php if(isset($_GET['action']) && ($_GET['action'] == 'enregistrement' || $_GET['action'] == 'modifier')) { ?> <!-- pour cacher le contenu suivant si on veut faire un enregistrement ou una ffichage , on ferme la balise à la fin d ela page -->
      <div class="row">
    <div class="col-12">
      <form method="post" action="">
        <div class="row">
          <div class="col-md-6 col-12">
            <div class="form-group h3">
            <input type="hidden" name="id_membre" readonly value="<?php echo $id_membre ; ?>"> <!--pour modif produit : on doit utiliser id du membre-->
              <label for="pseudo" class="mt-1">Pseudo</label>
              <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo ?>">
              <!-- cacher le champ mdp en modification-->
              <?php if(isset($_GET['action']) && $_GET['action'] == 'modifier'){ ?>
              <input type="hidden" name="mdp" id="mdp" class="form-control" value=""> <?php } ?>
              <?php if(isset($_GET['action']) && $_GET['action'] == 'enregistrement'){ ?>
              <label for="mdp" class="mt-1">Mot de passe</label>
              <input type="text" name="mdp" id="mdp" class="form-control" value=""> <?php } ?>
              
              
              <label for="nom" class="mt-1">Nom</label>
              <input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom ?>">
              <label for="prenom" class="mt-1">Prénom</label>
              <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom ?>">
              <label for="email" class="mt-1">Email</label>
              <input type="text" name="email" id="email" class="form-control" value="<?php echo $email ?>">

            </div>
          </div>
          <div class="col-md-6 col-12">
            <div class="form-group h3">
              <label for="civilite">Civilité</label>
              <select name="civilite" class="form-control">
                <option value="m">Homme</option>
                <option value="f" <?php if ($civilite == 'f') {
                                    echo 'selected';
                                  } ?>>Femme</option>
              </select>
            </div>
            <div class="form-group h3">
              <label for="statut">Statut</label>
              <select name="statut" class="form-control">
                <option value="1">utilisateur</option>
                <option value="2" <?php if ($statut == '2') {
                                    echo 'selected';
                                  } ?>>admin</option>
              </select>
            </div>
            <div class="form-group col-12">
              <button type="submit" class="btn btn-light border-dark w-100 mt-2"> ENREGISTREMENT</button>
            </div>

          </div>
      </form>

    </div>

  </div>

  <?php } // fermeture action = enregistrement ou modification ?> 

  <?php
  //************************************
     // AFFICHAGE des membres
     //************************************

    if(isset($_GET['action']) && $_GET['action'] == 'affichage'){

  $liste_membre = $pdo->query("
  	SELECT *
    FROM membre");

      echo '<div class="row">';
      echo '<div class="col-12">';

      echo '<p>' . htmlspecialchars($liste_membre->rowCount(), ENT_QUOTES, 'UTF-8') . ' produit(s)</p>';

      echo '<div class="table-responsive">';
      echo '<table class="table table-bordered text-center  table-hover table-striped">';
      echo '<tr>';
      echo '  <thead class="thead-dark">
              <th scope="col">Id membre</th>
              <th scope="col">Pseudo</th>
              <th scope="col">Nom</th>
              <th scope="col">Prénom</th>
              <th scope="col">Email</th>
              <th scope="col">Civilité</th>
              <th scope="col">Statut</th>
              <th scope="col">Date enregistrement</th>
              <th scope="col">Modif</th>
              <th >Suppr</th>
              </thead>';

       echo '</tr>'; 

       // une boucle pour afficher les articles dans le tableau 

       while($ligne = $liste_membre->fetch(PDO::FETCH_ASSOC)) { 
        echo '<tr>';
        echo '<td>' . htmlspecialchars($ligne['id_membre'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['pseudo'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['nom'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['prenom'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['email'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['civilite'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['statut'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['date_enregistrement'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>
        <a href="?action=modifier&id_membre=' . htmlspecialchars($ligne['id_membre'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-warning"><i class="fas fa-edit"></i>
        </a></td><td>
        <a href="?action=supprimer&id_membre=' . htmlspecialchars($ligne['id_membre'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="far fa-trash-alt"></i>
        </a></td>
        ';
        echo '</tr>';
       }
         

      echo '</table>';

      echo '</div>';
      echo '</div>';
      echo '</div>';




       

    }

?>
</main><!-- /.container -->

<?php
include '../inc/footer.inc.php';
?>