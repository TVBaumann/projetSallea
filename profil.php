<?php
//CODE
include 'inc/init.inc.php';

//code

//Restriciton d'acces, si l'utilisateur n'est pas connecté, on le renvoie sur connexion.php
if (!user_is_connect()) {
	header("location:connexion.php");
}




if (isset($_GET['avis']) && isset($_GET['id_commande']) && is_numeric($_GET['id_commande']) && isset($_POST['enreg_com'])) {

	if (empty($_POST['avis']) ||  empty($_POST['note'])) {

		$msg .= '<div class="alert alert-danger h1">Vous devez renseigner la note et un avis pour enregistrer votre commentaire. </div>';
	} else {

		// Pour que l'utilisateur ne puisse pas laisser plusieurs  commentaires
		$avis_dispo1 = $pdo->prepare("SELECT * FROM avis WHERE id_commande = :id_commande");
		$avis_dispo1->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
		$avis_dispo1->execute();

		if ($avis_dispo1->rowCount() == 0) {

			// recupere les infos pr les enregistrer dans la table avis

			$info_avis = $pdo->prepare("SELECT *
    FROM membre, salle, commande , produit
    WHERE membre.id_membre = commande.id_membre
    AND salle.id_salle = produit.id_salle
	AND produit.id_produit = commande.id_produit
	AND commande.id_commande = :id_commande");

			$info_avis->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
			$info_avis->execute();

			$result_avis = $info_avis->fetch(PDO::FETCH_ASSOC);

			$id_membre = $result_avis['id_membre'];
			$id_salle = $result_avis['id_salle'];

			//print_r($result_avis);

			$enreg_avis = $pdo->prepare("INSERT INTO  avis (id_membre, id_salle,commentaire, note, date_enregistrement, id_commande) VALUES (:id_membre, :id_salle, :commentaire,:note, NOW(), :id_commande)");
			$enreg_avis->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
			$enreg_avis->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
			$enreg_avis->bindParam(':commentaire', $_POST['avis'], PDO::PARAM_STR);
			$enreg_avis->bindParam(':note', $_POST['note'], PDO::PARAM_STR);
			$enreg_avis->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
			$enreg_avis->execute();

			$msg .= '<div class="alert alert-danger h1">Votre commantaire a bien été enregistré. </div>';
		} else {
			$msg .= '<div class="alert alert-danger h1">Vous avez déjà publié un commentaire pour cette commande </div>';
		}
	}
}

include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

<main role="main" class="container ">

	<div class="starter-template ">
		<h1>PROFIL</h1>
		<p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
						?>
			<hr>
		</p>
		<p class="lead"></p>
	</div>
	<div class="row">
		<div class="col-12 col-md-6">

			<ul class="list-group">
				<li class="list-group-item bg-dark text-light">VOS INFORMATIONS: </li>
				<li class="list-group-item"><b>N°:</b> <?PHP echo htmlspecialchars($_SESSION['membre']['id_membre'], ENT_QUOTES, 'UTF-8'); ?></li>
				<li class="list-group-item"><b>nom:</b> <?PHP echo  htmlspecialchars($_SESSION['membre']['statut'], ENT_QUOTES, 'UTF-8'); ?></li>
				<li class="list-group-item"><b>pseudo:</b> <?PHP echo  htmlspecialchars($_SESSION['membre']['pseudo'], ENT_QUOTES, 'UTF-8'); ?></li>
				<li class="list-group-item"><b>nom:</b> <?PHP echo  htmlspecialchars($_SESSION['membre']['nom'], ENT_QUOTES, 'UTF-8'); ?></li>
				<li class="list-group-item"><b>pénom:</b> <?PHP echo  htmlspecialchars($_SESSION['membre']['prenom'], ENT_QUOTES, 'UTF-8'); ?></li>
				<li class="list-group-item"><b>email:</b> <?PHP echo  htmlspecialchars($_SESSION['membre']['email'], ENT_QUOTES, 'UTF-8'); ?></li>
				<li class="list-group-item"><b>sexe: </b>
					<?PHP if ($_SESSION['membre']['civilite'] == 'm') {
						echo 'Homme';
					} else {
						echo 'femme';
					}; ?>
				</li>

				<li class="list-group-item"><b>statut: </b>
					<?PHP if ($_SESSION['membre']['statut'] == '1') {
						echo 'membre';
					} else {
						echo 'administrateur';
					}; ?>
				</li>

			</ul>
		</div>
		<div class="col-md-6 d-none d-md-block">
			<?php if ($_SESSION['membre']['civilite'] == 'm') {
				echo '<img src="image/avatar-homme.png" class="w-75 ml-3" alt=photoprofil>';
			}

			if ($_SESSION['membre']['civilite']  == 'f') {
				echo '<img src="image/avatar-femme.png" class="w-75 ml-3" alt=photoprofil>';
			}
			?>
		</div>



	</div>




	<div class="row">
		<div class="col-12 col-md-6"><a href="?action=voir_commandes" class="btn btn-light border-dark mt-4 ml-3 mb-5 w-70 btn-lg">Voir mes commandes/Laisser un commentaire</a></div>
	</div>





	<?php

	if (isset($_GET['avis']) && $_GET['avis'] == 'laisser_avis') {  ?>

		<div class="row">
			<div class="col-12 mx-auto ">
				<form method="post" action="">
					<div class="row">
						<div class="col-8 ">
							<div class="form-group h3">
								<label for=" avis" class="mt-1 h3">Avis commande N° <?php echo htmlspecialchars($_GET['id_commande'], ENT_QUOTES, 'UTF-8') ?></label>
								<textarea name="avis" id="avis" class="form-control" rows="7"></textarea>
							</div>
							<div class="form-group h3">
								<label for="note" class="mt-1">Note</label>
								<select name="note" id="note" class="form-control">
									<?php
									for ($i = 5; $i >= 1; $i--) {

										echo '<option value="' . $i . '">' . $i . ' /5';
										echo '</option>';
									}
									?>
								</select>
							</div>
							<div class="form-group col-6 offset-3 mt-3">
								<button type="submit" name="enreg_com" class="btn btn-dark w-100 mt-2">ENREGISTREMENT</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>


	<?php }
	?>

	<?php

	//************************************
	// AFFICHAGE des commandes
	//************************************
	if (isset($_GET['action']) && $_GET['action'] == 'voir_commandes') {
		$liste_commande = $pdo->query("SELECT *
    FROM membre, produit, salle, commande
    WHERE membre.id_membre = commande.id_membre
    AND salle.id_salle = produit.id_salle
    AND produit.id_produit = commande.id_produit
	AND commande.id_membre = " . $_SESSION['membre']['id_membre'] . " ORDER BY id_commande");

		echo '<div class="row">';
		echo '<div class="col-12">';

		echo '<p>' . htmlspecialchars($liste_commande->rowCount(), ENT_QUOTES, 'UTF-8') . ' commande(s)</p>';

		echo '<div class="table-responsive">';
		echo '<table class="table table-bordered text-center table-hover table-striped">';
		echo '<tr>';
		echo '  <thead class="thead-dark">
              <th scope="col">Id commande</th>      
			  <th scope="col">Réservation</th>
			  <th scope="col">Heure arrivée / <br> heure départ</th>
			  <th scope="col">Adresse</th>
              <th scope="col">Montant</th>
              <th scope="col">date réservation</th>
              <th> Laisser<br> un Avis</th>
              </thead>';

		echo '</tr>';

		// une boucle pour afficher les articles dans le tableau 

		while ($ligne = $liste_commande->fetch(PDO::FETCH_ASSOC)) {
			echo '<tr>';
			echo '<td>' . htmlspecialchars($ligne['id_commande'], ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>' . htmlspecialchars($ligne['titre'], ENT_QUOTES, 'UTF-8') . '<br> du ' . htmlspecialchars($ligne['date_arrivee'], ENT_QUOTES, 'UTF-8') . ' au ' . htmlspecialchars($ligne['date_depart'], ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>'  . htmlspecialchars($ligne['heure_arrivee'], ENT_QUOTES, 'UTF-8') . ' / ' . htmlspecialchars($ligne['heure_depart'], ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>' . htmlspecialchars($ligne['adresse'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($ligne['cp'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($ligne['ville'], ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>' . htmlspecialchars($ligne['prix'], ENT_QUOTES, 'UTF-8') . ' € </td>';
			echo '<td>' . htmlspecialchars($ligne['date_enregistrement'], ENT_QUOTES, 'UTF-8') . '</td>';

			// pour que le bouton laisser_un_avis n apparaisse uniquement si le membre n' en a pas encore laissé

			$avis_dispo = $pdo->prepare("SELECT * FROM avis WHERE id_commande = :id_commande");
			$avis_dispo->bindParam(':id_commande', $ligne['id_commande'], PDO::PARAM_STR);
			$avis_dispo->execute();

			if ($avis_dispo->rowCount() == 0) {

				echo '<td>
        <a href="?action=voir_commandes&avis=laisser_avis&id_commande=' . htmlspecialchars($ligne['id_commande'], ENT_QUOTES, 'UTF-8') . '" class="btn btn-light border"><i class="far fa-comment-alt"></i>
        </a></td>
        ';
				echo '</tr>';
			} else {
				echo '<td>vous avez déjà <br> laissé un commentaire</td>';
			}
		}


		echo '</table>';

		echo '</div>';
		echo '</div>';
		echo '</div>';
	}








	?>

</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>