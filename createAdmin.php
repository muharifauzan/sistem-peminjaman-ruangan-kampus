<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbadmin.php';  

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

    $prefill_nama = $nama_admin;
    $prefill_kontak = $kontak;
    $prefill_peran = $peran;

    if (isset($selectedPeran[$peran])) {
        $selectedPeran[$peran] = "selected";
    }
}
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
