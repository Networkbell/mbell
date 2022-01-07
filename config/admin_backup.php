<?php
/**
 * La configuration de base de votre installation MBell
 *
 * Le fichier admin_backup.php est utilisé par le script de création de admin.php pendant
 * le processus d’installation. 
 *  
 * Il est préférable de respecter la procédure d'installation de Mbell, mais si vous le souhaitez,
 * vous pouvez simplement renommer ce fichier en « admin.php » et remplir les
 * valeurs avec l'aide d'un éditeur de texte (notepad++ par exemple). 
 * Ce fichier renommé "admin.php" doit ensuite être placé dans le dossier /config de votre FTP
 * 
 * Si vous souhaitez réinstaller MBell, vous pouvez supprimer le fichier admin.php situé sur votre FTP. 
 * Mbell détectera automatiquement qu'il doit être réinstallé et générera un nouveau fichier admin.php.
 *  
 * __________
 * ATTENTION : Ne supprimez jamais admin_backup.php pour autant
 * et ne modifiez jamais admin_backup.php en l'enregistrant sans changer son nom.
 * Si vous décidez de le modifier, vous devez impérativement l'enregistrer sous "admin.php"
 * 
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Débugage
 * Réglages MySQL
 * Clés secrètes (pour MdP)
 * Préfixe de table
 * Version Installée
 * Installation Statut
 * Pour les Développeurs
 * Chemins Absolus
 * 
 */

/** DEBUG
 * Pour activer les informations de débuguage de Mbell, changer "false" par "true"
 * $debug = false; --> $debug = true;
 */
$debug = false;





/** Réglages MySQL - Votre hébergeur doit vous fournir ces informations (sinon en local)
 * Si vous êtes dans "admin_backup" :  Remplacez le contenu entre guillemets avec des accolades '{...}' par les votres '...' 
 * puis sauvegardez en admin.php (gardez toujours un exemplaire de admin_backup non modifié ; la seule exception réside dans le réglage de MB_DEBUG)
 * 
 * Si vous êtes dans "admin.php", normalement ce contenu a été rempli par la procédure d'installation
 */

/** Adresse de l’hébergement MySQL. (En Local = localhost) */
if (!defined('DB_HOST'))
  define('DB_HOST', '{bdd_localhost}');

/** Utilisateur de la base de données MySQL. (En Local = root)*/
if (!defined('DB_USER'))
  define('DB_USER', '{bdd_identifiant}');

/** Mot de passe de la base de données MySQL. (non nécéssaire en Local)*/
if (!defined('DB_PASSWORD'))
  define('DB_PASSWORD', '{bdd_password}');

/** Nom de la base de données de Mbell. */
if (!defined('DB_NAME'))
  define('DB_NAME', '{bdd_mbell}');


/** Jeu de caractères à utiliser par la base de données lors de la création des tables. (utilisez par ex utf8_general_ci dans PHPMyAdmin) */
if (!defined('DB_CHARSET'))
  define('DB_CHARSET', 'utf8mb4');


/**
 * Clés uniques d’authentification et salage.
 * La clef est générée aléatoirement à l'installation
 */
if (!defined('KEY_CRYPT'))
  define('KEY_CRYPT', '{key_crypt}');



/**
 * Préfixe de base de données pour les tables de Mbell.
 *
 * Vous pouvez installer plusieurs Mbell sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */

$table_prefix = '{bdd_meta}';



/** Version Installé
 * 
 * Sert lors de l'installation pour comparer votre version actuelle et celle que vous essayez d'installer si vous avez écrasé les nouveaux fichiers sans supprimer le fichier "admin.php"
 * 
 * 1. Si c'est la première fois que vous installez mbell, l'installation se poursuit normalement (pas de version à comparer)
 * 2. Si vous tentez d'installer une version plus ancienne, un message d'erreur vous en informe et lance une réinstallation complète
 * 3. Si vous réinstallez la même version, vous devez soit supprimer le fichier admin.php, soit mettre 'no' à $installed dans le fichier admin.php pour relancer la procédure d'installation,
 * Mbell supprimera alors toutes les tables de la bdd déjà installé (sauf mb_data) et les réinstallera automatiquement. La table md_data est cependant gardé intacte
 * 4. Si vous installez une version plus récente, il s'agit d'une mise à jour, si des tables existent dans la bdd, elles ne sont pas supprimés, mais seront mises à jour selon les besoins de la version que vous installez
 */
$version_installed = '{version}';



/**
 * INSTALLED
 * Défini si Mbell a été installé
 *
 * Devient défini sur $installed = 'yes';  à l'étape 8 de l'installation 
 * afin d'empécher l'accès aux liens relatifs à index.php?controller=install à quiconque
 * et donc empécher la réinitialisation de l'installation par une personne extérieure
 * 
 * Pour relancer l'installation, changez dans admin.php :
 * $installed = 'yes'; 
 * par 
 * $installed = 'no';
 * 
 * ATTENTION : relancer l'installation supprimera totalement aussi toutes les tables (sauf mb_data) de votre base de données avec le prefixe de votre fichier admin.php 
 * Pour ajouter une nouvelle station active, installez plutôt un nouveau dossier mbell et changez le prefixe des tables au moment de l'installation
 * Pour ajouter une nouvelle préférence de station, allez dans les Préférences rubrique "Changer de Station"
 * 
 * Si vous souhaitez simplement mettre à jour mbell dans une version plus récente, il est préférable d'écraser simplement les nouveaux fichiers sans toucher à ce paramètre, mbell détectera alors automatiquement qu'il doit être mis à jour
 * 
 * Si vous rencontrez un problème lors de la phase d'installation, retournez simplement à la phase 2 d'installation : index.php?controller=install&action=step2
 * cela réinitialisera la phase d'installation depuis le début en supprimant la bdd que vous venez de créer, ou en supprimant l'ancienne bdd si vous n'avez pas changez de prefixe de table depuis. 
 * Revenir jusqu'à l'étape 2 en arrière dans l'étape d'installation, si $installed est sur 'no', revient donc à supprimer admin.php et à supprimer les tables de la bdd (sauf mb_data) car mbell détectera alors que vous tentez de le réinstaller avec la même version
 *
 */
$installed = '{no}';




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
 * Différentes façon de retrouver l'URL 
 * et la Racine du site où Mbell est installé
 *
 */

$dir_temp = dirname(__FILE__);

/** 
 * $dir
 * URL avant le dossier /config = mbell/ 
 */
$dir = substr($dir_temp, 0, -7);

/** 
 * DIRPATH
 * Chemin absolu vers le dossier config/
 */
if (!defined('DIRPATH'))
  define('DIRPATH', $dir_temp . '/');

/** 
 * $root
 * URL Racine du site
 */
$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';



