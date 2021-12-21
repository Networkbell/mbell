# Read-Me (EN) : go to below
CMS for weather station (Davis-Weatherlink)
# Lisez-Moi (FR) :
CMS pour station météo (Davis-Weatherlink)

Pour de plus amples détails, aller sur le wiki de Mbell : https://github.com/Networkbell/mbell/wiki/MBell-fr


# Description (FR)

MBell est un système de gestion de contenu (CMS - content management system) pour propriétaire de stations météos disposant d'une API au format json, leur permettant de diffuser leurs données météorologiques en temps réel sur leur propre hébergement internet.

Exemple de station météo utilisant mbell à cette adresse : http://www.meteobell.com/mbell/index.php


# Prérequis (FR)

- Disposer d'une station météo de la marque Davis Instruments avec datalogger IP (weatherlink v1 et v2), connexion USB (weatherlink v2) ou Weatherlink Live.
- Disposer d'un hébergement internet
- Disposer d'1 base de données sur son hébergement internet

NOTE 1 : Si vous ne disposez ni d'hébergement internet et/ou ni de base de données, vous pouvez cependant installer Mbell sur votre ordinateur en local. Suivre dans ce cas la procédure d'installation avec localhost.

NOTE 2 : Une base de donnée est nécessaire depuis la version 2.0 de mbell, mais vous pouvez toutefois télécharger la version précédente, sans base de données, à cette adresse, avec mbell 1.6 : http://www.meteobell.com/mbell.php Attention cette version, bien que parfaitement opérationnelle, ne sera plus mis à jour et ne disposera donc pas des dernières évolutions depuis la version 2.0. Contrairement à la version 2.0, cette version 1.6, plus ancienne, nécessite une inscription obligatoire sur meteobell.com.


# Installation (FR)

1. Télécharger MBell sur Github (clic sur le bouton vert "Code" -> "Download ZIP")
2. Dézipper le fichier mbell-main.zip

NOTE 1 : le dossier "mbell-main" obtenus peut être renommé comme bon vous semble.

## Installation sur Internet (FR)

3. Avec un logiciel FTP (exemple FileZilla), installer le dossier "mbell-main" sur votre site internet
4. Aller à l'adresse url de votre site à l'emplacement de mbell : http://www.votre-site.com/mbell-main/
5. La procédure d'installation se lance automatiquement en vous guidant pas à pas

NOTE 1 : vous pouvez placer le dossier "mbell-main" à n'importe quel endroit de votre site.

NOTE 2 : les informations de connexions à votre base de données vous sont fournis par votre hébergeur. Il vous sera peut-être nécessaire de créer la base de données (avec PHPMyAdmin) si elle n'existe pas encore (clic sur "Nouvelle base de données"). Dans ce cas vous n'avez qu'à donner un nom de base de donnée et choisissez l'encodage "utf8_general_ci". Enfin préférez MariaDB que MySQL si possible.

## Installation en localhost (FR)

3. Avec un logiciel de serveur virtuel (Wamp ou EasyPHP) aller à l'emplacement du dossier "mbell-main" sur votre disque dur
4. La procédure d'installation se lance automatiquement en vous guidant pas à pas

NOTE 1 : La base de données en local doit avoir comme propriétés : Adresse = localhost / Utilisateur = root / Pas besoin de mot de passe / Comme pour l'installation Internet, le nom de la base de données doit être créé au préalable avec PHPMyAdmin (même procédure)


 # Versions

    2.0 (-0.55) - Initial Release
    2.1 (-0.58) - Addon : Weatherlink Live

Version actuelle = Publique 2.1 (Développement -0.58)


# Problèmes connus

 ## Général :

- bug introduit avec la version 2.1 : si vous ajoutez une station dans "Changer Station" un message d'erreur PHP a lieu, mais la station est bien ajoutée (sera réparé dans la prochaine version)

## Avec Weatherlink Live :

- Toutes les infos de stations ne sont pas proposés, je recherche des personnes possédant ce type de sondes auxiliaires, afin de réaliser des tests et les ajouter à Mbell :

1. Station Météo Auxiliaire de Température Air-Eau-Sol (6372)
2. Station Météo Auxiliaire de Température & Humidité de l'Air (6382)
3. Station Météo Auxiliaire Humectation du Feuillage / Température & Humidité du Sol (6345)

- L'API Weatherlink Live en gratuite est très basique et possède beaucoup moins d'informations proposées qu'auparavant, je ne peux donc afficher toutes les informations que Mbell propose avec les API précédentes, le template Mbell a été donc allégé en conséquence.
- Un bug provenant de weatherlink lui-même rend aléatoire la génération de leur API et provoque alors des bugs sur certaines stations. (une méthode pour contourner ce problème, sera effective dans la prochaine version) 
