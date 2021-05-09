<?php
include '../inc/init.inc.php';
if (!user_is_admin()) {
  header("location:../connexion.php");
  exit(); // bloque l'execution du code, page blanche . (sécurité )
}


//CODE  


/*********************************
//*********************************
//***02 SUPPRESSIOn PRODUIT*****
//********************************
//*********************************/
if (isset($_GET['action']) && $_GET['action']  == 'supprimer'  && isset($_GET['id_produit']) && is_numeric($_GET['id_produit'])) {
  //si l'indice action existe dans GET et si sa valeur ets égale à supprimer MAIS aussi si id-article exsite dans GET et si c'est bien sous forme nuùmérique.
  $suppression_produit = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id");
  $suppression_produit->bindParam(':id', $_GET['id_produit'], PDO::PARAM_STR);
  $suppression_produit->execute();
  $msg .= '<div class="alert alert-success">L\'article n°' . $_GET['id_produit'] . ' a bien été supprimé</div';
  $_GET['action'] = 'affichage'; // on force l'affichage du tableau.
}


/*********************************
//*********************************
//***01 DEBUT ENRGEISTREMENT PRODUIT*
//********************************
//*********************************/
$id_produit = '';
$id_salle = '';
$date_arrivee = '';
$date_depart = '';
$prix = '';
$etat = '';
$heure_arrivee = '';
$heure_depart = '';


/*********************************
//*********************************
//***03 RECUPERATION DES INFIRMATIONS 
 ******POUR LA MODIFIFICATION*******
//************&& 04 MODIFICATION*****
//*********************************/

if (isset($_GET['action']) && $_GET['action']  == 'modifier'  && isset($_GET['id_produit']) && is_numeric($_GET['id_produit'])) {

  // une requete pour récuperer les informations en BDD
  $recup_infos = $pdo->prepare("SELECt * FROM produit WHERE id_produit = :id_produit");
  $recup_infos->bindParam(":id_produit", $_GET['id_produit'], PDO::PARAM_STR);
  $recup_infos->execute();

  $info_produit = $recup_infos->fetch(PDO::FETCH_ASSOC);

  $id_produit = $info_produit['id_produit'];
  $id_salle = $info_produit['id_salle'];
  $date_arrivee = $info_produit['date_arrivee'];
  $date_depart = $info_produit['date_depart'];
  $prix = $info_produit['prix'];
  $heure_arrivee = $info_produit['heure_arrivee'];
  $heure_depart = $info_produit['heure_depart'];
}

if (

  isset($_POST['date_arrivee']) &&
  isset($_POST['date_depart']) &&
  isset($_POST['id_salle']) &&
  isset($_POST['heure_arrivee']) &&
  isset($_POST['heure_depart']) &&
  isset($_POST['id_produit']) &&
  isset($_POST['prix'])
) {

  //var_dump($_POST);


  $id_produit = $_POST['id_produit'];
  $id_salle = trim($_POST['id_salle']);
  $date_arrivee = trim($_POST['date_arrivee']);
  $date_depart = trim($_POST['date_depart']);
  $prix = trim($_POST['prix']);
  $heure_arrivee = $_POST['heure_arrivee'];
  $heure_depart = $_POST['heure_depart'];


  if (
    empty($id_salle) ||
    empty($date_arrivee) ||
    empty($date_depart) ||
    empty($heure_arrivee) ||
    empty($heure_depart) ||
    empty($prix)
  ) {
    $msg .= '<div class="alert alert-danger">Vous devez remplir tous les champs</div>';
  } else {

    //controle sur les dates

    if (strtotime($date_arrivee) > strtotime($date_depart)) {
      $msg .= '<div class="alert alert-danger">Vos dates ne sont pas corects</div>';
    }

    if (!is_numeric($prix)) {
      $msg .= '<div class="alert alert-danger">Le prix doit être numérique</div>';
    }

    // controle sur la disponibilité des dates 

    if (empty($id_produit)) {
      //echo 'je rentre';
      $date_dispo = $pdo->prepare("SELECT  date_arrivee , date_depart FROM produit WHERE (:date_arrivee BETWEEN date_arrivee AND date_depart AND id_salle = :id_salle ) OR (:date_depart BETWEEN date_arrivee AND date_depart AND id_salle = :id_salle ) OR (:date_arrivee < date_arrivee AND :date_depart > date_depart AND id_salle = :id_salle) ");
    } else {

      $date_dispo = $pdo->prepare("SELECT  date_arrivee , date_depart FROM produit WHERE (:date_arrivee BETWEEN date_arrivee AND date_depart AND id_salle = :id_salle AND id_produit != :id_produit ) OR (:date_depart BETWEEN date_arrivee AND date_depart AND id_salle = :id_salle AND id_produit != :id_produit) OR (:date_arrivee < date_arrivee AND :date_depart > date_depart AND id_salle = :id_salle AND id_produit != :id_produit)   ");
      var_dump($id_produit);

      $date_dispo->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
    }



    $date_dispo->bindParam(':date_arrivee', $date_arrivee, PDO::PARAM_STR);
    $date_dispo->bindParam(':date_depart', $date_depart, PDO::PARAM_STR);
    $date_dispo->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
    $date_dispo->execute();
    while ($ligne = $date_dispo->fetch(PDO::FETCH_ASSOC))
      print_r($ligne);


    // on vérifie le nombre de ligne obtenue, s'il y a au moins une ligne, alors le pseudo est déjà pris
    if ($date_dispo->rowCount() > 0) {

      $msg  .= '<div class="alert alert-danger h1">Attention,<br> dates indisponible, veuillez recommencer </div>';
    }


    if (empty($msg)) {

      if (empty($id_produit)) {
        $enregistrement_produit = $pdo->prepare("INSERT INTO produit( id_salle, date_arrivee, date_depart, prix, heure_arrivee, heure_depart, etat ) VALUES(:id_salle, :date_arrivee, :date_depart, :prix, :heure_arrivee, :heure_depart, 'libre' )");
      } else {
        //UPDATE =>modification

        $enregistrement_produit = $pdo->prepare("UPDATE produit SET id_salle = :id_salle, date_arrivee = :date_arrivee, date_depart = :date_depart, prix = :prix, heure_arrivee = :heure_arrivee, heure_depart = :heure_depart WHERE id_produit = :id_produit");
        $enregistrement_produit->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
      }

      $enregistrement_produit->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
      $enregistrement_produit->bindParam(':date_arrivee', $date_arrivee, PDO::PARAM_STR);
      $enregistrement_produit->bindParam(':date_depart', $date_depart, PDO::PARAM_STR);
      $enregistrement_produit->bindParam(':prix', $prix, PDO::PARAM_STR);
      $enregistrement_produit->bindParam(':heure_arrivee', $heure_arrivee, PDO::PARAM_STR);
      $enregistrement_produit->bindParam(':heure_depart', $heure_depart, PDO::PARAM_STR);
      $enregistrement_produit->execute();
    }
  }
}




include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>

<main role="main" class="container ">

  <div class="starter-template ">

    <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                    ?>
      <hr>
    </p>

    <h1>GESTION DES PRODUITS</h1>
    <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                    ?>
      <hr>
    </p>

    <a href="?action=enregistrement" class="btn btn-outline-dark"><b>Enregistrement produits</b></a>
    <a href="?action=affichage" class="btn btn-outline-dark"><b>Affichage des produits</b></a>
  </div>
  <?php if (isset($_GET['action']) && ($_GET['action'] == 'enregistrement' || $_GET['action'] == 'modifier')) { ?>
    <!-- pour cacher le contenu suivant si on veut faire un enregistrement ou una ffichage , on ferme la balise à la fin d ela page -->
    <div class="row">
      <div class="col-12 mx-auto ">
        <form method="post" action="">
          <div class="row">
            <div class="col-md-5 col-12">
              <div class="form-group h3">
                <input type="hidden" name="id_produit" readonly value="<?php echo $id_produit; ?>">
                <!--pour modif produit : on doit utiliser id du produit-->
                <label for="date_arrivee" class="mt-1">Date arrivée</label>
                <input type="date" name="date_arrivee" id="date_arrivee" class="form-control" value="<?php echo $date_arrivee ?>">
              </div>
              <div class="form-group h3">
                <label for="date_depart" class="mt-1">Date départ</label>
                <input type="date" name="date_depart" id="date_depart" class="form-control" value="<?php echo $date_depart ?>">
              </div>
              <div class="form-group h3">
                <label for="heure_arrivee" class="mt-1">Heure arrivée</label>
                <select name="heure_arrivee" id="heure_arrivee" class="form-control">
                  <?php
                  for ($i = 8; $i < 19; $i++) {
                    echo '<option';
                    if ($heure_arrivee == ($i . 'h00')) {
                      echo ' selected ';
                    }
                    echo '>';
                    echo  $i . 'h00</option>';
                  }
                  ?>
                </select>
              </div>
            </div>


            <div class="col-md-5 offset-md-2 col-12 ">
              <div class="form-group h3">
                <label for="heure_depart" class="mt-1">Heure départ</label>
                <select name="heure_depart" id="heure_depart" class="form-control">
                  <?php
                  for ($i = 9; $i < 20; $i++) {
                    echo '<option';
                    if ($heure_depart == ($i . 'h00')) {
                      echo ' selected ';
                    }
                    echo '>';
                    echo $i . 'h00';
                    echo '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-group h3">
                <label for="id_salle" class="mt-1">Salle</label>
                <select name="id_salle" id="id_salle" class="form-control">
                  <?php
                  $liste_salle = $pdo->query("SELECT * FROM salle ORDER BY id_salle");
                  while ($ligne = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option';
                    if ($id_salle == $ligne['id_salle']) {
                      echo ' selected ';
                    }
                    echo ' value="' . $ligne['id_salle'] . '">' . $ligne['id_salle'] . '-' . $ligne['titre'] . '-' . $ligne['adresse'] . '-' . $ligne['capacite'] . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-group h3">
                <label for="prix" class="mt-1">Prix</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">€</div>
                  </div>
                  <input type="text" class="form-control" id="prix" name="prix" placeholder="prix" value="<?php echo $prix; ?>">
                </div>
              </div>


            </div>
          </div>
          <div class="form-group col-6 offset-3 mt-3">
            <button type="submit" class="btn btn-dark w-100 mt-2">ENREGISTREMENT</button>
          </div>


        </form>
      </div>
    </div>
  <?php } // fermture du if  action == 'enregistrement' 
  ?>

  <?php
  //************************************
  // AFFICHAGE des articles
  //************************************

  if (isset($_GET['action']) && $_GET['action'] == 'affichage') {

    $liste_produit = $pdo->query("
  	SELECT *
    FROM produit, salle
    WHERE salle.id_salle = produit.id_salle ORDER BY id_produit");


    echo '<div class="row">';
    echo '<div class="col-12">';

    echo '<p>' . htmlspecialchars($liste_produit->rowCount(), ENT_QUOTES, 'UTF-8') . ' produit(s)</p>';
    
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered text-center table-hover table-striped">';
    echo '<tr>';
    echo '  <thead class="thead-dark ">
              <th scope="col">Id produit</th>
              <th scope="col">date d\'arrivée</th>
              <th scope="col">Date de départ</th>
              <th scope="col">heure d\'arrivée</th>
              <th scope="col">heure départ</th>
              <th scope="col">Id salle</th>
              <th scope="col">Prix</th>
              <th scope="col">Etat</th>
              <th scope="col">Modif</th>
              <th >Suppr</th>
              </thead>';



    echo '</tr>';

    // une boucle pour afficher les articles dans le tableau
    while ($ligne = $liste_produit->fetch(PDO::FETCH_ASSOC)) {
      echo '<tr>';
      $date_arrivee = $ligne['date_arrivee'];
      $date_arrivee =  date("d/m/Y", strtotime($date_arrivee));
      $date_depart = $ligne['date_depart'];
      $date_depart =  date("d/m/Y", strtotime($date_depart));

      echo '<td>' .  htmlspecialchars($ligne['id_produit'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($date_arrivee, ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($date_depart, ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['heure_arrivee'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['heure_depart'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['id_salle'], ENT_QUOTES, 'UTF-8') . ' -' . $ligne['titre'] . '<img src="' . URL . $ligne['photo'] . '" class="img-thumbnail" width="100"></td>';
      echo '<td>' . htmlspecialchars($ligne['prix'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . htmlspecialchars($ligne['etat'], ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>
                <a href="?action=modifier&id_produit=' .  htmlspecialchars($ligne['id_produit'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-warning"><i class="fas fa-edit"></i>
                </a></td><td>
                <a href="?action=supprimer&id_produit=' .  htmlspecialchars($ligne['id_produit'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="far fa-trash-alt"></i>
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