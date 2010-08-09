<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Translation
*
* French
* 
* @version 1.0
* @author Eloisa Astudillo Fernandez
*/

// FR-FR

$words = array 
(
	// BASIC INTERFACE PARTS
	"indexhibit" => "Indexhibit",
	"preferences" => "Préférences",
	"help" => "Aide",
	"logout" => "Se déconnecter",
	
	// BASIC MAIN NAV PARTS
	"content" => "Contenu",
	"admin" => "Admin",
	"pages" => "Pages",
	"section" => "Section",
	"exhibits" => "Expositions",
	"stats" => "Statistiques",
	"settings" => "Réglages",
	"system" => "Système",
	"user" => "Utilisateur",
	
	// error messages
	"login err" => "Votre mot de passe est incorrect. Veuillez réessayer.",
	"router err 1" => "Ce module/page n'existe pas.",
	"class not found" => "Fichier introuvable",
	"database is unavailable" => "La base de données n'est pas disponible.",
	"error finding settings" => "La base de données n'est pas disponible.",
	"no menu created" => "La base de données n'est pas disponible.",
	"no results" => "La base de données n'est pas disponible.",
		
	// Location descriptors
	"main" => "Principal Page",
	"edit" => "Éditer",
	"preview" => "Prévisualiser",
	"search" => "Rechercher",
	"new" => "Nouveau",
	
	// some tabs
	"text" => "Texte",
	"images" => "Images",
	
	// meed tp tranlsate the default sections
	"project" => "project",
	"on-going" => "ongoing",
	
	// generic forms parts & labels
	"page title" => "Nom de l'exposition",//?
	"add page" => "Ajouter une exposition",
	"submit" => "Soumission",
	"update" => "Mise à jour",
	"required" => "Obligatoire",
	"optional" => "Optionnel",
	"hidden" => "Caché",
	"delete" => "Effacer",
	"publish" => "Publier",
	"unpublish" => "Dépublier",
	"choose file" => "Choisir fichier",
	
	"exhibition name" => "Nom de l'exposition",//?
	"advanced mode" => "Mode avancé",
	"theme" => "Thème",
	"api key" => "Clé API",
	"image max" => "Taille d'image maxi",
	"thumb max" => "Taille d'Thumbnail maxi",
	"image quality" => "Qualité d'image",
	"freelancer" => "Disponibilité freelance",
	"pre nav text" => "Texte Pre-Nav",
	"post nav text" => "Texte Post-Nav",
	"html allowed" => "(HTML permis)",
	"update order" => "Mettre à jour l'ordre",
	"view" => "Voir",
	"no images" => "Pas d'images",
	"add images" => "Ajouter Images",
	"image title" => "Titre de l'image",
	"image caption" => "Légende de l'image",
	"attach more files" => "Attacher d'autres fichiers",
	"page process" => "Traiter la Page",
	"page hide" => "Cacher la page",
	"background image" => "Image de fond",
	"background color" => "Couleur de fond",
	"edit color" => "Cliquer la couleur pour éditer la sélection.",
	"uploaded" => "Téléchargé",
	"updated" => "Mis à jour",
	"width" => "Largeur",
	"height" => "Hauteur",
	"kb" => "KB",
	"saving" => "Sauvegarde...",
	
	// editor buttons & such
	"bold" => "Gras",
	"italic" => "Italique",
	"underline" => "Souligner",
	"links manager" => "Gestion de liens",
	"files manager" => "Gestion de fichiers",
	"save preview" => "Sauver /Prévisualiser",
	"upload" => "Télécharger",
	"make selection" => "Faire une sélection",
	"on" => "On",
	"off" => "Off",
	
	// popup editor parts
	"create link" => "Créer un lien",
	"hyperlink" => "Hyperlien",
	"urlemail" => "URL / Email",
	"none found" => "Aucun trouvé",
	"allowed filetypes" => "Types de fichier permis",
	"upload files" => "Téléchargement de fichiers",
	"attach more" => "Attacher d'autres fichiers",
	"title" => "Titre",
	"edit file info" => "Éditer l'information du fichier",
	"description" => "Description",
	"if applicable" => "(si d'application)",
	
	// statistics related things
	"referrers" => "Affluents",
	"page visits" => "Visites de la page",
	
	"total" => "Total",
	"unique" => "Unique",
	"refers" => "Affluents",
	
	"since" => "Depuis",
	"ip" => "IP",
	"country" => "Pays",
	"date" => "Date",
	"keyword" => "Keyword",
	"total pages" => "total de pages",
	"next" => "Suivant",
	"previous" => "Précédent",
	"visits" => "Visites",
	"page" => "Page",
	
	"this week" => "Cette semaine",
	"today" => "Aujourd'hui",
	"yesterday" => "Hier",
	"this month" => "Ce mois",
	"last week" => "La semaine passée",
	"year" => "Année",
	"last month" => "Le mois passé",
	"top 10 referrers" => "Top 10 Affluents",
	"top 10 keywords" => "Top 10 Keywords",
	"top 10 countries" => "Top 10 Pays",
	"past 30" => "derniers 30 jours",
	
	"2 weeks ago" => "Il y a 2 semaines",
	"3 weeks ago" => "Il y a 3 semaines",
	"4 weeks ago" => "Il y a 4 semaines",
	"2 days ago" => "Il y a 2 jours",
	"3 days ago" => "Il y a 3 jours",
	"4 days ago" => "Il y a 4 jours",
	"5 days ago" => "Il y a 5 jours",
	"6 days ago" => "Il y a 6 jours",
	"2 months ago" => "Il y a 2 mois",
	"3 months ago" => "Il y a 3 mois",
	"4 months ago" => "Il y a 4 mois",
	"5 months ago" => "Il y a 5 mois",
	"6 months ago" => "Il y a 6 mois",
	"7 months ago" => "Il y a 7 mois",
	"8 months ago" => "Il y a 8 mois",
	"9 months ago" => "Il y a 9 mois",
	"10 months ago" => "Il y a 10 mois",
	"11 months ago" => "Il y a 11 mois",
	
	// system strings
	"name" => "Prénom",
	"last name" => "Nom",
	"email" => "Email",
	"login" => "Login",
	"password" => "Mot de passe",
	"confirm password" => "Confirmation du mot de passe",
	"number chars" => "6-12 caractères",
	"if change" => "si nécessaire",
	"time now" => "Quelle heure est-il maintenant?",
	"time format" => "Format de l'heure",
	"your language" => "Langue",
	
	// installation
	"htaccess ok" => "Le fichier .htaccess est prêt...",
	"htaccess not ok" => "Régler 'MODREWRITE' sur 'false' dans config.php...",
	"files ok" => "Le directoire /files est ok ...",
	"files not ok" => "Les permissions du directoire /files sont incorrectes ...",
	"filesgimgs ok" => "Le directoire /files/gimgs folder est ok...",
	"filesgimgs not ok" => "Les permissions du directoire /files/gimgs folder sont incorrectes...",
	"try db setup now" => "Maintenant, nous créons la base de données.",
	"continue" => "Continuer",
	"please correct errors" => "Corrigez les erreurs ci-dessus, svp.",
	"refresh page" => "Maintenant, régénérer cette page.",
	"goto forum" => "Pour plus d'infos, allez au <a href='http://www.indexhibit.org/forum/'>forum d'aide</a>.",
	"edit config" => "Vous devez éditer le fichier config.php avec les réglages appropriés de la base de donnés.",
	"refresh this page" => "Régénérez la page après avoir fini cette étape.",
	"contact webhost" => "Si vous ne connaissez pas ceux-ci, contactez votre fournisseur d'espace web.",
	"database is ready" => "La base de données est prête.",
	"tried installing" => "Nous avons essayé d'installer la base de données.",
	"cannot install" => "Nous ne pouvons pas nous connecter ou installer la base de données.",
	"check config" => "Veuillez vérifier vos réglages config.",
	"default login" => "le login / mot de passe par défaut est index1 / exhibit.",
	"change settings" => "Changez ceux-ci et les réglages de votre site quand vous serez connectés",
	"lets login" => "Connectez-vous",
	"freak out" => "Il y a une erreur grossière et terrible, aillez peur!",
	
	// javascript confirm pops
	"are you sure" => "Êtes vous certain?",
	
	// additions 17.03.2007
	'change password' => 'Changer le mot de passe',
	'project year' => 'Année du projet',
	'report' => 'Diffuser',
	'email address' => 'Courriel',
	'below required' => 'Champ obligatoire pour la diffusion en indexihibit',
	'from registration' => 'Du registre Indexhibit',
	'register at' => 'Se registrer à',
	'background tiling' => 'Image de fond en mosaïque',
	'page process' => 'Procésser le texte',
	'hide page' => 'Cacher la page',
	'allowed formats' => 'seulement jpg, png et gif.',
	'filetypes' => 'Types de fichiers',
	'updating' => 'Mise à jour...',

	// additions 18.03.2007
	'max file size' => 'Taille max. du fichier',
	'exhibition format' => 'Format de exposition',
	'view full size' => 'Taille réelle',
	'cancel' => 'Annuler',
	'view site' => 'Voir ta page',
	'store' => 'Magasin',
	
	// additions 19.03.2007
	'config ok' => "le dossier /ndxz-studio/config est modifiable...",
	'config not ok' => "le dossier /ndxz-studio/config n'est pas modifiable...",
	'database server' => "Serveur de la base de données",
	'database username' => "Nom d'utilisateur de la base de données",
	'database name' => "Nom de la base de données",
	'database password' => "Mot de passe de la base de données",
	
	// additions 10.04.2007
	'create new section' => "Créer une nouvelle section",
	'section name' => "Nom de la section", 
	'folder name' => "Nom du dossier",
	'chronological' => "Chronologique",
	'sectional' => "Par section",
	'use editor' => "Editeur WYSIWYG", 
	'organize' => "Organiser", 
	'sections' => "Sections", 
	'path' => "Chemin", 
	'section order' => "Ordre des sections",
	'reporting' => "Rapport",
	'sure delete section' => "Sûr? Cette action effacera toutes les pages contenues dans la section",
	'projects section' => "Section pour projets", 
	'about this site' => "À propos de ce site",
	'additional options' => "Configurations additionnelles",
	'add section' => "Ajouter une section",
	
	// additions 21.04.2007
	'section display' => "Display Section Title",
	
	"the_end" => "FIN"
	//"" => "",
);


?>