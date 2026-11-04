<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-6">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary">
                <div>
                    <h3 class="fw-bold text-white mb-1">Editar Mi Perfil</h3>
                    <p class="text-muted small mb-0">Actualiza tus datos públicos de jugador</p>
                </div>
                <a href="<?= htmlspecialchars($baseUrl) ?>/profile/<?= htmlspecialchars($currentUser["username"]) ?>" class="btn btn-outline-custom">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Perfil
                </a>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/profile/edit" method="POST">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-3">
                    <label for="full_name" class="form-label text-white small fw-bold">Nombre Completo</label>
                    <input type="text" class="form-control bg-dark border-secondary text-white" id="full_name" name="full_name" required value="<?= htmlspecialchars($profile["full_name"] ?? "") ?>">
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label text-white small fw-bold">Biografía o Lema</label>
                    <textarea class="form-control bg-dark border-secondary text-white" id="bio" name="bio" rows="3" maxlength="300" placeholder="Escribe un breve lema o descripción personal"><?= htmlspecialchars($profile["bio"] ?? "") ?></textarea>
                    <div class="form-text text-muted">Máximo 300 caracteres. Visible en tu perfil público.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white small fw-bold">Seleccionar Avatar</label>
                    <div class="row g-2">
                        <?php
                        $avatars = ["default_avatar.png", "avatar_inca.png", "avatar_chasqui.png", "avatar_moche.png", "avatar_marinera.png"];
                        $currentAvatar = $profile["avatar"] ?? "default_avatar.png";
                        ?>
                        <?php foreach ($avatars as $av): ?>
                            <div class="col-auto">
                                <label class="border p-2 rounded cursor-pointer <?= $currentAvatar === $av ? "border-warning bg-dark" : "border-secondary" ?>" style="cursor: pointer;">
                                    <input type="radio" name="avatar" value="<?= $av ?>" <?= $currentAvatar === $av ? "checked" : "" ?> class="me-1">
                                    <span class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        <?= strtoupper(substr($av, 7, 2)) ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-peru w-100 py-2">
                    <i class="bi bi-save me-2"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</div>

