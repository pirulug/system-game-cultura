<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary-subtle gap-3">
    <div>
        <h3 class="fw-bold text-white mb-0">Gestión de Preguntas</h3>
        <div class="text-muted small">Total registradas: <?= number_format($totalQuestions) ?> preguntas</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions/create" class="btn btn-peru">
            <i class="bi bi-plus-circle me-1"></i> Nueva Pregunta
        </a>
        <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions/import" class="btn btn-outline-custom">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Importar CSV
        </a>
    </div>
</div>

<!-- Filtros de Busqueda -->
<div class="card-custom p-3 mb-4">
    <form action="<?= htmlspecialchars($baseUrl) ?>/admin/questions" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" placeholder="Buscar por enunciado o explicación..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="0">Todas las Categorías</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat["id"] ?>" <?= $categoryId === (int)$cat["id"] ? "selected" : "" ?>>
                        <?= htmlspecialchars($cat["name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-peru flex-grow-1">Filtrar</button>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions" class="btn btn-outline-custom">Limpiar</a>
        </div>
    </form>
</div>

<!-- Tabla de Preguntas -->
<div class="card-custom p-0 overflow-hidden">
    <?php if (!empty($questions)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 180px;">Categoría</th>
                        <th>Pregunta</th>
                        <th style="width: 110px;">Dificultad</th>
                        <th style="width: 90px;">Estado</th>
                        <th class="text-end" style="width: 110px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $q): ?>
                        <tr>
                            <td class="text-muted small">#<?= (int)$q["id"] ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($q["category_name"]) ?></span>
                            </td>
                            <td class="fw-semibold text-white"><?= htmlspecialchars($q["question_text"]) ?></td>
                            <td>
                                <span class="badge bg-<?= $q["difficulty"] === "facil" ? "success" : ($q["difficulty"] === "intermedio" ? "warning" : "danger") ?>">
                                    <?= htmlspecialchars($q["difficulty"]) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ((int)$q["is_active"] === 1): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions/<?= (int)$q["id"] ?>/edit" class="btn btn-sm btn-outline-warning py-1 px-2" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/admin/questions/<?= (int)$q["id"] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta pregunta?')">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginacion -->
        <?php if ($totalPages > 1): ?>
            <div class="p-3 border-top border-secondary-subtle">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $currentPage ? "active" : "" ?>">
                                <a class="page-link" href="<?= htmlspecialchars($baseUrl) ?>/admin/questions?page=<?= $p ?>&category_id=<?= $categoryId ?>&q=<?= urlencode($search) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-muted text-center py-5 mb-0">No se encontraron preguntas registradas con los criterios dados.</p>
    <?php endif; ?>
</div>
