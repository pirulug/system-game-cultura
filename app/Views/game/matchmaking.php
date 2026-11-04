<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5 text-center">
        <div class="card-custom p-5">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger p-4 pulse-radar" style="width: 120px; height: 120px;">
                    <i class="bi bi-radar fs-1"></i>
                </div>
            </div>

            <h3 class="fw-bold text-white mb-2" id="searchTitle">Buscando Oponente...</h3>
            <p class="text-muted small mb-4" id="searchDesc">
                Modo: <strong class="text-white text-uppercase"><?= htmlspecialchars($mode) ?></strong> &bull; Emparejando por nivel de habilidad ELO
            </p>

            <div class="p-3 bg-dark rounded border border-secondary mb-4">
                <div class="small text-muted mb-1">Tiempo de búsqueda</div>
                <div class="fs-3 fw-bold text-warning" id="searchTimer">00:00</div>
            </div>

            <div class="progress mb-4" style="height: 6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger w-100"></div>
            </div>

            <button type="button" class="btn btn-outline-danger w-100" id="btnCancelQueue">
                <i class="bi bi-x-circle me-2"></i> Cancelar Búsqueda
            </button>
        </div>
    </div>
</div>

<script>
    window.MATCHMAKING_MODE = "<?= htmlspecialchars($mode) ?>";
    window.BASE_URL = "<?= htmlspecialchars($baseUrl) ?>";
</script>
<script src="<?= htmlspecialchars($baseUrl) ?>/js/matchmaking.js"></script>

