
<?php
/* ---------- Fuzzy search helpers ---------- */
function normalize_str(string $s): string {
  $s = mb_strtolower($s, 'UTF-8');
  $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);     // enlève les accents
  $s = preg_replace('/[^a-z0-9\s-]/', ' ', $s);           // garde lettres/chiffres/espaces
  $s = preg_replace('/\s+/', ' ', trim($s));
  return $s;
}

function stem_fr_simple(string $w): string {
  // simplissime: enlève pluriels/s courants (s, es, x) si mot assez long
  if (strlen($w) > 3) {
    $w = preg_replace('/(e?s|x)$/', '', $w); // cornes -> corne ; jeux -> jeu
  }
  return $w;
}

function tokenize(string $s): array {
  $s = normalize_str($s);
  $parts = preg_split('/\s+/', $s);
  $parts = array_values(array_filter(array_map('trim', $parts), fn($t)=>$t!==''));
  return array_map('stem_fr_simple', $parts);
}

/**
 * Retourne true si AU MOINS UN mot de $query est proche d'au moins un mot de $text
 * $max_ratio = distance max autorisée / longueur max des deux mots (0.0 à 1.0)
 */
function fuzzy_match_text(string $text, string $query, float $max_ratio = 0.35): bool {
  $qTokens = tokenize($query);
  if (!$qTokens) return true; // query vide -> match
  $tTokens = tokenize($text);
  if (!$tTokens) return false;

  foreach ($qTokens as $q) {
    $okForThisQ = false;
    foreach ($tTokens as $t) {
      $maxLen = max(strlen($q), strlen($t));
      if ($maxLen === 0) continue;
      $dist = levenshtein($q, $t);
      $ratio = $dist / $maxLen;
      if ($ratio <= $max_ratio) { // assez proche
        $okForThisQ = true;
        break;
      }
      // bonus: correspondance exacte de sous-chaîne (après normalisation+stemming)
      if ($q !== '' && str_contains($t, $q)) {
        $okForThisQ = true;
        break;
      }
    }
    if (!$okForThisQ) return false; // ce mot de la requête n'a pas trouvé d'équivalent
  }
  return true; // tous les mots de la requête ont matché "assez bien"
}
?>