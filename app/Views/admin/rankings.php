<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h4 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-trophy text-warning me-2"></i> Estado Actual de Puntuaciones ELO
            </h4>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>Jugador</th>
                            <th>Nivel</th>
                            <th>ELO Actual</th>
                            <th>Pico ELO</th>
                            <th>Victorias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rankings as $idx => $r): ?>
                            <tr>
                                <td class="fw-bold text-warning">#<?= $idx + 1 ?></td>
                                <td>
                                    <div class="fw-bold text-white"><?= htmlspecialchars($r["username"]) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($r["full_name"] ?? "") ?></div>
                                </td>
                                <td><span class="badge-level">Nvl <?= (int)$r["level"] ?></span></td>
                                <td><span class="badge-elo fs-6"><?= (int)$r["elo_rating"] ?> ELO</span></td>
                                <td><span class="text-muted small"><?= (int)$r["peak_elo_rating"] ?> ELO</span></td>
                                <td><strong class="text-success"><?= (int)($r["matches_won"] ?? 0) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4 border-warning">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-arrow-clockwise text-warning me-2"></i> Reiniciar Temporada Competitiva
            </h5>
            <p class="text-muted small mb-3">
                Esta acción archiva la temporada actual y aplica un reinicio suave de puntuación ELO hacia el valor base (1200) para todos los jugadores activos.
            </p>
            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/season/reset" method="POST" onsubmit="return confirm('¿Estás seguro de reiniciar la temporada competitiva? Esta acción quedará registrada en el log de auditoría.')">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-3">
                    <label for="season_name" class="form-label text-white small fw-bold">Nombre de la Temporada a Archivar</label>
                    <input type="text" class="form-control bg-dark border-secondary text-white" id="season_name" name="season_name" required value="Temporada Q<?= ceil(date("n") / 3) ?> - <?= date("Y") ?>">
                </div>

                <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-dark">
                    <i class="bi bi-shield-lock me-1"></i> Confirmar Reinicio de Temporada
                </button>
            </form>
        </div>
    </div>
</div>

