<div class="py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-people-fill text-danger me-2"></i> Amigos y Desafíos Directos
            </h2>
            <p class="text-muted small mb-0">Conéctate con otros jugadores, sigue su actividad y enfréntate a duelos privados</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl) ?>/game/room/create" class="btn btn-gold">
            <i class="bi bi-plus-circle me-1"></i> Crear Sala Privada
        </a>
    </div>

    <!-- Buscador de Jugadores -->
    <div class="card-custom p-4 mb-4">
        <h5 class="fw-bold text-white mb-3">
            <i class="bi bi-search text-warning me-2"></i> Buscar Jugadores en la Plataforma
        </h5>
        <form action="<?= htmlspecialchars($baseUrl) ?>/friends" method="GET" class="row g-2">
            <div class="col-md-9">
                <input type="text" name="q" class="form-control bg-dark border-secondary text-white" placeholder="Escribe el nombre de usuario o nombre completo..." value="<?= htmlspecialchars($searchQuery) ?>" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-peru w-100">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </div>
        </form>

        <?php if (!empty($searchQuery)): ?>
            <div class="mt-4 pt-3 border-top border-secondary">
                <h6 class="text-muted mb-3">Resultados de la búsqueda:</h6>
                <?php if (!empty($searchResults)): ?>
                    <div class="row g-3">
                        <?php foreach ($searchResults as $res): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar-circle me-2" style="width: 36px; height: 36px;">
                                            <?= strtoupper(substr($res["username"], 0, 1)) ?>
                                        </span>
                                        <div>
                                            <a href="<?= htmlspecialchars($baseUrl) ?>/profile/<?= htmlspecialchars($res["username"]) ?>" class="fw-bold text-white text-decoration-none small">
                                                <?= htmlspecialchars($res["username"]) ?>
                                            </a>
                                            <div class="text-muted" style="font-size: 0.75rem;">Nivel <?= (int)$res["level"] ?> &bull; <?= (int)($res["elo_rating"] ?? 1200) ?> ELO</div>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-success" onclick="sendFriendRequest(<?= (int)$res['id'] ?>)">
                                        <i class="bi bi-person-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-0">No se encontraron jugadores que coincidan con la búsqueda.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Solicitudes Pendientes -->
        <?php if (!empty($pendingRequests)): ?>
            <div class="col-12">
                <div class="card-custom p-4 border-warning">
                    <h5 class="fw-bold text-white mb-3">
                        <i class="bi bi-bell-fill text-warning me-2"></i> Solicitudes de Amistad Pendientes (<?= count($pendingRequests) ?>)
                    </h5>
                    <div class="row g-3">
                        <?php foreach ($pendingRequests as $req): ?>
                            <div class="col-md-6 col-lg-4" id="req-card-<?= (int)$req["id"] ?>">
                                <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar-circle me-2" style="width: 36px; height: 36px;">
                                            <?= strtoupper(substr($req["username"], 0, 1)) ?>
                                        </span>
                                        <div>
                                            <div class="fw-bold text-white small"><?= htmlspecialchars($req["username"]) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Nvl <?= (int)$req["level"] ?> &bull; <?= date("d/m", strtotime($req["created_at"])) ?></div>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-success" onclick="respondFriendRequest(<?= (int)$req['id'] ?>, 'accept')">Aceptar</button>
                                        <button class="btn btn-outline-danger" onclick="respondFriendRequest(<?= (int)$req['id'] ?>, 'reject')">Rechazar</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de Amigos Confirmados -->
        <div class="col-12">
            <div class="card-custom p-4">
                <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                    <i class="bi bi-person-check-fill text-success me-2"></i> Mis Amigos (<?= count($friends) ?>)
                </h5>
                <?php if (!empty($friends)): ?>
                    <div class="row g-3">
                        <?php foreach ($friends as $f): ?>
                            <div class="col-md-6 col-lg-4" id="friend-box-<?= (int)$f["id"] ?>">
                                <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative me-3">
                                            <span class="avatar-circle">
                                                <?= strtoupper(substr($f["username"], 0, 1)) ?>
                                            </span>
                                            <span class="status-indicator status-<?= htmlspecialchars($f["connection_status"] ?? "offline") ?> position-absolute bottom-0 end-0"></span>
                                        </div>
                                        <div>
                                            <a href="<?= htmlspecialchars($baseUrl) ?>/profile/<?= htmlspecialchars($f["username"]) ?>" class="fw-bold text-white text-decoration-none">
                                                <?= htmlspecialchars($f["username"]) ?>
                                            </a>
                                            <div class="text-muted small">
                                                Nvl <?= (int)$f["level"] ?> &bull; <?= (int)($f["elo_rating"] ?? 1200) ?> ELO
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                Estado: <span class="text-<?= $f["connection_status"] === "online" ? "success" : ($f["connection_status"] === "in_game" ? "warning" : "muted") ?>">
                                                    <?= htmlspecialchars($f["connection_status"] ?? "offline") ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="<?= htmlspecialchars($baseUrl) ?>/game/room/create" class="btn btn-sm btn-peru" title="Crear sala para retar">
                                            <i class="bi bi-controller"></i> Retar
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="removeFriend(<?= (int)$f['id'] ?>)" title="Eliminar amigo">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 mb-2"></i>
                        <p class="mb-2">Aún no tienes amigos agregados a tu lista.</p>
                        <p class="small">Utiliza el buscador superior para encontrar a otros jugadores y enviarles una solicitud de amistad.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    window.BASE_URL = "<?= htmlspecialchars($baseUrl) ?>";
</script>
<script src="<?= htmlspecialchars($baseUrl) ?>/js/friends.js"></script>

