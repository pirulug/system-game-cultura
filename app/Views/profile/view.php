<div class="py-4">
    <div class="row g-4">
        <!-- Tarjeta de Perfil Lateral -->
        <div class="col-lg-4">
            <div class="card-custom p-4 text-center mb-4">
                <div class="mb-3 position-relative d-inline-block">
                    <span class="avatar-circle avatar-lg">
                        <?= strtoupper(substr($user["username"], 0, 1)) ?>
                    </span>
                    <span class="status-indicator status-<?= htmlspecialchars($profile["connection_status"] ?? "offline") ?> position-absolute bottom-0 end-0 p-2" title="<?= htmlspecialchars($profile["connection_status"] ?? "offline") ?>"></span>
                </div>

                <h4 class="fw-bold text-white mb-1"><?= htmlspecialchars($profile["full_name"] ?? $user["username"]) ?></h4>
                <div class="text-muted small mb-2">@<?= htmlspecialchars($user["username"]) ?></div>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge-level">Nivel <?= (int)$profile["level"] ?></span>
                    <span class="badge bg-secondary"><?= htmlspecialchars($user["role"]) ?></span>
                </div>

                <p class="text-muted small mb-4">
                    <?= !empty($profile["bio"]) ? htmlspecialchars($profile["bio"]) : "Sin biografía disponible." ?>
                </p>

                <div class="row g-2 border-top border-secondary pt-3 text-start small">
                    <div class="col-6 text-muted">ID de Jugador:</div>
                    <div class="col-6 text-end text-white fw-bold">#<?= (int)$user["id"] ?></div>
                    <div class="col-6 text-muted">Registrado el:</div>
                    <div class="col-6 text-end text-white"><?= date("d/m/Y", strtotime($user["created_at"])) ?></div>
                    <div class="col-6 text-muted">Puntos Totales:</div>
                    <div class="col-6 text-end text-warning fw-bold"><?= number_format((int)$profile["total_points"]) ?> pts</div>
                    <div class="col-6 text-muted">Experiencia:</div>
                    <div class="col-6 text-end text-white"><?= number_format((int)$profile["experience_points"]) ?> XP</div>
                </div>

                <div class="mt-4 pt-3 border-top border-secondary">
                    <?php if ($isOwnProfile): ?>
                        <a href="<?= htmlspecialchars($baseUrl) ?>/profile/edit" class="btn btn-outline-custom w-100">
                            <i class="bi bi-pencil me-1"></i> Editar Mi Perfil
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-peru w-100" onclick="sendFriendRequest(<?= (int)$user['id'] ?>)">
                            <i class="bi bi-person-plus me-1"></i> Enviar Solicitud de Amistad
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resumen ELO -->
            <div class="card-custom p-4">
                <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                    <i class="bi bi-trophy-fill text-warning me-2"></i> Puntuación Competitiva
                </h5>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-bold text-white">Duelos 1 vs 1</div>
                        <div class="small text-muted">Máx: <?= (int)($profile["peak_elo_1vs1"] ?? 1200) ?> ELO</div>
                    </div>
                    <span class="badge-elo fs-6"><?= (int)($profile["elo_1vs1"] ?? 1200) ?> ELO</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-white">Equipos 2 vs 2</div>
                        <div class="small text-muted">Máx: <?= (int)($profile["peak_elo_2vs2"] ?? 1200) ?> ELO</div>
                    </div>
                    <span class="badge bg-primary fs-6"><?= (int)($profile["elo_2vs2"] ?? 1200) ?> ELO</span>
                </div>
            </div>
        </div>

        <!-- Estadisticas, Logros e Historial -->
        <div class="col-lg-8">
            <!-- Metricas de Desempeno -->
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                    <i class="bi bi-graph-up text-danger me-2"></i> Estadísticas de Combate
                </h5>
                <div class="row g-3 text-center">
                    <div class="col-3">
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <div class="fs-4 fw-bold text-white"><?= (int)($profile["matches_played"] ?? 0) ?></div>
                            <div class="small text-muted">Partidas</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <div class="fs-4 fw-bold text-success"><?= (int)($profile["matches_won"] ?? 0) ?></div>
                            <div class="small text-muted">Victorias</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <div class="fs-4 fw-bold text-danger"><?= (int)($profile["matches_lost"] ?? 0) ?></div>
                            <div class="small text-muted">Derrotas</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <div class="fs-4 fw-bold text-warning"><?= (float)($profile["win_ratio"] ?? 0) ?>%</div>
                            <div class="small text-muted">Efectividad</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medallas y Logros -->
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                    <i class="bi bi-award-fill text-warning me-2"></i> Medallas e Insignias Desbloqueadas
                </h5>
                <div class="row g-3">
                    <?php foreach ($achievements as $ach): ?>
                        <?php $unlocked = !empty($ach["unlocked_at"]); ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded border <?= $unlocked ? "border-warning bg-dark" : "border-secondary bg-transparent opacity-50" ?> d-flex align-items-center">
                                <span class="p-2 rounded-circle <?= $unlocked ? "bg-warning-subtle text-warning" : "bg-secondary-subtle text-secondary" ?> fs-4 me-3">
                                    <i class="bi <?= htmlspecialchars($ach["badge_icon"]) ?>"></i>
                                </span>
                                <div>
                                    <div class="fw-bold text-white small"><?= htmlspecialchars($ach["name"]) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($ach["description"]) ?></div>
                                    <?php if ($unlocked): ?>
                                        <div class="text-success small mt-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-check-circle me-1"></i> Desbloqueado el <?= date("d/m/Y", strtotime($ach["unlocked_at"])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Historial de Partidas -->
            <div class="card-custom p-4">
                <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                    <i class="bi bi-clock-history text-info me-2"></i> Historial Reciente de Enfrentamientos
                </h5>
                <?php if (!empty($matches)): ?>
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Modo</th>
                                    <th>Puntaje</th>
                                    <th>Aciertos</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matches as $m): ?>
                                    <?php
                                    $isWin = ($m["mode"] === "2vs2") ? ($m["team"] === $m["winner_team"]) : ($m["winner_user_id"] == $user["id"]);
                                    ?>
                                    <tr>
                                        <td class="small text-muted"><?= date("d/m/Y H:i", strtotime($m["finished_at"] ?? $m["created_at"])) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($m["mode"]) ?></span></td>
                                        <td class="fw-bold text-white"><?= (int)$m["score"] ?> pts</td>
                                        <td><?= (int)$m["correct_answers"] ?>/<?= (int)$m["total_questions"] ?></td>
                                        <td>
                                            <?php if ($isWin): ?>
                                                <span class="badge bg-success">Victoria</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Derrota</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small text-center py-3 mb-0">Sin partidas registradas recientemente.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

