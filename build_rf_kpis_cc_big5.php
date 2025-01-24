<?php
$d = "2025-01-21";
include('connectToDB.php');
include('connectToLocalDB.php');

// Vérification des connexions
if ($conn->connect_error || $connLocal->connect_error) {
    die("Erreur de connexion à la base de données : " . ($conn->connect_error ?? $connLocal->connect_error));
}

// Supprimer les données existantes dans rf_kpis_cc_big5
$connLocal->query('DELETE FROM `rf_kpis_cc_big5` WHERE 1');

// Fonction pour insérer des données dans rf_kpis_cc_big5
function insertIntoRfKpis($conn, $data) {
    $sqlInsert = "INSERT INTO rf_kpis_cc_big5 (kpi, source, frequence, slot_count, borne_orange, borne_rouge) 
                  VALUES (?, ?, ?, 0, 0, 0)";
    $stmt = $conn->prepare($sqlInsert);

    if ($stmt) {
        foreach ($data as $row) {
            $stmt->bind_param("sss", $row['kpi'], $row['source'], $row['frequence']);
            if (!$stmt->execute()) {
                echo "Erreur lors de l'insertion : " . $stmt->error . "<br>";
            }
        }
        echo "Données transférées avec succès.<br>";
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error . "<br>";
    }
}

// Récupérer les données depuis rf_kpis_cc_big5
$result = $conn->query("SELECT kpi, source, frequence FROM rf_kpis_cc_big5");
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
    insertIntoRfKpis($connLocal, $data);
}

// Récupérer les nouvelles données depuis kpis_cc
$sqlSelect = "SELECT * FROM oma_control.kpis_cc kc 
              WHERE kc.kpi NOT IN (SELECT rkc.kpi FROM oma_control.rf_kpis_cc_big5 rkc)
              AND kc.upd_dt='$d'";
$result = $conn->query($sqlSelect);
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
    insertIntoRfKpis($connLocal, $data);
}

// Mise à jour des données dans rf_kpis_cc_big5
$result = $connLocal->query("SELECT `KPI`, `Slot`, `Borne Orange`, `Borne Rouge` FROM kpi_control");
if ($result && $result->num_rows > 0) {
    $sqlUpdate = "UPDATE rf_kpis_cc_big5 
                  SET slot_count = ?, borne_orange = ?, borne_rouge = ? 
                  WHERE kpi = ?";
    $stmtUpdate = $connLocal->prepare($sqlUpdate);

    if ($stmtUpdate) {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $i++;
            $stmtUpdate->bind_param("idds", $row['Slot'], $row['Borne Orange'], $row['Borne Rouge'], $row['KPI']);
            if ($stmtUpdate->execute()) {
                echo "$i - Mise à jour réussie pour KPI : {$row['KPI']}<br>";
            } else {
                echo "Erreur lors de la mise à jour pour KPI : {$row['KPI']} -> " . $stmtUpdate->error . "<br>";
            }
        }
    } else {
        echo "Erreur de préparation de la requête d'update : " . $connLocal->error . "<br>";
    }
} else {
    echo "Aucune donnée trouvée dans la table kpi_control.<br>";
}
?>
