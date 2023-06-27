<?php

function func_chatGPT_get_product_description($product_param_keys)
{
    // Fehleranzeige aktivieren
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "parceldealz";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn)
    {
        die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
    }

	$sql = "SELECT t1.titel, t1.beschreibung, t1.hersteller_nr, t2.ean, t1.groessenbezeichnung, t4.artikelart, t1.groessenbezeichnung, t5.verwendung
			FROM s_artikel AS t1
			JOIN s_groessen_zuweisung AS t2 ON t1.id_artikel = t2.id_artikel
			JOIN s_zuweisung AS t3 ON t1.id_artikel = t3.id_artikel
			JOIN s_artikelart AS t4 ON t3.id_artikelart = t4.id_artikelart
			JOIN s_verwendung AS t5 ON t3.id_verwendung = t5.id_verwendung
			WHERE anzahl = 1
			AND ean is true
			LIMIT 2"; //AND beschreibung is Null
	
    $result = mysqli_query($conn, $sql);

    if (!$result)
    {
        die("SQL-Abfrage fehlgeschlagen: " . mysqli_error($conn));
    }

    // Array zum Speichern der generierten Produktbeschreibungen
    $responses = [];

    while ($row = mysqli_fetch_assoc($result))
    {
        if (!$row)
        {
            die("Fehler beim Abrufen der Daten aus der Datenbank: " . mysqli_error($conn));
        }

        // Produktinformationen aus der aktuellen Zeile extrahieren
        $product_param = array(
            $product_param_keys['Produktname'] => $row['titel'],
            $product_param_keys['Herstellernummer'] => $row['hersteller_nr'],
            $product_param_keys['EAN'] => $row['ean'],
            $product_param_keys['Marke'] => $row['groessenbezeichnung'],
            $product_param_keys['Produktart'] => $row['artikelart'],
            $product_param_keys['Größe'] => $row['groessenbezeichnung'],
            $product_param_keys['Verwendungszweck'] => $row['verwendung'],
        );

        // Prompt für den Chatbot erstellen
        $prompts = 'Bitte erstellen Sie eine ansprechende, kreative und detaillierte Produktbeschreibung in deutscher Sprache basierend auf den folgenden Informationen: ' . implode(', ', $product_param);

        // Initialisiert cURL, um die OpenAI API aufzurufen
        $curl = curl_init();

        if (!$curl)
        {
            die("cURL-Initialisierung fehlgeschlagen: " . curl_error($curl));
        }

        // Setzt die Optionen für den cURL-Aufruf
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 15,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
		   "model": "text-davinci-003",
		   "prompt": "' . $prompts . '",
		   "temperature": 0.9,
		   "max_tokens": 200
		 }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer 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',
                'Content-Type: application/json',
            ) ,
        ));

        // Führt den cURL-Aufruf aus und speichert die Antwort
        $response = curl_exec($curl);

        if (curl_errno($curl))
        {
            echo 'Error:' . curl_error($curl);
        }
        else
        {
            $response_data = json_decode($response, true);
            $generated_description = $response_data['choices'][0]['text'];
            $max_words = 40; // Die maximale Anzahl der Wörter, die Sie wünschen
            $truncated_description = funk_truncate_text_to_words($generated_description, $max_words);

            // Herstellernummer und EAN aus der Beschreibung entfernen
            $truncated_description = str_replace($row['hersteller_nr'], '', $truncated_description);
            $truncated_description = str_replace($row['ean'], '', $truncated_description);

            // Erster Buchstabe der Beschreibung groß
            $truncated_description = ucfirst(preg_replace('/(^[\.,:;]+)/', '', $truncated_description));

            $responses[] = ['product_info' => implode(', ', $product_param) , 'description' => $truncated_description, ];
        }

        // Schließt die cURL-Sitzung
        curl_close($curl);
    } // Ende der While Schleife

    // die generierten Produktbeschreibungen ausgeben
    foreach ($responses as $response_item)
    {
        $product_info_array = explode(', ', $response_item['product_info']);
        $title = $product_info_array[0];
        $herstellernr = $product_info_array[1];

        echo '<h2>Generierte Produktbeschreibung:</h2>';
        echo '<strong>Titel: ' . $title . ', Herstellernummer: ' . $herstellernr . '</strong>';
        echo '<p>' . $response_item['description'] . '</p>';
    }
}


?>
