<?php
include 'card_functions.php';

// Inicializa o deck se ainda não estiver inicializado
initializeDeck();

// Processa a adição de cartas ao deck
if (isset($_POST['add_card'])) {
    $card_id = $_POST['card_id'];
    $message = addCardToDeck($card_id);
    if ($message !== "Carta adicionada ao deck.") {
        echo "<script>alert('$message');</script>";
    }
}

// Processa a remoção de cartas do deck
if (isset($_POST['remove_card'])) {
    $card_id = $_POST['card_id'];
    removeCardFromDeck($card_id);
}

// Processa a busca de cartas
$search_results = [];
if (isset($_POST['search']) || isset($_POST['add_card'])) {
    $query = $_POST['query'];
    $search_results = searchCards($query);
}

$deck = $_SESSION['deck'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yu-Gi-Oh! Deck Builder</title>
    <style>
        body {
            background: url('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/0d3ef048-d33f-4c3d-b0ca-e7ef0282ba9b/de5amab-3f60a288-a04f-48d6-b463-c3b0ba818d26.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcLzBkM2VmMDQ4LWQzM2YtNGMzZC1iMGNhLWU3ZWYwMjgyYmE5YlwvZGU1YW1hYi0zZjYwYTI4OC1hMDRmLTQ4ZDYtYjQ2My1jM2IwYmE4MThkMjYuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.JR55-nEvJJBw-uJSKql1PqFEJbj5A9Y0XCo5FooY2OA') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
        }
        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 1200px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
        }
        h1 {
            margin-bottom: 20px;
        }
        .main-content, .sidebar {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 3;
        }
        .sidebar {
            flex: 1;
            margin-left: 20px;
        }
        .deck, .search-results {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 20px;
            justify-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: left;
            justify-content: space-between;
            box-sizing: border-box;
            width: 120px;
            min-height: 400px;
            height: 400px;
            overflow: hidden;
        }
        .card img {
            width: 100%;
            height: auto;
            max-height: 180px;
            border-radius: 5px;
            cursor: pointer;
        }
        .card .card-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card p {
            margin: 5px 0;
            text-align: justify;
        }
        .card .card-info {
            margin-top: 10px;
        }
        .card .card-actions {
            margin-top: 10px;
        }
        .card-actions button {
            padding: 8px;
            width: 100%;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        .card-actions button:hover {
            background-color: #555;
        }
        #card-search{
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: left;
            justify-content: space-between;
            box-sizing: border-box;
            width: 120px;
            min-height: 400px;
            height: 400px;
            overflow: hidden;

        }
        .search-container {
            margin-bottom: 20px;
            text-align: left;
        }
        .search-container input {
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
        }
        .search-container button {
            padding: 10px;
            width: 100%;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .search-container button:hover {
            background-color: #555;
        }
    </style>
    <script>
        function addCard(cardId) {
            document.getElementById('add_card_id').value = cardId;
            document.getElementById('add_card_form').submit();
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="main-content">
            <h1>Yu-Gi-Oh! Deck Builder</h1>
            <h2>Deck (<?= count($deck) ?> / 60)</h2>
            <div class="deck">
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
                <?php endforeach; ?>
            </div>
        </div>
        <div class="sidebar">
            <div class="search-container">
                <form method="POST">
                    <input type="text" name="query" placeholder="Pesquisar cartas" value="<?= htmlspecialchars($query ?? '', ENT_QUOTES) ?>" required>
                    <button type="submit" name="search">Pesquisar</button>
                </form>
            </div>
            <div class="search-results">
                <?php foreach ($search_results as $card): ?>
                    <div class="card" id="card-search" onclick="addCard(<?= $card['id'] ?>)">
                        <img src="<?= $card['card_images'][0]['image_url'] ?>" alt="<?= $card['name'] ?>">
                        <div class="card-title"><?= $card['name'] ?></div>
                        <div class="card-info">
                            <p><strong>Tipo:</strong> <?= $card['type'] ?></p>
                            <p><strong>Atributo:</strong> <?= $card['attribute'] ?? 'N/A' ?></p>
                            <p><strong>Nível:</strong> <?= $card['level'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <form id="add_card_form" method="POST" style="display: none;">
        <input type="hidden" id="add_card_id" name="card_id">
        <input type="hidden" name="add_card" value="1">
        <input type="hidden" name="query" value="<?= htmlspecialchars($query ?? '', ENT_QUOTES) ?>">
    </form>
</body>
</html>
