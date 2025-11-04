<?php
declare(strict_types=1);

function old(array $src, string $key, string $default=''): string {
  return ($src[$key] ?? $default);
} // ré-afficher la dernière valeur saisie (p.ex. old($_POST,'a'))

$errors = [];      //stocke les messages d'erreur par champ (ex: $errors['a'])
$out = null;    //stocke le resultat si tout est valide

//traiter le formulaire uniquement si la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On récupére les valeurs saisies par l'utilisateur'
    $a = old($_POST, 'a');
    $b = old($_POST, 'b');
    $op = old($_POST, 'op');

    // valider a et b (non vide ET numérique)
    if (!is_numeric($a) || !is_numeric($b)) {
        $errors['ab'] = 'Veuillez saisir des nombres';
    }
    // limiter l'opérateur à + - * /
    if (!in_array($op, ['+', '-', '*', '/'])) {
        $errors['op'] = 'Veuillez choisir un opérateur valide';
    }

    // Vérifier division par zéro
    if ($op == '/' && $b == '0') {
        $errors['b'] = 'Division par zéro impossible';
    }

    // Si pas d'erreurs, effectuer le calcul
    if (empty($errors)) {
        $a = floatval($a);
        $b = floatval($b);
        switch ($op) {
            case '+':
                $out = $a + $b;
                break;
            case '-':
                $out = $a - $b;
                break;
            case '*':
                $out = $a * $b;
                break;
            case '/':
                $out = $a / $b;
                break;
        }
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">
<title>Mini-calculatrice</title>
<h1>Mini-calculatrice</h1>

<!-- Créer un formulaire vers cette page -->
<form action="TP_calc.php" method="post">
    <!--champ A + message d'erreur sous le champ -->
    <input type="text" name="a" value="<?php echo old($_POST, 'a'); ?>">
    <section class="error"><?php echo $errors['ab'] ?? ''; ?></section>
    <!-- select pour l'opérateur limité à + - * / -->
    <label>
        <select name="op">
            <option value="+" <?php if (old($_POST, 'op') == '+') echo 'selected'; ?>>+</option>
            <option value="-" <?php if (old($_POST, 'op') == '-') echo 'selected'; ?>>-</option>
            <option value="*" <?php if (old($_POST, 'op') == '*') echo 'selected'; ?>>*</option>
            <option value="/" <?php if (old($_POST, 'op') == '/') echo 'selected'; ?>>/</option>
        </select>
    </label>
    <section class="error"><?php echo $errors['op'] ?? ''; ?></section>
    <!-- champ B + message d'erreur sous le champ -->
    <input type="text" name="b" value="<?php echo old($_POST, 'b'); ?>">
    <section class="error"><?php echo $errors['b'] ?? ''; ?></section>
    <!-- Bouton de soumission -->
    <input type="submit" value="Calculer">
    <!-- Afficher le résultat ici, si présent -->
    <?php if (isset($out)): ?>
        <p>Résultat: <?php echo $out; ?></p>
    <?php endif; ?>
</form>

