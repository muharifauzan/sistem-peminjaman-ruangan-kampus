<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nama_admin = $input['nama_admin'] ?? '';
    $kontak = $input['kontak'] ?? '';
    $peran = $input['peran'] ?? '';

    // Validasi input
    if (empty($nama_admin) || empty($kontak) || empty($peran)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }

    // Validasi peran sesuai constraint
    $allowed_roles = ['SuperAdmin', 'Admin Fakultas', 'Admin Departemen', 'Admin DPKU', 'Admin DUI'];
    if (!in_array($peran, $allowed_roles)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Peran tidak valid. Harus salah satu dari: ' . implode(', ', $allowed_roles)]);
        exit;
    }

    // Validasi format kontak (minimal 10 digit)
    if (!preg_match('/^[0-9+]{10,15}$/', $kontak)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format kontak tidak valid. Minimal 10 digit angka']);
        exit;
    }

    try {
        // Insert ke tabel Admin
        $insert_query = "INSERT INTO admin (nama_admin, kontak, peran) VALUES (:nama_admin, :kontak, :peran) RETURNING id_admin";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bindParam(':nama_admin', $nama_admin);
        $insert_stmt->bindParam(':kontak', $kontak);
        $insert_stmt->bindParam(':peran', $peran);
        
        $insert_stmt->execute();
        $result = $insert_stmt->fetch();
        $id_admin = $result['id_admin'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Admin berhasil dibuat',
            'data' => [
                'id_admin' => $id_admin,
                'nama_admin' => $nama_admin,
                'kontak' => $kontak,
                'peran' => $peran
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}

$conn = null;
?>
