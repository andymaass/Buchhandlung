<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Buchhandlung";

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Abfrage1: Alle Bücher anzeigen
$query1 = "SELECT * FROM Bücher";
$result1 = $conn->query($query1);
if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        echo "ID: " . $row["ID"] . " - Titel: " . $row["Titel"] . " - Autor-ID: " . $row["Autor-ID"] . "<br>";
    }
} else {
    echo "Keine Bücher gefunden.";
}

// Abfrage2: Titel eines Buches und zugehörigen Autor anzeigen
$query2 = "SELECT b.Titel, a.Vorname, a.Nachname
           FROM Bücher b
           INNER JOIN Autoren a ON b.Autor-ID = a.ID";
$result2 = $conn->query($query2);
if ($result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        echo "Titel: " . $row["Titel"] . " - Autor: " . $row["Vorname"] . " " . $row["Nachname"] . "<br>";
    }
} else {
    echo "Keine Bücher gefunden.";
}

// Abfrage3: Autoren mit mehr als einem Buch anzeigen
$query3 = "SELECT a.Vorname, a.Nachname, COUNT(b.ID) AS Anzahl_Bücher
           FROM Autoren a
           INNER JOIN Bücher b ON a.ID = b.Autor-ID
           GROUP BY a.ID
           HAVING Anzahl_Bücher > 1";
$result3 = $conn->query($query3);
if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        echo "Autor: " . $row["Vorname"] . " " . $row["Nachname"] . " - Anzahl Bücher: " . $row["Anzahl_Bücher"] . "<br>";
    }
} else {
    echo "Keine Autoren gefunden.";
}

// Abfrage4: Autoren mit Büchern nach dem Jahr 2000 anzeigen
$query4 = "SELECT DISTINCT a.Vorname, a.Nachname
           FROM Autoren a
           INNER JOIN Bücher b ON a.ID = b.Autor-ID
           WHERE b.Veröffentlichungsjahr > 2000";
$result4 = $conn->query($query4);
if ($result4->num_rows > 0) {
    while ($row = $result4->fetch_assoc()) {
        echo "Autor: " . $row["Vorname"] . " " . $row["Nachname"] . "<br>";
    }
} else {
    echo "Keine Autoren gefunden.";
}

// Abfrage5: Veröffentlichungsjahr eines Buchs aktualisieren
$bookIDToUpdate = 1; // ID des zu aktualisierenden Buchs
$newYear = 2023; // Neues Veröffentlichungsjahr
$query5 = "UPDATE Bücher SET Veröffentlichungsjahr = $newYear WHERE ID = $bookIDToUpdate";
if ($conn->query($query5) === TRUE) {
    echo "Veröffentlichungsjahr des Buchs mit ID $bookIDToUpdate wurde auf $newYear aktualisiert.<br>";
} else {
    echo "Fehler beim Aktualisieren: " . $conn->error;
}

// Abfrage6: Bücher löschen, die vor 1990 veröffentlicht wurden und deren Autoren keine weiteren Bücher nach 1990 veröffentlicht haben
$query6 = "DELETE b
           FROM Bücher b
           INNER JOIN Autoren a ON b.Autor-ID = a.ID
           WHERE b.Veröffentlichungsjahr < 1990
           AND a.ID NOT IN (
               SELECT Autor-ID
               FROM Bücher
               WHERE Veröffentlichungsjahr > 1990
           )";
if ($conn->query($query6) === TRUE) {
    echo "Bücher wurden gelöscht.<br>";
} else {
    echo "Fehler beim Löschen: " . $conn->error;
}

// Abfrage7: Indizes hinzufügen
$query7_1 = "ALTER TABLE Bücher ADD INDEX idx_Autor_ID (Autor-ID)";
$query7_2 = "ALTER TABLE Bücher ADD INDEX idx_Veröffentlichungsjahr (Veröffentlichungsjahr)";
$query7_3 = "ALTER TABLE Autoren ADD INDEX idx_Nachname (Nachname)";

if ($conn->query($query7_1) === TRUE &&
    $conn->query($query7_2) === TRUE &&
    $conn->query($query7_3) === TRUE) {
    echo "Indizes wurden hinzugefügt.";
} else {
    echo "Fehler beim Hinzufügen von Indizes: " . $conn->error;
}

// Verbindung schließen
$conn->close();
?>
