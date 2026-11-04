<?php
require __DIR__ . "/header.php";
?>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar Admin Armonizado -->
    <aside class="admin-sidebar d-flex flex-column">
        <div class="admin-sidebar-header">
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin" class="text-white text-decoration-none d-flex align-items-center">
                <i class="bi bi-shield-shaded text-danger fs-5 me-2"></i>
                <span class="fs-6 fw-bold tracking-tight">Admin Panel</span>
            </a>
            <div class="text-muted mt-1" style="font-size: 0.72rem;">Cultura Perú v<?= htmlspecialchars(\App\Config\App::APP_VERSION) ?></div>
        </div>

        <div class="p-2 flex-grow-1">
            <ul class="nav flex-column mb-auto">
                <li class="admin-nav-item">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin" class="nav-link <?= str_ends_with($_SERVER["REQUEST_URI"] ?? "", "/admin") ? "active" : "" ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions" class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/admin/questions") ? "active" : "" ?>">
                        <i class="bi bi-question-circle me-2"></i> Preguntas
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/categories" class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/admin/categories") ? "active" : "" ?>">
                        <i class="bi bi-tags me-2"></i> Categorías
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/users" class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/admin/users") ? "active" : "" ?>">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/matches" class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/admin/matches") ? "active" : "" ?>">
                        <i class="bi bi-joystick me-2"></i> Partidas
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/rankings" class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/admin/rankings") ? "active" : "" ?>">
                        <i class="bi bi-trophy me-2"></i> Rankings y Temporadas
                    </a>
                </li>
            </ul>
        </div>

        <div class="p-3 border-top border-secondary-subtle">
            <a href="<?= htmlspecialchars($baseUrl) ?>/dashboard" class="btn btn-sm btn-outline-light w-100 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Ir al Dashboard
            </a>
            <form action="<?= htmlspecialchars($baseUrl) ?>/logout" method="POST">
                <?= \App\Core\CSRF::field() ?>
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Contenido Principal Admin -->
    <main class="flex-grow-1 p-4" style="background-color: var(--bg-base); overflow-y: auto;">
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show card-custom" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger alert-dismissible fade show card-custom" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= htmlspecialchars($baseUrl) ?>/js/app.js"></script>
</body>
</html>
