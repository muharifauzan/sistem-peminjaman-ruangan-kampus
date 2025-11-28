<?php
include 'koneksi.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nama_admin = $input['nama_admin'] ?? '';
    $kontak = $input['kontak'] ?? '';
    $peran = $input['peran'] ?? '';
    
    if (empty($nama_admin) || empty($kontak) || empty($peran)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbadmin.php';  

    $allowed_roles = ['SuperAdmin', 'Admin Fakultas', 'Admin Departemen', 'Admin DPKU', 'Admin DUI'];
    if (!in_array($peran, $allowed_roles)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Peran tidak valid. Harus salah satu dari: ' . implode(', ', $allowed_roles)]);
        exit;
    }
$error = '';
$prefill_nama = '';
$prefill_kontak = '';
$prefill_peran = '';
$show_form = true;
$selectedPeran = [
    "Admin Fakultas" => "",
    "Admin Departemen" => "",
    "Admin DPKU" => "",
    "Admin DUI" => ""
];
if (isset($_POST['create_admin'])) {
    $nama_admin = trim($_POST['nama_admin']);
    $kontak = trim($_POST['kontak']);
    $peran = trim($_POST['peran']);
    if ($nama_admin == '' || $kontak == '' || $peran == '') {
        $error = "Semua field wajib diisi.";
    } else {

    if (!preg_match('/^[0-9+]{10,15}$/', $kontak)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format kontak tidak valid. Minimal 10 digit angka']);
        exit;
        $query = "
            INSERT INTO admin (nama_admin, kontak, peran)
            VALUES ($1, $2, $3)
            RETURNING id_admin
        ";
        $res = pg_query_params($conn, $query, [$nama_admin, $kontak, $peran]);
        if (!$res) {
            $error = "Gagal menambahkan admin: " . pg_last_error($conn);
        } else {
            $id_admin = pg_fetch_result($res, 0, 0);
            $_SESSION['id_admin'] = $id_admin;
            $_SESSION['nama_admin'] = $nama_admin;
            header("Location: adminDashboard.php");
            exit;
        }
    }

    try {
        
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
    $prefill_nama = $nama_admin;
    $prefill_kontak = $kontak;
    $prefill_peran = $peran;
    if (isset($selectedPeran[$peran])) {
        $selectedPeran[$peran] = "selected";
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
$conn = null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
</head>
<body>
<h2>Tambah Admin Baru</h2>
<?php if ($error != '') { ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php } ?>
<form method="POST">
    Nama Admin:<br>
    <input type="text" name="nama_admin" required 
           value="<?php echo htmlspecialchars($prefill_nama); ?>"><br><br>
    Kontak:<br>
    <input type="text" name="kontak" required 
           value="<?php echo htmlspecialchars($prefill_kontak); ?>"><br><br>
    Peran Admin:<br>
    <select name="peran" required>
        <option value="">--Pilih Peran--</option>
        <option value="Admin Fakultas" <?php echo $selectedPeran["Admin Fakultas"]; ?>>Admin Fakultas</option>
        <option value="Admin Departemen" <?php echo $selectedPeran["Admin Departemen"]; ?>>Admin Departemen</option>
        <option value="Admin DPKU" <?php echo $selectedPeran["Admin DPKU"]; ?>>Admin DPKU</option>
        <option value="Admin DUI" <?php echo $selectedPeran["Admin DUI"]; ?>>Admin DUI</option>
    </select>
    <br><br>
    <button name="create_admin">Tambah Admin</button>
</form>
</body>
</html>
