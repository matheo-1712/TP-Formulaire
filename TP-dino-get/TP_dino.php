<?php 
require __DIR__.'/fuzzy_search.php';
function old(array $src, string $key, string $default=''): string {
  return ($src[$key] ?? $default);
} // ré-afficher la dernière valeur saisie (p.ex. old($_POST,'a'))

/* --------- Lecture CSV associatif --------- */
function read_dinos_csv(string $path): array {


  $rows = [];

  return $rows;
}

/* --------- Chargement du CSV --------- */
$data = read_dinos_csv(__DIR__.'/dinos.csv');

/* --------- Récup des filtres GET $q, $category, $era, $wmin, $wmax --------- */


/* -------- Filtrage — implémentation (points-clefs) --------
 * q : fuzzy_match_text sur (name + notes), seuil ajustable (≈0.35).
 * category : whitelist -> map vers Diet attendu.
 * era : comparer directement si non vide.
 * poids : comparer numériquement ; w peut être null ; court-circuiter tôt.
 */
$filtered = array_filter($data, function(array $d) use ($q, $category, $era, $wmin, $wmax) {



    
  // mot-clé dans name ou notes

  // catégorie -> diet
  
  // ère
 
  // poids
  



  return true;
});

/* -------- Tri — ultra-succinct --------
 * Règle : clé primaire = champ choisi ; clé secondaire stable = name (A-Z, case-insensitive).
 * weight : gérer null en dernier idéalement ; sinon le name.
 * era    : alphabétique ; sinon name.
 * name   : alphabétique insensitive (défaut).
 */
usort($filtered, function($a,$b) use ($sort) {
  
  // default: name
  return strcasecmp($a['name'],$b['name']);
});

/* --------- Limite (arraySlice) --------- */


?>
<!doctype html><meta charset="utf-8"><link rel="stylesheet" href="style.css">
<title>DinoGet</title>
<h1>DinoGet</h1>

<!-- Formulaire principal (méthode GET attendue, action = même page) -->

<!-- Champ de recherche (mot-clé) : input type="search", placeholder explicite-->

<!-- Sélecteur de catégorie : select valeurs {tous|herbivores|carnivores etc.} -->

<!-- Sélecteur d’ère géologique : select {Trias|Jurassique|Crétacé} + option vide "Toutes" pour ne pas filtrer -->

<!-- Groupe filtres poids (en tonnes), validation wmin ≤ wmax -->
<!-- Champ poids minimum -->
<!-- Champ poids maximum -->

<!-- Groupe tri & limite -->
<!-- Sélecteur de tri : select {name|era|weight}, tri appliqué après filtrage -->
<!-- Limite de résultats : input type="number" min=1 max=200, défaut = 91, travail côté serveur -->


<!-- Bouton soumettre  -->
<!-- (Optionnel) Bouton réinitialiser -->

<!-- Chaque input est englobé dans un <label> -->


<!----------------------------------------------->
<!--               Bloc résultats              -->
<!----------------------------------------------->



<!-- Bloc résultats : affichage si requête présente ($_GET non vide) -->

<!-- En-tête synthèse de recherche :
     - Afficher le mot-clé
     - Catégorie : valeur fermée (whitelist) 
     - Ère : afficher la valeur ou “toutes” si vide
     - Poids : intervalle wmin..wmax 
     - Tri : rappeler le champ choisi (name|era|weight)
     - Limite 
-->

<!-- Statistiques :
     - Afficher total global des correspondance
-->

<!-- Si des résultats existent : table de données accessible -->
<!-- Table :
     - <thead> avec <th scope="col"> pour Nom, Ère, Régime, Poids (t), Notes.
     - <tbody> : une ligne par dinosaure.
-->
<!-- Si vide :
     - Message court “Aucun résultat avec ces filtres.”
     - Proposer de réinitialiser les filtres (lien/bouton en dehors du présent bloc).
-->



<p><a href="index.php">Retour</a></p>
