@extends('layouts.store')
@section('title', 'Politique de confidentialité | Vigilus Guinée')
@section('meta_description', 'Comprenez comment Vigilus Guinée utilise les données de vos demandes de devis, de commande et de formation, ainsi que les données de navigation.')
@section('canonical', route('legal.privacy'))
@section('robots', config('site.privacy_approved') ? 'index,follow' : 'noindex,follow')
@section('content')
<x-legal-document title="Politique de confidentialité" intro="Comprendre quelles informations sont recueillies, pourquoi elles sont utilisées et comment nous contacter à leur sujet." :approved="config('site.privacy_approved')" :sections="['responsable' => 'Responsable & périmètre', 'collecte' => 'Données recueillies', 'finalites' => 'Utilisation des données', 'destinataires' => 'Destinataires', 'stockage' => 'Cookies & stockage local', 'services' => 'Services tiers', 'conservation' => 'Conservation', 'securite' => 'Sécurité', 'droits' => 'Vos droits', 'evolution' => 'Mises à jour']">
    <section id="responsable"><h2>01. Responsable et périmètre</h2>
        <p>Cette politique concerne le site Vigilus Guinée et les demandes qui y sont transmises. Elle ne couvre pas les sites des autres filiales, ni les systèmes de vidéosurveillance installés chez les clients.</p>
        <dl class="legal-facts"><div><dt>Responsable du traitement</dt><dd>{{ config('site.legal_name') ?: 'Entité exploitante à confirmer' }}</dd></div><div><dt>Adresse</dt><dd>{{ config('site.legal_address') ?: 'Adresse légale à compléter' }}</dd></div><div><dt>Contact confidentialité</dt><dd>{{ config('site.privacy_contact') ?: (config('site.email') ?: 'Adresse dédiée à compléter') }}</dd></div></dl>
    </section>
    <section id="collecte"><h2>02. Les données recueillies</h2>
        <p>Les champs obligatoires sont indiqués dans chaque formulaire. Une donnée facultative peut être laissée vide. Les informations demandées varient selon votre démarche :</p>
        <ul><li><strong>Devis :</strong> nom, téléphone, email si renseigné, entreprise, produit éventuel, quantité et description du besoin.</li><li><strong>Commande :</strong> nom, téléphone, email éventuel, ville, quartier, adresse de livraison, notes, références et quantités sélectionnées.</li><li><strong>Formation :</strong> nom, email, téléphone, entreprise lorsque la demande concerne une session en entreprise, nombre de participants, session choisie, période et objectifs éventuels.</li><li><strong>Suivi :</strong> date de réception, état de traitement, échanges et notes utiles au suivi de la demande, ainsi que la date de l’accord recueilli dans le formulaire.</li></ul>
        <p>Le serveur et les prestataires techniques peuvent également traiter des données de connexion, telles que l’adresse IP, l’heure d’accès et les caractéristiques du navigateur, pour servir les pages et diagnostiquer les incidents. Les informations bancaires, mots de passe de vos équipements et flux vidéo ne sont pas nécessaires aux formulaires du site : ne les transmettez pas dans les messages libres.</p>
    </section>
    <section id="finalites"><h2>03. À quoi servent ces informations ?</h2>
        <p>Les informations permettent de comprendre votre projet, répondre à vos questions, vérifier une disponibilité, établir une proposition, préparer une formation et suivre les demandes de commande. Les coordonnées et l’adresse servent également à organiser les échanges et, après confirmation, la livraison.</p>
        <p>Le traitement des demandes s’inscrit dans les démarches précontractuelles que vous initiez et, lorsqu’une prestation est conclue, dans son exécution. Les informations nécessaires à une obligation légale ou à la sécurité du service peuvent être traitées à ces seules fins, selon le cadre applicable. Les fondements et modalités définitifs doivent être validés par l’exploitant.</p>
        <p>L’accord demandé dans les formulaires porte sur le traitement de votre demande. Il ne constitue pas une inscription à une newsletter. Le site ne propose actuellement ni abonnement publicitaire ni profilage commercial automatisé.</p>
    </section>
    <section id="destinataires"><h2>04. Qui peut recevoir les données ?</h2>
        <p>Les demandes sont consultables par les personnes habilitées à leur traitement dans le backoffice. Selon la configuration, des notifications de devis et de commande peuvent être adressées à l’équipe commerciale par messagerie. Les demandes de formation sont enregistrées pour être suivies par l’équipe.</p>
        <p>Les prestataires d’hébergement, de maintenance, de messagerie ou de livraison peuvent intervenir pour les besoins de leur mission. La présence de filiales du Groupe dans plusieurs pays ne signifie pas que toutes reçoivent automatiquement vos demandes.</p>
        <p>{{ config('site.privacy_providers_notice') ?: 'La liste des prestataires de production, leurs pays de traitement et les conditions applicables aux éventuels transferts internationaux restent à préciser avant la mise en ligne commerciale.' }}</p>
    </section>
    <section id="stockage"><h2>05. Cookies et stockage dans le navigateur</h2>
        <div class="legal-table-wrap"><table><thead><tr><th>Fonction</th><th>Utilité</th><th>Conservation / maîtrise</th></tr></thead><tbody>
            <tr><td>Session et sécurité</td><td>Maintenir le panier, les messages de confirmation et protéger les formulaires ; maintenir la connexion des administrateurs.</td><td>Session configurée pour {{ config('session.lifetime') }} minutes d’inactivité. Les paramètres du navigateur peuvent aussi limiter les cookies.</td></tr>
            <tr><td>Choix de devise</td><td>Mémoriser GNF, USD ou EUR dans le stockage local du navigateur.</td><td>Jusqu’à modification du choix ou suppression des données du site dans le navigateur.</td></tr>
        </tbody></table></div>
        <p>La mémorisation de la devise ne modifie pas les prix de référence enregistrés en GNF. Aucun outil de mesure d’audience publicitaire n’est intégré dans la version actuelle. Le blocage des cookies nécessaires peut empêcher le panier, la connexion ou certains formulaires de fonctionner correctement.</p>
    </section>
    <section id="services"><h2>06. Google Maps, WhatsApp et ressources externes</h2>
        <p><strong>Google Maps :</strong> lorsqu’une carte intégrée est affichée sur la page Contact, votre navigateur échange avec Google pour la charger. Des données techniques, dont l’adresse IP et des informations sur le navigateur, peuvent être communiquées à ce service.</p>
        <p><strong>WhatsApp :</strong> ouvrir le bouton de discussion vous dirige vers le service WhatsApp. Un texte peut être prérempli ; vous choisissez de l’envoyer. Votre utilisation de ce service et les messages que vous y transmettez relèvent également de ses propres conditions.</p>
        <p><strong>Polices et images :</strong> certaines polices sont chargées depuis Google Fonts et certains visuels depuis des sites externes, notamment celui du Groupe. Ces chargements impliquent des requêtes de votre navigateur vers leurs serveurs.</p>
        <p>Consultez les politiques de <a href="https://policies.google.com/privacy?hl=fr" target="_blank" rel="noopener noreferrer">Google</a> et de <a href="https://www.whatsapp.com/legal/privacy-policy" target="_blank" rel="noopener noreferrer">WhatsApp</a> avant d’utiliser leurs services (liens ouverts dans un nouvel onglet).</p>
    </section>
    <section id="conservation"><h2>07. Durées de conservation</h2>
        <p>{{ config('site.privacy_retention_notice') ?: 'Les durées exactes de conservation des demandes, commandes, justificatifs, journaux techniques et sauvegardes restent à définir par l’exploitant avant le lancement commercial. Aucune durée de purge automatique ne peut donc être annoncée à ce stade.' }}</p>
        <p>Une demande clôturée dans le backoffice n’est pas automatiquement effacée. La politique définitive doit distinguer les dossiers actifs, les archives nécessaires à une obligation légale ou à un litige et les données pouvant être supprimées ou anonymisées.</p>
    </section>
    <section id="securite"><h2>08. Protection des informations</h2>
        <p>L’administration exige une authentification et des habilitations. Les formulaires comportent des contrôles de validation et des protections contre les soumissions abusives. Les liens de suivi de commande donnent accès à des informations sur votre dossier : conservez-les comme des liens privés et évitez de les diffuser.</p>
        <p>Aucun dispositif ne supprime entièrement les risques. En cas de suspicion d’accès indu ou d’erreur de destinataire, contactez l’équipe en indiquant la référence de la demande concernée, sans communiquer de mot de passe.</p>
    </section>
    <section id="droits"><h2>09. Vos droits et vos demandes</h2>
        <p>Dans les conditions prévues par le droit applicable, vous pouvez demander l’accès aux informations vous concernant, leur rectification ou leur suppression, et vous opposer à certains traitements. Lorsque le traitement repose sur un consentement, vous pouvez demander son retrait pour l’avenir. Certaines données peuvent toutefois devoir être conservées pour satisfaire une obligation légale ou traiter un litige.</p>
        <p>Adressez votre demande à {{ config('site.privacy_contact') ?: (config('site.email') ?: 'notre équipe via les coordonnées de la page Contact') }}, en précisant le droit souhaité et, si possible, la référence du dossier. Une vérification proportionnée de votre identité peut être nécessaire pour éviter de communiquer vos données à une autre personne. N’envoyez pas spontanément de pièce d’identité dans un formulaire commercial.</p>
        <p>Les informations générales sur le cadre guinéen de protection des données sont disponibles auprès de l’<a href="https://anssi.gov.gn/cadre-juridique/" target="_blank" rel="noopener noreferrer">ANSSI Guinée (nouvel onglet)</a>.</p>
    </section>
    <section id="evolution"><h2>10. Évolution de la politique</h2>
        <p>La politique peut être mise à jour si les formulaires, prestataires ou fonctionnalités évoluent. La date figurant en tête de page identifie cette version. Les questions concernant vos informations peuvent être adressées à l’équipe depuis la <a href="{{ route('contact') }}">page Contact</a>.</p>
    </section>
</x-legal-document>
@endsection
