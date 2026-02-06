<?php
$pageTitle = 'Penjualan (POS)';
require_once 'header.php';

$conn = getConnection();
$userInfo = getUserInfo();
$success = '';
$error = '';

// Proses transaksi
if (isset($_POST['proses_transaksi'])) {
    $namaPelanggan = escapeString($conn, $_POST['nama_pelanggan']);
    $keranjang = json_decode($_POST['keranjang_data'], true);
    $totalHarga = floatval($_POST['total_harga']);
    
    if (!empty($keranjang) && $totalHarga > 0) {
        // Cek atau tambah pelanggan
        $checkPelanggan = query($conn, "SELECT PelangganID FROM pelanggan WHERE NamaPelanggan = '$namaPelanggan'");
        
        if (numRows($checkPelanggan) > 0) {
            $pelangganId = fetchArray($checkPelanggan)['PelangganID'];
        } else {
            query($conn, "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES ('$namaPelanggan', '-', '-')");
            $pelangganId = lastInsertId($conn);
        }
        
        // Insert penjualan
        $userId = $userInfo['id'];
        $sql = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID, UserID) 
                VALUES (NOW(), $totalHarga, $pelangganId, $userId)";
        
        if (query($conn, $sql)) {
            $penjualanId = lastInsertId($conn);
            
            // Insert detail dan update stok
            $berhasil = true;
            foreach ($keranjang as $item) {
                $produkId = intval($item['id']);
                $jumlah = intval($item['qty']);
                $subtotal = floatval($item['subtotal']);
                
                // Insert detail
                $sqlDetail = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) 
                              VALUES ($penjualanId, $produkId, $jumlah, $subtotal)";
                
                if (query($conn, $sqlDetail)) {
                    // Update stok
                    query($conn, "UPDATE produk SET Stok = Stok - $jumlah WHERE ProdukID = $produkId");
                } else {
                    $berhasil = false;
                    break;
                }
            }
            
            if ($berhasil) {
                $success = "Transaksi berhasil! Nota #" . str_pad($penjualanId, 6, '0', STR_PAD_LEFT);
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'cetak_nota.php?id=$penjualanId';
                    }, 1500);
                </script>";
            } else {
                $error = 'Gagal menyimpan detail transaksi!';
            }
        } else {
            $error = 'Gagal memproses transaksi!';
        }
    } else {
        $error = 'Keranjang kosong atau total tidak valid!';
    }
}

// Ambil semua produk
$produkList = query($conn, "SELECT * FROM produk WHERE Stok > 0 ORDER BY NamaProduk ASC");
?>

<style>
    .pos-container {
        display: grid;
        grid-template-columns: 2fr 1.3fr;
        gap: 20px;
        height: calc(100vh - 150px);
    }

    .produk-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        padding: 20px;
    }

    .produk-card {
        background: white;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .produk-card:hover {
        transform: translateY(-5px);
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
    }

    .produk-card.stok-rendah {
        border-color: var(--warning);
        background: #fff9f0;
    }

    .produk-nama {
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-dark);
        font-size: 0.95em;
    }

    .produk-harga {
        color: var(--success);
        font-weight: 700;
        font-size: 1.1em;
        margin-bottom: 8px;
    }

    .produk-stok {
        font-size: 0.85em;
        color: var(--text-light);
    }

    .keranjang-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 150px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .keranjang-items {
        flex: 1;
        overflow-y: auto;
        margin: 15px 0;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        padding: 10px 0;
    }

    .keranjang-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: var(--light-bg);
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .keranjang-item-info {
        flex: 1;
    }

    .keranjang-item-nama {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .keranjang-item-detail {
        font-size: 0.85em;
        color: var(--text-light);
    }

    .qty-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .qty-btn {
        width: 30px;
        height: 30px;
        border: none;
        background: #667eea;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .qty-btn:hover {
        background: #5568d3;
        transform: scale(1.1);
    }

    .qty-value {
        min-width: 40px;
        text-align: center;
        font-weight: 600;
    }

    .remove-btn {
        background: var(--danger);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85em;
        margin-left: 10px;
    }

    .total-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .total-label {
        font-size: 0.9em;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    .total-value {
        font-size: 2em;
        font-weight: 700;
        font-family: 'Crimson Pro', serif;
    }

    .payment-info {
        display: grid;
        gap: 10px;
        margin-bottom: 15px;
    }

    .payment-row {
        display: grid;
        grid-template-columns: 120px 1fr;
        align-items: center;
        gap: 10px;
    }

    .payment-row label {
        font-weight: 600;
        color: var(--text-dark);
    }

    .kembalian-display {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        font-size: 1.5em;
        font-weight: 700;
        font-family: 'Crimson Pro', serif;
        margin-top: 10px;
    }
</style>

<div class="page-header">
    <h2>💳 Point of Sale</h2>
    <p>Proses transaksi penjualan</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="pos-container">
    <!-- Katalog Produk -->
    <div class="card">
        <div class="card-header">
            <h3>📦 Katalog Produk</h3>
        </div>
        <div class="produk-grid">
            <?php while ($produk = fetchArray($produkList)): ?>
                <div class="produk-card <?php echo $produk['Stok'] < 20 ? 'stok-rendah' : ''; ?>" 
                     onclick="tambahKeKeranjang(<?php echo $produk['ProdukID']; ?>, 
                              '<?php echo htmlspecialchars($produk['NamaProduk']); ?>', 
                              <?php echo $produk['Harga']; ?>, 
                              <?php echo $produk['Stok']; ?>)">
                    <div style="font-size: 2.5em; margin-bottom: 10px;">🛒</div>
                    <div class="produk-nama"><?php echo htmlspecialchars($produk['NamaProduk']); ?></div>
                    <div class="produk-harga">Rp <?php echo number_format($produk['Harga'], 0, ',', '.'); ?></div>
                    <div class="produk-stok">Stok: <?php echo $produk['Stok']; ?> unit</div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Keranjang & Checkout -->
    <div class="keranjang-section">
        <h3 style="font-family: 'Crimson Pro', serif; color: var(--primary); margin-bottom: 15px;">
            🛍️ Keranjang Belanja
        </h3>

        <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" id="namaPelanggan" class="form-control" placeholder="Masukkan nama pelanggan" 
                   value="Umum" required>
        </div>

        <div class="keranjang-items" id="keranjangItems">
            <p style="text-align: center; color: var(--text-light); padding: 40px;">
                Keranjang masih kosong<br>Pilih produk untuk memulai transaksi
            </p>
        </div>

        <div class="total-section">
            <div class="total-label">Grand Total</div>
            <div class="total-value" id="grandTotal">Rp 0</div>
        </div>

        <div class="payment-info">
            <div class="payment-row">
                <label>Uang Bayar:</label>
                <input type="number" id="uangBayar" class="form-control" placeholder="0" 
                       oninput="hitungKembalian()" step="1000">
            </div>
        </div>

        <div class="kembalian-display" id="kembalianDisplay" style="display: none;">
            Kembalian: Rp <span id="kembalianValue">0</span>
        </div>

        <button onclick="prosesTransaksi()" class="btn btn-success" style="width: 100%; padding: 15px; font-size: 1.1em; margin-top: 15px;">
            💰 Proses Pembayaran
        </button>

        <button onclick="resetKeranjang()" class="btn btn-danger" style="width: 100%; padding: 12px; margin-top: 10px;">
            🗑️ Kosongkan Keranjang
        </button>
    </div>
</div>

<form id="transaksiForm" method="POST" style="display: none;">
    <input type="hidden" name="nama_pelanggan" id="formNamaPelanggan">
    <input type="hidden" name="keranjang_data" id="formKeranjang">
    <input type="hidden" name="total_harga" id="formTotal">
    <input type="hidden" name="proses_transaksi" value="1">
</form>

<script>
let keranjang = [];

function tambahKeKeranjang(id, nama, harga, stokMax) {
    const existing = keranjang.find(item => item.id === id);
    
    if (existing) {
        if (existing.qty < stokMax) {
            existing.qty++;
            existing.subtotal = existing.qty * existing.harga;
        } else {
            alert('Stok tidak mencukupi!');
            return;
        }
    } else {
        keranjang.push({
            id: id,
            nama: nama,
            harga: harga,
            qty: 1,
            stokMax: stokMax,
            subtotal: harga
        });
    }
    
    updateKeranjang();
}

function updateQty(id, change) {
    const item = keranjang.find(i => i.id === id);
    if (item) {
        item.qty += change;
        
        if (item.qty > item.stokMax) {
            alert('Stok tidak mencukupi!');
            item.qty = item.stokMax;
        }
        
        if (item.qty <= 0) {
            hapusDariKeranjang(id);
        } else {
            item.subtotal = item.qty * item.harga;
            updateKeranjang();
        }
    }
}

function hapusDariKeranjang(id) {
    keranjang = keranjang.filter(item => item.id !== id);
    updateKeranjang();
}

function updateKeranjang() {
    const container = document.getElementById('keranjangItems');
    
    if (keranjang.length === 0) {
        container.innerHTML = `
            <p style="text-align: center; color: var(--text-light); padding: 40px;">
                Keranjang masih kosong<br>Pilih produk untuk memulai transaksi
            </p>
        `;
    } else {
        container.innerHTML = keranjang.map(item => `
            <div class="keranjang-item">
                <div class="keranjang-item-info">
                    <div class="keranjang-item-nama">${item.nama}</div>
                    <div class="keranjang-item-detail">
                        Rp ${item.harga.toLocaleString('id-ID')} × ${item.qty} = 
                        Rp ${item.subtotal.toLocaleString('id-ID')}
                    </div>
                </div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                    <div class="qty-value">${item.qty}</div>
                    <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                    <button class="remove-btn" onclick="hapusDariKeranjang(${item.id})">×</button>
                </div>
            </div>
        `).join('');
    }
    
    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    hitungKembalian();
}

function hitungKembalian() {
    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    const bayar = parseFloat(document.getElementById('uangBayar').value) || 0;
    const kembalian = bayar - total;
    
    const kembalianDisplay = document.getElementById('kembalianDisplay');
    const kembalianValue = document.getElementById('kembalianValue');
    
    if (bayar > 0 && kembalian >= 0) {
        kembalianDisplay.style.display = 'block';
        kembalianValue.textContent = kembalian.toLocaleString('id-ID');
    } else {
        kembalianDisplay.style.display = 'none';
    }
}

function prosesTransaksi() {
    const namaPelanggan = document.getElementById('namaPelanggan').value.trim();
    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    const bayar = parseFloat(document.getElementById('uangBayar').value) || 0;
    
    if (!namaPelanggan) {
        alert('Nama pelanggan harus diisi!');
        return;
    }
    
    if (keranjang.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }
    
    if (bayar < total) {
        alert('Uang bayar kurang!');
        return;
    }
    
    if (confirm('Proses transaksi ini?')) {
        document.getElementById('formNamaPelanggan').value = namaPelanggan;
        document.getElementById('formKeranjang').value = JSON.stringify(keranjang);
        document.getElementById('formTotal').value = total;
        document.getElementById('transaksiForm').submit();
    }
}

function resetKeranjang() {
    if (confirm('Kosongkan keranjang?')) {
        keranjang = [];
        updateKeranjang();
        document.getElementById('uangBayar').value = '';
        document.getElementById('namaPelanggan').value = 'Umum';
    }
}
</script>

<?php
closeConnection($conn);
require_once 'footer.php';
?>
