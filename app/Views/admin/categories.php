<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h4 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-tags-fill text-danger me-2"></i> Categorías Oficiales (<?= count($categories) ?>)
            </h4>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Preguntas</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= (int)$cat["display_order"] ?></td>
                                <td><i class="bi <?= htmlspecialchars($cat["icon_class"]) ?> fs-5 text-warning"></i></td>
                                <td class="fw-bold text-white"><?= htmlspecialchars($cat["name"]) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($cat["slug"]) ?></td>
                                <td><span class="badge bg-secondary"><?= (int)$cat["questions_count"] ?></span></td>
                                <td>
                                    <?php if ((int)$cat["is_active"] === 1): ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form action="<?= htmlspecialchars($baseUrl) ?>/admin/categories/<?= (int)$cat["id"] ?>/toggle" method="POST" class="d-inline">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            <?= ((int)$cat["is_active"] === 1) ? "Desactivar" : "Activar" ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-plus-circle text-warning me-2"></i> Nueva Categoría
            </h5>
            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/categories/create" method="POST">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-3">
                    <label for="name" class="form-label text-white small fw-bold">Nombre</label>
                    <input type="text" class="form-control bg-dark border-secondary text-white" id="name" name="name" required placeholder="Ej: Arqueología Andina">
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label text-white small fw-bold">Slug URL (Opcional)</label>
                    <input type="text" class="form-control bg-dark border-secondary text-white" id="slug" name="slug" placeholder="arqueologia-andina">
                </div>

                <div class="mb-3">
                    <label for="icon_class" class="form-label text-white small fw-bold">Clase Bootstrap Icon</label>
                    <input type="text" class="form-control bg-dark border-secondary text-white" id="icon_class" name="icon_class" value="bi-book" required>
                </div>

                <div class="mb-3">
                    <label for="display_order" class="form-label text-white small fw-bold">Orden de visualización</label>
                    <input type="number" class="form-control bg-dark border-secondary text-white" id="display_order" name="display_order" value="<?= count($categories) + 1 ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label text-white small fw-bold">Descripción</label>
                    <textarea class="form-control bg-dark border-secondary text-white" id="description" name="description" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-peru w-100 py-2">
                    <i class="bi bi-check-lg me-1"></i> Guardar Categoría
                </button>
            </form>
        </div>
    </div>
</div>

