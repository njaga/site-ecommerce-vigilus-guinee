# Vigilus Guinée — e-commerce sécurité électronique

Nouveau site commercial **Vigilus Guinée** avec catalogue Dahua, gestion de produits, prix en GNF, stocks, pages SEO et demandes de devis. Le marché Guinée/Conakry reste à confirmer en raison d'une mention de Côte d'Ivoire dans le brief initial.

## État de cette branche

**Premier lot P0 fonctionnel côté code** :
- Laravel 12 / Blade / Tailwind CSS 4 (interface mobile-first).
- Accueil commercial, catalogue, catégorie, recherche, filtres, fiches techniques et SEO produit.
- Administration protégée avec gestion des produits, photos, prix, inventaire, catégories et lecture des demandes de devis.
- Requêtes commerciales sauvegardées avec consentement, validations, contrôle de débit et anti-spam simple.
- Sitemap/robots dynamiques, métadonnées, canoniques et balisage Product/Offer lorsque le prix est connu.
- Tests automatisés et CI GitHub.

**Pas encore implémenté dans cette branche :** panier, checkout, paiement, livraison, compte client, factures, SAV, retours et migration complète des anciennes URLs. Ne pas déployer en boutique publique transactionnelle à ce stade.

**Aucune référence, image officielle, garantie, stock ou prix réel Dahua n'est inventé.** Seules les catégories sont amorcées. Le front présente un état vide utile avant import du catalogue.

## Installation locale

Prérequis : PHP 8.2+ avec extensions Laravel (mbstring, pdo_sqlite ou pdo_mysql, xml, curl, fileinfo…), Composer 2, Node 20+, npm. MySQL/MariaDB recommandé en production.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# SQLite pour démarrage local :
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run dev
# Dans un autre terminal :
php artisan serve
```

Pour créer un administrateur, renseigner dans `.env` **ADMIN_EMAIL** et **ADMIN_PASSWORD** (14 caractères minimum), puis relancer `php artisan db:seed`. **Ne jamais commiter le fichier .env ni partager ces identifiants.** Les mots de passe sont hashés via le cast Laravel. Retirer les secrets d'administration des scripts de déploiement et utiliser un gestionnaire de secrets.

Pour tester :

```bash
php artisan test
npm run build
```

Les photos du back-office sont stockées sur le disque `public` : lancer `php artisan storage:link`. Fournir des photographies fabricant autorisées, non déformées.

## Variables métier

`SITE_COUNTRY=GN`, `SITE_CURRENCY=GNF`, `SITE_CITY=Conakry` (configuration provisoire), `SITE_PHONE`, `SITE_EMAIL`, `SITE_ADDRESS` et `APP_URL`. Ne pas substituer des numéros sénégalais/ivoiriens aux coordonnées de Guinée.

## Livraison et sécurité

- Les prix affichés doivent être validés par l'équipe commerciale.
- Le champ stock est manuel pour ce lot ; il n'est **pas synchronisé en temps réel**.
- Le site n'accepte **aucun paiement** : le bouton commercial ouvre une demande de devis.
- Validation légale des CGV, garantie, données personnelles et statut Dahua requise avant toute vente.
- **Ne pas communiquer l'exclusivité Dahua sans justificatif contractuel du pays couvert.**
- Préserver le SEO historique via mapping précis des URLs et redirections 301 avant bascule du domaine.

## Suivi

- Cadrage détaillé : PR [#1](https://github.com/njaga/site-ecommerce-vigilus-guinee/pull/1).
- Tickets : [#2 catalogue](https://github.com/njaga/site-ecommerce-vigilus-guinee/issues/2), [#3 storefront](https://github.com/njaga/site-ecommerce-vigilus-guinee/issues/3), [#4 paiement/panier](https://github.com/njaga/site-ecommerce-vigilus-guinee/issues/4), [#5 migration SEO](https://github.com/njaga/site-ecommerce-vigilus-guinee/issues/5).
