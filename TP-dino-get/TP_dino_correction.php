<?php 
require __DIR__.'/fuzzy_search.php';
function old(array $src, string $key, string $default=''): string {
  return ($src[$key] ?? $default);
} // ré-afficher la dernière valeur saisie (p.ex. old($_POST,'a'))

/* --------- Mini data-layer : lecture CSV associatif --------- */
function read_dinos_csv(string $path): array {
  if (!is_readable($path)) return [];
  $fh = fopen($path, 'r');
  if (!$fh) return [];
  $header = fgetcsv($fh, 0, ';');
  if (!$header) { fclose($fh); return []; }
  $rows = [];
  while (($row = fgetcsv($fh, 0, ';')) !== false) {
    if (count($row) !== count($header)) continue;
    $assoc = array_combine($header, $row);
    // normalisation légère
    $assoc['weight_tons'] = is_numeric($assoc['weight_tons'] ?? null) ? (float)$assoc['weight_tons'] : null;
    $rows[] = $assoc;
  }
  fclose($fh);
  return $rows;
}

/* --------- Chargement du CSV --------- */
$data = read_dinos_csv(__DIR__.'/dinos.csv');

/* --------- Récup des filtres GET --------- */
$q        = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? 'tous';        // carnivores / herbivores / omnivore / piscivore / tous
$era      = $_GET['era'] ?? '';                 // Trias / Jurassique / Crétacé / (vide)
$wmin     = ($_GET['wmin'] ?? '') !== '' ? (float)$_GET['wmin'] : null;
$wmax     = ($_GET['wmax'] ?? '') !== '' ? (float)$_GET['wmax'] : null;
$sort     = $_GET['sort'] ?? 'name';            // name / era / weight
$limit    = (int)($_GET['limit'] ?? 20);
if ($limit <= 0 || $limit > 200) $limit = 20;

/* --------- Filtrage --------- */
$filtered = array_filter($data, function(array $d) use ($q, $category, $era, $wmin, $wmax) {



    /*
  // mot-clé dans name ou notes
  if ($q !== '') {
    $hay = mb_strtolower(($d['name'] ?? '').' '.($d['notes'] ?? ''));
    if (mb_strpos($hay, mb_strtolower($q)) === false) return false;
  }
*/

// APRES : recherche floue sur nom + notes
if ($q !== '') {
  $hay = ($d['name'] ?? '').' '.($d['notes'] ?? '');
  if (!fuzzy_match_text($hay, $q, 0.35)) return false;
}


  // catégorie -> diet
  if (in_array($category, ['carnivores','herbivores'], true)) {
    if (($d['diet'] ?? '') !== ($category === 'carnivores' ? 'Carnivore' : 'Herbivore')) return false;
  }
  // ère
  if ($era !== '' && ($d['era'] ?? '') !== $era) return false;
  // poids
  $w = $d['weight_tons'] ?? null;
  if ($wmin !== null && ($w === null || $w < $wmin)) return false;
  if ($wmax !== null && ($w === null || $w > $wmax)) return false;
  return true;
});

/* --------- Tri --------- */
usort($filtered, function($a,$b) use ($sort) {
  if ($sort === 'weight') {
    return ($a['weight_tons'] <=> $b['weight_tons']) ?: strcasecmp($a['name'],$b['name']);
  } elseif ($sort === 'era') {
    return strcasecmp($a['era'],$b['era']) ?: strcasecmp($a['name'],$b['name']);
  }
  // default: name
  return strcasecmp($a['name'],$b['name']);
});

/* --------- Limite --------- */
$total = count($filtered);
$filtered = array_slice($filtered, 0, $limit);
?>
<!doctype html><meta charset="utf-8"><link rel="stylesheet" href="style.css">
<title>Ex1 — GET (recherche)</title>
<h1>Ex1 — GET (recherche)</h1>

<form method="get">
  <label>Mot-clé
    <input type="search" name="q" value="<?= old($_GET,'q') ?>" placeholder="ex: dinosaure, cornes, T-Rex">
  </label>

  <label>Catégorie
    <select name="category">
      <?php foreach (['tous','herbivores','carnivores'] as $c): ?>
        <option value="<?= ($c) ?>" <?= (($_GET['category']??'')===$c?'selected':'') ?>><?= ($c) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>Ère
    <select name="era">
      <option value="">Toutes</option>
      <?php foreach (['Trias','Jurassique','Crétacé'] as $e): ?>
        <option value="<?= ($e) ?>" <?= (($_GET['era']??'')===$e?'selected':'') ?>><?= ($e) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <div style="display:flex; gap:.5rem; align-items:end">
    <label>Poids min (t)
      <input type="number" step="0.001" name="wmin" value="<?= old($_GET,'wmin') ?>">
    </label>
    <label>Poids max (t)
      <input type="number" step="0.001" name="wmax" value="<?= old($_GET,'wmax') ?>">
    </label>
  </div>

  <div style="display:flex; gap:.5rem; align-items:end">
    <label>Trier par
      <select name="sort">
        <?php foreach (['name'=>'Nom','era'=>'Ère','weight'=>'Poids'] as $val=>$lab): ?>
          <option value="<?= ($val) ?>" <?= (($_GET['sort']??'name')===$val?'selected':'') ?>><?= ($lab) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Limite
      <input type="number" min="1" max="200" name="limit" value="<?= old($_GET,'limit','20') ?>">
    </label>
  </div>

  <button>Rechercher</button>
</form>

<?php if (!empty($_GET)): ?>
  <div class="result">
    <p><b>Recherche :</b> “<?= ($q) ?>”, cat. <?= ($category) ?>, ère <?= ($era ?: 'toutes') ?>.
    Poids: <?= (($wmin===null?'—':$wmin).'..'.($wmax===null?'—':$wmax)) ?> —
    Tri: <?= ($sort) ?> — Limite: <?= (int)$limit ?></p>
    <p><b><?= (int)$total ?></b> résultat(s) au total — affichage des <b><?= count($filtered) ?></b> premiers.</p>

    <?php if ($filtered): ?>
      <table border="1" cellpadding="6" cellspacing="0">
        <thead>
          <tr>
            <th>Nom</th><th>Ère</th><th>Régime</th><th>Poids (t)</th><th>Notes</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($filtered as $d): ?>
          <tr>
            <td><?= ($d['name']) ?></td>
            <td><?= ($d['era']) ?></td>
            <td><?= ($d['diet']) ?></td>
            <td><?= ($d['weight_tons'] === null ? '—' : (string)$d['weight_tons']) ?></td>
            <td><?= ($d['notes']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Aucun résultat avec ces filtres.</p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<p><a href="index.php">Retour</a></p>
