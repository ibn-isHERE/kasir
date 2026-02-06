<?php
$pageTitle = 'Registrasi User';
require_once 'header.php';
requireAdmin();

$conn = getConnection();
$success = '';
$error = '';

// Proses tambah user
if (isset($_POST['tambah'])) {
    $username = escapeString($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $role = escapeString($conn, $_POST['role']);
    
    // Cek username sudah ada atau belum
    $check = query($conn, "SELECT * FROM user WHERE Username = '$username'");
    if (numRows($check) > 0) {
        $error = 'Username sudah digunakan!';
    } else {
        $sql = "INSERT INTO user (Username, Password, Role) VALUES ('$username', '$password', '$role')";
        if (query($conn, $sql)) {
            $success = 'User berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan user!';
        }
    }
}

// Proses hapus user
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    // Tidak bisa hapus diri sendiri
    if ($id == $_SESSION['user_id']) {
        $error = 'Tidak dapat menghapus akun sendiri!';
    } else {
        $sql = "DELETE FROM user WHERE UserID=$id";
        if (query($conn, $sql)) {
            $success = 'User berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus user!';
        }
    }
}

// Proses edit user
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $username = escapeString($conn, $_POST['username']);
    $role = escapeString($conn, $_POST['role']);
    
    // Cek username conflict
    $check = query($conn, "SELECT * FROM user WHERE Username = '$username' AND UserID != $id");
    if (numRows($check) > 0) {
        $error = 'Username sudah digunakan!';
    } else {
        if (!empty($_POST['password'])) {
            $password = md5($_POST['password']);
            $sql = "UPDATE user SET Username='$username', Password='$password', Role='$role' WHERE UserID=$id";
        } else {
            $sql = "UPDATE user SET Username='$username', Role='$role' WHERE UserID=$id";
        }
        
        if (query($conn, $sql)) {
            $success = 'User berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate user!';
        }
    }
}

// Ambil semua user
$userList = query($conn, "SELECT * FROM user ORDER BY Role ASC, Username ASC");

// Data untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $result = query($conn, "SELECT * FROM user WHERE UserID=$editId");
    $editData = fetchArray($result);
}
?>

<div class="page-header">
    <h2>👥 Registrasi User</h2>
    <p>Kelola akun admin dan petugas</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Form Tambah/Edit User -->
    <div class="card">
        <div class="card-header">
            <h3><?php echo $editData ? '✏️ Edit User' : '➕ Tambah User'; ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?php echo $editData['UserID']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" 
                           value="<?php echo $editData ? htmlspecialchars($editData['Username']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Password <?php echo $editData ? '(Kosongkan jika tidak diubah)' : ''; ?></label>
                    <input type="password" name="password" class="form-control" 
                           <?php echo $editData ? '' : 'required'; ?>>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" <?php echo ($editData && $editData['Role'] == 'admin') ? 'selected' : ''; ?>>
                            Admin
                        </option>
                        <option value="petugas" <?php echo ($editData && $editData['Role'] == 'petugas') ? 'selected' : ''; ?>>
                            Petugas
                        </option>
                    </select>
                </div>

                <?php if ($editData): ?>
                    <button type="submit" name="edit" class="btn btn-warning" style="width: 100%;">
                        💾 Update User
                    </button>
                    <a href="user.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px;">
                        ❌ Batal
                    </a>
                <?php else: ?>
                    <button type="submit" name="tambah" class="btn btn-success" style="width: 100%;">
                        ➕ Tambah User
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Daftar User -->
    <div class="card">
        <div class="card-header">
            <h3>📋 Daftar User</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (numRows($userList) > 0): ?>
                            <?php while ($row = fetchArray($userList)): ?>
                                <tr>
                                    <td><?php echo $row['UserID']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['Username']); ?></strong>
                                        <?php if ($row['UserID'] == $_SESSION['user_id']): ?>
                                            <span style="background: #667eea; color: white; padding: 2px 8px; 
                                                         border-radius: 10px; font-size: 0.75em; margin-left: 5px;">
                                                Anda
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['Role'] == 'admin'): ?>
                                            <span style="background: #fdcb6e; color: #2d3436; padding: 4px 12px; 
                                                         border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                                👑 Admin
                                            </span>
                                        <?php else: ?>
                                            <span style="background: #74b9ff; color: #2d3436; padding: 4px 12px; 
                                                         border-radius: 20px; font-size: 0.85em; font-weight: 600;">
                                                👨‍💼 Petugas
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $row['UserID']; ?>" class="btn btn-warning btn-sm">
                                            ✏️ Edit
                                        </a>
                                        <?php if ($row['UserID'] != $_SESSION['user_id']): ?>
                                            <a href="?hapus=<?php echo $row['UserID']; ?>" class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                🗑️ Hapus
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                                    Belum ada user
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
closeConnection($conn);
require_once 'footer.php';
?>
