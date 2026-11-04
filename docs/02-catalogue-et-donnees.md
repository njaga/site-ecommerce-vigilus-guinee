# 02 — Catalogue Dahua, taxonomie & données

## Catégories de départ

Adapter ensuite au portefeuille **réellement disponible à la vente**. La nomenclature suit les grandes familles du catalogue officiel Dahua : https://www.dahuasecurity.com/sa/products

| Catégorie SEO | Sous-catégories / filtres principaux |
|---|---|
| Caméras IP / réseau | Dôme, bullet, PTZ, Wi-Fi, extérieure, résolution MP, PoE, IR, Full-color, IA |
| Caméras HDCVI | Dôme, tube, PTZ, MP, lentille, portée IR, micro |
| Enregistreurs NVR | 4/8/16/32/64 canaux, PoE, disques, bande passante, WizSense |
| Enregistreurs XVR | 4/8/16/32 canaux, technologie, capacité disque |
| Kits vidéosurveillance | 2/4/8/16 caméras, stockage inclus ou non, installation en option |
| Contrôle d'accès et pointage | Lecteurs, terminaux, contrôleurs, serrures, logiciels, biométrie selon législation |
| Interphonie et visiophonie | Platines, moniteurs, kits, IP/2 fils/4 fils |
| Alarmes et détection | Centrale, capteurs, sirènes, kits sans fil / filaires |
| Réseau et PoE | Switches PoE, adaptateurs, émetteurs, Wi-Fi, transmission |
| Stockage & accessoires | Disques compatibles, cartes microSD, câbles, connectique, alimentation |
| Moniteurs et affichage | Moniteurs vidéosurveillance, écrans dédiés, accessoires |
| Sécurité incendie / spécialisée | Uniquement si produits commercialisés et conformité confirmée |

## Fiche produit : champs requis

- Identité : identifiant interne, fabricant, référence/SKU unique, nom commercial en français, slug pérenne, EAN/GTIN si réel, catégorie, sous-catégorie, série constructeur.
- Technique : description originale et utile ; caractéristiques typées (MP, objectif, IR en m, IPxx, PoE, compression vidéo, compatibilité NVR, alimentation, dimensions).
- Commerce : prix public en GNF **entier**, prix barré uniquement si justifiable, statut publiable, quantité de stock, seuil de rupture, coût interne non public, poids et dimensions d'expédition.
- Médias : photos officielles autorisées + alt précis, fiche PDF autorisée, vidéo optionnelle ; conserver aspect-ratio et nom de référence.
- SEO : title/meta, description, texte éditorial, canonical, FAQ utile, produits associés et accessoires, liens vers guides.
- SAV : garantie réelle, exclusions, installation disponible, conditions de retour ; aucune promesse fictive.

## Règles de disponibilité et de prix

- Si prix confirmé et vente autorisée : afficher prix clair, devise GNF, disponibilité réelle et achat.
- Si tarif inconnu ou sur devis : **ne pas afficher de faux prix ni balisage Offer avec prix inventé** ; bouton « Demander un prix » ou « Obtenir un devis ».
- Si hors stock : conserver la fiche indexable si produit encore vendu ; CTA d'alerte stock / alternatives, mais empêcher achat.
- Le prix appliqué au serveur prévaut toujours sur le prix affiché dans le navigateur. Stock réservé atomiquement au moment de confirmation selon règle métier.
- Pour revendeurs : grille tarifaire distincte authentifiée ; pas d'affichage de prix confidentiels dans le HTML public.

## Entités minimales (MCD fonctionnel)

- Category : id, parent_id, name, slug, description, seo_title, seo_description, sort_order, is_active.
- Product : id, category_id, brand_id, sku, slug, name, short_description, description, specifications_json, public_price_gnf nullable, compare_at_gnf nullable, stock_qty, status, tax_class, weight_g.
- ProductMedia : product_id, storage_path, alt_text, position, media_kind.
- ProductRelation : product_id, related_product_id, relation_type (compatible/alternative/accessory).
- Customer : id, name, email nullable, phone, type (particulier/pro), company_name, verified_at.
- Address : customer_id, city, district, landmark, geo_opt_in nullable, instructions.
- Cart / CartItem : session/customer, product, quantity, expires_at.
- Order : reference, customer_snapshot, fulfillment_method, status, payment_status, subtotal_gnf, shipping_gnf nullable, total_gnf nullable, currency, internal_note.
- OrderItem : order_id, sku_snapshot, name_snapshot, unit_price_gnf, quantity, amount_gnf.
- PaymentAttempt : order_id, provider, method, provider_reference, idempotency_key, amount_gnf, status, verified_webhook_at ; aucun secret en clair.
- Shipment : order_id, destination, carrier, tracking, status, delivered_at.
- QuoteRequest / QuoteLine : client/pro, références, quantités, installation, délai, statut, responsable.
- Coupon, PromotionRule, InventoryMovement, AuditLog, ContentPage, Article, Redirect301 et Settings.

La disponibilité doit se baser sur mouvements de stock transactionnels, pas simplement sur une valeur calculée dans le front.

## Import catalogue (PIM léger)

Importer une liste CSV validée : SKU, famille, nom, prix, stock, garantie, URL/chemin d'image autorisée, fiche technique, caractéristiques. Contrôler unicité SKU, doublons, modèles Dahua obsolètes, modèles incompatibles, formats d'image, faux prix et liens brisés. Rapport d'erreurs avant publication.

**Ne pas copier les fiches ASLAN/Hikvision** : les prix sont des références de positionnement, les fiches Dahua proviennent des documents fabricant et de données commerciales internes validées.

## Sources

- Dahua Africa, familles de produits : https://www.dahuasecurity.com/sa/products
- Catalogue concurrent de référence : https://hikguinee.com/product-category/hikvision-network-products/?per_page=-1
