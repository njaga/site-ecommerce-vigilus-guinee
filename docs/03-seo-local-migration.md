# 03 — Référencement SEO, local & migration sans pertes

## Contexte

Pays cible technique déduit des dépôts et du concurrent : **Guinée (GN) / GNF / Conakry**. L'ancienne application expose déjà des routes indexables et un sitemap statique. Le futur site ne doit pas basculer de pays/domaine sans validation.

## Arborescence cible

- / (page d'accueil commerciale)
- /boutique
- /boutique/cameras-ip
- /boutique/enregistreurs-nvr
- /boutique/controle-acces
- /produit/{slug-reference-constructeur}
- /solutions/videosurveillance-commerce
- /solutions/videosurveillance-maison
- /solutions/controle-acces-entreprise
- /services/installation-videosurveillance-conakry
- /services/maintenance-videosurveillance
- /professionnels
- /guides/choisir-camera-surveillance
- /guides/camera-ip-ou-hdcvi
- /livraison ; /garanties ; /retours ; /contact ; /a-propos
- /sitemap.xml ; /robots.txt

Les catégories portent une intention d'achat ; les guides répondent à des problèmes et renvoient aux produits. Ne pas créer de centaines de pages identiques ciblant seulement des noms de quartiers.

## Clusters SEO

| Intention | Exemples de requêtes à étudier | Page cible |
|---|---|---|
| Transactionnelle | caméra Dahua Conakry ; acheter caméra IP Guinée | Catégorie caméra IP + fiches modèles |
| Référence exacte | Dahua + référence officielle | Page produit unique |
| Solutions | kit vidéosurveillance 4 caméras Conakry | Catégorie kits / bundle |
| B2B | fournisseur Dahua Guinée ; grossiste vidéosurveillance | Professionnels / preuves distributeur |
| Service local | installation caméra surveillance Conakry | Page service locale |
| Informationnelle | quelle caméra pour boutique ; NVR 8 ou 16 canaux | Guides comparatifs originaux |
| Après-vente | garantie caméra Dahua Guinée | Page SAV / garanties |

Les formulations ci-dessus sont des hypothèses à valider par Search Console, Google Trends, Keyword Planner et retours commerciaux, pas des promesses de volume SEO.

## Exigences techniques

- SSR Blade/Laravel : HTML de chaque fiche réellement crawlable, liens internes HTML et pagination indexable.
- Meta title/H1/description uniques par produit ; slug lisible avec SKU ; URL canonical absolue ; aucun doublon via facettes.
- Robots : interdire panier, compte, back-office, commandes privées, résultats de recherche interne et paramètres de filtres inutiles ; ne pas bloquer fichiers CSS/JS nécessaires.
- Sitemaps dynamiques (produits actifs, catégories, articles, services) avec lastmod réel, exclusion pages privées/404.
- Données structurées : Organization, BreadcrumbList, LocalBusiness **avec contacts/adresse vérifiés**, Product+Offer **uniquement si prix/stock sont réels**, politiques de livraison/retour uniquement si réelles.
- Images AVIF/WebP ou optimisées, width/height, alt descriptif et photos fidèles ; lazy-load sauf image LCP.
- Core Web Vitals : limiter JS, requêtes SQL, images XXL, scripts tiers ; CDN/cache contrôlés.
- Vérifier Merchant Center pour le pays et les programmes effectivement éligibles avant de promettre la visibilité Shopping.

Documentation Google :
- https://developers.google.com/search/docs/appearance/structured-data/product-snippet
- https://developers.google.com/search/docs/appearance/structured-data/merchant-listing
- https://developers.google.com/search/docs/appearance/structured-data/local-business

## Référencement local

- Fiche d'établissement Google (si éligible) vérifiée : nom commercial réel, adresse physique, téléphone guinéen, horaires, catégorie, URL et photos du site réelles.
- Même NAP (name/address/phone) sur site, fiche Google, documents, annuaires locaux et réseaux.
- Zone desservie non exagérée : Conakry puis autres villes uniquement si livraison/installations réelles.
- Pages de services avec réalisations vérifiables (photos autorisées), cas d'usage, conditions d'intervention et FAQ.
- Annuaires locaux de qualité, partenariats installateurs et citations éditoriales ; aucune génération d'avis fictifs.

## Migration de l'ancien site

Ancien dépôt : https://github.com/njaga/Site-vigilus-guin-e

Routes constatées : /produits ; /produits/{slug} ; /services ; /services/{...} ; /logiciel-rh-pointage ; /demande-de-devis ; /contact ; etc. Elles ne doivent pas être supprimées sans cartographie.

1. Exporter toutes les pages indexées Search Console, sitemaps, analytics, liens entrants, logs et URLs HTTP connues du domaine actuel.
2. Construire un tableau URL ancienne -> URL nouvelle avec justification et état HTTP. Conserver les URLs si possible, sinon **301 vers l'équivalent exact** ; 410 si page réellement supprimée sans remplacement utile.
3. Ne pas envoyer tout l'ancien catalogue vers la homepage ; préserver les pages/produits populaires et les liens entrants.
4. Revoir le contenu legacy : données immobilières « 2SMS », nom Côte d'Ivoire résiduel dans un partial, mentions HIKVISION de l'ancien catalogue, promesses non vérifiées et anciennes URLs de produits.
5. Faire une recette sur staging sans indexation ; tester 301/200/404, canonical, hreflang si nécessaire, robots et sitemaps.
6. Déployer en gardant le même domaine historique si possible ; soumettre sitemap et surveiller couverture, 404 et clics par URL pendant 8 à 12 semaines.

## Exemples (modèles à compléter, pas du contenu publié)

Title de catégorie : « Caméras IP Dahua à Conakry | Prix, modèles et installation — Vigilus Guinée »

H1 : « Caméras IP Dahua : comparer et acheter en Guinée »

Fiche : « Caméra Dahua [référence exacte] — [résolution] | Vigilus Guinée »

Les prix, les avis clients et les disponibilités ne doivent jamais être inventés pour générer des rich results.
