# Lot 10 — Catalogue complet à 0 GNF pour la prévisualisation locale

## Objectif

Afficher **le maximum de références Dahua déjà connues dans la base locale**, y compris les brouillons, sans les publier commercialement, afin de vérifier les fiches produits, familles, recherche en direct, accueil, panier et formulaire de commande.

### Point essentiel : prix non destructifs

Dans ce mode, l'interface affiche **0 GNF partout** (accueil, catalogue, fiche, recherche instantanée, panier, récapitulatif de commande et liste admin), quel que soit le vrai prix en base.

**Aucune colonne `price_gnf`, `stock_qty`, `price_on_request` ou `is_active` des produits existants n'est modifiée.** L'affichage temporaire est effectué par `Product::display_price_gnf`.

Le mode n'est autorisé que si TOUTES les conditions sont respectées :
- `APP_ENV=local` (ou environnement de test explicitement activé) ;
- `APP_URL` utilise `localhost` ou `127.0.0.1` ;
- `SEO_INDEXING_ENABLED=false` ;
- `CATALOG_PREVIEW_ENABLED=true`.

En production, un drapeau laissé activé ne permet pas d'exposer des brouillons ou des prix fictifs sur un domaine public.

## Activation sur votre ordinateur

Appliquer les modifications de la branche `feat/local-preview-full-catalog` après intégration de la PR, puis dans le fichier `.env` :

```dotenv
APP_ENV=local
APP_URL=http://127.0.0.1:8000
SEO_INDEXING_ENABLED=false
CATALOG_PREVIEW_ENABLED=true
```

Exécuter ensuite :

```bash
php artisan optimize:clear
php artisan migrate
php artisan catalogue:preparer-apercu --dry-run
```

La commande `--dry-run` indique le nombre de fiches **déjà** dans `products` et le nombre de lignes d'import **admissibles** à convertir.

Pour matérialiser le maximum de lignes techniques déjà présentes dans les lots d'import locaux :

```bash
php artisan catalogue:preparer-apercu
```

Cette commande :
- Convertit les lignes techniques **en attente** ayant un modèle externe et une catégorie associée en fiches **brouillon**.
- Utilise le service d'approbation existant, qui n'importe pas les prix fournisseur, remises, quantités d'achat ou données financières.
- Respecte les doublons, les lignes invalides et les produits existants ; elle n'écrase aucune fiche déjà présente.
- Peut être relancée sans créer une deuxième fiche pour les mêmes modèles.
- N'active pas `is_active`, ne renseigne pas de fausses quantités de stock et ne remplace aucun prix commercial.

### Alternative sans terminal

Dans **Administration → Produits Dahua**, un petit panneau **Aperçu local actif · Prix affichés : 0 GNF** propose le bouton **Ajouter les références importées à l'aperçu**. Le bouton n'apparaît que sur un environnement local autorisé et s'il reste des lignes admissibles. Après l'import, les références peuvent être ouvertes directement depuis l'administration.

### Si le catalogue reste peu rempli

Il faut que les références soient **réellement présentes dans la base locale** :
- soit déjà sous forme de produits (même brouillons) ;
- soit dans les lots techniques visibles sous **Administration → Imports techniques**.

Si les lignes du BOQ ne sont encore présentes ni dans `products` ni dans `catalog_import_rows`, importer localement le BOQ original via l'écran protégé d'import, puis exécuter la commande. Ne pas ajouter le BOQ complet ni les coûts fournisseur à GitHub.

Le nombre exact de produits dépend du contenu de **votre** base locale. Cette PR ne prétend pas en avoir activé 176 sans accéder à votre base.

## Interfaces testables

- **Accueil :** toutes les catégories non vides, huit références en sélection même si aucune n'avait été mise à la une.
- **Catalogue :** jusqu'à 24 fiches par page en local, recherche, tri et filtre.
- **Fiche :** désignation réelle issue du catalogue et 0 GNF ; si la photo constructeur manque, une illustration thématique de la catégorie est identifiée comme telle.
- **Recherche instantanée :** brouillons locaux autorisés avec suggestions de SKU, photos et prix temporaire 0 GNF.
- **Panier :** il est possible d'ajouter les références même avec un stock à zéro afin de voir l'interface.
- **Commande :** formulaire et récapitulatif sont visibles mais **l'envoi est volontairement bloqué côté interface et serveur**. Aucune commande à 0 GNF n'est créée et aucun email de commande n'est envoyé.
- **Administration :** 0 GNF affiché dans le listing et accès direct à la fiche en aperçu.

Les véritables données commerciales sont préservées.

## Retour au fonctionnement commercial réel

Avant d'exposer le site sur un domaine public :

1. Définir `CATALOG_PREVIEW_ENABLED=false` et `APP_ENV=production`.
2. Mettre le `APP_URL` final en HTTPS.
3. Exécuter `php artisan optimize:clear`.
4. Revoir chaque fiche destinée à être **véritablement publiée**, avec prix final positif ou prix « sur devis », données techniques, photos autorisées, stock réel et validations requises.
5. Contrôler les droits, les articles, les emails, les conditions de livraison et le SEO, puis activer l'indexation seulement lorsque tout est prêt.

Les produits restés en brouillon ou dans une catégorie inactive ne seront **pas visibles en production**. Les pages de produits réels restent contrôlées par les exigences de `ProductPublicationGuard`.

## Tests

Tests de non-régression prévus : affichage 0 GNF sur catalogue/fiches/recherche, restauration du prix réel par simple désactivation, catégories et brouillons inactifs, import massif idempotent, sécurité admin, panier sans stock et blocage de la commande réelle.
