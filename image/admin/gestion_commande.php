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
if(isset($_GET['action']) && $_GET['action']  == 'supprimer'  && isset($_GET['id_commande']) && is_numeric($_GET['id_commande']) && isset($_GET['id_produit']) ) {
  //si l'indice action existe dans GET et si sa valeur ets égale à supprimer MAIS aussi si id-article exsite dans GET et si c'est bien sous forme nuùmérique.
  $suppression_produit = $pdo->prepare("DELETE FROM commande WHERE id_commande = :id");
  $suppression_produit->bindParam(':id', $_GET['id_commande'], PDO::PARAM_STR);
  $suppression_produit->execute();
  $msg .= '<div class="alert alert-success">L\'article n°'.$_GET['id_commande'] . ' a bien été supprimé</div';
  $_GET['action'] = 'affichage'; // on force l'affichage du tableau.

  // modification du statut du produit en cas de supp commande pr le rendre de nouveau dispo

  $modif_statut_produit = $pdo->prepare("UPDATE produit SET etat = 'libre' WHERE id_produit = :id_produit ");
  $modif_statut_produit->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
  $modif_statut_produit->execute();

}


/*********************************
//*********************************
//***02 FIN SUPRESISON PRODUIT*****
//********************************
//*********************************/







?>

  <main role="main" class="container ">

      <div class="starter-template mt-5">
           <h1>GESTION DES COMMANDES</h1>
            <p class="lead "><?php echo $msg; // variable destinée à afficher des messages utilisateur ?><hr></p>
      </div>
      

  <?php
  //************************************
     // AFFICHAGE des commandes
     //************************************

  $liste_commande = $pdo->query("SELECT *
    FROM membre, produit, salle, commande
    WHERE membre.id_membre = commande.id_membre
    AND salle.id_salle = produit.id_salle
    AND produit.id_produit = commande.id_produit
    ORDER BY id_commande");

      echo '<div class="row">';
      echo '<div class="col-12">';

      echo '<p>' . htmlspecialchars($liste_commande->rowCount(), ENT_QUOTES, 'UTF-8') . ' commande(s)</p>';

      echo '<div class="table-responsive">';
      echo '<table class="table table-bordered text-center table-hover table-striped">';
      echo '<tr>';
      echo '  <thead class="thead-dark">
              <th scope="col">Id commande</th>
              <th scope="col">Id membre</th>
              <th scope="col">Id produit</th>
              <th scope="col">Montant</th>
              <th scope="col">date_enregistrement</th>
              <th >Suppr</th>
              </thead>';

       echo '</tr>'; 

       // une boucle pour afficher les articles dans le tableau 

       while($ligne = $liste_commande->fetch(PDO::FETCH_ASSOC)) { 
        echo '<tr>';
        echo '<td>' . htmlspecialchars($ligne['id_commande'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['id_membre'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($ligne['email'], ENT_QUOTES, 'UTF-8') .  '</td>';
        echo '<td>' . htmlspecialchars($ligne['id_produit'], ENT_QUOTES, 'UTF-8') . '-' . htmlspecialchars($ligne['titre'], ENT_QUOTES, 'UTF-8') . '<br>' . htmlspecialchars($ligne['date_arrivee'], ENT_QUOTES, 'UTF-8') . ' au ' . htmlspecialchars($ligne['date_depart'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($ligne['prix'], ENT_QUOTES, 'UTF-8') . ' € </td>';
        echo '<td>' . htmlspecialchars($ligne['date_enregistrement'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>
        <a href="?action=supprimer&id_commande=' . $ligne['id_commande'] . '&id_produit=' . $ligne['id_produit'].'" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="far fa-trash-alt"></i>
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