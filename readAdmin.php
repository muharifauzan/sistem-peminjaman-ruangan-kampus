<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_admin = $_GET['id_admin'] ?? '';
    
    try {
        if (!empty($id_admin)) {
   
            $query = "SELECT a.id_admin, a.nama_admin, a.kontak, a.peran,
                             f.nama_fakultas, d.nama_departemen, 
                             dp.nama_unit as nama_unit_dpku, du.nama_unit as nama_unit_dui
                      FROM admin a
                      LEFT JOIN fakultas f ON a.id_admin = f.id_admin
                      LEFT JOIN departemen d ON a.id_admin = d.id_admin
                      LEFT JOIN dpku dp ON a.id_admin = dp.id_admin
                      LEFT JOIN dui du ON a.id_admin = du.id_admin
                      WHERE a.id_admin = :id_admin";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_admin', $id_admin);
        } else {
 
            $query = "SELECT a.id_admin, a.nama_admin, a.kontak, a.peran,
                             f.nama_fakultas, d.nama_departemen, 
                             dp.nama_unit as nama_unit_dpku, du.nama_unit as nama_unit_dui
                      FROM admin a
                      LEFT JOIN fakultas f ON a.id_admin = f.id_admin
                      LEFT JOIN departemen d ON a.id_admin = d.id_admin
                      LEFT JOIN dpku dp ON a.id_admin = dp.id_admin
                      LEFT JOIN dui du ON a.id_admin = du.id_admin
                      ORDER BY a.id_admin";
            $stmt = $conn->prepare($query);
        }
        
        $stmt->execute();
        $admins = $stmt->fetchAll();
        
        $result_data = [];
        foreach ($admins as $row) {

            $unit = '';
            switch ($row['peran']) {
                case 'Admin Fakultas':
                    $unit = $row['nama_fakultas'] ?? '';
                    break;
                case 'Admin Departemen':
                    $unit = $row['nama_departemen'] ?? '';
                    break;
                case 'Admin DPKU':
                    $unit = $row['nama_unit_dpku'] ?? '';
                    break;
                case 'Admin DUI':
                    $unit = $row['nama_unit_dui'] ?? '';
                    break;
                default:
                    $unit = 'Sistem';
            }
            
            $result_data[] = [
                'id_admin' => $row['id_admin'],
                'nama_admin' => $row['nama_admin'],
                'kontak' => $row['kontak'],
                'peran' => $row['peran'],
                'unit' => $unit
            ];
        }
        
        if (!empty($id_admin) && empty($result_data)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Admin tidak ditemukan']);
        } else {
            echo json_encode([
                'success' => true, 
                'data' => $result_data,
                'total' => count($result_data)
            ]);
        }
        
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
