<div class="row justify-content-center py-5">
    <div class="col-md-7 col-lg-6">
        <div class="card-custom p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-danger fs-1"></i>
                <h2 class="fw-bold text-white mt-2 mb-1">Registro de Jugador</h2>
                <p class="text-muted small">Únete a la comunidad de trivia más competitiva del Perú</p>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/register" method="POST">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-3">
                    <label for="full_name" class="form-label text-white small fw-bold">Nombre Completo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-card-text"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="full_name" name="full_name" required placeholder="Ej: Andrés Avelino Cáceres">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label text-white small fw-bold">Nombre de Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-at"></i></span>
                            <input type="text" class="form-control bg-dark border-secondary text-white" id="username" name="username" required placeholder="usuario_peru">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label text-white small fw-bold">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-dark border-secondary text-white" id="email" name="email" required placeholder="correo@ejemplo.pe">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label text-white small fw-bold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control bg-dark border-secondary text-white" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label text-white small fw-bold">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-shield-check"></i></span>
                            <input type="password" class="form-control bg-dark border-secondary text-white" id="password_confirm" name="password_confirm" required minlength="6" placeholder="Repite la contraseña">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-peru w-100 py-2 mb-3">
                    <i class="bi bi-check-lg me-2"></i> Crear Cuenta y Comenzar
                </button>
            </form>

            <div class="text-center pt-3 border-top border-secondary">
                <span class="text-muted small">¿Ya tienes una cuenta registrada?</span>
                <a href="<?= htmlspecialchars($baseUrl) ?>/login" class="text-warning fw-bold small text-decoration-none ms-1">Inicia sesión</a>
            </div>
        </div>
    </div>
</div>

