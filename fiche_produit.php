<?php
include 'inc/init.inc.php';
// s'il n'y a pas d'id_article dans l'url, on renvoie sur l'accueil
if (empty($_GET['id_produit'])) {
      header('location:' . URL);
}
// CODE

// récupération du produit et des avis en BDD
$info_produit = $pdo->prepare("SELECT * , AVG(note) AS note_moyenne FROM produit, salle , avis   WHERE id_produit = :id_produit AND salle.id_salle = produit.id_salle AND avis.id_salle = salle.id_salle");
$info_produit->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
$info_produit->execute();

if ($info_produit->rowCount() < 1) {
      header('location:' . URL);
}

$produit = $info_produit->fetch(PDO::FETCH_ASSOC);
/*
// création de produits aléatoires qui se trouvent uniquement dans la mm ville que le produit choisi.
 
*/
$produit_aleatoire = $pdo->prepare("SELECT DISTINCT *
FROM produit
LEFT JOIN salle ON produit.id_salle = salle.id_salle
LEFT JOIN avis ON salle.id_salle = avis.id_salle
WHERE date_arrivee > now() 
AND etat = 'libre' 
AND id_produit != :id_produit
AND salle.ville = '" . $produit['ville'] ."'
GROUP BY produit.id_produit
ORDER BY rand() LIMIT 4");



$produit_aleatoire->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
$produit_aleatoire->execute();




include 'inc/header.inc.php';
include 'inc/nav.inc.php';
//echo '<pre>'; var_dump($produit); echo '</pre>';
?>

<main role="main" class="container p-5">

      <div class="starter-template ">
            <h1></h1>
            <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                              ?>
            </p>
            <p class="lead"></p>
      </div>
      <div class="row ">
            <div class="col-md-9 col-12">
                  <h1><?php echo htmlspecialchars(ucfirst($produit['titre']), ENT_QUOTES, 'UTF-8') . display_stars(round($produit['note_moyenne'])); ?></h1>
            </div>
            <div class="col-md-3 col-12">
                  <form method="post" action="panier.php">
                        <input type="hidden" name="id_produit" value="<?php echo htmlspecialchars($produit['id_produit'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" name="ajout_panier" class="btn btn-btn-light border-dark w-100 ">Réserver</button>
                  </form>

            </div>
      </div>
      <hr>
      <div class="row mt-4">
            <figure class="figure col-md-8 col-12 ">
                  <img src="<?php echo  htmlspecialchars($produit['photo'], ENT_QUOTES, 'UTF-8') ?>" class="figure-img img-fluid w-md-100 " style="height: 27em" alt="<?php echo $produit['titre'] ?>">
            </figure>
            <div class="col-md-4 col-12">
                  <div style="min-height: 11em;">
                        <h4 class="font-weight-bold">Description</h4>
                        <p><?php echo  htmlspecialchars(ucfirst($produit['description']), ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                  <h4 class="font-weight-bold">Localisation</h4>
                  <?php
                  // création variables pr insertion iframe localisation 
                  $ville_url = str_replace(' ', '+', $produit['ville']); // remplace espace par +
                  $adresse_url = str_replace(' ', '+', $produit['adresse']); // remplace espace par +
                  $MapCoordsUrl = urlencode($produit['cp'] . '+' . $ville_url . '+' . $adresse_url); //url_encode : encodage pour URL
                  ?>

                  <iframe width="100%" height="220" src="http://maps.google.fr/maps?q=<?php echo $MapCoordsUrl; ?>&amp;t=m&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
            </div>
      </div>
      <div class="row">
            <h5 class="mt-3 font-weight-bold col-12 pl-0">Informations complémentaires</h5>
            <div class="col-lg-4 col-12 mt-3">
                  <p><i class="fas fa-calendar-alt mr-3"></i> Arrivée : <?php echo htmlspecialchars(date('d/m/Y', strtotime($produit['date_arrivee'])), ENT_QUOTES, 'UTF-8') . ' à ' . $produit['heure_arrivee']; ?> </p>
                  <p class="mt-3"><i class="fas fa-calendar-alt mr-3"></i> Départ : <?php echo htmlspecialchars(date('d/m/Y', strtotime($produit['date_depart'])), ENT_QUOTES, 'UTF-8') . ' à ' . $produit['heure_depart']; ?> </p>
            </div>
            <div class="col-lg-4 col-12 mt-3">
                  <p><i class="fas fa-user"></i> capacité : <?php echo  htmlspecialchars($produit['capacite'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p><i class="fas fa-building"></i> Catégorie : <?php echo htmlspecialchars($produit['categorie'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="col-lg-4 col-12 mt-3">
                  <p><i class="fas fa-map-marker"></i> <?php echo htmlspecialchars($produit['adresse'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($produit['cp'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars(strtoupper($produit['ville']), ENT_QUOTES, 'UTF-8'); ?></p>
                  <p><i class="fas fa-euro-sign"></i> Tarif : <?php echo htmlspecialchars($produit['prix'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
      </div>

      <div class="row">
            <div class="col-12 pl-0 h5 font-weight-bold mt-5 border-bottom pb-4">COMMENTAIRES</div>

            <?php
            $recup_avis = $pdo->prepare(" SELECT avis.id_membre, membre.id_membre, avis.date_enregistrement, commentaire, note, pseudo  
                 FROM avis , membre WHERE avis.id_membre = membre.id_membre AND id_salle = :id_salle ORDER BY avis.date_enregistrement DESC LIMIT 3 ");

            $recup_avis->bindParam(':id_salle', $produit['id_salle'], PDO::PARAM_STR);
            $recup_avis->execute();

            if ($recup_avis->rowCount() < 1) {
                  echo '<div class="p-4"><p>Aucun commentaire n\'a été laissé pour cette salle</p></div>';
            }

            while ($commentaire = $recup_avis->fetch(PDO::FETCH_ASSOC)) {


                  echo '<div class="col-12 mt-3"><p><span class="font-weight-bold h5">' . htmlspecialchars($commentaire['pseudo'], ENT_QUOTES, 'UTF-8') . '</span> - ' . htmlspecialchars(DATE('d/m/Y', strtotime($commentaire['date_enregistrement'])), ENT_QUOTES, 'UTF-8') . ' : </p></div>';
                  echo '<div class="col-10 offset-2 text-break"><p>Note: ' . display_stars(round($commentaire['note'])) . '<p>
                        <p>' . htmlspecialchars($commentaire['commentaire'], ENT_QUOTES, 'UTF-8') . '</p></div>';
                  echo '<div class="col-10 offset-1"><hr></div>';
            }
            ?>
      </div>

      <div class="row">
            <div class="col-12 pl-0 h5 font-weight-bold mt-5 border-bottom pb-4">AUTRES SALLES</div>




            <?php
            // boucle pour afficher les produits aléatoires
            while ($autresprod = $produit_aleatoire->fetch(PDO::FETCH_ASSOC)) {

                  // requete pour generer les avis 
                  $moy_note = $pdo->prepare(" SELECT AVG(note) AS stars FROM avis WHERE  id_salle = :id_salle");
                  $moy_note->bindParam(':id_salle', $autresprod['id_salle'], PDO::PARAM_STR);
                  $moy_note->execute();

                  $moy_stars = $moy_note->fetch(PDO::FETCH_ASSOC);
                  //var_dump($moy_stars);
                  //var_dump($autresprod);
                  //var_dump($autresprod['id_salle']);
                  echo '<div class="col-lg-3 col-12 mb-5">';
                  // echo $article['titre'];

                  echo '<div class="card mt-3">
							  <img src="' . URL . htmlspecialchars($autresprod['photo'], ENT_QUOTES, 'UTF-8') . '" class="card-img-top p-2 " style="height:10rem; width="100" "alt="' . htmlspecialchars($autresprod['titre'], ENT_QUOTES, 'UTF-8') . '">
							  <div class="card-body">
                                                <h5 class="card-title"><b>' . htmlspecialchars(ucfirst($autresprod['titre']), ENT_QUOTES, 'UTF-8') . '</b></h5>';

                  if (!empty($moy_stars['stars'])) {

                        echo '<p class="h5 ">' . display_stars(round($moy_stars['stars'])) . '</p>';
                  } else {
                        echo '<p style="min-height: 1em;"></p>';
                  }
                  echo '<p class= my-1><b>Catégorie:</b> ' . htmlspecialchars($autresprod['categorie'], ENT_QUOTES, 'UTF-8') . '</p>';
                  echo '<p class="card-text"><i class="fas fa-calendar-alt"></i><small> ' . htmlspecialchars(date('d/m/Y', strtotime($autresprod['date_arrivee'])), ENT_QUOTES, 'UTF-8') . ' au ' . htmlspecialchars(date('d/m/Y', strtotime($autresprod['date_depart'])), ENT_QUOTES, 'UTF-8') . '</small></p>
                                                 <p class="card-text"><b>' . htmlspecialchars($autresprod['prix'], ENT_QUOTES, 'UTF-8') . ' €</b></p>
                                                
								<div class="row justify-content-center"><a  href="fiche_produit.php?id_produit=' . $autresprod['id_produit'] . '" class="btn btn-light border-dark ">Voir la fiche produit</a></div>
							  </div>
							</div>';

                  echo '</div>';
            }
            ?>
      </div>

</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>