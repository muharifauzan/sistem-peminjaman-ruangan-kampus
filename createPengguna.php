<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbPengguna.php';

$error = '';
$prefill_email = '';

// Proses login
if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $res = pg_query_params($conn, "SELECT id_pengguna, nama FROM pengguna WHERE email=$1", [$email]);

    if($row = pg_fetch_assoc($res)){
        $_SESSION['id_pengguna'] = $row['id_pengguna'];
        $_SESSION['nama'] = $row['nama'];
        header("Location: dashboard.php");
        exit;
    } else {
        // Email belum ada → simpan untuk prefill
        $prefill_email = $email;
        $show_signup = true; // menampilkan form signup
    }
}

// Proses signup
if(isset($_POST['signup'])){
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $hp = trim($_POST['hp']);
    $peran = $_POST['peran'];

    $res1 = pg_query_params($conn, 
        "INSERT INTO pengguna(nama,email,nomor_handphone) VALUES($1,$2,$3) RETURNING id_pengguna",
        [$nama,$email,$hp]
    );
    if(!$res1) die("Gagal insert pengguna: ".pg_last_error($conn));

    $id_pengguna = pg_fetch_result($res1,0,0);

    if($peran=='mahasiswa'){
        $nim = trim($_POST['nim']);
        $ps = trim($_POST['program_studi']);
        pg_query_params($conn, "INSERT INTO mahasiswa(id_pengguna, nim, program_studi) VALUES($1,$2,$3)",
            [$id_pengguna,$nim,$ps]);
    } else {
        $nip = trim($_POST['nip']);
        $dept = trim($_POST['departemen']);
        pg_query_params($conn, "INSERT INTO dosen(id_pengguna, nip, departemen) VALUES($1,$2,$3)",
            [$id_pengguna,$nip,$dept]);
    }

    // Auto login
    $_SESSION['id_pengguna'] = $id_pengguna;
    $_SESSION['nama'] = $nama;

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login / Signup</title>
</head>
<body>

<h2>Login</h2>
<form method="POST">
    Email: <input type="email" name="email" required value="<?=htmlspecialchars($prefill_email)?>">
    <button name="login">Login</button>
</form>

<p id="signup-link" style="margin-top:10px;">
    Belum memiliki akun? <a href="#" onclick="document.getElementById('signup-form').style.display='block'; this.style.display='none'; return false;">Silakan mendaftar</a>
</p>

<div id="signup-form" style="display:<?=!empty($show_signup)?'block':'none'?>; margin-top:20px;">
    <h2>Signup</h2>
    <form method="POST">
        Nama: <input name="nama" required><br>
        Email: <input name="email" type="email" name="email" required value="<?=htmlspecialchars($prefill_email)?>"><br>
        Nomor HP: <input name="hp" required><br><br>

        Peran:
        <select name="peran" id="peran">
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
        </select><br><br>

        <div id="mahasiswa_form">
            NIM: <input name="nim"><br>
            Program Studi: <input name="program_studi"><br>
        </div>

        <div id="dosen_form" style="display:none;">
            NIP: <input name="nip"><br>
            Departemen: <input name="departemen"><br>
        </div>

        <button name="signup">Daftar & Login</button>
    </form>
</div>

<script>
document.getElementById("peran").addEventListener("change", function(){
    if(this.value=="mahasiswa"){
        document.getElementById("mahasiswa_form").style.display="block";
        document.getElementById("dosen_form").style.display="none";
    } else {
        document.getElementById("mahasiswa_form").style.display="none";
        document.getElementById("dosen_form").style.display="block";
    }
});
</script>

</body>
</html>
