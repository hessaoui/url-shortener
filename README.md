# URL Shortener

Ce depot contient ma proposition pour un exercice technique Laravel autour d'un raccourcisseur d'URL.

L'objectif etait de livrer une application simple a prendre en main, conforme au besoin, tout en gardant une structure lisible et des choix techniques proportionnes a la taille du sujet.

## Ce que fait l'application

L'application est organisee en deux blocs.

Le premier bloc est une zone d'administration accessible apres inscription / connexion. Un utilisateur peut y creer un lien court a partir d'une URL valide, retrouver ses liens dans un tableau pagine, modifier l'URL cible et supprimer un lien s'il ne souhaite plus le conserver.

Le second bloc est public. Lorsqu'un code court est appele sur l'endpoint de redirection, l'application retrouve le lien correspondant puis redirige vers l'URL originale.

## Fonctionnalites livrees

### Bloc administration

- inscription et connexion via Laravel Breeze
- creation d'un lien court avec generation automatique d'un code unique
- affichage pagine des liens du seul utilisateur connecte
- edition d'un lien existant
- suppression logique d'un lien
- copie en un clic du lien court depuis le dashboard

### Bloc redirection

- resolution d'un code via `/r/{code}`
- redirection vers l'URL d'origine
- incrementation du compteur de clics
- mise a jour de `last_used_at` a chaque utilisation
- affichage d'une page dediee si le lien a deja ete supprime

### Bonus pris en charge

- compteur d'utilisation
- copie en 1 clic du lien court
- page "lien non valable" pour les liens supprimes
- commande quotidienne de nettoyage des liens inactifs depuis plus de 3 mois

## Stack retenue

- PHP 8.2+
- Laravel 12
- Blade
- jQuery pour le comportement de copie dans le dashboard
- Tailwind CSS pour la couche UI existante de Breeze
- SQLite pour la base de donnees
- PHPUnit pour les tests fonctionnels

## Quelques choix de conception

Je suis volontairement reste sur une structure Laravel classique, avec un niveau d'abstraction modere.

- La validation des formulaires de creation et de mise a jour est isolee dans `StoreLinkRequest` et `UpdateLinkRequest`.
- L'autorisation d'acces aux liens d'un utilisateur est centralisee dans `LinkPolicy`.
- La redirection met a jour `clicks` et `last_used_at` en une seule operation afin d'eviter un suivi incoherent.
- Les liens sont supprimes avec `SoftDeletes`, ce qui permet d'afficher une page dediee au lieu d'une 404 brute.
- La creation des codes courts a ete durcie pour mieux resister a une collision d'unicite au moment de l'insertion.

Le domaine reste volontairement simple. Je n'ai donc pas ajoute de couche de service partout par principe, afin d'eviter de sur-concevoir un exercice de cette taille.

## Structure des elements principaux

- `LinkController` gere le CRUD des liens dans le dashboard
- `RedirectController` gere la resolution publique d'un code court
- `LinkPolicy` porte la logique d'ownership
- `PruneStaleLinks` gere le nettoyage quotidien des liens inactifs

## Routes utiles

- `/register` et `/login` pour l'authentification
- `/dashboard/links` pour le tableau de bord des liens
- `/dashboard/links/create` pour la creation
- `/dashboard/links/{link}/edit` pour l'edition
- `/r/{code}` pour la redirection publique

## Installation

### Prerequis

- PHP 8.2 ou plus
- Composer
- Node.js et npm
- extension SQLite activee

### Installation manuelle

```bash
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Installation rapide

Le projet expose aussi un script Composer pratique:

```bash
composer setup
```

## Lancer l'application

Pour un lancement simple:

```bash
php artisan serve
```

Puis ouvrir `http://127.0.0.1:8000`.

Pour un environnement de dev plus confortable:

```bash
composer dev
```

Cette commande lance le serveur Laravel, Vite, la queue et Pail pour disposer d'un environnement de travail complet.

## Lancer les tests

```bash
php artisan test
```

ou

```bash
composer test
```

Etat de la suite au moment de cette livraison:

- 76 tests passes
- 0 echec

## Nettoyage des liens inactifs

La commande suivante supprime logiquement les liens consideres comme stale:

```bash
php artisan links:prune-stale
```

La tache est planifiee quotidiennement dans l'application.

Regle appliquee actuellement:

- un lien est purge s'il n'a pas ete utilise depuis plus de 3 mois
- un lien jamais utilise est aussi purge si sa date de creation depasse 3 mois

Ce comportement est volontairement explicite ici, car la specification parle d'un lien "qui n'est plus utilise". J'ai choisi d'etendre cette logique aux liens jamais utilises au-dela de trois mois.

En production, il faut naturellement activer le scheduler Laravel cote systeme:

```bash
* * * * * php /chemin/vers/projet/artisan schedule:run >> /dev/null 2>&1
```

## Ce que couvrent les tests

La suite verifie notamment:

- les parcours d'authentification Breeze
- le CRUD des liens
- l'isolation des donnees entre utilisateurs
- les regles d'autorisation sur les ressources qui n'appartiennent pas a l'utilisateur connecte
- la validation des URLs en creation et en mise a jour
- la redirection publique
- le tracking des clics
- le comportement d'un lien supprime
- la commande de nettoyage des liens stale

## Pistes pour la suite

Je n'ai pas voulu charger cette livraison avec de l'infrastructure qui n'etait pas prioritaire pour le sujet. Les evolutions naturelles du projet seraient plutot les suivantes:

- ajouter une stack Docker pour standardiser l'environnement local
- integrer un framework E2E pour couvrir les parcours navigateur complets
- brancher une pipeline GitHub Actions pour lancer les tests, le build frontend et les futurs tests E2E
- eventuellement extraire la generation du code court dans un service dedie si le domaine grossit

## Git

Le travail a ete structure avec deux branches principales:

- `main` pour la base stable
- `dev` pour les evolutions incrementales

J'ai volontairement garde des commits thematiques et lisibles afin que l'historique montre clairement la progression du projet.
