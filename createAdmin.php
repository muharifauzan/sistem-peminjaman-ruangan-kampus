<?php
require 'koneksi.php';

$nama   = $_POST['nama_admin'];
$kontak = $_POST['kontak'];
$peran  = $_POST['peran'];
$unit   = $_POST['unit'];

$query = "
    INSERT INTO admin (nama_admin, kontak, peran)
    VALUES ('$nama', '$kontak', '$peran')
    RETURNING id_admin
";

$result = pg_query($conn, $query);
$row = pg_fetch_assoc($result);
$id_admin = $row['id_admin'];

switch ($peran) {
    case 'Admin Fakultas':
        pg_query($conn, "
            INSERT INTO fakultas (id_admin, nama_fakultas, kontak)
            VALUES ($id_admin, '$unit', '$kontak')
        ");
        break;

    case 'Admin Departemen':
        pg_query($conn, "
            INSERT INTO departemen (id_admin, nama_departemen, kontak)
            VALUES ($id_admin, '$unit', '$kontak')
        ");
        break;

    case 'Admin DPKU':
        pg_query($conn, "
            INSERT INTO dpku (dpku_id_admin, nama_unit, kontak)
            VALUES ($id_admin, '$unit', '$kontak')
        ");
        break;

    case 'Admin DUI':
        pg_query($conn, "
            INSERT INTO dui (dui_id_admin, nama_unit, kontak)
            VALUES ($id_admin, '$unit', '$kontak')
        ");
        break;
}

echo "Admin berhasil dibuat.";
?>
