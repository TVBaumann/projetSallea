<?php
//CODE
include '../inc/init.inc.php';
if (!user_is_admin()) {
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
if (isset($_GET['action']) && $_GET['action']  == 'supprimer'  && isset($_GET['id_salle']) && is_numeric($_GET['id_salle'])) {
  //si l'indice action existe dans GET et si sa valeur est égale à supprimer MAIS aussi si id-article exsite dans GET et si c'est bien sous forme nuùmérique.
  $suppression_salle = $pdo->prepare("DELETE FROM salle WHERE id_salle = :id");
  $suppression_salle->bindParam(':id', $_GET['id_salle'], PDO::PARAM_STR);
  $suppression_salle->execute();
  $msg .= '<div class="alert alert-success">La salle n°' . $_GET['id_salle'] . ' a bien été supprimé</div';
  //$_GET['action'] = 'affichage'; // on force l'affichage du tableau.
}


/*********************************
//*********************************
//***02 FIN SUPPRESSIOn PRODUIT*****
//********************************
//*********************************/
$titre = '';
$description = '';
$photo = '';
$pays = '';
$ville = '';
$adresse = '';
$cp = '';
$capacite = '';
$categorie = '';
$id_salle = '';

/*********************************
//*********************************
//***03 RECUPERATION DES INFIRMATIONS 
 ******POUR LA MODIFIFICATION*******
//************&& 04 MODIFICATION*****
//*********************************/

if (isset($_GET['action']) && $_GET['action']  == 'modifier'  && isset($_GET['id_salle']) && is_numeric($_GET['id_salle'])) {

  // une requete pour récuperer les informations en BDD
  $recup_infos = $pdo->prepare("SELECt * FROM salle WHERE id_salle= :id_salle");
  $recup_infos->bindParam(":id_salle", $_GET['id_salle'], PDO::PARAM_STR);
  $recup_infos->execute();

  $info_salle = $recup_infos->fetch(PDO::FETCH_ASSOC);

  $id_salle = $info_salle['id_salle']; // pour la modif
  $photo_actuelle = $info_salle['photo']; // pour la modif
  $titre = $info_salle['titre'];
  $description = $info_salle['description'];
  $pays = $info_salle['pays'];
  $ville = $info_salle['ville'];
  $adresse = $info_salle['adresse'];
  $cp = $info_salle['cp'];
  $capacite = $info_salle['capacite'];
  $categorie = $info_salle['categorie'];
}


/*********************************
//*********************************
//***03 FIN  RECUPERATION DES INFIRMATIONS 
 ******POUR LA MODIFIFICATION******
//*******04- MODIFICATION**********
//*********************************/




/*********************************
//*********************************
//***01 DEBUT ENREGISTREMENT PRODUIT*
//********************************
//*********************************/





if (
  isset($_POST['titre']) &&
  isset($_POST['description']) &&
  isset($_POST['pays']) &&
  isset($_POST['ville']) &&
  isset($_POST['adresse']) &&
  isset($_POST['cp']) &&
  isset($_POST['capacite']) &&
  isset($_POST['categorie'])
) {

  //echo '<pre>'; var_dump($_POST); echo '</pre>';


  // récupération d el'id article dans le cadre d ela modif
  if (!empty($_POST['id_salle'])) {

    $id_salle = trim($_POST['id_salle']);
  }

  // récupération de la photo actuelle dans le modif

  if (!empty($_POST['photo_actuelle'])) {

    $photo = trim($_POST['photo_actuelle']);
  }

  $titre = trim($_POST['titre']);
  $description = trim($_POST['description']);
  $ville = trim($_POST['ville']);
  $pays = trim($_POST['pays']);
  $adresse = trim($_POST['adresse']);
  $cp = trim($_POST['cp']);
  $capacite = trim($_POST['capacite']);
  $categorie = trim($_POST['categorie']);


  if (
    empty($titre) ||
    empty($description) ||
    empty($adresse) ||
    empty($categorie)
  ) {
    $msg .= '<div class="alert alert-danger">Vous devez renseigner obligatoirement les champs: titre , description, adresse et categorie</div>';
  } else {

    if (!is_numeric($cp)) {
      $msg .= '<div class="alert alert-danger">Le code postal doit être numérique</div>';
    }

    // le titre étant unique en BDD, on vérifie si elle existe déjà
    $verif_titre = $pdo->prepare("SELECT * FROM salle WHERE titre = :titre");
    $verif_titre->bindParam(":titre", $titre, PDO::PARAM_STR);
    $verif_titre->execute();

    $verif_titre->rowCount();
    //var_dump($verif_titre->rowCount());

    if (empty($msg) && $verif_titre->rowCount() > 0 && empty($id_salle)) {
      // on a rajoté empty (id_salle) car si c'est une modif , la référence existe donc si $id_salle est pas vide c'est un enregistrement et on vérifie si la ref existe sinon non.
      //si la reference existe en BDD
      $msg .= '<div class="alert alert-danger">titre indisponible</div>';
    } else {
      // la référence est disponible
      // controle sur la photo
      // les fichiers media (input type="file") se retoruve dans la superglobale $_FILES
      if (!empty($_FILES['photo']['name'])) {
        // on enregistre dans un tableau array des extensions valides
        $tab_extension = array('png', 'jpg', 'jpeg', 'gif');
        // on récup l'extension du fichier
        $extension = strrchr($_FILES['photo']['name'], '.');
        // la focntion strrchr() permet de découper une chaine en partant de la fin jusqu'à l'information fournie en deuxième argument (ici le .)
        //exemple:
        //fichier ma_photo.jpg on obtient .jpg

        //on enleve le . sur $extension et on passe la chaine en minuscule
        $extension = strtolower(substr($extension, 1));
        //exemple : .PNG on obtient png

        //// in_array() renvoie true si le prmeier argument fait parti d'une des valeurs d'un tableau fourni en deuxième argument.
        $verif_extension = in_array($extension, $tab_extension);
        //l'extension, ou elle se trouve
        if (!$verif_extension) {
          $msg .= '<div class="alert alert-danger">Photo invalide, format acceptés : png, jpg, jpeg, gif</div>';
        } else {
          //pour eviter que le nom d el'image soit similaire à une autre image déjà enregistrée sur le serveur, on rajoute la référence qui est unique.
          $nom_photo = time() . '-' . $_FILES['photo']['name'];
          //var_dump($nom_photo);

          //on prépare la valeur du src qui sera enregistré en BDD
          $photo = 'photo/' . $nom_photo;
          // var_dump($photo);

          //chemin complet là ou la photo va ertre enregistrée
          $chemin_dossier = SERVER_ROOT . ROOT_URL . $photo;
          //var_dump($chemin_dossier);

          //copy() pemret de copier un fichier depuis un emplacement fourni en prmier argumentvers un emplacement fourni  en deuxième argument
          copy($_FILES['photo']['tmp_name'], $chemin_dossier);
        }
      } // est-ce qu'une photo a été chargée

      if (empty($msg)) {
        //s 'il n'y a pas eu d'erreur, on déclenche l'enregistrement

        if (empty($id_salle)) {
          // INSERT =>enregistrement

          $enregistrement_salle = $pdo->prepare("INSERT INTO salle(titre, description, photo, pays, ville, adresse, cp, capacite, categorie) VALUES(:titre, :description, :photo, :pays, :ville, :adresse, :cp, :capacite, :categorie)");
        } else {
          //UPDATE =>modification
          $enregistrement_salle = $pdo->prepare("UPDATE salle SET titre = :titre, description = :description, photo = :photo, pays = :pays, ville = :ville, adresse = :adresse, cp = :cp, capacite = :capacite, categorie = :categorie WHERE id_salle = :id_salle");
          $enregistrement_salle->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
        } // pour l'update on a besoin de l'id donc on rjaoute le bindParam

        $enregistrement_salle->bindParam(':titre', $titre, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':description', $description, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':photo', $photo, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':pays', $pays, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':ville', $ville, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':adresse', $adresse, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':cp', $cp, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':capacite', $capacite, PDO::PARAM_STR);
        $enregistrement_salle->bindParam(':categorie', $categorie, PDO::PARAM_STR);
        $enregistrement_salle->execute();
      }
    }
  }
}



//*********************************
//*********************************
//***FIN ENREGISTREMENT PRODUIT*****
//********************************
//*********************************



?>

<main role="main" class="container ">

  <div class="starter-template ">
    <h1>GESTION DES SALLES</h1>
    <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                    ?>
      <hr>
    </p>

    <a href="?action=enregistrement" class="btn btn-outline-dark"><b>Enregistrement salles</b></a>
    <a href="?action=affichage" class="btn btn-outline-dark"><b>Affichage des salles</b></a>
  </div>
  <?php if (isset($_GET['action']) && ($_GET['action'] == 'enregistrement' || $_GET['action'] == 'modifier')) { ?>
    <!-- pour cacher le contenu suivant si on veut faire un enregistrement ou una ffichage , on ferme la balise à la fin d ela page -->
    <div class="row">
      <div class="col-8 mx-auto">
        <form method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="id_salle" readonly value="<?php echo $id_salle; ?>">
          <!--pour modif produit : on doit utiliser id du produit-->
          <div class="form-group h3">
            <label for="titre" class="mt-1">Titre</label>
            <input type="text" name="titre" id="titre" class="form-control" value="<?php echo $titre; ?>">
          </div>
          <div class="form-group h3">
            <label for="description" class="mt-1">Description</label>
            <textarea type="text" name="description" id="description" class="form-control"><?php echo $description; ?></textarea>
          </div>
          <?php // pour midifier la photo
          if (!empty($photo_actuelle)) {
            // si photo_actuelle n'est pas vide , on est dans la modif et une photo existe pour le produit à modifier
            echo '<div class="form-group"><label>photo actuelle </label><br>';

            echo '<input type="hidden" name="photo_actuelle" value="' . $photo_actuelle . '" >';
            echo '<img src="' . URL . $photo_actuelle . '" class="img-thumbnail w-25">';
            echo '</div>';
          }    ?>
          <div class="form-group h3">
            <label for="photo">Photo</label>
            <input type="file" name="photo" id="photo" class="form-control">
          </div>
          <div class="form-group h3">
            <label for="pays" class="mt-1">Pays</label>
            <select name="pays" id="pays" class="form-control">
              <option <?php if ($pays == 'France') {
                        echo 'selected';
                      } ?>>France</option>
            </select>


          </div>
          <div class="form-group h3">
            <label for="ville" class="mt-1">Ville</label>
            <select name="ville" id="ville" class="form-control">
              <option <?php if ($ville == 'Paris') {
                        echo 'selected';
                      } ?>>Paris</option>
              <option <?php if ($ville == 'Lyon') {
                        echo 'selected';
                      } ?>>Lyon</option>
              <option <?php if ($ville == 'Marseille') {
                        echo 'selected';
                      } ?>>Marseille</option>
            </select>

          </div>
          <div class="form-group h3">
            <label for="adresse" class="mt-1">Adresse</label>
            <textarea type="text" name="adresse" id="adresse" class="form-control"><?php echo $adresse; ?></textarea>
          </div>
          <div class="form-group h3">
            <label for="cp" class="mt-1">Code postal</label>
            <input type="text" name="cp" id="cp" class="form-control" value="<?php echo $cp; ?>">
          </div>
          <div class="form-group h3">
            <label for="capacite" class="mt-1">Capacité</label>
            <select name="capacite" id="capacite" class="form-control">
              <option <?php if ($capacite == '0-20') {
                        echo 'selected';
                      } ?>>0-20</option>
              <option <?php if ($capacite == '20-50') {
                        echo 'selected';
                      } ?>>20-50</option>
              <option <?php if ($capacite == '50-100') {
                        echo 'selected';
                      } ?>>50-100</option>
              <option <?php if ($capacite == '+ 100') {
                        echo 'selected';
                      } ?>>+ 100</option>
            </select>
          </div>
          <div class="form-group h3">
            <label for="categorie" class="mt-1">Catégorie</label>
            <select name="categorie" id="categorie" class="form-control">
              <option <?php if ($categorie == 'réunion') {
                        echo 'selected';
                      } ?>>réunion</option>
              <option <?php if ($categorie == 'bureau') {
                        echo 'selected';
                      } ?>>bureau</option>
              <option <?php if ($categorie == 'information') {
                        echo 'selected';
                      } ?>>information</option>
            </select>
          </div>
          <div class="form-group mt-3">
            <button type="submit" class="btn btn-dark btn-lg text-white w-100 mt-2">ENREGISTREMENT
            </button>
          </div>

      </div>
    </div>
  <?php } // fermeture action = enregistrement ou modification 
  ?>


  <?php
  //************************************
  // AFFICHAGE des salles
  //************************************

  if (isset($_GET['action']) && $_GET['action'] == 'affichage') {

    $liste_salle = $pdo->query("SELECT * FROM salle ORDER BY titre");

    echo '<div class="row">';
    echo '<div class="col-12">';

    echo '<p>' . htmlspecialchars($liste_salle->rowCount(), ENT_QUOTES, 'UTF-8') . ' salle(s)</p>';

    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered text-center table-hover table-striped">';
    echo '<tr>';
    echo '  <thead class="thead-dark">
              <th scope="col">Id salle</th>
              <th scope="col">Titre</th>
              <th scope="col">description</th> 
              <th scope="col">photo</th>
              <th scope="col">pays</th>
              <th scope="col">ville</th>
              <th scope="col">adresse</th>
              <th scope="col">cp</th>
              <th scope="col">capacite</th>
              <th scope="col">categorie</th>
              <th scope="col">Modif</th>
              <th >Suppr</th>
              </thead>';



    echo '</tr>';

    // une boucle pour afficher les articles dans le tableau
    while ($ligne = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
      echo '<tr>';

      echo '<td>' . htmlspecialchars($ligne['id_salle'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['titre'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars(iconv_substr($ligne['description'], 0, 5), ENT_QUOTES, 'UTF-8') . '<a href="">...</a></td>';  // pr ne pas afficher toute la description
      echo '<td> <img src="' . URL . htmlspecialchars($ligne['photo'], ENT_QUOTES, 'UTF-8') . '" class="img-thumbnail" width="100"></td>';
      echo '<td>' . htmlspecialchars($ligne['pays'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['ville'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td class="text-left">' . htmlspecialchars(iconv_substr($ligne['adresse'], 0, 15), ENT_QUOTES, 'UTF-8') . '<a href="">...</a></td>';
      echo '<td>' . htmlspecialchars($ligne['cp'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['capacite'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['categorie'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>
                <a href="?action=modifier&id_salle=' . htmlspecialchars($ligne['id_salle'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-warning"><i class="fas fa-edit"></i>
                </a></td><td>
                <a href="?action=supprimer&id_salle=' . htmlspecialchars($ligne['id_salle'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="far fa-trash-alt"></i>
                </a>
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