<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id_pengguna = $input['id_pengguna'] ?? '';

    if (empty($id_pengguna)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID Pengguna harus diisi']);
        exit;
    }

    try {
        $conn->beginTransaction();
        
        $check_query = "SELECT id_pengguna FROM mahasiswa WHERE id_pengguna = :id_pengguna 
                       UNION 
                       SELECT id_pengguna FROM dosen WHERE id_pengguna = :id_pengguna";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':id_pengguna', $id_pengguna);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus pengguna yang masih terdaftar sebagai Mahasiswa/Dosen']);
            $conn->rollBack();
            exit;
        }

        $delete_query = "DELETE FROM pengguna WHERE id_pengguna = :id_pengguna";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':id_pengguna', $id_pengguna);
        
        $delete_stmt->execute();
        
        if ($delete_stmt->rowCount() > 0) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Pengguna berhasil dihapus']);
        } else {
            $conn->rollBack();
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Pengguna tidak ditemukan']);
        }
        
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}

$conn = null;
?>
