# Lot 6 — Recette préproduction, leads et sécurité

## Objectif

Terminer une base de **recette technique et commerciale** pour la boutique Dahua de Vigilus Guinée sans publier de vraies données fournisseurs et **sans paiement en ligne**.

## Fonctionnalités livrées

- Demandes de devis : nouvel email au commercial (si `QUOTE_ALERT_EMAIL` existe) et accusé de réception au client s'il fournit un email. Les erreurs d'envoi sont journalisées sans supprimer la demande. La boîte de réception doit être testée en conditions réelles.
- Qualification des devis : statut `new → in_progress → closed`, possibilité de note de suivi et historique privé avec auteur/date. Aucun renvoi de note interne au client.
- Vérification côté serveur : une référence indisponible ou issue d'une catégorie désactivée ne peut plus être choisie pour un nouveau devis.
- Panier et checkout : un article « prix sur devis » ne peut jamais être commandé, même si un ancien import contient un prix chiffré par erreur ; vérification renforcée lors de la soumission.
- Double soumission de commande : pas de seconde notification en cas de collision de l'identifiant de soumission.
- En-têtes de protection : `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy: no-referrer`, `Permissions-Policy`, interdiction de framing, HSTS uniquement en HTTPS.
- UX : lien de saut clavier, focus et ajustements d'espacement sur mobile ; ces corrections de code **ne remplacent pas** une vraie inspection graphique sur les appareils.
- Documents d'information : mentions légales, politique de confidentialité et conditions commerciales, **en brouillon non indexé** jusqu'à validation juridique et commerciale. Les champs légaux sont injectés depuis le serveur : pas de fausse identité légale préremplie.
- Administration `/admin/recette` : tableau de configuration préproduction sans secrets ni mots de passe affichés.
- Artisan `php artisan commerce:preflight --strict` : contrôle du domaine HTTPS, de la devise/pays, de l'indexation, de la messagerie transactionnelle, du destinataire des alertes devis/commandes, des contacts, de l'identité légale, des textes validés, des médias publics, de l'accès administrateur et de l'absence de paiement web.
- Tests PHPUnit incluant notifications et contrôles d'accès, formulaires, données confidentielles, panier, SEO et préproduction.

## Variables supplémentaires

```env
QUOTE_ALERT_EMAIL=
SITE_LEGAL_NAME=
SITE_LEGAL_ADDRESS=
SITE_LEGAL_REGISTRATION=
SITE_PRIVACY_EMAIL=
SITE_PRIVACY_RETENTION_NOTICE=
SITE_DELIVERY_TERMS=
SITE_RETURNS_TERMS=
SITE_WARRANTY_TERMS=
SITE_PRIVACY_APPROVED=false
SITE_SALES_APPROVED=false
```

**Ne renseignez aucun secret SMTP, mot de passe et aucune condition fournisseur dans les commits GitHub.** Ces paramètres appartiennent uniquement aux secrets de l'environnement sécurisé du serveur.

## Recette métier manuelle obligatoire

1. [ ] Domaine Guinée confirmé, APP_URL HTTPS, certificat et DNS.
2. [ ] Dépendances PHP, migrations, stockage des fichiers, sauvegarde/restauration de la base.
3. [ ] Recette visuelle : téléphone petit écran, tablette et bureau, menus, hero, filtres, galeries produits, recherche et panier.
4. [ ] Accessibilité : navigation au clavier, contraste, lecteurs d'écran, champs et erreurs.
5. [ ] Email : test SMTP réel à une boîte commerciale contrôlée, livrabilité, SPAM, réponses, commandes et devis.
6. [ ] Achat réel test : produit vérifié avec prix GNF et stock physique, panier, soumission, réservation, confirmation, annulation et suivi privé.
7. [ ] Rôle commercial : qui examine les nouvelles demandes, à quelle fréquence, dans quels délais.
8. [ ] Produits : données Dahua, codes fabricant, prix GNF validés, droit images, garanties client, catégories et stocks.
9. [ ] Mentions et politique de données validées par la personne compétente, y compris conservation/suppression des données.
10. [ ] Politique livraison, modalités de règlement **hors site**, garanties et retours approuvées.
11. [ ] Redirections historiques uniquement du domaine **Guinée** avec mapping testé ; aucune migration involontaire depuis la Côte d'Ivoire.
12. [ ] Activer `SEO_INDEXING_ENABLED=true` seulement quand les pages finales sont prêtes.
13. [ ] Sauvegarder l'ancien site et la base, préparer un plan de retour arrière, surveiller les erreurs 500, 404 et les commandes après déploiement.

## Sortie de la préproduction

La CI et `commerce:preflight --strict` sont des **outils d'aide**, pas une preuve d'absence de bugs, de conformité légale ni de réussite des vrais emails. L'exploitation ne commence qu'après une recette humaine sur staging et une décision de publication explicite.
