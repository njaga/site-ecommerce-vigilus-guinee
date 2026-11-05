<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class EditorialBlogSeeder extends Seeder
{
    public function run(): void
    {
        // Contenus d'amorçage : brouillons uniquement.
        // Ne jamais publier sans photo autorisée et relecture.
        $drafts = [
            [
                'title' => 'Choisir ses caméras de vidéosurveillance à Conakry',
                'slug' => 'choisir-cameras-videosurveillance-conakry',
                'category' => 'Conseils',
                'excerpt' => 'Caméras IP, visibilité de nuit, emplacement et compatibilité : les critères utiles pour préparer son projet de vidéosurveillance.',
                'body' => <<<'MD'
## Commencer par définir les zones à surveiller

Avant de comparer les références, définissez les espaces que vous souhaitez observer : entrée, accueil, stockage, circulation ou périmètre extérieur. Les besoins ne sont pas identiques. Une caméra destinée à un couloir n'aura pas nécessairement les mêmes caractéristiques qu'un modèle placé à l'extérieur.

La première question n'est donc pas « combien de caméras ? », mais **quelles zones doivent être couvertes et dans quelles conditions ?** Un plan simple des lieux aide à préciser les angles, les distances et les obstacles.

## Résolution et conditions d'éclairage

Une définition élevée n'est utile que si le champ de vision, l'optique et l'éclairage correspondent à l'usage. Comparez la résolution, l'objectif, les conditions de basse lumière et les fonctions annoncées sur la fiche technique du modèle exact.

Pour les entrées exposées à des contrastes importants ou les zones faiblement éclairées, demandez les caractéristiques de gestion de contre-jour et d'éclairage nocturne. Une photographie commerciale ne suffit pas à juger du résultat attendu.

## Caméra, réseau et enregistrement

Une caméra IP doit être envisagée dans son environnement : réseau local, alimentation, bande passante, enregistreur et espace de stockage. Les équipements doivent être compatibles entre eux. Pour une extension d'un système existant, vérifiez les références et les protocoles effectivement pris en charge.

## Préparer votre liste d'équipements

Pour recevoir un devis pertinent, rassemblez les informations suivantes :

- Nombre de zones à couvrir et type d'environnement.
- Besoin intérieur ou extérieur.
- Conditions d'éclairage.
- Enregistrement souhaité et durée de conservation envisagée.
- Réseau et équipements déjà présents sur le site.

La durée de conservation des images et l'accès aux enregistrements doivent être définis selon les obligations applicables à votre activité.

Consultez le [catalogue des équipements](/produits) ou [transmettez votre besoin](/demande-de-devis) à l'équipe Vigilus Guinée.
MD,
            ],
            [
                'title' => 'NVR, XVR et stockage : bien dimensionner son enregistrement',
                'slug' => 'nvr-xvr-stockage-videosurveillance',
                'category' => 'Guide technique',
                'excerpt' => 'Canaux, capacité, compatibilité et conservation : comprendre les points qui influencent le choix d’un enregistreur.',
                'body' => <<<'MD'
## À quoi sert un enregistreur ?

Dans un système de vidéosurveillance, l'enregistreur centralise les flux compatibles et organise leur conservation. Il peut aussi faciliter la consultation des images selon les fonctions prévues par le fabricant.

Les gammes **NVR** sont généralement conçues pour les flux vidéo issus de caméras réseau IP. Les **XVR** peuvent répondre à des environnements combinant certains formats de caméras analogiques et IP, selon leurs caractéristiques exactes. Avant de choisir, il faut donc identifier les technologies utilisées sur le site.

## Ne pas se limiter au nombre de canaux

Un modèle annoncé pour plusieurs canaux ne garantit pas automatiquement la compatibilité avec chaque caméra. Vérifiez la résolution prise en charge, le débit total, le type de compression et les fonctions d'analyse souhaitées. Ces paramètres influencent la sélection du matériel et son évolution future.

## Prévoir la capacité de stockage

La capacité nécessaire dépend notamment de la résolution, de la fréquence d'images, de la compression, du nombre de caméras, des horaires d'enregistrement et de la durée de conservation attendue.

Un dimensionnement sérieux tient compte des réglages réellement prévus. Il est préférable de demander une estimation adaptée à la configuration plutôt que d'appliquer une durée universelle à tous les sites.

## Vérifications utiles avant le devis

Préparez la liste des caméras existantes, le nombre de voies à ajouter, les fonctions de consultation souhaitées et les contraintes de connexion. Vérifiez aussi les modalités d'accès aux images et les obligations relatives à leur conservation.

Notre équipe peut vous accompagner dans la sélection de références. Parcourez le [catalogue Dahua](/produits) ou [demandez un devis](/demande-de-devis) avec vos quantités et besoins.
MD,
            ],
            [
                'title' => 'Badge, code ou biométrie : quel contrôle d’accès choisir ?',
                'slug' => 'badge-code-biometrie-controle-acces',
                'category' => 'Conseils',
                'excerpt' => 'Chaque technologie répond à des contraintes différentes. Voici les questions à poser avant de retenir un système de contrôle d’accès.',
                'body' => <<<'MD'
## Identifier les accès à protéger

Le contrôle d'accès permet de définir qui peut entrer dans une zone et à quelles conditions. Les besoins d'un petit bureau, d'un site industriel et d'un bâtiment accueillant du public peuvent être différents.

Avant de choisir une technologie, identifiez les portes concernées, les catégories d'utilisateurs, les horaires, les besoins de traçabilité et les possibilités de secours en cas d'incident.

## La simplicité des badges

Les badges peuvent faciliter l'attribution et la révocation de droits dans des environnements où les utilisateurs sont nombreux. Le choix du lecteur et du support doit être cohérent avec la compatibilité et les protections proposées par le fabricant.

La gestion des badges perdus, prêtés ou non restitués mérite une procédure claire.

## Codes : usage pratique et discipline

Un clavier à code peut convenir à certains accès ponctuels ou collectifs. Il impose toutefois une politique de création, de changement et de protection des codes. Un code connu de plusieurs personnes n'apporte pas la même traçabilité qu'un identifiant individuel.

## Biométrie : besoins et vigilance

La biométrie peut répondre à certains besoins précis, mais elle implique une analyse renforcée des données personnelles, des droits des utilisateurs et du cadre réglementaire applicable. Il ne faut pas choisir cette option simplement parce qu'elle paraît plus moderne.

## Choisir sans suréquiper

L'installation physique, l'alimentation, les mécanismes de secours, la gestion des accès et la maintenance future comptent autant que le type d'identification.

Pour préparer votre choix, détaillez les zones, les utilisateurs, la fréquence de passage et les équipements déjà présents. [Consultez les références](/produits) ou [contactez Vigilus Guinée](/demande-de-devis) pour une proposition adaptée.
MD,
            ],
        ];

        foreach ($drafts as $draft) {
            BlogPost::firstOrCreate(
                ['slug' => $draft['slug']],
                array_merge($draft, [
                    'status' => 'draft',
                    'is_featured' => false,
                    'published_at' => null,
                    'cover_path' => null,
                ]),
            );
        }
    }
}
