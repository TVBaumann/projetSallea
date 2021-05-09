<?php

//fonction.inc.php
// return true si il ets connecté et false si il ne l 'est pas'
function user_is_connect(){
	if(isset($_SESSION['membre'])){
		return true;
	}else {
		return false;
	}
}

//fonciton pour savoir si l'utilisateur est admin
//si lutlisateur est passé par connection à a le statut admin
function user_is_admin(){
	if(user_is_connect() && $_SESSION['membre'] ['statut'] == 2){
		return true;
	}
	return false;
}

//création du panier 
function creation_panier() {
	if(!isset($_SESSION['panier'])) {
		//si le panier n'existe pas dans session (!isset), on le crée , sinon rien.
		$_SESSION['panier'] = array();
		$_SESSION['panier'] ['id_produit'] = array();
		$_SESSION['panier'] ['prix'] = array();
		$_SESSION['panier'] ['titre'] = array();

	}
}

//fonction display note page fiche article 
function display_stars($note) {
	
	switch($note) {
		case '1' :	return '<i class="far fa-star "></i>'; break;
		case '2' : return '<i class="far fa-star "></i><i class="far fa-star "></i>'; break;
		case '3' : return '<i class="far fa-star "></i><i class="far fa-star "></i><i class="far fa-star "></i>'; break;
		case '4' : return '<i class="far fa-star"></i><i class="far fa-star "></i><i class="far fa-star "></i><i class="far fa-star "></i>'; break;
		case '5' : return '<i class="far fa-star"></i><i class="far fa-star "></i><i class="far fa-star "></i><i class="far fa-star "></i><i class="far fa-star "></i>'; break;
		default : return '';
	}
}