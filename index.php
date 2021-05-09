<?php
include 'inc/init.inc.php';

//CODE
$capacite = '';
$categorie = '';
$ville = '';
$prix = '';
$date_arrivee = '';
$date_depart = '';
$pagination = '';
$page = '';
$pages = '';
$where = '';
$precedent = '';
$suivant = '';
$arg = array();
if (isset($_POST['categorie']) || isset($_POST['capacite']) || (isset($_POST['prix']) && is_numeric($_POST['prix'])) || isset($_POST['date_arrivee']) || isset($_POST['date_depart'])) {

      if (!empty($_POST['categorie'])) {
            $where .= ' AND categorie = :categorie';
            $arg[':categorie'] = $_POST['categorie'];
            $categorie = $_POST['categorie'];
      }

      if (!empty($_POST['capacite'])) {
            $where .= ' AND capacite = :capacite';
            $arg[':capacite'] = $_POST['capacite'];
            $capacite = $_POST['capacite'];
      }
      if (!empty($_POST['ville'])) {
            $where .= ' AND ville = :ville';
            $arg[':ville'] = $_POST['ville'];
            $ville = $_POST['ville'];
      }
      if (!empty($_POST['prix'])) {
            $where .= ' AND prix <= :prix';
            $arg[':prix'] = $_POST['prix'];
            $prix = $_POST['prix'];
      }
      if (!empty($_POST['date_arrivee'])) {
            $where .= ' AND date_arrivee = :date_arrivee';
            $arg[':date_arrivee'] = $_POST['date_arrivee'];
            $date_arrivee = $_POST['date_arrivee'];
      }
      if (!empty($_POST['date_depart'])) {
            $where .= ' AND date_depart <= :date_depart';
            $arg[':date_depart'] = $_POST['date_depart'];
            $date_depart = $_POST['date_depart'];
      }
}
/*$liste_produit = $pdo->prepare(
     "SELECT * 
 FROM salle , produit 
 WHERE date_arrivee < now() AND etat = 'libre' $where
 AND produit.id_salle = salle.id_salle"
); */

//pagination

if (!isset($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] < 1 || isset($_POST['valid_filtre']) ) {

      $page = 1;
} else {

      $page = $_GET['page'];
}
// pour refaire venir à page 1 après l application d' un filtre


$precedent = $page - 1;
$suivant = $page + 1;

$limit = 6;

//var_dump($pages);

$nombre_produit = $pdo->prepare(
      "SELECT DISTINCT *
FROM produit
LEFT JOIN salle ON produit.id_salle = salle.id_salle
LEFT JOIN avis ON salle.id_salle = avis.id_salle
WHERE date_arrivee > now() AND etat = 'libre' $where GROUP BY produit.id_produit "
);
$nombre_produit->execute($arg);

$total = $nombre_produit->rowCount();

$pages = ceil($total / $limit);


//var_dump($total);
$pagination = 'LIMIT ' . ($page - 1) * $limit . ', ' . $limit;


$liste_produit = $pdo->prepare(
      "SELECT DISTINCT *
FROM produit
LEFT JOIN salle ON produit.id_salle = salle.id_salle
LEFT JOIN avis ON salle.id_salle = avis.id_salle
WHERE date_arrivee > now() AND etat = 'libre' $where GROUP BY produit.id_produit  $pagination "
);

$liste_produit->execute($arg);


$pas_de_resultat = $liste_produit->rowCount();
if ($pas_de_resultat == 0) {
      $msg .= 'aucun produit ne correspond à votre recherche.';
}


include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

<main role="main" class="container ">

      <div class="starter-template ">
            <h1>Nos produits</h1>

      </div>
      <div class="row">
            <div class="col-lg-3 col-12 col-md-8 offset-md-2 offset-lg-0 border-lg-right">
                  <a href="<?php echo URL; ?>index.php" class="text-body font-weight-bold">Effacer les filtres de recherche</a>
                  <form method="post" action="<?php echo URL; ?>">
                        <div class="form-group h3 mr-5 mt-3 ">
                              <label for="categorie" class="mt-1 ">Catégorie</label>
                              <select name="categorie" id="categorie" class="form-control">
                                    <option value="">Choisir une catégorie</option>
                                    <option <?php if ($categorie == 'réunion') {
                                                      echo 'selected';
                                                } ?>>réunion</option>
                                    <option <?php if ($categorie == 'bureau') {
                                                      echo 'selected';
                                                } ?>>bureau</option>
                                    <option <?php if ($categorie == 'formation') {
                                                      echo 'selected';
                                                } ?>>information</option>
                              </select>
                        </div>

                        <div class="form-group h3 mr-5">
                              <label for="capacite" class="mt-1 mr-5">Capacité</label>
                              <select name="capacite" id="capacite" class="form-control">
                                    <option></option>
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
                        <div class="form-group h3 mr-5">
                              <label for="ville" class="mt-1">Ville</label>
                              <select name="ville" id="ville" class="form-control">
                                    <option></option>
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
                        <div class="form-group h3 mr-5">
                              <label for="prix" class="mt-1">Prix max.</label>
                              <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                          <div class="input-group-text">€</div>
                                    </div>
                                    <input type="text" class="form-control" id="prix" name="prix" placeholder="prix" value="<?php echo $prix; ?>">
                              </div>
                        </div>
                        <div class="form-group h3 mr-5">
                              <label for="date_arrivee" class="mt-1">Date arrivée</label>
                              <input type="date" name="date_arrivee" id="date_arrivee" class="form-control" value="<?php echo $date_arrivee ?>">
                        </div>
                        <div class="form-group h3 mr-5">
                              <label for="date_depart" class="mt-1">Date départ</label>
                              <input type="date" name="date_depart" id="date_depart" class="form-control" value="<?php echo $date_depart ?>">
                        </div>

                        <div class="form-group mt-3 mr-5">
                              <button type="submit"  class="btn btn-dark text-white w-100 mt-2">Recherche
                              </button>
                        </div>

                  </form>

            </div>

            <div class="col-lg-9 col-12">
                  <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                                    ?>
                  </p>
                  <div class="row">
                        <div class="col-8 col-lg-5 offset-2 offset-lg-4">

                              <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                          <li class="page-item  <?php if (($precedent ) == 0) {
                                                                        echo 'disabled';
                                                                  } ?>"><a class="page-link text-dark " href="<?PHP echo URL . 'index.php?page=' . $precedent; ?>">Previous</a></li>

                                          <?php for ($i = 1; $i <= $pages; $i++) {?>
                                                <li class="page-item "><a class="page-link text-dark" href="<?PHP echo URL . 'index.php?page=' . $i; ?>"><?php echo $i; ?></a></li>
                                          <?php }  ?>
                                                 
                                          <li class="page-item <?php if ($suivant - 1  == $pages) {
                                                                        echo 'disabled';
                                                                  } ?>"><a class="page-link text-dark " href="<?PHP echo URL . 'index.php?page=' . $suivant; ?>">Next</a></li>
                                    </ul>
                              </nav>


                        </div>

                        <?php

                        while ($produit = $liste_produit->fetch(PDO::FETCH_ASSOC)) {

                              $moy_note = $pdo->prepare(" SELECT AVG(note) AS stars FROM salle, avis WHERE salle.id_salle = avis.id_salle AND avis.id_salle = :id_salle");
                              $moy_note->bindParam(':id_salle', $produit['id_salle'], PDO::PARAM_STR);
                              $moy_note->execute();

                              $moy_stars = $moy_note->fetch(PDO::FETCH_ASSOC);


                              echo '<div class="col-8 offset-2 offset-lg-0 col-lg-5 ml-lg-5 mr-xl-3 mb-5">';
                              // echo $article['titre'];

                              echo '<div class="card mt-3 ">
							  <img src="' . URL . htmlspecialchars($produit['photo'], ENT_QUOTES, 'UTF-8') . '" class="card-img-top  w-40" style="height:12rem;" "alt="' . htmlspecialchars($produit['titre'], ENT_QUOTES, 'UTF-8') . '">
							  <div class="card-body">
                                                <h5 class="card-title">' . htmlspecialchars(ucfirst($produit['titre']), ENT_QUOTES, 'UTF-8') . '</h5>';
                                                if (!empty($moy_stars['stars'])) {

                                                      echo '<p class="h5 ">' . display_stars(round($moy_stars['stars'])) . '</p>';
                                                } else {
                                                      echo '<p style="min-height: 1.5em;">Salle pas encore notée</p>';
                                                }

                                   echo  '<p class="card-text mt-3">Catégorie: ' . htmlspecialchars($produit['categorie'], ENT_QUOTES, 'UTF-8') . '</p>
                                                 <p class="card-text"><i class="fas fa-calendar-alt"></i> ' . htmlspecialchars(date('d/m/Y', strtotime($produit['date_arrivee'])), ENT_QUOTES, 'UTF-8') . ' au ' . htmlspecialchars(date('d/m/Y', strtotime($produit['date_depart'])), ENT_QUOTES, 'UTF-8') . '</p>
                                                 <p class="card-text">Prix: ' . htmlspecialchars($produit['prix'], ENT_QUOTES, 'UTF-8') . ' €</p>
                                                
								<div class="row justify-content-center"><a  href="fiche_produit.php?id_produit=' . htmlspecialchars($produit['id_produit'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-light border-dark ">Voir la fiche produit</a></div>
							  </div>
							</div>';

                              echo '</div>';
                        }
                        ?>
                  </div>
            </div>
      </div>
      <div class="row">
            <div>
                  <hr>
            </div>
      </div>


</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>