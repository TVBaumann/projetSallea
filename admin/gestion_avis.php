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
if (isset($_GET['action']) && $_GET['action']  == 'supprimer'  && isset($_GET['id_avis']) && is_numeric($_GET['id_avis'])) {
  //si l'indice action existe dans GET et si sa valeur ets égale à supprimer MAIS aussi si id-article exsite dans GET et si c'est bien sous forme nuùmérique.
  $suppression_avis = $pdo->prepare("DELETE FROM avis WHERE id_avis = :id");
  $suppression_produit->bindParam(':id', $_GET['id_avis'], PDO::PARAM_STR);
  $suppression_produit->execute();
  $msg .= '<div class="alert alert-success">L\'avis n°' . $_GET['id_avis'] . ' a bien été supprimé</div';
  $_GET['action'] = 'affichage'; // on force l'affichage du tableau.
}


/*********************************
//*********************************
//***02 FIN SUPRESISON PRODUIT*****
//********************************
//*********************************/

$note = '';
$commentaire = '';
$id_avis = ''; //pour la modif

/*********************************
//*********************************
//***03 RECUPERATION DES INFIRMATIONS 
 ******POUR LA MODIFIFICATION*******
//************&& 04 MODIFICATION*****
//*********************************/

if (isset($_GET['action']) && $_GET['action'] == 'modifier' && isset($_GET['id_avis']) && is_numeric($_GET['id_avis'])) {

  //requete pour récuperer les informations en BDD
  $recup_info = $pdo->prepare("SELECT * FROM avis WHERE id_avis = :id_avis");
  $recup_info->bindParam(":id_avis", $_GET['id_avis'], PDO::PARAM_STR);
  $recup_info->execute();

  $info_avis = $recup_info->fetch(PDO::FETCH_ASSOC);

  $note = $info_avis['note'];
  $commentaire = $info_avis['commentaire'];
  $id_avis = $info_avis['id_avis']; //pour la modif
}


/*********************************
//*********************************
//***03 FIN  RECUPERATION DES INFIRMATIONS 
 ******POUR LA MODIFIFICATION******
//*******04- MODIFICATION**********
//*********************************/

if (
  isset($_POST['note']) &&
  isset($_POST['commentaire']) &&
  isset($_POST['id_avis'])
) {
  var_dump($_POST);
  $note = trim($_POST['note']);
  $commentaire = trim($_POST['commentaire']);
  $id_avis = trim($_POST['id_avis']);


  if (
    empty($_POST['note']) ||
    empty($_POST['commentaire'])
  ) {

    $msg  .= '<div class="alert alert-danger h1">Un des champs n\'est pas renseigné</div>';
  } else {



    if (!is_numeric($_POST['note'])) {
      //cas d'erreur
      $msg  .= '<div class="alert alert-danger h1">La note  n\'est pas valide, note de 1 à 5 /5';
    }



    //Si $msg ets vide alor sil ny a pas eu d'erreur
    if (empty($msg)) {


      //UPDATE=> modification

      $enregistrement = $pdo->prepare("UPDATE avis set commentaire = :commentaire, note = :note WHERE id_avis = :id_avis ");
      $enregistrement->bindParam(':id_avis', $id_avis, PDO::PARAM_STR);
      $enregistrement->bindParam(":commentaire", $commentaire, PDO::PARAM_STR);
      $enregistrement->bindParam(":note", $note, PDO::PARAM_STR);
      $enregistrement->execute();
    }
  }
}

?>

<main role="main" class="container ">

  <div class="starter-template ">
    <h1>GESTION DES AVIS</h1>
    <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                    ?>
      <hr>
    </p>

  </div>
  <?php if (isset($_GET['action']) &&  $_GET['action'] == 'modifier') { ?>
    <!-- pour cacher le contenu suivant si on veut faire un enregistrement ou una ffichage , on ferme la balise à la fin d ela page -->
    <div class="row">
      <div class="col-6 offset-3">
        <form method="post" action="">
          <div class="form-group h3">
            <input type="hidden" name="id_avis" readonly value="<?php echo $id_avis; ?>">
          </div>
          <!--pour modif produit : on doit utiliser id du membre-->
          <div class="form-group h3">
            <label for="note" class="mt-1">Note</label>
          </div>

          <select name="note" id="note" class="form-control">
            <?php for ($i = 1; $i <= 5; $i++) {
              echo '<option ';
              if ($note == $i) {
                echo 'selected';
              }

              echo '>' . $i . '</option>';
            }

            ?>
          </select>
          <div class="form-group h3">
            <label for="commentaire" class="mt-1">Commentaire</label>
            <textarea type="text" name="commentaire" id="commentaire" class="form-control"><?php echo $commentaire ?></textarea>
          </div>
          <div class="form-group h3 ">
            <button type="submit" class="btn btn-light border-dark col-5  mt-2 ml-3 font-weight-bold">Enregistrement modif</button>
            <a href="<?php echo URL; ?>admin/gestion_avis.php" type="button" class="offset-1 btn btn-light border-dark col-5 mt-2 font-weight-bold">Retour tableau</a>
          </div>

        </form>

      </div>

    </div>

  <?php } // fermeture action = enregistrement ou modification 
  ?>

  <?php
  //************************************
  // AFFICHAGE des membres
  //************************************



  $liste_avis = $pdo->query("SELECT *
      FROM membre, salle, avis
      WHERE membre.id_membre = avis.id_membre
      AND salle.id_salle = avis.id_salle
      ORDER BY id_avis");

  echo '<div class="row">';
  echo '<div class="col-12">';

  echo '<p>' . htmlspecialchars($liste_avis->rowCount(), ENT_QUOTES, 'UTF-8') . ' avis</p>';

  echo '<div class="table-responsive">';
  echo '<table class="table table-bordered text-center table-hover table-striped">';
  echo '<tr>';
  echo '  <thead class="thead-dark">
                <th scope="col">Id avis</th>
                <th scope="col">Id membre</th>
                <th scope="col">Id salle</th>
                <th scope="col">Commentaire</th>
                <th scope="col">Note</th>
                <th scope="col">Date_enregistrement</th>
                <th>Modif</th>
                <th >Suppr</th>
                </thead>';

  echo '</tr>';

  // une boucle pour afficher les articles dans le tableau 

  while ($ligne = $liste_avis->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($ligne['id_avis'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($ligne['id_membre'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($ligne['email'], ENT_QUOTES, 'UTF-8') .  '</td>';
    echo '<td>' . htmlspecialchars($ligne['id_salle'], ENT_QUOTES, 'UTF-8') . '-' . htmlspecialchars($ligne['titre'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td width="30%" class="text-break">' . htmlspecialchars($ligne['commentaire'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($ligne['note'], ENT_QUOTES, 'UTF-8') . '/5 </td>';
    echo '<td>' . htmlspecialchars($ligne['date_enregistrement'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>
        <a href="?action=modifier&id_avis=' . htmlspecialchars($ligne['id_avis'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-warning"><i class="fas fa-edit"></i>
        </a></td><td>
        <a href="?action=supprimer&id_avis=' . htmlspecialchars($ligne['id_avis'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="far fa-trash-alt"></i>
        </a></td>
        ';
    echo '</tr>';
  }


  echo '</table>';

  echo '</div>';
  echo '</div>';


  ?>
</main><!-- /.container -->

<?php
include '../inc/footer.inc.php';
?>