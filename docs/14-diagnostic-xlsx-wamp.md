# Diagnostic XLSX — WAMP

Le message générique « Le fichier XLSX ne peut pas être lu » provenait d'un catch `Throwable` qui masquait entièrement la cause. Le chargeur journalise maintenant une exception limitée/sanitizée dans les logs. Sur `APP_ENV=local`, le formulaire affiche aussi la classe de l'erreur et une courte description.

## Vérification directe avant import

Dans PowerShell :

```powershell
php -v
php --ini
php -r "echo class_exists('ZipArchive') ? 'ZIP OK' : 'ZIP MANQUANT';"
```

Si ZIP manque, rechercher dans la configuration PHP retournée par `php --ini` :

```ini
extension=zip
```

Dans WAMP, le PHP CLI et le PHP d'Apache peuvent utiliser des `php.ini` distincts : vérifier les **deux**, redémarrer WAMP/Apache et ouvrir un nouveau terminal.

## Tester le classeur sans l'importer

Après avoir récupéré les modifications de la branche, exécuter :

```powershell
php artisan catalogue:diagnostic-xlsx "C:\Users\VotreCompte\Downloads\Catalogue_Dahua_Vigilus_Guinee_Import_CORRIGE.xlsx"
```

La commande vérifie : présence du fichier, extension ZIP, structure de l'archive, lecture par le **même lecteur PHPSpreadsheet** que l'importateur, nom `Data Sheet` et en-têtes exacts. **Aucune donnée du classeur n'est imprimée, sauvegardée ou importée en base**.

Si elle échoue, copier seulement les lignes `Échec PHPSpreadsheet` / code et message d'erreur. Ne jamais envoyer des coûts fournisseur ou le BOQ confidentiel aux logs ou au dépôt GitHub.

Si la commande réussit alors que le formulaire web échoue, comparer le `php.ini` CLI avec celui utilisé par Apache et vérifier la limite `upload_max_filesize` / `post_max_size` ainsi que les permissions du dossier temporaire PHP. Réessayer l'import web puis lire les avertissements `catalogue.xlsx.read_failed` dans `storage/logs/laravel.log`.

## Contournement manuel éventuel

Si la lecture d'un classeur généré par un outil externe est rejetée alors que ZIP est disponible, ouvrir le fichier avec **Microsoft Excel**, puis utiliser **Enregistrer sous → Classeur Excel (*.xlsx)**, sans ajouter, copier ni déplacer de colonnes. Cette opération peut normaliser le conditionnement OOXML. Refaire le diagnostic avant de tenter l'import.

## Confidentialité

L'importateur conserve uniquement B, C, D, E, F, M et O pour les références. Aucune quantité d'achat, remise, prix fournisseur, fichier d'origine ou feuille complète n'est conservée.
