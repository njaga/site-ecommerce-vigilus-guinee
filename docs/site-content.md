# Coordonnées, Groupe et devises

## Coordonnées

Renseigner `SITE_PHONE`, `SITE_PHONE_SECONDARY`, `SITE_EMAIL` et `SITE_ADDRESS` dans `.env`.
Sans valeur, le pied de page affiche des emplacements provisoires, sans lien téléphonique ou email fictif.

## Groupe Vigilus

`config/group.php` centralise le message Dahua, les certifications du Groupe, les implantations et les filiales.
La représentation officielle exclusive Dahua est affichée selon les informations de Vigilus.
La page `/formations` affiche les sessions publiées à venir. Dans `/admin/formations`, créer, modifier ou dépublier les sessions et suivre les demandes (nouvelle, contactée, clôturée). Les demandes sont enregistrées dans `training_requests` ; aucun email automatique ni inscription définitive n’est déclenché. Une demande entreprise exige le nom de l’entreprise ; les demandes de participation acceptent uniquement une session publiée à venir.

Déposer les logos officiels dans `public/images/brands/`, puis renseigner les chemins publics dans :

- `GROUP_PROPERTIES_LOGO_URL`
- `GROUP_MOBILITY_LOGO_URL`
- `GROUP_FACILITIES_LOGO_URL`
- `GROUP_SPEAR_LOGO_URL`

Sans logo, le nom de la filiale apparaît dans l’espace réservé.

## Devises indicatives

Taux fournis le 16 novembre 2026 : 1 GNF = 0,00011 USD = 0,00010 EUR.
`config/currency.php` utilise `CURRENCY_USD_PER_GNF`, `CURRENCY_EUR_PER_GNF` et `CURRENCY_RATES_DATE`.
Mettre à jour les taux et leur date ensemble. Aucun service de change en temps réel n’est utilisé.

Le sélecteur mémorise localement le choix GNF/USD/EUR. Les prix convertis sont précédés de ≈ et accompagnés d’un avis daté.
Les produits, totaux enregistrés, emails, administration et données SEO restent en GNF. Le panier et la commande rappellent le sous-total de référence en GNF.
Sans JavaScript, les montants restent en GNF et le sélecteur est masqué.

Après modification de la configuration, exécuter `php artisan config:clear` (ou reconstruire le cache de configuration utilisé en production).

## Page Contact, Google Maps et WhatsApp

La page `/contact` reprend les coordonnées du site et oriente vers les formulaires existants de devis et de formation.
La localisation par défaut est **Conakry, Guinée**, sans prétendre situer précisément les bureaux.

- `GOOGLE_MAPS_EMBED_API_KEY` : clé pour Maps Embed API. Sans clé, la page propose un lien vers Google Maps ; avec une clé, elle affiche la carte intégrée.
- `GOOGLE_MAPS_QUERY` : adresse ou identifiant de lieu à afficher. Si vide, utilise `SITE_ADDRESS`, puis Conakry par défaut. Renseigner les deux avec des valeurs cohérentes lorsque l'adresse sera connue.
- `SITE_WHATSAPP` : numéro international complet, par exemple au format `+224` suivi des neuf chiffres du numéro guinéen. Le préfixe `+224` seul garde le widget visible avec une indication de numéro à venir et un lien vers le formulaire de devis. Aucun lien de discussion incomplet n'est généré.

La clé Maps Embed est une clé navigateur, visible dans l'URL de l'iframe : la restreindre à **Maps Embed API** et aux référents HTTP des domaines de développement/production autorisés dans Google Cloud. L'iframe envoie un référent d'origine compatible avec ces restrictions. [Documentation Google](https://developers.google.com/maps/documentation/embed/get-api-key).
Le bouton WhatsApp ouvre une discussion avec un texte prérempli ; le visiteur décide de l'envoyer. [Documentation WhatsApp](https://faq.whatsapp.com/5913398998672934).

Le sélecteur de devises utilise des SVG locaux (Guinée, États-Unis, Union européenne). Il se ferme par clic extérieur ou Échap et reste utilisable au clavier.
Les implantations du Groupe et ses certifications occupent maintenant deux sections distinctes sur l'accueil et la page À propos.

## Pages légales

Les pages disposent d'un sommaire et décrivent les fonctions réellement mises en œuvre : demandes hors paiement, formations, devises indicatives, cookies de session, préférence de devise, Maps, WhatsApp, polices et images externes.

Compléter dans `.env` les données existantes d'identité, de conservation, de livraison, de retours et de garantie, ainsi que :

- `SITE_LEGAL_FORM`, `SITE_LEGAL_CAPITAL`, `SITE_LEGAL_TAX_ID`, `SITE_PUBLICATION_DIRECTOR`
- `SITE_HOSTING_NAME`, `SITE_HOSTING_ADDRESS`, `SITE_HOSTING_CONTACT`
- `SITE_PRIVACY_PROVIDERS_NOTICE` : prestataires, pays de traitement et modalités de transferts à confirmer.
- `SITE_LEGAL_UPDATED_AT` : date de la version, à modifier lors d'une mise à jour du contenu.

L'identité juridique de la société guinéenne n'est pas déduite de la seule marque « Vigilus Groupe SA ». Les délais légaux, durées de garantie et modalités de retour ne sont pas inventés. Les indicateurs existants `SITE_PRIVACY_APPROVED` et `SITE_SALES_APPROVED` restent inchangés ; ils ne doivent être activés qu'après validation du contenu et des informations manquantes. La mise en forme de ces pages ne vaut pas validation juridique.

Références consultées pour le cadre général : [ANSSI Guinée — cadre juridique](https://anssi.gov.gn/cadre-juridique/), [loi relative aux transactions électroniques](https://www.anssi.gov.gn/wp-content/uploads/2025/03/loi_l-2016-035-an_relative_aux_transactions_electroniques.pdf), [texte sur la protection des données publié par l'Assemblée nationale](https://cnt.gov.gn/archive.assemblee/www.assemblee.gov.gn/loi-ndeg-l-2016-relative-la-cybersecurite-et-la-protection-des-donnees-caractere-personnel-en.html). Les clauses opérationnelles décrivent le code du site et les informations fournies par Vigilus.
