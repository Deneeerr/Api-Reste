<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para buscar cartas de tipos específicos
function getCards($num = 0) {
    $api_url = "https://db.ygoprodeck.com/api/v7/cardinfo.php";
    $data = json_decode(file_get_contents($api_url), true);
    
    $valid_types = ['Effect Monster', 'Normal Monster', 'Spell Card', 'Trap Card'];
    
    if (isset($data['data'])) {
        $cards = array_filter($data['data'], function($card) use ($valid_types) {
            return in_array($card['type'], $valid_types);
        });
        $cards = array_values($cards); // Reindexar o array após o filtro
        if ($num > 0) {
            shuffle($cards);
            return array_slice($cards, 0, $num);
        }
        return $cards;
    }
    return [];
}

// Função para inicializar o baralho com 5 cartas aleatórias
function initializeDeck() {
    if (!isset($_SESSION['deck']) || empty($_SESSION['deck'])) {
        $_SESSION['deck'] = [];
    }
}

// Função para adicionar uma carta ao baralho
function addCardToDeck($card_id) {
    $deck = $_SESSION['deck'];
    $card_count = array_count_values(array_column($deck, 'id'))[$card_id] ?? 0;
    
    if ($card_count >= 3) {
        return "Você não pode ter mais de 3 cópias da mesma carta.";
    }
    
    if (count($deck) >= 60) {
        return "Você não pode ter mais de 60 cartas no deck.";
    }
    
    $all_cards = getCards();
    foreach ($all_cards as $card) {
        if ($card['id'] == $card_id) {
            $_SESSION['deck'][] = $card;
            break;
        }
    }
    return "Carta adicionada ao deck.";
}

// Função para remover uma carta do baralho
function removeCardFromDeck($card_id) {
    foreach ($_SESSION['deck'] as $key => $card) {
        if ($card['id'] == $card_id) {
            unset($_SESSION['deck'][$key]);
            $_SESSION['deck'] = array_values($_SESSION['deck']);
            break;
        }
    }
}

// Função para buscar cartas pelo nome
function searchCards($query) {
    $all_cards = getCards();
    $query = strtolower($query);
    return array_filter($all_cards, function($card) use ($query) {
        return strpos(strtolower($card['name']), $query) !== false;
    });
}
?>
