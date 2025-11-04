<?php
declare(strict_types=1);

function old(array $src, string $key, string $default=''): string {
  return ($src[$key] ?? $default);
}// ré-afficher la dernière valeur saisie (p.ex. old($_POST,'a'))

$errors = [];      //stocke les messages d'erreur par champ (ex: $errors['a'])
$stats  = null; // contiendra les statistiques calculées (min, max, sum, avg, med, nums)

/**
 * Parser de la liste de nombres saisie.
 * - Normaliser les séparateurs (;, retours à la ligne) -> virgules
 * - Découper par virgule, trim, filtrer les vides
 * - Vérifier chaque valeur: numérique -> (float), sinon retourner une erreur
 * - Retourner [array<float> $nums, null] si ok, ou [null, string $message] si erreur
 */
function parse_numbers(string $raw): array {
  // TODO: normaliser les séparateurs ; \n \r -> ','
  

  // TODO: découper, nettoyer, filtrer les éléments vides
 

  $nums = [];
  // TODO: pour chaque vérifier qu'il s'agit de valeur non numérique
  

  // TODO: si pas de nombre valide écrire un msg d'erreur
  return [$nums, null];
}

/**
 * Médiane d'un tableau de floats.
 * - Trier croissant
 * - Si n impair -> élément du milieu
 * - Si n pair -> moyenne des deux éléments centraux
 */
function median(array $xs): float {

  return 0.0; 
}

/** Traitement de la soumission du formulaire */
if () {
  // TODO: récupérer la chaîne 
 
  // TODO: parser la chaine

  // TODO: si pas d'erreurs, calculer les stats: sum, min, max, avg, med
  
  // TODO: stocker pour l'affichage -> $stats
}
?>
<!doctype html>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css"><!-- classe utile: .error, .result-->
<title>Ex3 — Stats</title>
<h1>Ex3 — Statistiques d'une liste</h1>

<!-- Formulaire: textarea 'list' + message d'erreur sous le champ -->
<form >

</form>


 <!-- Bloc résultat: min, max, somme, moyenne, médiane + liste nettoyée -->
