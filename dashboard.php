<?php
session_start();
if(!isset($_SESSION['id_pengguna'])){
    header("Location: createPengguna.php");
    exit;
}
include 'dbPengguna.php';

// Ambil daftar ruangan
$res = pg_query($conn, "SELECT id_ruangan, nama_ruangan, kapasitas, biaya_sewa, jenis_ruangan FROM ruangan");
$ruangan = pg_fetch_all($res);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Ruangan</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: 0.2s;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .btn {
            padding: 8px 14px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn-detail {
            background: #0d6efd;
            color: white;
        }

        .btn-pinjam {
            background: #198754;
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background:white;
            padding: 20px;
            width: 450px;
            border-radius:10px;
        }
    </style>
</head>
<body>

<h2>Selamat datang, <?=htmlspecialchars($_SESSION['nama'])?></h2>
<h3 style="padding-left:20px;">Daftar Ruangan</h3>

<div class="grid">
<?php foreach($ruangan as $r){ ?>
    <div class="card">
        <h3><?=htmlspecialchars($r['nama_ruangan'])?></h3>
        <p><b>Jenis:</b> <?=htmlspecialchars($r['jenis_ruangan'])?></p>
        <p><b>Kapasitas:</b> <?=$r['kapasitas']?> orang</p>
        <p><b>Biaya Sewa:</b> Rp <?=number_format($r['biaya_sewa'],0,',','.')?></p>

        <button class="btn btn-detail" onclick="showDetail(<?=$r['id_ruangan']?>)">Detail</button>

        <form method="GET" action="formPeminjaman.php" style="display:inline;">
            <input type="hidden" name="id_ruangan" value="<?=$r['id_ruangan']?>">
            <button class="btn btn-pinjam">Pinjam</button>
        </form>
    </div>
<?php } ?>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
    <div class="modal-content" id="modal-content">
    </div>
</div>

<script>
function showDetail(id){
    fetch("detailRuangan.php?id=" + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById("modal-content").innerHTML = html;
            document.getElementById("modal").style.display = "flex";
        });
}

window.onclick = function(e){
    if(e.target.id === "modal"){
        document.getElementById("modal").style.display = "none";
    }
}
</script>

</body>
</html>
