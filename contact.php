<?php
//CODE
include 'inc/init.inc.php';


include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

<main role="main" class="container ">

      <div class="starter-template ">
      <hr>
                  <h1 class="text-center">Contact</h1>
                  <hr>
            
            <p class="lead"><?php echo $msg; // variable destinée à afficher des messages utilisateur 
                              ?>
                  
            <p class="lead"></p>
      </div>
      <div class="row">
            <div class="col-6 mx-auto mt-3">
                  

                  <?php

                  // afficher les saisies du formulaire proprement avec des echos

                  if (isset($_POST['expediteur'])  && isset($_POST['sujet'])  && isset($_POST['message'])) {

                        $expediteur = $_POST['expediteur'];
                        //$destinataire = $_POST['destinataire'];
                        $sujet = $_POST['sujet'];
                        $message = $_POST['message'];

                        echo '<p>Expéditeur : ' . $expediteur . '</p>';
                        echo '<p>Destinataire : ' . $destinataire . '</p>';
                        echo '<p>Sujet : ' . $sujet . '</p>';
                        echo '<p>Message : ' . $message . '</p>';

                        $expediteur = "From: " . $expediteur . "\n";
                        $expediteur .= "MIME-Version: 1.0 \n";
                        $expediteur .= "Content-type: text/html; charset=iso-8859-1 \n";

                        // mail(destinataire, sujet, message, expediteur);
                        mail($destinataire, $sujet, $message, $expediteur);
                        // mail('monmail@mail.fr', $sujte, $message, $expediteur);

                  }
                  ?>





                  <form method="post" action="">
                        <div class="form-group border_form">
                              <label for="expediteur" class="h4 font-weight-bold">Expediteur</label>
                              <input type="text" class="form-control " name="expediteur" id="expediteur" value="">
                        </div>
                        
                        <div class="form-group border_form">
                              <label for="sujet" class="h4 font-weight-bold">Sujet</label>
                              <input type="text" class="form-control" name="sujet" id="sujet" value="">
                        </div>
                        <div class="form-group border_form_textarea">
                              <label for="message" class="h4 font-weight-bold">Message</label>
                              <textarea  class="form-control" name="message" id="message"  rows="7"></textarea>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-lg font-weight-bold bg_whitesmoke btn-dark border-dark w-100 h3 text-dark"> Envoyer </button>
                  </form>





            </div>
      </div>

</main><!-- /.container -->

<?php
include 'inc/footer.inc.php';
?>