<?php
//CODE
include '../inc/init.inc.php';


include '../inc/header.inc.php';
include '../inc/nav.inc.php';

// 5 salles les plus commandées
$best_commande = $pdo->query("SELECT titre, count(salle.id_salle) AS best_m FROM commande LEFT JOIN produit ON commande.id_produit = produit.id_produit LEFT JOIN salle ON salle.id_salle = produit.id_salle GROUP BY titre ORDER BY best_m DESC LIMIT 5");

// 5 salles les mieux notées
$moyenne_note = $pdo->query("SELECT titre, AVG(avis.note) AS moy_note  FROM avis LEFT JOIN salle ON salle.id_salle = avis.id_salle GROUP BY titre ORDER BY moy_note DESC LIMIT 5");

// 5 membres qui achètent le plus (quantité)
$membre_commmande = $pdo->query("SELECT membre.id_membre, pseudo, count(commande.id_commande) AS nbrcom FROM commande LEFT JOIN membre ON commande.id_membre = membre.id_membre GROUP BY membre.id_membre ORDER BY nbrcom DESC LIMIT 5");

// 5 membres qui achètent le plus (prix)
$membre_prix = $pdo->query("SELECT membre.id_membre, pseudo, sum(commande.prix) AS prxm FROM commande LEFT JOIN membre ON commande.id_membre = membre.id_membre GROUP BY membre.id_membre ORDER BY prxm DESC LIMIT 5");

?>

<main role="main" class="container ">

      <div class="starter-template ">
            <h1>STATISTIQUES</h1>
            <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                              ?>

      </div>
      <div class=row>
            <div class="dropdown show col-6 offset-3">
                  <a class="btn btn-light btn-lg dropdown-toggle border-dark" style="width: 100%;" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Statistiques
                  </a>

                  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="width: 100%">
                        <a class="dropdown-item" href="?action=salles_les_plus_commandees">Top 5 des salles les plus commandées</a>
                        <a class="dropdown-item" href="?action=salles_les_mieux_notees">Top 5 salles les mieux notées</a>
                        <a class="dropdown-item" href="?action=membre_nb_commandes">Top 5 membres qui achètent le + (quantité)</a>
                        <a class="dropdown-item" href="?action=membre_prix_commandes">Top 5 membres qui achètent le + (prix)</a>
                  </div>
            </div>
      </div>
      <div class="row">
            <!-- Top 5 des salles les plus commandées -->
            <?php if (isset($_GET['action']) && $_GET['action'] == 'salles_les_plus_commandees') { ?>

                  <div class="col-6 offset-3 mt-5 h4">
                        <ul class="list-group">
                              <li class="list-group-item  justify-content-between align-items-center text-center bg_whitesmoke">
                                    Top 5 des salles les plus commandées</li>
                              <?php while ($best_com = $best_commande->fetch(PDO::FETCH_ASSOC)) {   ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center h5">
                                          <?php echo  htmlspecialchars($best_com['titre'], ENT_QUOTES, 'UTF-8') ?>
                                          <span class="badge badge-primary badge-pill"><?php echo  htmlspecialchars($best_com['best_m'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </li>
                              <?php } ?>

                        </ul>

                  </div>

            <?php  } ?>

            <!-- Top 5 salles les mieux notées -->
            <?php if (isset($_GET['action']) && $_GET['action'] == 'salles_les_mieux_notees') { ?>

                  <div class="col-6 offset-3 mt-5 h4">
                        <ul class="list-group">
                              <li class="list-group-item  justify-content-between align-items-center text-center bg_whitesmoke">
                                    Top 5 des salles les mieux notées</li>
                              <?php while ($m_note = $moyenne_note->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center h5">
                                          <?php echo  htmlspecialchars($m_note['titre'], ENT_QUOTES, 'UTF-8') ?>
                                          <span class="badge badge-primary badge-pill"><?php echo htmlspecialchars(round($m_note['moy_note']), ENT_QUOTES, 'UTF-8') ?></span>
                                    </li>
                              <?php } ?>

                        </ul>

                  </div>
            <?php  } ?>

            <!-- Top 5 des membres qui achetent le plus (quantité) -->
            <?php if (isset($_GET['action']) && $_GET['action'] == 'membre_nb_commandes') { ?>

                  <div class="col-6 offset-3 mt-5 h4">
                        <ul class="list-group">
                              <li class="list-group-item  justify-content-between align-items-center text-center bg_whitesmoke">
                                    Top 5 des membre qui achetent le + (quantité)</li>
                              <?php while ($best_membre_commande = $membre_commmande->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center h5">
                                          <?php echo  htmlspecialchars($best_membre_commande['id_membre'], ENT_QUOTES, 'UTF-8'). ' ' .$best_membre_commande['pseudo'] ?>
                                          <span class="badge badge-primary badge-pill"><?php echo  htmlspecialchars($best_membre_commande['nbrcom'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </li>
                              <?php } ?>

                        </ul>

                  </div>
            <?php  } ?>

            <!-- Top 5 des membres qui achetent le plus (prix) -->
            <?php if (isset($_GET['action']) && $_GET['action'] == 'membre_prix_commandes') { ?>

                  <div class="col-6 offset-3 mt-5 h4">
                        <ul class="list-group">
                              <li class="list-group-item  justify-content-between align-items-center text-center bg_whitesmoke">
                                    Top 5 des membre qui achetent le + (prix)</li>
                              <?php while ($best_membre_prix = $membre_prix->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center h5">
                                          <?php echo  htmlspecialchars($best_membre_prix['id_membre'], ENT_QUOTES, 'UTF-8') . ' ' .$best_membre_prix['pseudo'] ?>
                                          <span class="badge badge-primary badge-pill"><?php echo htmlspecialchars(round($best_membre_prix['prxm']), ENT_QUOTES, 'UTF-8'). ' €' ?></span>
                                    </li>
                              <?php } ?>

                        </ul>

                  </div>
            <?php  } ?>

      </div>

</main><!-- /.container -->

<?php
include '../inc/footer.inc.php';
?>