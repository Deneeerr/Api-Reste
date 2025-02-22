<?php
include 'card_functions.php';

// Inicializa o baralho com 5 cartas aleatórias
initializeDeck();

// Adiciona uma nova carta se o botão "Comprar carta" for clicado
if (isset($_POST['buy_card'])) {
    addCardToDeck();
}

// Remove uma carta específica se o botão "Remover" for clicado
if (isset($_POST['remove_card'])) {
    $card_id = $_POST['card_id'];
    removeCardFromDeck($card_id);
}

$deck = $_SESSION['deck'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Yu-Gi-Oh! Deck</title>
</head>
<body>
    <div class="container">
        <h1>Yu-Gi-Oh! Deck</h1>
        <form method="POST">
            <button type="submit" name="buy_card">Comprar carta</button>
        </form>
        <div class="results">
            <?php foreach ($deck as $card): ?>
                <div class="card">
                    <img src="<?= $card['card_images'][0]['image_url'] ?>" alt="<?= $card['name'] ?>">
                    <div class="card-title"><?= $card['name'] ?></div>
                    <div class="card-info">
                        <p><strong>Tipo:</strong> <?= $card['type'] ?></p>
                        <p><strong>Atributo:</strong> <?= $card['attribute'] ?? 'N/A' ?></p>
                        <p><strong>Nível:</strong> <?= $card['level'] ?? 'N/A' ?></p>
                    </div>
                    <div class="card-actions">
                        <form method="POST">
                            <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                            <button type="submit" name="remove_card">Remover</button>
                        </form>
                    </div>
                </div>
                <a href="deck_builder.php">Criador de deck</a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
