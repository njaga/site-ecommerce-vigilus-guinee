# Vigilus Guinée — Plateforme e-commerce Dahua

**Statut : cadrage stratégique et technique (aucune vente/paiement en production).**

Ce nouveau dépôt est dédié à la transformation de la vitrine `Site-vigilus-guin-e` en **plateforme e-commerce spécialisée en sécurité électronique**, orientée particuliers, installateurs, revendeurs et entreprises.

## Principes
- Catalogue riche avec références constructeur, caractéristiques vérifiables, catégories, filtres, recherche et comparaison.
- **Prix publics en GNF uniquement après validation commerciale**, stock et disponibilités synchronisés ; demandes de devis pour projets/volumes B2B.
- Panier, commandes, suivi, facturation, livraison et installation ; moyens de paiement locaux via prestataire agréé, une fois contractualisés/testés.
- SEO technique et local Guinée/Conakry, URLs produit indexables, migration 301 des anciennes URLs utiles.
- Positionnement sur la gamme Dahua ; ne pas publier « distributeur exclusif / officiel » sans justificatif validé et territoire précisément défini.
- Aucune fausse disponibilité, prix inventé, photo produit altérée ni témoignage factice.

## Stack de référence proposée
Laravel 12 + PHP 8.2+, Livewire, Tailwind CSS, MySQL/MariaDB ; back-office métier sécurisé et tests automatisés. La stack sera initialisée dans un lot dédié, après cadrage du catalogue et des flux.

## Documents
- [Stratégie produit et expérience d'achat](docs/01-strategie-commerce.md)
- [Catalogue et modèle de données](docs/02-catalogue-et-donnees.md)
- [SEO local et migration](docs/03-seo-local-migration.md)
- [Backlog, sécurité et critères de recette](docs/04-roadmap-et-recette.md)
- [Analyse concurrentielle ASLAN](docs/05-benchmark-aslan.md)

## Points à valider avant la production
1. Pays cible : les dépôts et le concurrent indiquent **Guinée (GN/GNF/Conakry)**, tandis que le brief mentionne une fois **Côte d'Ivoire**.
2. Autorisation de communiquer sur le statut de distributeur/représentant officiel Dahua et son périmètre géographique.
3. Liste fabricant/références effectivement commercialisées, prix publics, stock, garanties et photos autorisées.
4. Adresse commerciale guinéenne, téléphone local, domaine final, livraison/retours et comptes marchands Orange Money/MTN.

**Important :** le code de l'ancien dépôt contient des éléments résiduels de 2SMS Immobilier, à ne pas importer tels quels. Son contenu/SEO doit être audité pour produire un plan de redirection précis.
