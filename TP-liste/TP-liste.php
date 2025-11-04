<?php
declare(strict_types=1);

function old(array $src, string $key, string $default = ''): string
{
    return ($src[$key] ?? $default);
}// ré-afficher la dernière valeur saisie (p.ex. old($_POST,'a'))

$errors = [];      //stocke les messages d'erreur par champ (ex: $errors['a'])
$stats = null; // contiendra les statistiques calculées (min, max, sum, avg, med, nums)

/**
 * Parser de la liste de nombres saisie.
 * - Normaliser les séparateurs (;, retours à la ligne) -> virgules
 * - Découper par virgule, trim, filtrer les vides
 * - Vérifier chaque valeur: numérique -> (float), sinon retourner une erreur
 * - Retourner [array<float> $nums, null] si ok, ou [null, string $message] si erreur
 */
function parse_numbers(string $raw): array
{
    // Normaliser les séparateurs ; \n \r -> ','
    $raw = str_replace([';', "\n", "\r"], ',', $raw);

    // Découper, nettoyer, filtrer les éléments vides
    $parts = array_filter(array_map('trim', explode(',', $raw)));

    $nums = [];
    // Pour chaque valeur vérifier qu'il s'agit bien d'un nombre
    foreach ($parts as $part) {
        if (!is_numeric($part)) {
            return [null, "La valeur '$part' n'est pas un nombre valide"];
        }
        $nums[] = (float)$part;
    }

    // Si pas de nombre valide écrire un msg d'erreur
    if (empty($nums)) {
        return [null, 'Veuillez saisir des nombres'];
    }
    return [$nums, null];
}

/**
 * Médiane d'un tableau de floats.
 * - Trier croissant
 * - Si n impair -> élément du milieu
 * - Si n pair -> moyenne des deux éléments centraux
 */
function median(array $xs): float
{
    sort($xs);
    $n = count($xs);
    $mid = floor($n / 2);

    if ($n % 2 == 0) {
        return ($xs[$mid - 1] + $xs[$mid]) / 2;
    }
    return $xs[$mid];
}

/** Traitement de la soumission du formulaire */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // On instancie les variables
    $avg = 0.0;
    $med = 0.0;
    $min = 0.0;
    $max = 0.0;
    $sum = 0.0;

    // Récupérer la chaîne
    $liste = $_POST['list'];

    // Parser la chaine
    [$nums, $error] = parse_numbers($liste);

    // Si pas d'erreurs, calculer les stats: sum, min, max, avg, med
    if ($error === null) {
        $avg = $nums ? array_sum($nums) / count($nums) : 0.0;
        $med = median($nums);
        $min = min($nums);
        $max = max($nums);
        $sum = array_sum($nums);
    }

    // Stocker pour l'affichage -> $stats
    $stats = [
            'avg' => $avg,
            'med' => $med,
            'min' => $min,
            'max' => $max,
            'sum' => $sum,
            'nums' => $nums
    ];

    $out = $stats;
}
?>
    <!doctype html>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css"><!-- classe utile: .error, .result-->
    <title>Ex3 — Stats</title>
    <h1>Ex3 — Statistiques d'une liste</h1>

    <!-- Formulaire: textarea 'list' + message d'erreur sous le champ -->
    <form method="post">
        <span>Format attendu liste de nombres "10,16,45,85,21,58" par exemple</span>
        <label>
            <textarea name="list" placeholder="10,16,45,85,21,58"><?= old($_POST, 'list') ?></textarea>
        </label>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif ?>
        <input type="submit" value="Soumettre">
    </form>

    <!-- Bloc résultat: min, max, somme, moyenne, médiane + liste nettoyée -->
<?php if (isset($out) && !isset($error)): ?>
    <div class="result">
        <p>Minimum: <?= $out['min'] ?></p>
        <p>Maximum: <?= $out['max'] ?></p>
        <p>Somme: <?= $out['sum'] ?></p>
        <p>Moyenne: <?= $out['avg'] ?></p>
        <p>Médiane: <?= $out['med'] ?></p>
        <p>Liste: <?= implode(', ', $out['nums']) ?></p>
    </div>
<?php endif; ?>