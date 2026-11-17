@extends('layouts.store')
@section('title', 'Conditions commerciales | Vigilus Guinée')
@section('meta_description', 'Modalités des devis, demandes de commande, prix GNF et conversions indicatives, livraison, garanties, retours et formations Vigilus Guinée.')
@section('canonical', route('legal.sales'))
@section('robots', config('site.sales_approved') ? 'index,follow' : 'noindex,follow')
@section('content')
<x-legal-document title="Conditions commerciales" intro="Les étapes et les modalités de vos demandes d’équipements et de formation auprès de Vigilus Guinée." :approved="config('site.sales_approved')" :sections="['objet' => 'Objet & interlocuteur', 'catalogue' => 'Produits & compatibilité', 'prix' => 'Prix & devises', 'devis' => 'Demande de devis', 'commande' => 'Demande de commande', 'reglement' => 'Règlement', 'livraison' => 'Livraison & réception', 'garanties' => 'Garanties & assistance', 'retours' => 'Retours & annulations', 'formations' => 'Formations', 'reclamations' => 'Réclamations', 'documents' => 'Documents & mises à jour']">
    <section id="objet"><h2>01. Objet et interlocuteur commercial</h2>
        <p>Ces conditions décrivent le fonctionnement du catalogue et des demandes effectuées sur le site Vigilus Guinée, pour les particuliers, les installateurs et les entreprises. Le vendeur ou prestataire contractant est {{ config('site.legal_name') ?: 'l’entité exploitante dont l’identité juridique reste à compléter dans les mentions légales' }}.</p>
        <p>Le site permet d’initier une demande. Les modalités propres à votre projet sont ensuite précisées dans une proposition commerciale. Les règles impératives applicables, notamment celles protégeant les consommateurs lorsque vous agissez à titre personnel, restent applicables.</p>
    </section>
    <section id="catalogue"><h2>02. Produits, documentation et compatibilité</h2>
        <p>Le catalogue regroupe des équipements de vidéosurveillance, d’enregistrement, de contrôle d’accès, d’alarme et leurs accessoires. Les caractéristiques doivent être appréciées pour la référence exacte : variantes, alimentation, stockage, licences et accessoires peuvent différer d’un modèle à l’autre.</p>
        <p>Avant validation, précisez vos besoins techniques, la configuration existante et les contraintes du lieu d’installation. L’équipe peut vous orienter vers une référence adaptée. Une photo d’illustration, un conseil général ou un article du journal ne remplace pas la validation technique du projet. Une installation, un paramétrage ou une maintenance ne sont inclus que s’ils figurent expressément dans la proposition.</p>
    </section>
    <section id="prix"><h2>03. Prix, devises et frais</h2>
        <p>Les montants de référence du site sont exprimés en <strong>francs guinéens (GNF)</strong>. Les prix en dollars américains et en euros sont des estimations calculées à partir de taux fixes. Ils sont précédés du signe « ≈ » ; la date des taux est indiquée lors de leur affichage. Il ne s’agit pas d’un service de change en temps réel.</p>
        <p>Le choix d’une devise d’affichage ne modifie ni le montant enregistré de la demande ni la devise de règlement convenue avec l’équipe. Les références « sur devis » nécessitent une proposition personnalisée.</p>
        <p>Le devis précise le prix applicable, les taxes, les frais de livraison et les éventuelles prestations supplémentaires. Le sous-total du panier correspond aux équipements sélectionnés ; il ne permet pas de déduire que les frais annexes sont inclus ou que la livraison est gratuite. Toute erreur de prix doit être clarifiée avec vous avant confirmation.</p>
    </section>
    <section id="devis"><h2>04. Demande de devis</h2>
        <p>Vous pouvez demander un devis pour un produit, une quantité ou un besoin général. Décrivez le projet et fournissez des coordonnées permettant de vous joindre. L’envoi du formulaire enregistre votre demande ; il ne constitue ni une acceptation de prix ni une facture.</p>
        <p>Après étude, l’équipe confirme les références, les quantités, les disponibilités, le prix, les modalités de règlement et la durée de validité de l’offre. Une modification du périmètre peut nécessiter une nouvelle proposition. La durée de validité est celle indiquée sur le devis concerné.</p>
    </section>
    <section id="commande"><h2>05. De la sélection à la confirmation</h2>
        <ol><li>Ajoutez les références disponibles au panier et vérifiez les quantités.</li><li>Renseignez vos coordonnées et les informations de livraison.</li><li>Transmettez la demande et conservez sa référence.</li><li>L’équipe vérifie les stocks et les modalités, puis vous recontacte pour obtenir votre accord.</li></ol>
        <p>La soumission du panier ne réserve pas automatiquement le stock, ne déclenche aucun paiement et ne constitue pas une facture. La réservation et la confirmation interviennent lors du traitement commercial. Une confirmation de commande ne signifie pas qu’un règlement a été encaissé.</p>
        <p>En cas d’indisponibilité ou de changement nécessaire, une solution doit être discutée avec vous avant finalisation. Les informations de suivi, lorsqu’un lien vous est remis, décrivent l’état du dossier et doivent être conservées de manière privée.</p>
    </section>
    <section id="reglement"><h2>06. Modalités de règlement</h2>
        <p>Le site ne collecte pas les données de carte bancaire et ne propose pas de paiement en ligne. Les moyens de règlement, coordonnées du bénéficiaire, échéances et éventuels acomptes sont précisés par l’équipe dans les documents commerciaux.</p>
        <p>Avant un transfert, vérifiez la cohérence du bénéficiaire avec votre devis et faites confirmer toute modification de coordonnées par votre interlocuteur habituel. Les frais bancaires ou de conversion éventuels doivent être clarifiés avant règlement.</p>
    </section>
    <section id="livraison"><h2>07. Livraison et réception</h2>
        <p>{{ config('site.delivery_terms') ?: 'La zone desservie, le mode de livraison ou de retrait, les frais et les délais sont confirmés pour chaque demande avant sa finalisation. Aucun délai uniforme ni livraison gratuite ne sont annoncés à ce stade.' }}</p>
        <p>Vérifiez l’adresse et les conditions d’accès au lieu de livraison. Signalez dès que possible un changement d’adresse ou une indisponibilité afin que l’équipe puisse étudier les conséquences sur l’organisation et les frais.</p>
        <p>À la réception, vérifiez les références, les quantités et l’état apparent des colis. En cas d’anomalie, conservez les emballages et justificatifs utiles et contactez l’équipe avec des photos et la référence du dossier. Ces indications pratiques ne limitent pas vos droits prévus par la réglementation applicable.</p>
    </section>
    <section id="garanties"><h2>08. Garanties et assistance</h2>
        <p>{{ config('site.warranty_terms') ?: 'La durée, le périmètre et les modalités de garantie commerciale doivent être précisés dans la proposition et les documents fournis avec les équipements. Aucune durée générale de garantie ne peut être annoncée sans validation du modèle et du circuit de distribution.' }}</p>
        <p>Pour une demande d’assistance, préparez la référence, le numéro de série si disponible, la preuve d’achat et une description du problème. L’équipe précise la marche à suivre après examen du dossier. Les mises à jour, interventions sur site, consommables ou prestations de maintenance relèvent des modalités expressément convenues.</p>
        <p>Les conditions commerciales de garantie ne remplacent pas les garanties ou recours qui s’appliquent obligatoirement en vertu du droit en vigueur.</p>
    </section>
    <section id="retours"><h2>09. Retours, échanges et annulations</h2>
        <p>{{ config('site.returns_terms') ?: 'Les modalités de retour, d’échange et d’annulation, ainsi que les frais éventuels, doivent être confirmées par l’exploitant avant la finalisation de la vente. Aucun délai ou forfait universel n’est fixé dans cette version.' }}</p>
        <p>Si vous souhaitez modifier ou annuler une demande en cours, contactez rapidement l’équipe avec sa référence. Avant d’expédier un équipement, demandez les instructions de retour et l’adresse à utiliser. Conservez les accessoires, emballages et documents utiles au traitement du dossier.</p>
        <p>Les modalités applicables peuvent dépendre du produit, de son état, d’une personnalisation ou d’une commande spécifique. Les droits impératifs applicables à votre situation restent préservés.</p>
    </section>
    <section id="formations"><h2>10. Sessions et formations en entreprise</h2>
        <p>La page Formations présente les sessions publiées à venir et permet de demander une session adaptée à une entreprise. Le formulaire exprime un intérêt ou un besoin ; il ne garantit pas à lui seul une place ni une date définitive.</p>
        <p>Le programme, les prérequis, le lieu, le nombre de participants, le tarif éventuel, les équipements nécessaires et les modalités de report ou d’annulation sont confirmés avant l’engagement. Une attestation ou une certification de participant n’est incluse que si cela est explicitement annoncé dans le programme validé. Les certifications ISO du Groupe ne constituent pas une certification des stagiaires.</p>
    </section>
    <section id="reclamations"><h2>11. Réclamations et règlement des difficultés</h2>
        <p>Adressez votre demande à {{ config('site.email') ?: 'notre équipe depuis la page Contact' }} en indiquant la référence du devis ou de la commande, l’objet de la difficulté et les justificatifs utiles. L’équipe pourra examiner le dossier et rechercher une solution avec vous.</p>
        <p>Cette démarche amiable ne vous prive pas des recours ouverts par le droit applicable. Aucune clause de compétence exclusive ni renonciation générale à vos droits n’est ajoutée à cette version.</p>
    </section>
    <section id="documents"><h2>12. Documents contractuels et mises à jour</h2>
        <p>Conservez votre devis accepté, les confirmations, les justificatifs et les conditions communiquées lors de l’achat. Une mise à jour du site ne modifie pas à elle seule les modalités déjà convenues pour une commande.</p>
        <p>Les informations sur l’entité exploitante figurent dans les <a href="{{ route('legal.mentions') }}">mentions légales</a>. L’utilisation des informations de votre dossier est expliquée dans la <a href="{{ route('legal.privacy') }}">politique de confidentialité</a>. Pour préciser une condition avant votre demande, <a href="{{ route('contact') }}">contactez notre équipe</a>.</p>
    </section>
</x-legal-document>
@endsection
