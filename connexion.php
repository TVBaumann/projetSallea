<?php
include 'inc/init.inc.php';

if (user_is_connect()) {
	header("location:profil.php");
}

if (isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
	session_destroy(); // on détruit la session
}

//code
$pseudo = '';

if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {

	if (empty($_POST['pseudo']) && empty($_POST['mdp'])) {
		$msg  .= '<div class="alert alert-danger h1">Un des champs n\'est pas renseigné</div>';
	} else {

		$pseudo = trim($_POST['pseudo']);
		$mdp = trim($_POST['mdp']);

		// on vérifie si le pseudo existe en bdd

		$membre = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
		$membre->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
		$membre->execute();

		//si on a une ligne le pseudo, le pseudo est correcte:

		if ($membre->rowCount() < 1) {

			$msg .= '<div class="alert alert-danger h1">Attention,<br> erreur sur le pseudo et/ou le mot de passe. </div>';
		} else {

			$infos_membre = $membre->fetch(PDO::FETCH_ASSOC);

			//echo '<pre>'; var_dump($infos_membre); echo '</pre>';
			if (password_verify($mdp, $infos_membre['mdp'])) {
				// on place les informations du membre récupérées dan sla bdd dans la session pour pouvoir l'interoger ensuite à tout moment.

				$_SESSION['membre'] = array();
				$_SESSION['membre']['id_membre'] = $infos_membre['id_membre'];
				$_SESSION['membre']['pseudo'] = $infos_membre['pseudo'];
				$_SESSION['membre']['nom'] = $infos_membre['nom'];
				$_SESSION['membre']['prenom'] = $infos_membre['prenom'];
				$_SESSION['membre']['email'] = $infos_membre['email'];
				$_SESSION['membre']['civilite'] = $infos_membre['civilite'];
				$_SESSION['membre']['statut'] = $infos_membre['statut'];

				echo '<pre>'; var_dump($_SESSION['membre']); echo '</pre>';

				// une fois la connexion mis ene place, on redirige vers la page profil.php
				header('location:profil.php');
			} else {
				$msg .= '<div class="alert alert-danger h1">Attention,<br> erreur sur le pseudo et/ou le mot de passe. </div>';
			}
		}
	}
}
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

<main role="main" class="container ">

	<div class="starter-template ">
		<h1>CONNEXION</h1>
		<p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
						?>
			<hr></p>
		<p class="lead"></p>
	</div>
	<div class="row">
		<div class=" col-10 offset-1 col-md-6 offset-md-3 col-lg-4 offset-lg-4 border border-dark">
			<form method="post" action="">
				<div class="row">
					<div class="col-10 offset-1 text-center">
						<div class="form-group h3">
							<label for="pseudo" class="mt-4">Pseudo</label>
							<input type="text" name="pseudo" id="pseudo" class="form-control" value="">
							<label for="mdp" class="mt-3">Mot de passe</label>
							<input type="password" name="mdp" id="mdp" class="form-control" value="">



							<div class="form-group col-10 offset-1 mt-3">
								<button type="submit" class="btn btn-light border-dark mb-3 w-100 mt-2"> Inscription</button>
							</div>
						</div>
					</div>


				</div>
			</form>

		</div>

	</div>

</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>