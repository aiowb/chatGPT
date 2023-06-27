<?php

require_once("chatGPT_get_product_description.php");

// Date: 17.04.2023
// Autor: Khalid Ayoub
// Zweck: KI-Tool zur Erstellung von Produktbeschreibungen

// Ein Funktion, um Den generierten Text auf eine bestimmte Anzahl von Wörtern zu kombinieren
function funk_truncate_text_to_words($text, $max_words)
{ // Überprüft, ob die übergebenen Parameter gültig sind
    if (!is_string($text) || !is_int($max_words) || $max_words <= 0)
    {
        die("Ungültige Parameter für funk_truncate_text_to_words Funktion.");
    }
    // Text in einzelne Wörter zerlegen
    $words = explode(' ', $text);

    if (count($words) > $max_words)
    {
        // Durch die Wörter rückwärts iterieren, beginnend mit dem max_words-ten Wort
        for ($i = $max_words;$i > 0;$i--)
        {
            // Überprüfen, ob das aktuelle Wort mit einem Satzzeichen endet (Punkt, Ausrufezeichen, Fragezeichen)
            if (preg_match('/[.!?]$/', $words[$i]))
            {
                // Schneiden Sie das Array der Wörter so, dass es nur die Wörter bis einschließlich des aktuellen Wortes enthält
                $words = array_slice($words, 0, $i + 1);
                break;
            }
        }
        // Die Wörter wieder zu einem Text zusammenfügen
        $text = implode(' ', $words);
    }
    // Den gekürzten Text zurückgeben
    return $text;
}

// Parameter für das product_param-Array
$product_param_keys = array(
    'Produktname' => 'Produktname',
    'Herstellernummer' => 'Herstellernummer',
    'EAN' => 'EAN',
    'Marke' => 'Marke',
    'Produktart' => 'Produktart',
    'Größe' => 'Größe',
    'Verwendungszweck' => 'Verwendungszweck',
);


func_chatGPT_get_product_description($product_param_keys);

?>