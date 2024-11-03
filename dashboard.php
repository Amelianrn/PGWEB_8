<?php
// Konfigurasi MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latihan"; // Sesuaikan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses update data jika form edit di-submit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kecamatan = $_POST['kecamatan'];
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];
    $luas = $_POST['luas'];
    $jumlah_penduduk = $_POST['jumlah_penduduk'];

    $updateSql = "UPDATE penduduk SET kecamatan = '$kecamatan', longitude = '$longitude', latitude = '$latitude', luas = '$luas', jumlah_penduduk = '$jumlah_penduduk' WHERE id = $id";
    if ($conn->query($updateSql) === TRUE) {
        echo "<script>alert('Data berhasil diperbarui'); window.location.href='';</script>"; // Refresh halaman setelah update
    } else {
        echo "Error: " . $conn->error;
    }
}

// Periksa jika ada permintaan hapus
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $deleteSql = "DELETE FROM penduduk WHERE id = $id";
    if ($conn->query($deleteSql) === TRUE) {
        echo "<script>alert('Data berhasil dihapus'); window.location.href='';</script>"; // Refresh halaman setelah hapus
    } else {
        echo "Error: " . $conn->error;
    }
}

// Query untuk mendapatkan data
$sql = "SELECT * FROM penduduk";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Peta dan Data Penduduk</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <style>
        /* CSS styling seperti sebelumnya */
        #map {
            width: 100%;
            height: 600px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #fff9f9;
            margin: 0;
            padding: 20px;
        }
        .title, .subtitle {
            text-align: center;
        }
        .title {
            color: #333;
            font-size: 36px;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #666;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        h3 {
            text-align: center;
            color: #333;
            font-size: 32px;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #ff80c0;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #ffbbdd;
        }
        tr:hover {
            background-color: #ddd;
        }
        .action-btn {
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            color: white;
        }
        .delete-btn {
            background-color: #ff9999;
        }
        .delete-btn:hover {
            background-color: #ff1a1a;
        }
        .edit-btn {
            background-color: #cd1455;
        }
        .edit-btn:hover {
            background-color: #1a8cff;
        }
        .no-results {
            text-align: center;
            color: #888;
        }
        /* Modal styling */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; /* Set a high z-index to ensure it appears above other elements */
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.7); /* Darker background for better contrast */
            padding-top: 60px; 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* Center the modal with a top margin */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 600px; 
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); /* Add a shadow for depth */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2 class="title">PETA</h2>
<p class="subtitle">Kabupaten Sleman</p>

<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    var map = L.map("map").setView([-6.1753924, 106.8271528], 14);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "L.marker([" . htmlspecialchars($row['latitude']) . ", " . htmlspecialchars($row['longitude']) . "]).addTo(map).bindPopup('Kecamatan: " . htmlspecialchars($row['kecamatan']) . "<br>Jumlah Penduduk: " . htmlspecialchars($row['jumlah_penduduk']) . "').openPopup();";
        }
    } else {
        echo "console.log('No results found.');";
    }
    ?>
</script>

<h3>Data Penduduk</h3>

<?php
$result->data_seek(0);
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Kecamatan</th>
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Luas (km²)</th>
                <th>Jumlah Penduduk</th>
                <th>Aksi</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                <td>" . htmlspecialchars($row["longitude"]) . "</td>
                <td>" . htmlspecialchars($row["latitude"]) . "</td>
                <td>" . htmlspecialchars($row["luas"]) . "</td>
                <td>" . htmlspecialchars($row["jumlah_penduduk"]) . "</td>
                <td>
                    <button class='action-btn edit-btn' onclick='editData(" . json_encode($row) . ")'>Edit</button>
                    <form style='display:inline;' method='post' action=''>
                        <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                        <input type='submit' class='action-btn delete-btn' name='delete' value='Hapus'>
                    </form>
                </td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='no-results'>Tidak ada data yang ditemukan.</p>";
}
?>

<!-- Modal for Editing -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Data</h2>
        <form method="post" action="">
            <input type="hidden" name="id" id="editId">
            <label>Kecamatan:</label><br>
            <input type="text" name="kecamatan" id="editKecamatan" required><br><br>
            <label>Longitude:</label><br>
            <input type="text" name="longitude" id="editLongitude" required><br><br>
            <label>Latitude:</label><br>
            <input type="text" name="latitude" id="editLatitude" required><br><br>
            <label>Luas (km²):</label><br>
            <input type="text" name="luas" id="editLuas" required><br><br>
            <label>Jumlah Penduduk:</label><br>
            <input type="text" name="jumlah_penduduk" id="editJumlahPenduduk" required><br><br>
            <input type="submit" name="update" value="Simpan Perubahan">
        </form>
    </div>
</div>

<script>
function editData(data) {
    document.getElementById("editId").value = data.id;
    document.getElementById("editKecamatan").value = data.kecamatan;
    document.getElementById("editLongitude").value = data.longitude;
    document.getElementById("editLatitude").value = data.latitude;
    document.getElementById("editLuas").value = data.luas;
    document.getElementById("editJumlahPenduduk").value = data.jumlah_penduduk;
    document.getElementById("editModal").style.display = "block";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}

// Menutup modal jika diklik di luar modal
window.onclick = function(event) {
    var modal = document.getElementById("editModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>
