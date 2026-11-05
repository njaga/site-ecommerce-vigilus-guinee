# Lot 8 — Recherche instantanée, identité Vigilus Groupe SA et contenus de confiance

## Recherche en fenêtre animée

- Endpoint **GET** `/recherche/suggestions?q=...` avec Laravel/JSON, sans accès aux drafts ni aux tableaux BOQ fournisseurs.
- Recherche par désignation, SKU et description sur la **portée Product::published()**, 6 produits maximum, 3 catégories actives et non vides.
- Minimum **2 caractères**, maximum 80. Limitation de débit : 40 requêtes/minute. Réponses `no-store, private` et `X-Robots-Tag: noindex, nofollow`.
- Fenêtre native `<dialog>`, focus géré par le navigateur, fond assombri et flou, transitions ouverture/fermeture, clic hors modal / Échap pour fermer.
- Recherche déclenchée au clic, raccourcis **Ctrl/⌘+K** et **/** ; 220 ms de délai de frappe, anciennes requêtes annulées, gestion d’échec et recherches populaires, flèches pour naviguer dans les résultats.
- Suggestions créées avec `document.createElement` / `textContent`, **sans** injecter du HTML serveur dans la page.
- Le formulaire reste une recherche Laravel normale et peut être soumis sur le catalogue.

## Pages et contenu

- Navbar : vrai logo Vigilus Groupe SA, menu catégories révélable avec les seules familles publiées, lien À propos, recherche rapide et navigation mobile.
- Footer : bandeau de devis, identité Groupe, navigation, cadre commercial hors paiement en ligne et liens légaux.
- Accueil : hero plus immersif, catégories illustrées selon leur famille, bénéfices sans promesses non vérifiées, références du Groupe et écosystème.
- À propos `/a-propos` : présentation de l'implantation Guinée et de sa mission de **distribution**, sections Pourquoi nous choisir, Nos clients, Nos partenaires, liens vers le Groupe.
- **Les logos de clients appartiennent au Groupe Vigilus, tous métiers et pays confondus**, pas à la seule filiale Guinée ; afficher cette précision pour éviter de tromper les visiteurs.
- **Dahua est présentée comme marque du catalogue, et NON comme partenariat agréé** en attente de contrat/autorisation.

## Visuels exploités et registre

| Sujet | Source retenue | Conditions de publication |
|---|---|---|
| Logo Groupe SA | `https://www.groupevigilus.com/media/site/logo.png` | Identité officielle du site institutionnel, conserver les proportions ; stocker localement la version validée |
| Hero et caméras | `https://www.groupevigilus.com/media/services/videosurveillance.jpg` | Image éditoriale du Groupe, ne pas faire passer pour une référence Dahua spécifique |
| Contrôle d’accès et barrières | `https://images.unsplash.com/photo-1754494977436-a5c202306fe4?w=900&q=82&fit=crop` | Photographie de lecteur de badge Unsplash (Jakub Żerdzicki) — licence commerciale, légende contextuelle |
| Alarmes / électronique | `https://www.groupevigilus.com/media/site/group/electronic.jpg` | Image métier, sans prétention produit exact |
| Kits / défaut | `https://www.groupevigilus.com/media/services/videosurveillance.jpg` | Visuel générique du Groupe |
| NVR, réseau et stockage | `https://images.unsplash.com/photo-1695668548342-c0c1ad479aee?w=900&q=82&fit=crop` | Photo de baie informatique Unsplash ; conserver traçabilité et vérifier adéquation/attribution |
| Références clients Groupe | `https://www.groupevigilus.com/media/site/group/client-logos/` | Reprise seulement pour des références **Groupe** réellement validées et autorisées, jamais une revendication de clients directs Guinée |

**Source des droits Unsplash :** `https://unsplash.com/license`. Les images sont autorisées pour usages commerciaux dans le cadre de la licence, mais ne doivent pas induire en erreur sur les modèles vendus, les lieux, les fabricants ou les prestations de la filiale. Attribution non obligatoire mais utile en documentation.

**Avant publication**, les fichiers doivent être copiés localement sur le serveur après validation de la communication du Groupe, de leur exactitude, de la pertinence et des droits de représentation. Les URLs live sont des **références d’intégration pour la préproduction**, pas une solution de distribution définitive.

## QA manuelle obligatoire

- [ ] Tester l’ouverture, la fermeture, les animations et le focus sur Chrome, Firefox, Safari mobile et desktop.
- [ ] Tester clavier : Tab, Entrée, flèches, Ctrl/⌘+K, Échap ; mode mouvement réduit.
- [ ] Vérifier que les suggestions sont lisibles et que les images s'affichent sur connexion mobile lente.
- [ ] Vérifier chaque image externe et valider les médias avant copie sur le domaine final.
- [ ] Confirmer la relation commerciale Dahua avant d’utiliser des mentions telles que « partenaire », « distributeur officiel », « agréé ».
- [ ] Confirmer les clients Groupe affichés et autorisation d'utilisation des marques.
- [ ] Vérifier la conformité de la page À propos et des pages légales.
- [ ] Vérifier l'absence de prix fournisseur, données d'import BOQ, paiement en ligne, ou autres documents commerciaux privés.
- [ ] Valider le design et ses captures d’écran sur appareils réels avant fusion et mise en ligne.

## Vérifications CI

Build Vite, PHPUnit, compilation de toutes les vues Blade et du cache des routes. Cela ne remplace pas une recette visuelle sur un site réellement servi.
