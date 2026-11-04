<?php
$isWinner = ($match["winner_user_id"] == $currentUserId);
$isTie = ($match["winner_user_id"] === null && ($match["winner_team"] === "tie" || empty($match["winner_team"])));

if ($match["mode"] === "2vs2") {
    $myTeam = "A";
    foreach ($players as $p) {
        if ((int)$p["user_id"] === $currentUserId) {
            $myTeam = $p["team"];
            break;
        }
    }
    $isWinner = ($match["winner_team"] === $myTeam);
}
?>

<div class="row justify-content-center py-4">
    <div class="col-lg-8">
        <div class="card-custom p-4 p-md-5 text-center mb-4">
            <!-- Icono y Titulo de Resultado -->
            <div class="mb-4">
                <?php if ($isWinner): ?>
                    <span class="d-inline-flex p-4 rounded-circle bg-success-subtle text-success fs-1">
                        <i class="bi bi-trophy-fill"></i>
                    </span>
                    <h2 class="display-5 fw-extrabold text-success mt-3 mb-1">¡Victoria Bicentenario!</h2>
                    <p class="text-muted">Has triunfado en la arena del conocimiento peruano</p>
                <?php elseif ($isTie): ?>
                    <span class="d-inline-flex p-4 rounded-circle bg-warning-subtle text-warning fs-1">
                        <i class="bi bi-hand-thumbs-up-fill"></i>
                    </span>
                    <h2 class="display-5 fw-extrabold text-warning mt-3 mb-1">¡Empate Épico!</h2>
                    <p class="text-muted">Un duelo reñido de alto calibre intelectual</p>
                <?php else: ?>
                    <span class="d-inline-flex p-4 rounded-circle bg-danger-subtle text-danger fs-1">
                        <i class="bi bi-shield-x"></i>
                    </span>
                    <h2 class="display-5 fw-extrabold text-danger mt-3 mb-1">Derrota de Batalla</h2>
                    <p class="text-muted">Sigue practicando para alcanzar la gloria en el ranking</p>
                <?php endif; ?>
            </div>

            <!-- Tabla de Participantes y Desempeno -->
            <div class="table-responsive mb-4">
                <table class="table table-custom text-start">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th>Puntuación</th>
                            <th>Aciertos</th>
                            <th>Cambio ELO</th>
                            <th>XP Ganada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $p): ?>
                            <tr class="<?= ((int)$p["user_id"] === $currentUserId) ? "table-active border-warning" : "" ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar-circle me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($p["username"], 0, 1)) ?>
                                        </span>
                                        <div>
                                            <span class="fw-bold text-white"><?= htmlspecialchars($p["username"]) ?></span>
                                            <?php if ((int)$p["user_id"] === $currentUserId): ?>
                                                <span class="badge bg-warning text-dark ms-1">Tú</span>
                                            <?php endif; ?>
                                            <?php if ($match["mode"] === "2vs2"): ?>
                                                <span class="badge <?= $p["team"] === "A" ? "bg-primary" : "bg-danger" ?> ms-1">Equipo <?= htmlspecialchars($p["team"]) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-extrabold text-warning"><?= (int)$p["score"] ?> pts</td>
                                <td><?= (int)$p["correct_answers"] ?>/<?= (int)$match["total_questions"] ?></td>
                                <td>
                                    <?php if ((int)$p["rating_change"] > 0): ?>
                                        <span class="text-success fw-bold">+<?= (int)$p["rating_change"] ?> ELO</span>
                                    <?php elseif ((int)$p["rating_change"] < 0): ?>
                                        <span class="text-danger fw-bold"><?= (int)$p["rating_change"] ?> ELO</span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-warning fw-bold">+<?= (int)$p["experience_gained"] ?> XP</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Botones de Navegacion -->
            <div class="d-flex flex-wrap justify-content-center gap-3 pt-3 border-top border-secondary">
                <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-peru px-4 py-2">
                    <i class="bi bi-arrow-repeat me-2"></i> Jugar Otra Partida
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/ranking" class="btn btn-outline-custom px-4 py-2">
                    <i class="bi bi-trophy me-2"></i> Ver Clasificación
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/dashboard" class="btn btn-outline-secondary px-4 py-2">
                    <i class="bi bi-speedometer2 me-2"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

