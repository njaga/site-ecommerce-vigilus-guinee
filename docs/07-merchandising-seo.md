# Lot 4 — Merchandising, qualité éditoriale et SEO catalogue

## Philosophie

L'import BOQ (#8) fournit des **brouillons techniques**, pas des articles immédiatement publiables. Les coûts fournisseurs et les quantités du document confidentiel ne sont **ni importés en prix de vente, ni diffusés**.

## Développé

- Fiches produit françaises administrables avec champs commerciaux, caractéristiques structurées, prix public GNF **ou** mode explicite « sur devis ».
- Contrôle bloquant de la publication : catégorie active, texte court ≥30 caractères, description ≥100 caractères, au moins 2 caractéristiques vérifiées, photo existante, titre SEO 20–75 caractères, méta-description 70–170 caractères, validation technique/commerciale et droits média.
- Statuts brouillon/publié avec `published_at` et dates de validation.
- Interface admin de filtres brouillons, fiches sans prix et recherche par référence.
- Bibliothèque photo : légendes alternatives, ordre d'affichage et suppression contrôlée ; impossible de supprimer la seule photo d'une fiche publiée.
- Catégories : champs d'introduction FR, titre et description SEO, URL slug immuable depuis l'interface d'édition.
- Page service « installation de vidéosurveillance à Conakry » avec CTA devis et liens vers le catalogue ; aucun résultat projet/client inventé.
- SEO : liens internes, BreadcrumbList JSON-LD, toutes les images déclarées dans Product JSON-LD, canoniques de pagination et exclusion sitemap/noindex des catégories vides.

## Parcours de mise en ligne

1. Importer le BOQ depuis l'admin (jamais via GitHub).
2. Vérifier les références, catégories et nomenclature des fiches.
3. Rédiger des contenus français originaux, utiles et techniquement exacts.
4. Ajouter des visuels officiels autorisés, puis vérifier droits, alt et qualité visuelle.
5. Fixer un vrai prix public **ou** accepter la vente sur devis pour les cas particuliers.
6. Contrôler le stock **physiquement disponible** ; le volume du BOQ n'est pas un stock.
7. Compléter les champs SEO et valider les trois attestations.
8. Cocher « Publier » : une vérification serveur empêche les fiches incomplètes de devenir visibles.
9. Tester la navigation mobile, l'accessibilité et les demandes de commande. **Aucun paiement en ligne.**

## Points restant à traiter

- Vérifier les contrats/licences de redistribution Dahua et les droits photos.
- Créer manuellement un inventaire des références réelles à publier : aucun prix/stock constructeur ne doit être inventé.
- SEO avancé : inventaire des URLs du site historique, mapping 301, Search Console, maillage éditorial, performance et mesure Core Web Vitals.
- Ajouter un flux de validation à plusieurs rôles si l'organisation nécessite séparation achat/commercial/communication.
- Mettre en place des tests visuels et une vraie recette métier avant fusion et déploiement.

**Les signatures de validation en back-office attestent une revue humaine ; elles ne remplacent pas un contrôle contractuel.** Cette PR demeure en brouillon jusqu'à validation par les équipes.
