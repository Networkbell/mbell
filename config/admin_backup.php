<?php

/**
 * La configuration de base de votre installation MBell
 *
 * Ce fichier admin_backup.php est utilisé par le script de création de admin.php pendant
 * le processus d’installation. 
 *  
 * Il est préférable de respecter la procédure d'installation de Mbell, mais si vous le souhaitez,
 * vous pouvez simplement renommer ce fichier en « admin.php » et remplir les
 * valeurs avec l'aide d'un éditeur de texte (notepad++ par exemple). 
 * Ce fichier renommé "admin.php" doit ensuite être placé dans le dossier /config de votre FTP
 * 
 * Si vous souhaitez réinstaller MBell, il vous suffit de supprimer le fichier admin.php
 * situé sur votre FTP. Mbell détectera automatiquement qu'il doit être réinstallé et générera
 * un nouveau fichier admin.php. 
 * 
 * ATTENTION : Ne supprimez jamais admin_backup.php pour autant
 * et ne modifiez jamais admin_backup.php en l'enregistrant sans changer son nom.
 * Si vous décidez de le modifier, vous devez impérativement l'enregistrer sous "admin.php"
 * 
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * Chemins Absolus
 *
 */

/** DEBUG
 * Pour activer les informations de débuguage de Mbell, changer "false" par "true"
 * $debug = false; --> $debug = true;
 */
$debug = false;



/** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. 
 * Si vous êtes dans "admin_backup" :  Remplacez le contenu entre guillemets avec des accolades '{...}' par les votres '...' 
 * puis sauvegardez en admin.php (gardez toujours un exemplaire de admin_backup non modifié ; la seule exception réside dans le réglage de MB_DEBUG)
 * 
 * Si vous êtes dans "admin.php", normalement ce contenu a été rempli par la procédure d'installation
 */

/** Adresse de l’hébergement MySQL. */
if (!defined('DB_HOST'))
  define('DB_HOST', '{bdd_localhost}');

/** Utilisateur de la base de données MySQL. */
if (!defined('DB_USER'))
  define('DB_USER', '{bdd_identifiant}');

/** Mot de passe de la base de données MySQL. */
if (!defined('DB_PASSWORD'))
  define('DB_PASSWORD', '{bdd_password}');

/** Nom de la base de données de Mbell. */
if (!defined('DB_NAME'))
  define('DB_NAME', '{bdd_mbell}');





/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
if (!defined('DB_CHARSET'))
  define('DB_CHARSET', 'utf8mb4');


/**
 * Clés uniques d’authentification et salage.
 * Remplacer 
 * 
 */
if (!defined('KEY_CRYPT'))
  define('KEY_CRYPT', '{key_crypt}');



/**#@-*/

/**
 * Préfixe de base de données pour les tables de Mbell.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */

$table_prefix = '{bdd_meta}';


/**
 * Pour les développeurs : le mode déboguage de MBell
 *
 * En passant la valeur $debug à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais. 
 * Attention cela peut vous aider à identifier les erreurs, 
 * mais peut empécher aussi le bon fonctionnement de Mbell dans certaines circonstances. 
 * N'oubliez pas de remettre sur false, une fois vos essais réalisés
 * C'est le seul réglage qui peut être changé et sauvegardé en tant que "admin_backup.php" afin de controller la procédure d'installation avant la création de "admin.php" (étape 3 de l'installation)
 *
 */
if (!defined('MB_DEBUG'))
  define('MB_DEBUG', $debug);


/**
 * Défini si Mbell a été installé
 *
 * Devient défini sur 'installed_true' à l'étape 8 de l'installation 
 * afin d'empécher l'accès aux liens relatifs à index.php?controller=install à quiconque
 * et donc empécher la réinitialisation de l'installation (voir dispatcher.php)
 * Pour relancer l'installation, changez 'installed_true' par '{installed_false}' (avec les accolades)
 * ATTENTION : relancer l'installation supprimera totalement aussi toutes les tables de votre base de données avec le prefixe de votre fichier admin.php
 * Pour ajouter une nouvelle station active, installez plutôt un nouveau dossier mbell et changez le prefixe des tables au moment de l'installation
 * Pour ajouter une nouvelle préférence de station, allez dans index.php?controller=pref rubrique Station
 *
 */
$installed = '{installed_false}';


/**
 * Différentes façon de retrouver l'URL 
 * et la Racine du site où Mbell est installé
 *
 */

$dir_temp = dirname(__FILE__);

/** 
 * $dir
 * URL avant le dossier /MBell. 
 */
$dir = substr($dir_temp, 0, -6);

/** 
 * DIRPATH
 * Chemin absolu vers le dossier de MBell.
 */
if (!defined('DIRPATH'))
  define('DIRPATH', $dir_temp . '/');

/** 
 * $root
 * URL Racine du site
 */
$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
