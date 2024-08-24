<?php
header('Content-Type: application/json');

// Fungsi untuk menjalankan skrip Python dan mendapatkan hasilnya
function generate_keys($num_keys) {
    // Menjalankan skrip WarpKeyGen.py dengan argumen jumlah kunci
    $command = escapeshellcmd("python3 WarpKeyGen.py " . intval($num_keys));
    $output = shell_exec($command);

    return parse_keys_from_output($output);
}

// Fungsi untuk memparsing output dari WarpKeyGen.py
function parse_keys_from_output($output) {
    $keys = [];
    $lines = explode("\n", $output);

    foreach ($lines as $line) {
        if (strpos($line, "Ключ ->") !== false) {
            list($key, $quota) = explode(" - ", trim(explode("Ключ ->", $line)[1]));
            $quota = explode(" ", $quota)[0]; // Mengambil angka pertama dari kuota
            $keys[] = [
                "key" => $key,
                "quota" => $quota
            ];
        }
    }

    return $keys;
}

// Mendapatkan data dari POST request
$data = json_decode(file_get_contents('php://input'), true);
$num_keys = isset($data['numKeys']) ? intval($data['numKeys']) : 1;

$keys = generate_keys($num_keys);

echo json_encode(["keys" => $keys]);
?>
