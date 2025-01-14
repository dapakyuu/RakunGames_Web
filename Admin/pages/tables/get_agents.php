<?php
include '../../../service/koneksi.php';

if (isset($_POST['game'])) {
    $game = $_POST['game'];

    // Cari agent yang gamenya cocok
    $sql = "SELECT id_agent, nama FROM agent WHERE game LIKE ?";
    $stmt = $koneksi->prepare($sql);
    $game_param = "%$game%";
    $stmt->bind_param("s", $game_param);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Select Agent</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id_agent'] . "'>" . $row['nama'] . "</option>";
    }
}
