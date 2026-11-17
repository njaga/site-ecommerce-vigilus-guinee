@extends('layouts.store')
@section('title', 'Mentions légales | Vigilus Guinée')
@section('meta_description', 'Identité de l’exploitant, hébergement, propriété intellectuelle et informations sur le site Vigilus Guinée.')
@section('canonical', route('legal.mentions'))
@section('robots', config('site.privacy_approved') ? 'index,follow' : 'noindex,follow')
@section('content')
<x-legal-document title="Mentions légales" intro="Les informations relatives à l’édition du site Vigilus Guinée et à son utilisation." :approved="config('site.privacy_approved')" :sections="['editeur' => 'Éditeur du site', 'publication' => 'Publication & contact', 'hebergement' => 'Hébergement', 'activite' => 'Activité du site', 'propriete' => 'Marques & contenus', 'utilisation' => 'Utilisation du site', 'liens' => 'Liens externes', 'donnees' => 'Données personnelles', 'cadre' => 'Cadre applicable']">
    <section id="editeur"><h2>01. Éditeur du site</h2>
        <p>Vigilus Guinée est la dénomination utilisée sur ce site pour l’activité de distribution d’équipements de sécurité électronique à Conakry, au sein du réseau Vigilus Groupe SA. L’identité juridique de l’entité qui exploite le site et établit les documents commerciaux est précisée ci-dessous.</p>
        <dl class="legal-facts">
            @foreach(['legal_name' => 'Raison sociale', 'legal_form' => 'Forme juridique', 'legal_capital' => 'Capital social', 'legal_address' => 'Siège social', 'legal_registration' => 'Immatriculation / RCCM', 'legal_tax_id' => 'Identification fiscale'] as $key => $label)
                <div><dt>{{ $label }}</dt><dd>{{ config('site.'.$key) ?: 'Information à compléter' }}</dd></div>
            @endforeach
        </dl>
        <p>Les mentions « Vigilus Groupe SA » et les implantations présentées sur le site décrivent le Groupe. Les coordonnées et responsabilités contractuelles de chaque entité figurent sur ses propres devis, contrats et factures.</p>
    </section>
    <section id="publication"><h2>02. Publication et contact</h2>
        <dl class="legal-facts"><div><dt>Responsable de publication</dt><dd>{{ config('site.publication_director') ?: 'Information à compléter' }}</dd></div><div><dt>Email</dt><dd>{{ config('site.email') ?: 'Adresse à compléter' }}</dd></div><div><dt>Téléphone</dt><dd>{{ config('site.phone') ?: '+224 · numéro complet à venir' }}</dd></div></dl>
        <p>Pour une question sur un produit, une correction de contenu ou une demande commerciale, utilisez la <a href="{{ route('contact') }}">page Contact</a>. Merci de préciser la page concernée et l’objet de votre demande. Aucun mot de passe, code d’accès ou document confidentiel n’est nécessaire pour signaler une erreur.</p>
    </section>
    <section id="hebergement"><h2>03. Hébergement</h2>
        <dl class="legal-facts"><div><dt>Hébergeur</dt><dd>{{ config('site.hosting_name') ?: 'Prestataire de production à confirmer' }}</dd></div><div><dt>Adresse de l’hébergeur</dt><dd>{{ config('site.hosting_address') ?: 'Information à compléter' }}</dd></div><div><dt>Contact de l’hébergeur</dt><dd>{{ config('site.hosting_contact') ?: 'Information à compléter' }}</dd></div></dl>
    </section>
    <section id="activite"><h2>04. Activité du site</h2>
        <p>Le site présente les équipements Dahua, les informations du Groupe et les formations destinées aux installateurs et aux entreprises. Il permet de rechercher une référence, de demander un devis et de transmettre une demande de commande ou de formation.</p>
        <p>Vigilus Guinée se présente comme le représentant officiel exclusif de Dahua en Guinée. Les certifications présentées concernent les systèmes de management de Vigilus Groupe SA ; elles ne constituent pas une certification individuelle de chaque produit ou de chaque participant à une formation.</p>
        <p>Aucun paiement en ligne n’est proposé. La réception d’un formulaire ne vaut pas confirmation définitive d’une vente ou d’une inscription. Consultez les <a href="{{ route('legal.sales') }}">conditions commerciales</a> pour le détail du parcours.</p>
    </section>
    <section id="propriete"><h2>05. Marques, images et contenus</h2>
        <p>Les marques, logos, photographies, documents techniques et autres contenus restent la propriété de leurs titulaires respectifs. Leur présence sur le site ne transfère aucun droit de propriété intellectuelle au visiteur. Dahua et les marques du Groupe sont présentées pour identifier les équipements et les activités concernés.</p>
        <p>Les contenus peuvent être consultés pour préparer un projet ou une demande. Toute réutilisation commerciale, republication ou modification doit respecter les droits applicables et, lorsque nécessaire, faire l’objet d’une autorisation du titulaire. Les liens vers les fiches constructeur permettent de consulter la documentation de la référence concernée.</p>
    </section>
    <section id="utilisation"><h2>06. Utilisation et disponibilité</h2>
        <p>Les informations du catalogue évoluent avec les références, les stocks et les données disponibles. Vérifiez auprès de l’équipe la compatibilité et les caractéristiques nécessaires à votre projet avant de confirmer une acquisition. Un visuel d’illustration ou un emplacement « Visuel à venir » ne remplace pas la photographie contractuelle du modèle.</p>
        <p>L’accès peut être temporairement interrompu pour maintenance ou en cas d’incident. Les visiteurs doivent utiliser le site sans perturber son fonctionnement ni tenter d’accéder à des espaces ou à des données non autorisés. Ces indications n’écartent pas les obligations et responsabilités prévues par la réglementation applicable.</p>
    </section>
    <section id="liens"><h2>07. Liens et services externes</h2>
        <p>Le site peut orienter vers Dahua, les sites du Groupe, ISO, Google Maps ou WhatsApp. Ces services disposent de leurs propres conditions d’utilisation et politiques de confidentialité. Les informations sur les implantations n’impliquent pas qu’une commande passée auprès de Vigilus Guinée soit prise en charge par une autre filiale.</p>
    </section>
    <section id="donnees"><h2>08. Données personnelles</h2>
        <p>La <a href="{{ route('legal.privacy') }}">politique de confidentialité</a> détaille les données des formulaires, le panier, la mémorisation de la devise, les destinataires et les moyens d’exercer vos droits. Les données de carte bancaire ne sont pas demandées sur ce site.</p>
    </section>
    <section id="cadre"><h2>09. Cadre applicable et mises à jour</h2>
        <p>L’activité du site s’inscrit dans le cadre juridique applicable en République de Guinée, notamment en matière de transactions électroniques et de protection des données personnelles. Les textes de référence sont accessibles auprès de l’<a href="https://anssi.gov.gn/cadre-juridique/" target="_blank" rel="noopener noreferrer">ANSSI Guinée (nouvel onglet)</a>.</p>
        <p>Ces mentions peuvent évoluer pour refléter un changement d’exploitant, d’hébergeur ou de fonctionnement du site. La date de version figure en tête de page. Pour toute question, contactez l’équipe avant de transmettre votre demande.</p>
    </section>
</x-legal-document>
@endsection
