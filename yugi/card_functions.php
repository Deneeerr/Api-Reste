<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para buscar cartas de tipos específicos
function getCards($num = 0) {
    $api_url = "https://db.ygoprodeck.com/api/v7/cardinfo.php";
    $data = json_decode(file_get_contents($api_url), true);
    
    $valid_types = ['Effect Monster', 'Normal Monster', 'Spell Card', 'Trap Card', 'Fusion Monster', 'Synchro Monster', 'XYZ Monster', 'Link Monster'];
    
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
    if (!isset($_SESSION['extra_deck']) || empty($_SESSION['extra_deck'])) {
        $_SESSION['extra_deck'] = [];
    }
}

// Função para adicionar uma carta ao baralho principal ou extra deck
function addCardToDeck($card_id) {
    $deck = $_SESSION['deck'];
    $extra_deck = $_SESSION['extra_deck'];
    $card_count = array_count_values(array_column($deck, 'id'))[$card_id] ?? 0;
    $extra_card_count = array_count_values(array_column($extra_deck, 'id'))[$card_id] ?? 0;
    
    if ($card_count >= 3 || $extra_card_count >= 3) {
        return "Você não pode ter mais de 3 cópias da mesma carta.";
    }
    
    $all_cards = getCards();
    foreach ($all_cards as $card) {
        if ($card['id'] == $card_id) {
            if (in_array($card['type'], ['Fusion Monster', 'Synchro Monster', 'XYZ Monster', 'Link Monster'])) {
                if (count($extra_deck) >= 15) {
                    return "O Deck Adicional não pode ter mais de 15 cartas.";
                }
                $_SESSION['extra_deck'][] = $card;
            } else {
                if (count($deck) >= 60) {
                    return "Você não pode ter mais de 60 cartas no deck principal.";
                }
                $_SESSION['deck'][] = $card;
            }
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
            return;
        }
    }
    foreach ($_SESSION['extra_deck'] as $key => $card) {
        if ($card['id'] == $card_id) {
            unset($_SESSION['extra_deck'][$key]);
            $_SESSION['extra_deck'] = array_values($_SESSION['extra_deck']);
            return;
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
