<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand brand-text d-flex align-items-center" href="<?= htmlspecialchars($baseUrl) ?>/">
            <i class="bi bi-shield-shaded me-2 text-danger fs-4"></i>
            <span>CULTURA</span>&nbsp;PERÚ
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <?php if (!empty($currentUser)): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/dashboard") ? "active fw-bold text-white" : "" ?>" href="<?= htmlspecialchars($baseUrl) ?>/dashboard">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/game/modes") ? "active fw-bold text-white" : "" ?>" href="<?= htmlspecialchars($baseUrl) ?>/game/modes">
                            <i class="bi bi-controller me-1"></i> Jugar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/friends") ? "active fw-bold text-white" : "" ?>" href="<?= htmlspecialchars($baseUrl) ?>/friends">
                            <i class="bi bi-people me-1"></i> Amigos
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER["REQUEST_URI"] ?? "", "/ranking") ? "active fw-bold text-white" : "" ?>" href="<?= htmlspecialchars($baseUrl) ?>/ranking">
                        <i class="bi bi-trophy me-1"></i> Ranking
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (!empty($currentUser)): ?>
                    <?php if ($currentUser["role"] === "admin" || $currentUser["role"] === "moderator"): ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-sm btn-outline-warning" href="<?= htmlspecialchars($baseUrl) ?>/admin">
                                <i class="bi bi-gear-fill me-1"></i> Panel Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDrop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-circle me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                <?= strtoupper(substr($currentUser["username"], 0, 1)) ?>
                            </span>
                            <span><?= htmlspecialchars($currentUser["username"]) ?></span>
                            <span class="badge-level ms-2">Nvl <?= (int)$currentUser["level"] ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark card-custom" aria-labelledby="userDrop">
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/profile/<?= htmlspecialchars($currentUser["username"]) ?>">
                                    <i class="bi bi-person me-2"></i> Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>/profile/edit">
                                    <i class="bi bi-pencil-square me-2"></i> Editar Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/logout" method="POST" class="d-inline">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/login">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-peru" href="<?= htmlspecialchars($baseUrl) ?>/register">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

