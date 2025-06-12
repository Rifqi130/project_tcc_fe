<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengaduan Mahasiswa</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Fallback Bootstrap CSS (local) -->
    <script>
        // Check if Bootstrap CSS loaded
        window.addEventListener('load', function() {
            var testEl = document.createElement('div');
            testEl.className = 'btn btn-primary d-none';
            document.body.appendChild(testEl);
            var styles = window.getComputedStyle(testEl);
            if (styles.display !== 'none' || !styles.backgroundColor) {
                console.warn('Bootstrap CSS failed to load, loading fallback...');
                var fallbackCSS = document.createElement('link');
                fallbackCSS.rel = 'stylesheet';
                fallbackCSS.href = 'assets/css/bootstrap.min.css';
                document.head.appendChild(fallbackCSS);
            }
            document.body.removeChild(testEl);
        });
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" 
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <!-- Emergency inline CSS for critical form elements -->
    <style>
        .form-control, .form-select, .btn {
            min-height: 38px;
            margin-bottom: 10px;
        }
        .card {
            margin-bottom: 20px;
        }
        .d-none { display: none !important; }
        .d-block { display: block !important; }
        .w-100 { width: 100% !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        .p-3 { padding: 1rem !important; }
        .border { border: 1px solid #dee2e6 !important; }
        .rounded { border-radius: 0.375rem !important; }
        .bg-light { background-color: #f8f9fa !important; }
    </style>
</head>

<body>
    <!-- Debug info for VM deployment -->
    <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
    <div id="debug-info" class="position-fixed top-0 end-0 bg-warning text-dark p-2 small" style="z-index: 9999; max-width: 300px;">
        <strong>Debug Mode:</strong><br>
        Server: <?php echo $_SERVER['HTTP_HOST'] ?? 'Unknown'; ?><br>
        PHP: <?php echo PHP_VERSION; ?><br>
        <span id="js-status">JS: Loading...</span>
    </div>
    <script>
        document.getElementById('js-status').textContent = 'JS: OK';
        setTimeout(() => {
            const debugEl = document.getElementById('debug-info');
            if (debugEl) debugEl.style.display = 'none';
        }, 5000);
    </script>
    <?php endif; ?>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-university me-2"></i>
                Sistem Pengaduan Mahasiswa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                               href="dashboard.php">
                               <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'complaints.php' ? 'active' : ''; ?>" 
                               href="complaints.php">
                               <i class="fas fa-list me-1"></i>Pengaduan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" 
                               href="users.php">
                               <i class="fas fa-users me-1"></i>Pengguna
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (!isAdmin()) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'public_complaints.php' ? 'active' : ''; ?>" 
                               href="public_complaints.php">
                               <i class="fas fa-eye me-1"></i>Pengaduan Publik
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['username'])) { ?>
                        <?php if (isMahasiswa()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'my_complaints.php' ? 'active' : ''; ?>" 
                                   href="my_complaints.php">
                                   <i class="fas fa-file-alt me-1"></i>Pengaduan Saya
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                               <i class="fas fa-user-circle me-1"></i>
                               <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text">
                                        <small class="text-muted">
                                            Role: <?php echo ucfirst($_SESSION['user_role']); ?>
                                        </small>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>"
                                href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="main-container fade-in">