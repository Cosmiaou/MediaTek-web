# Mediatekformation
## Présentation
Ce site, développé avec Symfony 6.4, permet d'accéder aux vidéos d'auto-formation proposées par une chaîne de médiathèques et qui sont aussi accessibles sur YouTube.<br> 
J'ai été chargé de coder le back-office et de mettre en ligne le site, ainsi que de réaliser quelques modifications mineures.<br>
## Les différentes pages
Voici les pages ayant reçu des modifications, ou ayant été créé. Des modifications mineures ont été réalisées sur toutes les autres pages.<br>
### Page 1 : /playlists
Cette page présente les playlists.<br>
La partie haute est identique à la page d'accueil (bannière et menu).<br>
La partie centrale contient un tableau composé de 3 colonnes :<br>
Au niveau de la colonne "playlist", 2 boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">"). Il est aussi possible de filtrer les lignes en tapant un texte : seuls les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "filtrer" permet de retrouver la liste complète.<br>
Cela était déjà conçu, mais il manquait une option pour trier les playlists en fonction de leur nombre de formations, pour indiquer à l'utilisateur leur longueur. Désormais, cette option a été rajoutée.<br>
<img width="1306" height="603" alt="image" src="https://github.com/user-attachments/assets/794d7895-2819-4044-91d7-d81f4fc06bab" />

### Page 2 : /login
Le site dispose désormais d'un back-office. Pour y accéder, il faut ajouter /admin à la fin de l'URL. L'utilisateur est forcément redirigé vers /login.<br>
Il doit indiquer un login et un mot de passe. S'il est incorrect, le site lui redemande d'entrer ses informations en affichant un message d'erreur.<br>
En cas de succès, il est redirigé vers la page du back-office auquel il tentait d'accéder (probablement /admin).<br>
<img width="1607" height="274" alt="image" src="https://github.com/user-attachments/assets/9bd09bc2-c92a-4222-83fe-90a001da5f36" />

### Page 3 : gestion des playlists
Cette page n'est pas accessible par le menu mais uniquement en ajoutant /admin à la fin de l'URL. Il s'agit en fait de la "page d'accueil" du back-office, qui permet en réalité la gestion des formations.<br>
La page est identique à la page de gestion des Formations.<br>
La partie centrale contient un tableau composé de 5 colonnes :<br>
• La 1ère colonne ("formation") contient le titre de chaque formation.<br>
• La 2ème colonne ("playlist") contient le nom de la playlist dans laquelle chaque formation se trouve.<br>
• La 3ème colonne ("catégories") contient la ou les catégories concernées par chaque formation (langage…).<br>
• La 4ème colonne ("date") contient la date de parution de chaque formation.<br>
• LA 5ème contient deux boutons : "Editer", qui permet de modifier la formation, et "Supprimer", qui demande confirmation avant de supprimer l'élément.<br>
Au niveau des colonnes "formation", "playlist" et "date", 2 boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">").<br>
Au niveau des colonnes "formation" et "playlist", il est possible de filtrer les lignes en tapant un texte : seuls les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "filtrer" permet de retrouver la liste complète.<br>
Au niveau de la catégorie, la sélection d'une catégorie dans le combo permet d'afficher uniquement les formations qui ont cette catégorie. Le fait de sélectionner la ligne vide du combo permet d'afficher à nouveau toutes les formations.<br>
Par défaut la liste est triée sur la date par ordre décroissant (la formation la plus récente en premier).<br>
Au dessus, dans l'entête du tableau, on trouve un bouton "Ajouter" qui permet de voir le formulaire d'ajout d'une formation.<br>
Le fait de cliquer sur un bouton "Editer" ou "Ajouter" permet d'accéder à la quatrième page contenant le formulaire de modification<br>
<img width="1517" height="587" alt="image" src="https://github.com/user-attachments/assets/fc6a1fa3-51a2-4aa6-a47f-2310b2d36aee" />

### Page 4 : modifier ou ajouter une formation
Cette page est pratiquement identique entre la modification et l'ajout : les seules différences sont qu'elle est pré-remplie pour les modifications, et bien entendu, le résultat au clic sur le bouton.<br>
La page est divisée en deux selon l'axe vertical :<br>
•	A gauche, on doit saisir l'URL de la vidéo YouTube, et voir la vidéo déjà enregistrée si applicable ;<br>
•	A droite, on peut saisir les informations de la formation.<br>
Sur la colonne de droite, on doit saisir le titre, sélectionner une date, une playlist, et une ou plusieurs catégorie, et on peut saisir une description.<br>
Dans le cas d'une modification, tous les champs sont pré-remplis en fonction des informations enregistrée. Pour un ajout, seul la date est pré-remplie sur la date du jour.<br>
Le bouton "Enregistrer" permet de valider.<br>
<img width="1607" height="790" alt="image" src="https://github.com/user-attachments/assets/81477b3c-8a80-4ada-a70b-096fb7f1008c" />


### Page 5 : gestion des playlists
Cette page présente les playlists.<br>
Elle les affiche dans un tableau identique à celui du front-office. Elle offre également les mêmes tris.<br>
La seule différence est constituée par la présence d'un bouton "Ajouter" à droite des tris en entête du tableau, ainsi que des boutons "Editer" et "Supprimer" identique à /admin.<br>
<img width="1496" height="560" alt="image" src="https://github.com/user-attachments/assets/7c762cbe-db3d-4646-81ff-6e690597b5d9" />

### Page 6 : modifier ou ajouter une playlist
Cette page est pratiquement identique à celle de modification des formations.<br>
Cette page est pratiquement identique entre la modification et l'ajout : les seules différences sont qu'elle est pré-remplie pour les modifications, et bien entendu, le résultat au clic sur le bouton.<br>
La page est divisée en deux selon l'axe vertical :<br>
•	A gauche, on doit saisir les informations de la playlist.<br>
•	A droite, on peut voir la liste des formations de la playlists. L'utilisateur peut cliquer sur chaque formation, et est alors redirigé vers la page de modification de celle-ci.<br>
Sur la colonne de gauche, on doit saisir le titre et on peut saisir une description.<br>
Dans le cas d'une modification, tous les champs sont pré-remplis en fonction des informations enregistrée. Dans le cas d'un ajout, la colonne de droite est vide.<br>
Le bouton "Enregistrer" permet de valider.<br>
<img width="1607" height="688" alt="image" src="https://github.com/user-attachments/assets/cb633d8a-ba65-4b4f-803c-3dd854c73d86" />

### Page 7 : gestion des catégories
Cette page liste les différentes catégories des formations.<br>
En entête du tableau, un petit formulaire avec un bouton permet l'ajout d'une nouvelle catégorie. Il faut que le formulaire ait entre 1 et 50 caractères.<br>
Dans le tableau, les catégories sont listées. Un bouton supprimer permet, après confirmation, de supprimer une catégorie.<br>
Aucune catégorie ne peut être supprimée si elle est rattachée à des formations.<br>
<img width="1607" height="847" alt="Page de gestion des catégories. Permet l'ajout et la suppression." src="https://github.com/user-attachments/assets/ce998c82-c5f5-4296-b9e3-9d1a5fc05483" />


## La base de données
La base de données exploitée par le site est au format MySQL.<br>
La seule modification consiste en l'ajout d'un champ pour gêrer les utilisateurs et leurs informations, notamment leur mot de passe, qui est hashé par sécurité.<br>

## Test de l'application en ligne
- Accéder à l'URL mediatekformation.autogend.fr.<br>
- Pour accéder au back-office, accéder à mediatekformation.autogend.fr/admin, puis indiquer les bons identifiants de connexion.<br>

## Test de l'application en local
- Vérifier que Composer, Git et Wampserver (ou équivalent) sont installés sur l'ordinateur.<br>
- Télécharger le code et le dézipper dans www de Wampserver (ou dossier équivalent) puis renommer le dossier en "mediatekformation".<br>
- Ouvrir une fenêtre de commandes en mode admin, se positionner dans le dossier du projet et taper "composer install" pour reconstituer le dossier vendor.<br>
- Dans phpMyAdmin, se connecter à MySQL en root sans mot de passe et créer la BDD 'mediatekformation'.<br>
- Récupérer le fichier mediatekformation.sql en racine du projet et l'utiliser pour remplir la BDD (si vous voulez mettre un login/pwd d'accès, il faut créer un utilisateur, lui donner les droits sur la BDD et il faut le préciser dans le fichier ".env" en racine du projet).<br>
- De préférence, ouvrir l'application dans un IDE professionnel. L'adresse pour la lancer est : http://localhost/mediatekformation/public/index.php<br>

## Documentation technique
La documentation technique peut se trouver sur l'URL : https://cosmiaou.github.io/Mediatek-doc/<br>
