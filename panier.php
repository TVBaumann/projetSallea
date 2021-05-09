<?php
//CODE
include 'inc/init.inc.php';

//var_dump($_POST);


// CODE
//Vider le panier // comme on teste panier parotut sur la page il faut mettre vider panier avant la création pr qu'il soit recreer tt suite grace à la fonction creation_panier et donc evite rles erreurs dans notre page.
if (isset($_GET['action']) && $_GET['action'] == 'vider') {
    unset($_SESSION['panier']);
}

//payer le panier
if (isset($_GET['action']) && $_GET['action'] == 'payer' && !empty($_SESSION['panier']['titre'])) {
    //enregistrement d ela commande dans la table commande
    $commande = $pdo->prepare("INSERT INTO commande ( id_membre, id_produit, date_enregistrement, prix) VALUES (:id_membre, :id_produit, NOW(), :prix)");
    $commande->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
    $commande->bindParam(':id_produit', $_SESSION['panier']['id_produit'][0], PDO::PARAM_STR);
    $commande->bindParam(':prix', $_SESSION['panier']['montant_total'], PDO::PARAM_STR);
    $commande->execute();

    // Update du statut du produit
    $update_produit = $pdo->prepare("UPDATE produit SET etat = 'reservation' WHERE id_produit = :id_produit");
    $update_produit->bindParam('id_produit', $_SESSION['panier']['id_produit'][0], PDO::PARAM_STR);
    $update_produit->execute();

    //on récupère l'id _commande qui vient d'être inséré dans la BDD
    $id_commande = $pdo->lastInsertId();

    // on vide le panier
    unset($_SESSION['panier']);
}
// on crée le panier (penser à créer la fonction dans le fichier fonction.inc)
creation_panier();

//Ajouter au panier
if (
    isset($_POST['ajout_panier']) &&
    !empty($_POST['id_produit']) &&
    is_numeric($_POST['id_produit'])
) {

    // on récupère en BDD les information de l'article à rjaouter pour avoir son prix et son titre
    $infos_produit = $pdo->prepare("SELECT * FROM salle, produit WHERE salle.id_salle = produit.id_salle AND  id_produit = :id_produit");
    $infos_produit->bindParam(':id_produit', $_POST['id_produit'], PDO::PARAM_STR);
    $infos_produit->execute();

    if ($infos_produit->rowCount() > 0) {
        $produit = $infos_produit->fetch(PDO::FETCH_ASSOC);
        //echo '<pre>'; var_dump($produit); echo '</pre>';

        $_SESSION['panier']['id_produit'][] = $_POST['id_produit'];
        $_SESSION['panier']['prix'][] = $produit['prix'];
        $_SESSION['panier']['titre'][] = $produit['titre'];

        // pour ne pas rajouter le mm article avec F5, on recharge la page graceà l'information dans $_SERVER PHP_SELF
        header('location:' . $_SERVER['PHP_SELF']);
    }
}

include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

<main role="main" class="container ">

    <div class="starter-template ">
        <h1>PANIER</h1>
        <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                        ?>
            </p><hr>
        <p class="lead"></p>
    </div>


    <div class="row">
        <div class="col-12">

            <?php
            if (!empty($_SESSION['panier']['titre'])) {
                //si le panier n'est pas vide, ajout d'un bouton pour vider le panier.
                echo '<a href="?action=vider" class="btn btn-danger">vider le panier</a><hr>';
            }

            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>N°produit</th>
                            <th>Titre</th>

                            <th>Prix unitaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($_SESSION['panier']['id_produit'])) {  //$_SESSION['panier' ne sera jamais vide car il a des indice à l'interieur(vide) , dc tester un de sindices à l'intereiur.
                            //si le panier n'est pas vide.

                            $montant_total = 0;


                            echo '<tr>';
                            echo '<td>' .  htmlspecialchars($_SESSION['panier']['id_produit'][0], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' .  htmlspecialchars($_SESSION['panier']['titre'][0], ENT_QUOTES, 'UTF-8') . '</td>';

                            echo '<td>' .  htmlspecialchars($_SESSION['panier']['prix'][0], ENT_QUOTES, 'UTF-8') . '</td>';

                            // calcul du montant total => prix * quantite pour chaque article
                            $montant_total += ($_SESSION['panier']['prix'][0]);
                            //
                            echo '</tr>';


                            // on rajoute 20% de tva
                            $montant_total = round($montant_total * 1.2, 2);
                            // on stock le montant total dans la session
                            $_SESSION['panier']['montant_total'] = $montant_total;
                            echo '<tr>';
                            echo '<td colspan="2">';
                            //exercice :
                            //afficher un bouton (a href) Payer si l'utilisateur est connecté. Sinon afficher du texte avec un lien pour se connecter et un lien pour s'inscrire

                            if (user_is_connect()) {
                                echo '<a href="?action=payer" class="btn btn-success w-50">Payer</a>';
                            } else {
                                echo 'Veuillez vous  <a href="' . URL . 'connexion.PHP">connecter</a> ou vous <a href="' . URL . 'inscription.php">inscrire</a> pour payer votre panier';
                            }


                            echo '</td>';
                            echo '<td colspan="2">';
                            echo 'Montant total TTC: <b>' . htmlspecialchars($montant_total, ENT_QUOTES, 'UTF-8') . '<b> €';
                            echo '</td>';
                            echo '</tr>';
                        } else {
                            echo '<tr><td colspan="4"><div class="alert alert-danger">Votre panier est vide</div></td></tr>';
                        }


                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>