# 04 — Roadmap e-commerce, qualité & recette

## Stack cible et responsabilités

- Laravel 12, Blade/Livewire, Tailwind CSS ; base MySQL/MariaDB ; workers pour mails, imports et notifications.
- Administration métier sécurisée : produits, stocks, commandes, paiements, livraisons, SAV, devis, pages/SEO, redirections 301, paramètres locaux et utilisateurs.
- Système de fichiers pour images/docs constructeur et journal des téléchargements sensibles ; éviter d'exposer les documents internes.
- Front optimisé smartphone et SSR pour SEO ; site transactionnel non dépendant de l'exécution de JavaScript.
- Environnements DEV/STAGING/PROD distincts ; sauvegardes, surveillance erreurs et déploiement réversible.

## Priorités

### P0 — Socle, catalogue, acquisition (avant annonce publique)

- [ ] Confirmer territoire : Guinée vs Côte d'Ivoire ; devise GN/GNF vs CI/XOF.
- [ ] Vérifier statut officiel Dahua, droit d'usage logos/images, territoire et garanties.
- [ ] Configurer projet Laravel propre, base de données, authentification et RBAC admin.
- [ ] Définir identité, composants et gabarits responsive sans casser l'image Vigilus.
- [ ] Modèles catégories/produits/médias/stock ; import CSV contrôlé ; SEO par fiche.
- [ ] Front : accueil commerce, catalogue paginé, recherche, filtres, pages produit, pages solution.
- [ ] Administrateur : CRUD produits/catégories/prix/photos/SKU, activation et rupture.
- [ ] Fiches : prix réels uniquement ; bouton devis si prix indisponible.
- [ ] Contenu local original, contact vérifié, suivi conversion, sitemap, canonical, robots.
- [ ] Inventaire des anciennes URLs, mapping 301 et tests de non-régression SEO.
- [ ] Mentions légales, confidentialité, CGV, garanties et politique de retour approuvées.

### P1 — Vente réelle et opérations

- [ ] Panier en session durable et validation des prix/stock au serveur.
- [ ] Checkout : adresse, zone livraison, choix enlèvement/installation, total final clair, consentement.
- [ ] Paiement intégration **réelle** Orange Money / MTN selon contrat pays ; signatures des webhooks, déduplication, vérification côté serveur.
- [ ] Création commande atomique, réservations de stock, annulation/expiration, remboursement, facture PDF conforme.
- [ ] Espace client, suivi, emails/SMS transactionnels et support.
- [ ] Back-office commandes, statuts, audit de toutes les opérations, rôles limitées.
- [ ] Calcul réel de livraison selon zones et poids, pas de forfait arbitraire.
- [ ] Tests bout en bout : commande, erreur paiement, double notification, rupture, charge, mobile, SEO.

### P2 — Croissance commerciale

- [ ] Comptes professionnels, prix volumes, clients revendeurs validés, devis PDF.
- [ ] Comparateur produits avec spécifications normalisées, bundles caméra + NVR + stockage.
- [ ] Catalogue enrichi, fiches techniques et compatibilités, avis clients authentiques et modérés.
- [ ] Bons de réduction, promotions auditées, abandons de panier avec consentement.
- [ ] Retours/RMA, SAV matériel, suivi de numéros de série, diagnostics.
- [ ] Automatisation des imports stock/prix fournisseurs et alertes rupture.
- [ ] Guides, études de cas locales vérifiables, tableaux analytics et attribution leads.

## Paiement Guinée : pistes à examiner, pas d'intégration décidée

- Orange Money Web Payment : disponible en Guinée selon Orange Developer, sous inscription/contrat marchand et conformité ; https://developer.orange.com/apis/om-webpay
- Prestataires publiant une offre Guinée : SilyLink (Orange Money + MTN MoMo) https://silylink.com/documentation ; DexPay (Orange Money, devise GNF) https://dexpay.africa/pays/guinee
- Comparer agrément/localisation, frais réels, possibilité de remboursement, sandbox, API, webhooks signés, délais de règlement, facturation et support. **Ne pas supposer un opérateur disponible sans contrat et tests réels**.
- Tant qu'aucune intégration n'est contractuellement disponible : commande « en attente de confirmation », jamais « paiement réussi ».

## Modèle de sécurité et confidentialité

- Auth back-office forte (MFA si disponible), rôles granulaires, rate-limit, protections CSRF, validation backend, stockage sécurisé des mots de passe.
- Aucun montant, statut de paiement ou disponibilité ne doit venir du navigateur sans revalidation serveur.
- Webhooks idempotents avec signature vérifiée et référence unique ; journal de réconciliation ; jamais confirmer sur simple redirection navigateur.
- Permissions strictes pour voir une commande ; URLs non devinables ; accès aux fichiers maîtrisé.
- Ne collecter que les données nécessaires ; durée de conservation documentée ; consentement marketing séparé ; cookies non essentiels sous contrôle.
- Sauvegardes testées, migrations réversibles, supervision d'erreurs sans logs de données sensibles, correctifs dépendances et scans automatisés.

## Définition de « prêt à vendre »

- [ ] Au moins une vraie référence testée de bout en bout avec prix/stock/visuels exacts.
- [ ] Prix, frais, garantie et modalités de livraison vérifiés et affichés sans ambiguïté.
- [ ] Commande test passée : calcul serveur, stock, transaction, email, historique et suivi.
- [ ] Paiement réel test marchand + échec + timeout + doublon webhook (si paiement activé).
- [ ] Administration permet de modifier produits/prix/stock et traiter les commandes sans toucher au code.
- [ ] Responsive vérifié sur petits smartphones, tests clavier, Core Web Vitals acceptables.
- [ ] Pages indexables, Product/Offer valides si éligibles, sitemap, 301, Search Console.
- [ ] Identité légale, CGV, politique de confidentialité, garantie et retours validés.
- [ ] Procédure de sauvegarde/reprise et logs opérationnels documentés.

**Ce dépôt en phase de cadrage ne doit pas être présenté comme une boutique déjà fonctionnelle.**
