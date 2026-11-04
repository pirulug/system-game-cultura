<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary-subtle gap-3">
    <div>
        <h3 class="fw-bold text-white mb-0">Panel de Control Administrativo</h3>
        <div class="text-muted small">Supervisión en tiempo real de métricas, jugadores y partidas</div>
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

<!-- Tarjetas de Metricas Principales -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card-custom p-3 text-center">
            <div class="fs-4 fw-bold text-white"><?= number_format($metrics["total_users"]) ?></div>
            <div class="small text-muted" style="font-size: 0.75rem;">Usuarios Registrados</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card-custom p-3 text-center">
            <div class="fs-4 fw-bold text-success"><?= number_format($metrics["online_users"]) ?></div>
            <div class="small text-muted" style="font-size: 0.75rem;">Jugadores Conectados</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card-custom p-3 text-center">
            <div class="fs-4 fw-bold text-danger"><?= number_format($metrics["total_matches"]) ?></div>
            <div class="small text-muted" style="font-size: 0.75rem;">Partidas Realizadas</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card-custom p-3 text-center">
            <div class="fs-4 fw-bold text-warning"><?= number_format($metrics["active_matches"]) ?></div>
            <div class="small text-muted" style="font-size: 0.75rem;">Partidas en Curso</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card-custom p-3 text-center">
            <div class="fs-4 fw-bold text-info"><?= number_format($metrics["total_questions"]) ?></div>
            <div class="small text-muted" style="font-size: 0.75rem;">Preguntas Activas</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card-custom p-3 text-center">
            <div class="fs-4 fw-bold text-secondary"><?= number_format($metrics["total_categories"]) ?></div>
            <div class="small text-muted" style="font-size: 0.75rem;">Categorías Oficiales</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Auditoria de Actividades Administrativas -->
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary-subtle">
                <i class="bi bi-journal-text text-danger me-2"></i> Registro de Auditoría Administrativa
            </h5>
            <?php if (!empty($recentLogs)): ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Administrador</th>
                                <th>Acción</th>
                                <th>Entidad</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td class="small text-muted"><?= date("d/m H:i", strtotime($log["created_at"])) ?></td>
                                    <td class="fw-semibold text-white"><?= htmlspecialchars($log["admin_username"]) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($log["action"]) ?></span></td>
                                    <td class="small text-muted"><?= htmlspecialchars($log["entity_type"]) ?> #<?= (int)$log["entity_id"] ?></td>
                                    <td class="small text-white"><?= htmlspecialchars($log["details"] ?? "") ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small text-center py-3 mb-0">No hay registros de auditoría recientes.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ultimas Partidas en la Plataforma -->
    <div class="col-lg-5">
        <div class="card-custom p-4">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary-subtle">
                <i class="bi bi-joystick text-warning me-2"></i> Partidas Concluidas Recientes
            </h5>
            <?php if (!empty($recentMatches)): ?>
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach ($recentMatches as $m): ?>
                        <div class="list-group-item bg-transparent text-white px-0 py-2 border-secondary-subtle">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-danger me-1"><?= htmlspecialchars($m["mode"]) ?></span>
                                    <span class="small fw-bold">Partida #<?= (int)$m["id"] ?></span>
                                </div>
                                <span class="small text-muted"><?= date("d/m H:i", strtotime($m["finished_at"] ?? $m["created_at"])) ?></span>
                            </div>
                            <div class="small text-muted mt-1">
                                Ganador: <strong class="text-warning"><?= htmlspecialchars($m["winner_name"] ?? ($m["winner_team"] ? "Equipo " . $m["winner_team"] : "Empate")) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small text-center py-3 mb-0">Sin partidas registradas recientemente.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
