<?php

//connexion BDD
$host = 'mysql:host=localhost;dbname=projet_sallea';
$login = 'root';
$mdp = '';
$options = array(
         PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // pour la gestion des erreurs
         PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8' // pour utf8
         );

$pdo = new PDO($host, $login, $mdp, $options);


//Variables pour afficher des messages utilissateur
$msg = '';

//Ouverture de la session
session_start();

//pour incorporer les fonctions lorsque init est appelé sur les page
include 'fonction.inc.php';

//déclaration de constante:
//---------------------------
//constante contenant le chemin racine de notre projet
define('URL', 'http://localhost/php/projet_sallea/');
//constante contenant la racine serveur, disponible grace var_dump (super global S_SERVER)
define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT']);
//Constante chemin du dossier photo depuis la racine serveur
define('ROOT_URL', '/php/projet_sallea/');

