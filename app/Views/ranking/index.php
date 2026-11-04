<div class="py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-trophy-fill text-warning me-2"></i> Tabla de Clasificación Oficial
            </h2>
            <p class="text-muted small mb-0">Compite contra los mejores exponentes de la cultura nacional</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-peru">
            <i class="bi bi-lightning-charge-fill me-1"></i> Desafiar Ahora
        </a>
    </div>

    <!-- Pestañas de Clasificacion -->
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link <?= $tab === "1vs1" ? "active bg-danger text-white fw-bold" : "btn-outline-custom text-white" ?>" href="<?= htmlspecialchars($baseUrl) ?>/ranking?tab=1vs1">
                <i class="bi bi-person me-1"></i> Global 1 vs 1
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === "2vs2" ? "active bg-primary text-white fw-bold" : "btn-outline-custom text-white" ?>" href="<?= htmlspecialchars($baseUrl) ?>/ranking?tab=2vs2">
                <i class="bi bi-people me-1"></i> Equipos 2 vs 2
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === "weekly" ? "active bg-warning text-dark fw-bold" : "btn-outline-custom text-white" ?>" href="<?= htmlspecialchars($baseUrl) ?>/ranking?tab=weekly">
                <i class="bi bi-calendar-week me-1"></i> Semanal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === "monthly" ? "active bg-info text-dark fw-bold" : "btn-outline-custom text-white" ?>" href="<?= htmlspecialchars($baseUrl) ?>/ranking?tab=monthly">
                <i class="bi bi-calendar-month me-1"></i> Mensual
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === "category" ? "active bg-success text-white fw-bold" : "btn-outline-custom text-white" ?>" href="<?= htmlspecialchars($baseUrl) ?>/ranking?tab=category">
                <i class="bi bi-tags me-1"></i> Por Categoría
            </a>
        </li>
    </ul>

    <?php if ($tab === "category"): ?>
        <div class="card-custom p-3 mb-4">
            <form action="<?= htmlspecialchars($baseUrl) ?>/ranking" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="category">
                <div class="col-md-8">
                    <select name="category_id" class="form-select bg-dark border-secondary text-white" onchange="this.form.submit()">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat["id"] ?>" <?= $categoryId === (int)$cat["id"] ? "selected" : "" ?>>
                                <?= htmlspecialchars($cat["name"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-custom w-100">Filtrar Categoría</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tabla de Posiciones -->
    <div class="card-custom p-4">
        <?php if (!empty($rankings)): ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Puesto</th>
                            <th>Jugador</th>
                            <th>Nivel</th>
                            <?php if ($tab === "1vs1" || $tab === "2vs2"): ?>
                                <th>Clasificación ELO</th>
                                <th>Victorias</th>
                                <th>Efectividad</th>
                            <?php elseif ($tab === "weekly" || $tab === "monthly"): ?>
                                <th>Victorias en Periodo</th>
                                <th>Puntaje Total</th>
                            <?php elseif ($tab === "category"): ?>
                                <th>Respuestas Correctas</th>
                            <?php endif; ?>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rankings as $idx => $r): ?>
                            <tr>
                                <td>
                                    <span class="fs-5 fw-extrabold text-<?= $idx === 0 ? "warning" : ($idx === 1 ? "secondary" : ($idx === 2 ? "danger" : "muted")) ?>">
                                        #<?= $idx + 1 ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar-circle me-3">
                                            <?= strtoupper(substr($r["username"], 0, 1)) ?>
                                        </span>
                                        <div>
                                            <a href="<?= htmlspecialchars($baseUrl) ?>/profile/<?= htmlspecialchars($r["username"]) ?>" class="fw-bold text-white text-decoration-none">
                                                <?= htmlspecialchars($r["username"]) ?>
                                            </a>
                                            <div class="small text-muted"><?= htmlspecialchars($r["full_name"] ?? $r["username"]) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-level">Nvl <?= (int)$r["level"] ?></span></td>
                                <?php if ($tab === "1vs1" || $tab === "2vs2"): ?>
                                    <td><span class="badge-elo fs-6"><?= (int)$r["elo_rating"] ?> ELO</span></td>
                                    <td><strong class="text-success"><?= (int)($r["matches_won"] ?? 0) ?></strong> <span class="text-muted small">/ <?= (int)($r["matches_played"] ?? 0) ?></span></td>
                                    <td><span class="text-warning fw-bold"><?= (float)($r["win_ratio"] ?? 0) ?>%</span></td>
                                <?php elseif ($tab === "weekly" || $tab === "monthly"): ?>
                                    <td><span class="text-success fw-bold fs-6"><?= (int)$r["wins_count"] ?> victorias</span></td>
                                    <td><span class="text-warning fw-bold"><?= number_format((int)$r["total_weekly_score"] ?? (int)($r["total_monthly_score"] ?? 0)) ?> pts</span></td>
                                <?php elseif ($tab === "category"): ?>
                                    <td><span class="badge bg-success fs-6"><?= (int)$r["correct_answers_count"] ?> correctas</span></td>
                                <?php endif; ?>
                                <td class="text-end">
                                    <a href="<?= htmlspecialchars($baseUrl) ?>/profile/<?= htmlspecialchars($r["username"]) ?>" class="btn btn-sm btn-outline-custom">
                                        Ver Perfil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-award fs-1 mb-2"></i>
                <p>No se encontraron jugadores registrados en esta clasificación.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

