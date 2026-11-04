# Lot 3 — Import technique Dahua depuis un BOQ confidentiel

> Ce dépôt est public. **Le document source, les prix fournisseurs, quantités du devis, remises, coûts logistiques, totaux et conditions négociées ne doivent jamais être commités ici.**

## Analyse de la structure fournie (sans données commerciales)

Le modèle étudié contient la feuille `Data Sheet` et des colonnes techniques/sensibles mélangées :
- **B** famille, **C** modèle interne (uniquement administration), **D** modèle externe public, **E** description générale, **F** caractéristiques, **M** numéro fabricant, **O** durée de garantie annoncée.
- Les colonnes **G/H/I/J/K/L/N/P/Q** ne sont pas lues par l'importateur ; elles ne sont pas stockées, même dans un champ masqué.
- Les photos/liens du BOQ ne sont pas considérés comme ayant des droits de publication automatiques.
- La quantité indiquée sur un BOQ n'est **pas** un stock disponible ; elle n'alimente jamais `stock_qty`.

## Fonctionnalité livrée

1. Dans le back-office : **Import BOQ** → téléverser XLSX depuis un poste autorisé.
2. Le serveur lit uniquement les colonnes techniques, vérifie l'en-tête et prépare des lignes de revue privée.
3. Suggestions de catégories (caméras IP, HDCVI, Wi-Fi, NVR/XVR, contrôle d'accès, interphonie, alarmes, incendie, réseau, écrans professionnels et accessoires).
4. Détection des références externes dupliquées dans un fichier et déjà présentes dans le catalogue ; aucune ligne répétée n'écrase un produit existant.
5. Revue par un administrateur : vérifier référence, désignation, caractéristiques, catégorie et situation commerciale.
6. **Approbation individuelle** : création d'un brouillon `is_active=false` avec `price_gnf=null`, `stock_qty=0`, sans photo et sans garantie commerciale automatiquement promue.
7. La publication et le prix public GNF doivent être renseignés séparément après accord commercial.
8. Possibilité de **purger** le lot temporaire ; les brouillons approuvés restent.

## Conditions de déploiement

- Installer les dépendances PHP (`phpoffice/phpspreadsheet`), extensions `gd`, `zip`, `xmlreader`, `xmlwriter` et autres extensions PHP indiquées par Composer.
- Configurer l'administration, le stockage temporaire sécurisé, le plafond d'upload web et les sauvegardes de base de données.
- Restreindre les accès BOQ aux rôles habilités (actuellement `can:manage-catalog`) : un rôle d'achat encore plus spécifique sera recommandé pour la production.
- Vérifier l'autorisation contractuelle de réutiliser descriptions, images et fiches constructeur.
- Traduire/éditorialiser en français les fiches avant mise en ligne ; ne jamais confondre modèle interne et variante commerciale.
- Tester la confidentialité : ne pas télécharger ou servir les fichiers BOQ au public, ne pas journaliser de prix d'achat.
- Vérifier l'environnement d'hébergement : les limites `upload_max_filesize`, `post_max_size`, `memory_limit`, `max_execution_time` doivent être adaptées à la taille d'un BOQ.

## Éléments encore à produire

- Vrais prix publics GNF validés par la direction commerciale et politique de marge conservée **hors dépôt**.
- Stock réellement réceptionné et vérifié, SKU et variantes.
- Photos/fiches officielles sous licence, relecture des caractéristiques et garanties.
- SEO FR détaillé et maillage produits/solutions.
- Gestion métier complète des promotions et des stocks fournisseurs.

**Paiement en ligne : désactivé.** Le site conserve le panier et la demande de commande gérée par Vigilus hors ligne.

## Validation multiple

Un administrateur peut sélectionner **jusqu'à 30 lignes admissibles par page** et créer leurs brouillons en une opération transactionnelle. Les lignes dupliquées ou invalides sont exclues. La création est annulée dans son ensemble en cas de sélection obsolète ou de collision SKU. Ce mécanisme ne modifie **ni prix, ni stock, ni visibilité publique**. Les descriptions et catégories nécessitent toujours une relecture avant publication.
