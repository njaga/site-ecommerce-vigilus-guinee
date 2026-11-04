# Lot P1 (partiel) : panier et demandes de commande

## Fait
- Panier session avec quantités et vérification serveur des produits actifs, des prix et du stock.
- Checkout de demande de commande, stockage des coordonnées et de la localisation en Guinée.
- Prix côté serveur et photographie des données financières au moment de la demande.
- Sous-total matériel affiché ; livraison/installation/total **restent indéterminés**.
- Référence générée pour chaque demande ; aucun paiement ni confirmation automatique.
- Back-office : voir les demandes, réserver le stock transactionnellement, annuler et rétablir le stock.
- Tests pour disponibilités, prix côté serveur, accès administrateur et cycle de réservation.

## À développer pour passer de demande à vente ferme
1. Modes et barèmes de livraison guinéens vérifiés par zone, adresse / retrait, transporteurs.
2. Paiement mobile money via prestataire agréé et vérifié pour Guinée : signature des notifications, réconciliation, timeout et remboursement.
3. Total ferme avec livraison/taxes avant paiement ; conditions de vente et politique de retour validées.
4. Verrouillage des stocks, expiration automatique des réservations non payées et règles d'annulation.
5. Notifications transactionnelles mail/SMS aux clients et à l'équipe ; compte client et historique.
6. Permissions / rôles plus granulaires au back-office ; accès données personnelles, export et suppression réglementaire.
7. Test de charge / concurrence, observabilité, sauvegarde, anti-fraude et conformité documentaire.

**Ne pas communiquer une commande comme payée ou livrable depuis cette version :** le statut `reserved` signifie seulement « stock réservé par l'équipe ». Le bouton client dit « Envoyer ma demande de commande ».
