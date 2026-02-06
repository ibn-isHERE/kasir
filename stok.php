<?php
$pageTitle = 'Cek Stok Barang';
require_once 'header.php';

$conn = getConnection();

// Ambil semua produk
$produkList = query($conn, "SELECT * FROM produk ORDER BY NamaProduk ASC");
?>

<div class="page-header">
    <h2>📦 Cek Stok Barang</h2>
    <p>Monitoring ketersediaan produk</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>📋 Daftar Stok Produk</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok Tersedia</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (numRows($produkList) > 0): ?>
                        <?php while ($row = fetchArray($produkList)): ?>
                            <tr>
                                <td><?php echo $row['ProdukID']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['NamaProduk']); ?></strong></td>
                                <td>Rp <?php echo number_format($row['Harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <span style="background: <?php echo $row['Stok'] < 20 ? '#ff6b6b' : '#00b894'; ?>; 
                                                 color: white; padding: 6px 16px; border-radius: 20px; 
                                                 font-size: 0.95em; font-weight: 600;">
                                        <?php echo $row['Stok']; ?> unit
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['Stok'] == 0): ?>
                                        <span style="background: #ff6b6b; color: white; padding: 6px 16px; 
                                                     border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                            ❌ Habis
                                        </span>
                                    <?php elseif ($row['Stok'] < 20): ?>
                                        <span style="background: #ffa502; color: white; padding: 6px 16px; 
                                                     border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                            ⚠️ Stok Rendah
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #00b894; color: white; padding: 6px 16px; 
                                                     border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                            ✅ Tersedia
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                                Belum ada produk
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
closeConnection($conn);
require_once 'footer.php';
?>
