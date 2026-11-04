# Lot 2 — Panier et commandes sans paiement en ligne

## Principe non négociable

**Aucun paiement sur le site** : pas de carte bancaire, pas d'API Orange Money/MTN, pas de redirection de paiement, pas de bouton « Payer », pas de webhooks. Les commandes sont des **demandes** examinées par Vigilus, et un règlement éventuel est organisé **hors plateforme**, selon les accords commerciaux réels.

## Parcours client

1. Le visiteur ajoute uniquement les produits actifs, tarifés et en stock au panier.
2. Le panier valide les quantités et indique le sous-total matériel en GNF ; frais de livraison non déterminés.
3. La page « Confirmez votre demande » collecte nom, téléphone, email facultatif, ville, adresse et consentement. Un jeton anti-double-soumission protège la demande ; la base impose son unicité.
4. Une référence `VG-...` est créée avec snapshots des prix/produits et statut `pending_review`. Le stock n'est pas décrémenté au dépôt de la demande.
5. Page de confirmation + lien de suivi privé signé valable 30 jours ; envoi par email au client uniquement si l'email a été fourni.
6. Notification de nouvelle demande à `ORDER_ALERT_EMAIL` si renseigné. Sur un serveur local avec `MAIL_MAILER=log`, les messages restent dans les journaux et ne sont pas envoyés.

## Traitement par Vigilus

- **En attente** (`pending_review`) : demande enregistrée, sans promesse de disponibilité et sans paiement.
- **Stock réservé** (`reserved`) : agent autorisé contrôle la disponibilité et décrémente le stock sous verrou transactionnel. La confirmation commerciale reste à effectuer.
- **Confirmée** (`confirmed`) : après accord réel sur l'expédition, l'agent saisit le coût de livraison validé (0 uniquement si effectivement gratuit) et confirme le total matériel + livraison. La facture et les conditions d'installation ne sont pas générées par cette étape. Le paiement n'est toujours pas fait sur le site.
- **Annulée** (`cancelled`) : possible depuis `pending_review` ou `reserved` ; restitution du stock seulement s'il avait été réservé.
- Une commande `confirmed` n'est pas annulable automatiquement : procédure manuelle pour éviter de remettre en stock un produit déjà transmis hors plateforme.

Chaque transition est historisée en `order_events` avec auteur, horodatage et note interne facultative. Les courriels clients suivent les changements de statut si une adresse a été fournie ; une panne du mail ne supprime pas une commande.

## Confidentialité et suivi

- Le suivi `/suivi-commande/{order}` nécessite une URL **signée et expirante** (30 jours) ; l'identifiant seul mène à une réponse 403.
- Les pages suivi/commande/panier sont `noindex`. Le suivi ne montre ni téléphone, ni adresse, ni notes internes.
- Ne transmettre le lien qu'au client concerné ; `APP_URL` doit être renseigné avec le domaine public final avant activation des emails.
- `ORDER_ALERT_EMAIL` doit pointer vers une boîte commerciale de confiance. Ne pas utiliser une adresse non validée ou externe.
- Consentement à la prise de contact enregistré dans `orders.consented_at`. Ajouter avant mise en production des politiques légales locales, une durée de conservation, une procédure de suppression et la configuration de messagerie expéditrice.
- Éviter tout envoi en réel avant de vérifier SMTP, DKIM/SPF, domaine et délivrabilité.

## Tests

Tests couvrant : ajout panier, rupture, absence de prix, validation serveur des montants, confirmation non payée, verrouillage de réservation, restitution, accès back-office, création jeton, rejet jeton invalide, doublon de demande, e-mails conditionnels, lien signé et cycle de confirmation sans paiement.

## Non inclus dans ce lot

Paiement en ligne (explicitement exclu), livraison automatisée, frais d'installation, facture, compte client, SMS/WhatsApp automatique, synchronisation stock fournisseur, livraisons physiquement exécutées, gestion des retours, remboursements et annulations après confirmation.

Ces éléments nécessitent des modalités commerciales vérifiées et des tests avant lancement. Conserver les PR en brouillon jusqu'à revue, recette mobile, textes légaux, coordonnées locales et import du catalogue Dahua.
