# Lot 5 — Migration SEO à périmètre contrôlé

## Constat prioritaire (5 novembre 2026)

Le dépôt historique nommé `njaga/Site-vigilus-guin-e` **ne correspond pas à un inventaire sûr des URLs d'une boutique guinéenne**. Son `routes/web.php` contient notamment les pages `/services`, `/services/solutions-monetiques`, `/services/videosurveillance-securite`, `/logiciel-rh-pointage`, et `/produits/compteuses-et-trieuses`. Le fichier `public/robots.txt` référence le domaine `groupevigilus.ci` et la **Côte d'Ivoire**. Une vérification publique du site correspondant révèle des services à Abidjan.

**Ne pas importer ces URLs ni rediriger le site ivoirien vers la boutique Dahua de Conakry.** Ne pas présumer du domaine guinéen à partir du seul nom d'un dépôt ou d'un email. Aucune ancienne URL n'est pré-enregistrée comme 301 dans ce lot.

## Fonctionnalités livrées

- Table `seo_redirects` avec `source_host`, `source_path`, `destination_path`, état et justification vérifiable ; créée vide.
- Back-office `/admin/redirections-seo` accessible uniquement aux administrateurs existants.
- L'opérateur saisit uniquement des chemins internes relatifs à des domaines **explicitement autorisés** (`APP_URL` et `SEO_APPROVED_LEGACY_HOSTS`).
- La création stocke toujours **un brouillon inactif** ; une seconde action active la 301 après contrôle de la page cible.
- Interception des vraies réponses 404 par le framework : compatible avec les anciennes pages `/produits/{slug}` qui rencontrent les routes dynamiques actuelles.
- Les URL privées, login, panier, administration, commandes, suivis et sitemap ne sont jamais utilisables comme destinations.
- Pas de redirection ouverte : URL de sortie construite exclusivement depuis le `APP_URL` du site cible et un chemin local validé.
- En cas de produit archivé, catégorie inactive ou domaine non autorisé, l'ancienne URL reste en **404** au lieu de rediriger vers une page non pertinente.
- Aucune redirection générique de tous les 404 vers la page d'accueil.
- `php artisan seo:audit-redirects --strict` liste les règles non prêtes (code de sortie 1 si anomalies).

## Précautions en préproduction

- `SEO_INDEXING_ENABLED=false` est le réglage par défaut du fichier d'environnement : `robots.txt` interdit le crawl, les pages affichent `noindex,nofollow`, `sitemap.xml` reste vide.
- Ne passer à `SEO_INDEXING_ENABLED=true` **qu'après recette** et vérification du domaine canonique `APP_URL`, des contenus, des accès et des règles 301.
- Le domaine source doit continuer à pointer vers une application capable de servir les redirections ; renseigner un domaine dans la base **ne transfère pas son trafic** et ne modifie pas le DNS.
- Aucune importation du fichier Excel BOQ dans GitHub ; les coûts et conditions fournisseurs restent privés.
- Aucun paiement en ligne : panier puis demande de commande, vérification et confirmation commerciale manuelles.

## Checklist de bascule

1. [ ] Confirmer le **domaine exact existant de Vigilus Guinée**, le domaine final et le plan DNS/certificats.
2. [ ] Exporter les pages indexées et clics depuis **Google Search Console du domaine Guinée** ainsi que les vrais sitemaps/crawls.
3. [ ] Relever les URLs historiques à valeur SEO ; classifier : correspondance exacte, nouvelle page à créer, maintien temporaire, 404/410 volontaire.
4. [ ] Compléter une table URL ancienne → URL équivalente, même marché, même intention, et faire valider par le responsable SEO.
5. [ ] Configurer `SEO_APPROVED_LEGACY_HOSTS` uniquement pour les domaines contrôlés et pertinents.
6. [ ] Créer les correspondances en brouillon ; publier d'abord les pages cibles et les valider sur mobile.
7. [ ] Activer ensuite les 301 validées ; exécuter `php artisan seo:audit-redirects --strict` et tester les entêtes Location.
8. [ ] Lancer `php artisan route:list`, `php artisan test`, `npm run build`, `php artisan migrate --force` en préproduction et vérifier /up, /robots.txt, /sitemap.xml.
9. [ ] Vérifier HTTPS, canoniques, images, droits, sitemap et absence de contenus sensibles ; publier uniquement sur décision explicite.
10. [ ] Mettre à jour Search Console avec le nouveau sitemap ; suivre 404, couverture, indexation, pages d'atterrissage et performances sur 30–90 jours.

## Périmètre métier vérifié

La présentation publique de l'implantation Guinée sur `groupevigilus.com/implantations/guinee/` décrit la **distribution d'équipements**. Par prudence, le prototype ne promet plus une prestation d'installation à Conakry. La page éditoriale est renommée `/equipements-videosurveillance-conakry` ; l'ancien slug du prototype dispose d'une 301 vers le nouveau. Cette page n'est pas une attestation de disponibilité physique de stock ou de services non confirmés.

## Points non résolus

- Le **domaine guinéen à préserver** n'est pas confirmé. Les codes historiques du dépôt ancien semblent affiliés à un autre marché, donc aucune redirection de ses URLs n'a été activée.
- Les prix, produits Dahua exacts, garanties et stocks nécessitent validation métier.
- Le projet accumule des PR non fusionnées (#6, #7, #8, #10 et ce lot) : fusion et déploiement séquentiels après revue, pas de publication directe.
- La recette mobile/visuelle, juridique et métier reste indispensable.
