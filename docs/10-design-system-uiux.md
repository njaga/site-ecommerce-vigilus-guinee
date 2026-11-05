# Lot 7 — Refonte UI/UX Vigilus Guinée · Dahua

## Objectifs

Un e-commerce professionnel de distribution d'équipements, pas une page promotionnelle générique :
navigation claire, recherche visible, consultation des références, devis B2B et commande sans paiement en ligne.

## Travail livré

### Identité et design system
- Tokens : texte `#162733`, rouge de commande `#bc2741`, bleu institutionnel `#143e55`, fonds froids sobres `#f5f7f8`.
- Typographie : Plus Jakarta Sans pour les titres, DM Sans pour le texte, hiérarchie plus rationnelle.
- Composants : boutons, recherche, navigation, cartes produits, catégories, panneaux de décision, icônes SVG légères.
- Moins de boîtes décoratives et de coins excessivement arrondis ; composants ajustés au contenu, pas de pseudo-preuves ni de statistiques inventées.

### Boutique
- En-tête avec marque, recherche Dahua, panier et menu mobile accessible au clavier avec `<details>`.
- Accueil : hero éditorial, photo reliée à une vraie référence vedette si disponible, catégories réelles, produits sélectionnés et circuit d'achat clair.
- Catégories / catalogue : recherche, tri, filtres repliables sur mobile, états vides utiles, photo produit et statut de disponibilité.
- Fiche produit : galerie interactive par boutons/miniatures (`aria-pressed`), image agrandie, quantité et actions devis/panier distinctes, tableau de caractéristiques et produits associés.
- Panier et commande : suppression explicite d'une ligne, libellés clairs, aucun paiement en ligne.
- Devis, commande et suivi : cohérence typographique, formulaires accessibles, affichage responsive.
- SEO : URLs, canoniques, BreadcrumbList et Product JSON-LD préservés. Les offres de prix sont volontairement exclues pour les produits sur devis, même en présence d'une donnée historique incohérente.

### Administration
- Rail latéral sur ordinateur, navigation horizontale scrollable sur mobile, statut actif lisible par ligne/bordure (pas de pilules génériques).
- Groupes : produits, ventes, préparation du site. Gestion de l'import, devis, commandes et redirections inchangée fonctionnellement.

## Visuels et droits de marque

**Important :** aucun logo de Vigilus n'a été redessiné, déformé ou imaginé. Le site en **préproduction uniquement** référence l'image `logo-vigilus-transparent.png` disponible dans le dépôt contrôlé `njaga/site-vigilus-securite`, et une illustration existante `Videosurveillance.jpg` du même dépôt lorsque le catalogue ne fournit pas encore de produit vedette photographié.

Ces images sont actuellement récupérées par URL GitHub raw **en attente de leur empaquetage local**. Leur fidélité visuelle et la variante de logo officielle spécifiquement validée pour la Guinée exigent une confirmation de la marque. Avant de passer en production :

1. Obtenir le fichier **officiel approuvé** du logo (PNG transparent de bonne définition ou SVG) et les visuels autorisés de la gamme Dahua.
2. Copier les fichiers autorisés dans `public/images/brand/` (sans modifier les proportions).
3. Configurer `SITE_BRAND_LOGO_URL=/images/brand/nom-fichier.png` et, si besoin, `SITE_HERO_ILLUSTRATION_URL=/images/brand/photo.jpg`.
4. Exécuter `php artisan commerce:preflight --strict` : les contrôles du logo et du visuel de couverture doivent passer.
5. Vérifier l'apparence réelle avec les variantes de logo approuvées sur fond blanc et sur fond sombre.

Aucune photo d'équipement Dahua spécifique n'est inventée ou associée à une référence incorrecte. Seules les photographies liées aux fiches réellement publiées sont présentées dans les cartes et les galeries.

## Recette visuelle manuelle à compléter

- [ ] iPhone compact 375–390 px : header, menu, recherche et panier sans chevauchement.
- [ ] Android 360–412 px : grilles produits, formulaire devis, touch targets et filtres.
- [ ] Tablette 768–1024 px : header, hero et cartes ; sidebar admin correctement repliée.
- [ ] Desktop 1280, 1440 et 1920 px : conteneurs, rythme, photos et titres.
- [ ] Contraste, navigation clavier, focus, images alternatives et parcours lecteur d'écran.
- [ ] Galerie : clic, touches Tab/Entrée/Espace, changement d'images et image agrandie.
- [ ] Images officielles sur produits, absence d'étirement et performance sur connexion mobile.
- [ ] Tests du panier, quantités, suppression, devis et suivi de demande.
- [ ] Vérifier que le site affiche uniquement du matériel et n'annonce pas d'installation en Guinée.
- [ ] Tester sur un déploiement de préproduction réel (pas seulement via PHPUnit/CI).
- [ ] Captures d'écran comparatives approuvées par la communication de Vigilus.

## Interdictions inchangées

Aucune donnée confidentielle du BOQ fournisseur en dépôt. Aucun paiement en ligne. Aucune publication directe. Les PR sont empilées : #6 → #7 → #8 → #10 → #12 → #14 → ce lot.
