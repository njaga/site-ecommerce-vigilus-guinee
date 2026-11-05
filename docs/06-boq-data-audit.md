# Audit d'import — structure, sans données financières

L'analyse du BOQ communiqué en privé par Vigilus montre :
- Une feuille `Data Sheet` comportant **176 lignes produit** structurées.
- **11 familles fournisseur** ; la nomenclature publique cible est différente.
- **175 modèles externes distincts** : une référence externe est répétée sur deux lignes.
- **Deux lignes sans modèle interne**, mais avec une référence externe renseignée.
- Un total de document et des prix d'achat en USD : **valeurs volontairement non reproduites ici**.
- Quantités du BOQ : ne pas les assimiler à des stocks physiques.

Ces observations sont des indicateurs de qualité d'import ; **aucune référence ni condition fournisseur issue du fichier original ne doit être copiée dans la documentation publique**.

## Points d'attention

- Les modèles externes portent la référence visible au client ; l'interne est réservé à la revue et peut être absent.
- Plusieurs produits de la famille `Intelligent Building` nécessitent une reclassification manuelle avant publication.
- `Storage Product` est à répartir entre NVR et XVR en fonction de la référence vérifiée.
- La source est largement anglophone : descriptions commerciales françaises et SEO à rédiger avant activation.
- Les garanties indiquées dans la source ne deviennent pas automatiquement un engagement de SAV de Vigilus.
- Les fiches images et fiches techniques doivent provenir de canaux autorisés, sans déformation.
- Les produits approuvés sont créés en brouillon avec prix public `null` et stock `0`.

## Politique de confidentialité appliquée à la conception

- Pas de fixture de test issue de l'original : les tests fabriquent leur propre XLSX synthétique.
- Pas de prix d'achat, quantités, volumes ou totaux fournisseurs dans la base d'import.
- Fichier source Excel non conservé par l'application.
- Visualisation des lignes techniques limitée aux administrateurs.
- Purge des données de préparation possible après contrôle.
