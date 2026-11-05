# Journal et blog — Vigilus Guinée

## Accès et structure

L'ancienne URL d'information `/equipements-videosurveillance-conakry` est désormais le **journal éditorial**, avec une disposition inspirée du modèle : grand article de tête et deux articles secondaires, suivis d'une grille sobre. Cette URL et son nom de route `solution.videosurveillance` restent stables pour les liens existants.

Les articles disposent d'une URL permanente `/blog/{slug}`. Une fois créée, l'URL d'un article ne change plus lors des modifications du titre. Il est possible d'indiquer cette URL comme destination d'une redirection SEO vérifiée.

- Journal : `/equipements-videosurveillance-conakry`
- Article : `/blog/{slug}`
- Administration : `/admin/articles` (accès administrateur uniquement)
- Création : `/admin/articles/creer`
- Aperçu : accessible uniquement à l'administrateur, **non indexable**.

## Fonctionnement du CMS

1. Se connecter avec un compte administrateur autorisé.
2. Ouvrir **Articles** et créer un texte : titre, rubrique, introduction, contenu.
3. Utiliser les outils simples « Titre », « Gras », « Liste », « Lien » et « Citation » ou écrire directement en Markdown.
4. Ajouter une **photo de couverture** JPG/PNG/WebP (4 Mo maximum) et sa description accessible.
5. Renseigner facultativement le titre et la description SEO.
6. Enregistrer en **brouillon**, prévisualiser, puis passer en « Publié » pour la date souhaitée.
7. Les publications futures restent privées jusqu'à l'heure programmée. Les brouillons ne figurent ni dans le journal ni dans le sitemap.

**Avant publication**, image de couverture et description alternative sont obligatoires. Les fichiers sont stockés via le disque public Laravel ; exécuter `php artisan storage:link` sur le serveur.

Un article retiré de la publication n'est plus accessible publiquement. Les articles ayant une histoire de publication ne sont pas détruits automatiquement pour conserver la traçabilité SEO.

## Amorçage éditorial facultatif

Trois articles originaux de conseils sont préparés, **tous en brouillon**, sans photographie ni diffusion publique :

- Choisir ses caméras de vidéosurveillance à Conakry.
- NVR, XVR et stockage : bien dimensionner son enregistrement.
- Badge, code ou biométrie : quel contrôle d'accès choisir ?

Pour les importer :

```bash
php artisan migrate
php artisan storage:link
php artisan db:seed --class="Database\\Seeders\\EditorialBlogSeeder"
```

La commande est idempotente. Elle n'écrase pas les contenus des articles déjà présents et **ne publie rien automatiquement**. Un éditeur doit ajouter de véritables visuels autorisés, relire la rédaction et valider la mise en ligne.

## Sécurité et SEO

- Markdown converti avec CommonMark / Laravel en mode `html_input=strip` et `allow_unsafe_links=false` pour bloquer HTML arbitraire et liens dangereux.
- Les routes de création, modification, aperçu et suppression exigent la connexion et la permission `manage-catalog`. CSRF activé.
- Limites d'upload et validation MIME pour les photos. Aucun prix fournisseur, BOQ ou donnée commerciale interne n'est exposé par le blog.
- Les brouillons et publications futures renvoient **404** sur leurs routes publiques.
- Les articles publiés sont ajoutés au sitemap seulement si `SEO_INDEXING_ENABLED=true`.
- Pas de changement de domaine, pas de suppression du chemin historique du journal, pas de paiement en ligne.
- L'ancien guide statique est remplacé par le journal. Sa matière technique a été retravaillée dans un brouillon initial. **Publier une version validée avant la bascule SEO est conseillé pour ne pas laisser une page sans contenu substantiel.**

## Finitions du front

Les textes de coulisses ne sont plus affichés : vérification de droits photos, préparation des fiches, conditions de développement, précautions techniques ou commentaires destinés à l'équipe. Les informations réellement nécessaires à la commande et au consentement sont conservées.

L'affichage comprend :
- Grille éditoriale sans cartes à bordures épaisses, dates typographiques, forte hiérarchie, images sur toute la largeur.
- Page article avec largeur de lecture maîtrisée, sous-titres et listes, images non déformées, recommandations d'articles.
- États de chargement vides très sobres, sans contenu trompeur.

## À vérifier visuellement avant publication

- [ ] Enregistrer et publier **au moins trois vrais articles avec couvertures** pour tester le modèle 1 grande + 2 petites.
- [ ] Vérifier les portraits/paysages et le cadrage des photos, les droits et l'absence d'étirement.
- [ ] Tester Chrome, Firefox et Safari sur mobile (360–390 px), tablette et grand écran.
- [ ] Tester les boutons Markdown, la prévisualisation, la suppression de brouillon, la programmation, l'archivage, le référencement et la redirection de l'ancien URL.
- [ ] Confirmer les métadonnées SEO, les URLs existantes, les sauvegardes et l'absence de notes internes dans les interfaces publiques.
- [ ] Tester le déploiement en préproduction avec de vraies images, sans intégrer de contenu d'achat fournisseur.

Les tests GitHub Actions valident PHP/Blade/routes/CSS/JS, **pas le rendu visuel réel sur appareils**.
