<?php
declare(strict_types=1);

namespace Staff\Admin;

use Staff\Auth\Auth;
use Staff\Models\StatsModel;

/**
 * Contrôleur — Dashboard Statistiques Admin
 */
class StatsController
{
    private StatsModel $stats;

    public function __construct()
    {
        Auth::requireRole('admin');
        $this->stats = new StatsModel();
    }

    /** Page principale dashboard */
    public function index(): void
    {
        $periode = $_GET['periode'] ?? 'month';
        $periode = in_array($periode, ['week', 'month', 'year']) ? $periode : 'month';

        $data = [
            'periode'                => $periode,
            'total_users'            => $this->stats->totalUtilisateurs(),
            'repartition'            => $this->stats->repartitionUtilisateurs(),
            'inscriptions'           => $this->stats->inscriptionsParPeriode($periode),
            'total_abonnements'      => $this->stats->totalAbonnementsActifs(),
            'abonnements_periode'    => $this->stats->abonnementsParPeriode($periode),
            'total_candidatures'     => $this->stats->totalCandidatures(),
            'depots'                 => $this->stats->repartitionDepots(),
            'candidatures_periode'   => $this->stats->candidaturesParPeriode($periode),
            'traitement'             => $this->stats->traitementDemandes(),
            'traitement_periode'     => $this->stats->traitementParPeriode($periode),
            'total_revenus'          => $this->stats->totalRevenus(),
            'revenus_periode'        => $this->stats->revenusParPeriode($periode),
        ];

        extract($data);
        require BASE_PATH . '/src/Views/admin/stats.php';
    }

    /** Endpoint AJAX pour rafraîchir les graphiques */
    public function ajax(): void
    {
        header('Content-Type: application/json');
        $periode = $_GET['periode'] ?? 'month';
        $periode = in_array($periode, ['week', 'month', 'year']) ? $periode : 'month';
        $type    = $_GET['type'] ?? 'inscriptions';

        $payload = match($type) {
            'inscriptions'  => $this->stats->inscriptionsParPeriode($periode),
            'abonnements'   => $this->stats->abonnementsParPeriode($periode),
            'candidatures'  => $this->stats->candidaturesParPeriode($periode),
            'traitement'    => $this->stats->traitementParPeriode($periode),
            'revenus'       => $this->stats->revenusParPeriode($periode),
            default         => [],
        };

        echo json_encode(['success' => true, 'data' => $payload]);
    }
}
