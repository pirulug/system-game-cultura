<div class="card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary">
        <div>
            <h3 class="fw-bold text-white mb-0">Supervisión e Historial de Partidas</h3>
            <div class="text-muted small">Inspección de resultados, detección de anomalías y auditoría de duelos</div>
        </div>
    </div>

    <?php if (!empty($matches)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modalidad</th>
                        <th>Fecha de Inicio</th>
                        <th>Duración</th>
                        <th>Participantes y Marcador</th>
                        <th>Ganador</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $m): ?>
                        <tr>
                            <td class="text-muted small">#<?= (int)$m["id"] ?></td>
                            <td><span class="badge bg-danger"><?= htmlspecialchars($m["mode"]) ?></span></td>
                            <td class="small text-white"><?= date("d/m/Y H:i:s", strtotime($m["started_at"] ?? $m["created_at"])) ?></td>
                            <td class="small text-muted">
                                <?php
                                if (!empty($m["started_at"]) && !empty($m["finished_at"])) {
                                    $diff = strtotime($m["finished_at"]) - strtotime($m["started_at"]);
                                    echo max(1, $diff) . " seg";
                                } else {
                                    echo "--";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($m["players"])): ?>
                                    <div class="small">
                                        <?php foreach ($m["players"] as $p): ?>
                                            <span class="d-inline-block me-2">
                                                <strong><?= htmlspecialchars($p["username"]) ?></strong>
                                                (<?= (int)$p["score"] ?> pts)
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">Sin datos</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark fw-bold">
                                    <?= htmlspecialchars($m["winner_name"] ?? ($m["winner_team"] ? "Equipo " . $m["winner_team"] : "Empate")) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?= htmlspecialchars($m["status"]) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted text-center py-5 mb-0">No hay partidas registradas en el historial.</p>
    <?php endif; ?>
</div>

