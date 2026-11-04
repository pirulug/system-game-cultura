<div class="card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h3 class="fw-bold text-white mb-0">Gestión de Usuarios y Jugadores</h3>
            <div class="text-muted small">Control de cuentas, roles y estados de suspensión</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Rol</th>
                    <th>Nivel</th>
                    <th>ELO 1vs1</th>
                    <th>Partidas (V/D)</th>
                    <th>Efectividad</th>
                    <th>Estado</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="text-muted small">#<?= (int)$u["id"] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar-circle me-2" style="width: 28px; height: 28px; font-size: 0.72rem;">
                                    <?= strtoupper(substr($u["username"], 0, 1)) ?>
                                </span>
                                <div>
                                    <div class="fw-semibold text-white"><?= htmlspecialchars($u["username"]) ?></div>
                                    <div class="small text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($u["email"]) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="small text-white"><?= htmlspecialchars($u["full_name"] ?? "--") ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($u["role"]) ?></span></td>
                        <td><span class="badge-level">Nvl <?= (int)($u["level"] ?? 1) ?></span></td>
                        <td><span class="badge-elo"><?= (int)($u["elo_1vs1"] ?? 1200) ?> ELO</span></td>
                        <td class="small">
                            <span class="text-success fw-semibold"><?= (int)($u["matches_won"] ?? 0) ?></span> /
                            <span class="text-danger fw-semibold"><?= (int)($u["matches_lost"] ?? 0) ?></span>
                        </td>
                        <td class="small text-warning fw-semibold"><?= (float)($u["win_ratio"] ?? 0) ?>%</td>
                        <td>
                            <?php if ($u["status"] === "active"): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php elseif ($u["status"] === "suspended"): ?>
                                <span class="badge bg-danger">Suspendido</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($u["status"]) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($u["role"] !== "admin"): ?>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/admin/users/<?= (int)$u["id"] ?>/toggle-status" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas cambiar el estado de este usuario?')">
                                    <?= \App\Core\CSRF::field() ?>
                                    <?php if ($u["status"] === "active"): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">Suspender</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2">Reactivar</button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Protegido</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
