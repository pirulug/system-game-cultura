<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card-custom p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock-fill text-danger fs-1"></i>
                <h2 class="fw-bold text-white mt-2 mb-1">Iniciar Sesión</h2>
                <p class="text-muted small">Ingresa tus credenciales para acceder a la arena</p>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/login" method="POST">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-3">
                    <label for="identifier" class="form-label text-white small fw-bold">Usuario o Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="identifier" name="identifier" required placeholder="Ej: chasqui_veloz">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-white small fw-bold">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control bg-dark border-secondary text-white" id="password" name="password" required placeholder="Tu contraseña">
                    </div>
                </div>

                <button type="submit" class="btn btn-peru w-100 py-2 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Ingresar al Sistema
                </button>
            </form>

            <div class="text-center pt-3 border-top border-secondary">
                <span class="text-muted small">¿Aún no tienes cuenta?</span>
                <a href="<?= htmlspecialchars($baseUrl) ?>/register" class="text-warning fw-bold small text-decoration-none ms-1">Regístrate aquí</a>
            </div>
        </div>
    </div>
</div>

