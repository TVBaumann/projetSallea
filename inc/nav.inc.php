


 <nav class="navbar navbar-expand-lg navbar-light  rounded container ">
 <a class="nav-link font-weight-bold text-dark" id="icon"  href="<?php echo URL; ?>index.php">SALLEA<span class="sr-only">(current)</span></a>
   <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
     <span class="navbar-toggler-icon"></span>
   </button>



   <div class="collapse navbar-collapse" id="navbarsExample09">
     <ul class="navbar-nav mr-auto">
       
       <li class="nav-item ml-3 font-weight-bold">
         <a class="nav-link" href="<?php echo URL; ?>contact.php">Nous contacter<span class="sr-only">(current)</span></a>
       </li>
     </ul>

     <div class=" my-2 my-lg-0 ">
       <ul class="navbar-nav">
       <li class="nav-item font-weight-bold">
        <a class="nav-link text-dark" href="<?php echo URL; ?>panier.php">Panier</a>
      </li>
         <li class="nav-item font-weight-bold">
           <a class="nav-link dropdown-toggle" href="#" id="dropdown09" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user mr-2"></i>Membre</a>
           <div class="dropdown-menu dropdown-menu-right my-2 my-lg-0" aria-labelledby="dropdown09">
             <?php if (!user_is_connect()) { ?>
               <a class="dropdown-item" href="<?php echo URL; ?>inscription.php">Inscription</a>
               <a class="dropdown-item" href="<?php echo URL; ?>connexion.php">Connexion</a>
             <?php } else { ?>
               <a class="dropdown-item" href="<?php echo URL; ?>profil.php">profil</a>
               <a class="dropdown-item" href="<?php echo URL; ?>connexion.php?action=deconnexion">deconnexion</a>
             <?php } ?>
           </div>
         </li>

         <?php if (user_is_admin()) { ?>
           <li class="nav-item dropdown font-weight-bold">
             <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Administration</a>
             <div class="dropdown-menu" aria-labelledby="dropdown01">
               <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_membre.php">Gestion membre</a>
               <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_salle.php">Gestion salle</a>
               <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_produit.php">Gestion produit</a>
               <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_commande.php">Gestion commande</a>
               <a class="dropdown-item" href="<?php echo URL; ?>admin/statistiques.php">Statistiques</a>
               <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_avis.php">Gestion avis</a>
             </div>
           </li>
         <?php } ?>

         <li class="nav-item font-weight-bold">
           <a class="nav-link " href="#"></a>
         </li>
         
       </ul>
     </div>

   </div>
 </nav>