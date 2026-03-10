<?php
declare(strict_types=1);

namespace Staff\Models;

use Staff\Core\Database;
use PDO;

/**
 * Modèle Statistiques — toutes les requêtes du dashboard admin
 */
class StatsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // --------------------------------------------------------
    // 1. UTILISATEURS
    // --------------------------------------------------------

    public function totalUtilisateurs(): int
    {
        return (int)$this->db
            ->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")
            ->fetchColumn();
    }

    public function repartitionUtilisateurs(): array
    {
        $stmt = $this->db->query("
            SELECT
                SUM(CASE WHEN role = 'entreprise' THEN 1 ELSE 0 END)   AS entreprises,
                SUM(CASE WHEN role = 'chercheur'  THEN 1 ELSE 0 END)   AS chercheurs,
                (SELECT COUNT(DISTINCT user_id) FROM abonnements
                 WHERE statut = 'actif' AND fin >= CURDATE())            AS abonnes
            FROM users WHERE role != 'admin'
        ");
        $row = $stmt->fetch();
        $row['non_abonnes'] = max(0, (int)$row['chercheurs'] - (int)$row['abonnes']);
        return $row;
    }

    /** Inscriptions par période: 'week' | 'month' | 'year' */
    public function inscriptionsParPeriode(string $periode = 'month', int $limite = 12): array
    {
        $format = match($periode) {
            'week'  => '%Y-%u',
            'year'  => '%Y',
            default => '%Y-%m',
        };
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(created_at, :fmt) AS periode,
                   COUNT(*) AS total
            FROM users
            WHERE role != 'admin'
            GROUP BY periode
            ORDER BY periode DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':fmt', $format);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }

    // --------------------------------------------------------
    // 2. ABONNEMENTS
    // --------------------------------------------------------

    public function totalAbonnementsActifs(): int
    {
        return (int)$this->db
            ->query("SELECT COUNT(*) FROM abonnements WHERE statut='actif' AND fin >= CURDATE()")
            ->fetchColumn();
    }

    public function abonnementsParPeriode(string $periode = 'month', int $limite = 12): array
    {
        $format = match($periode) {
            'week'  => '%Y-%u',
            'year'  => '%Y',
            default => '%Y-%m',
        };
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(created_at, :fmt) AS periode,
                   COUNT(*) AS total,
                   SUM(montant) AS revenus
            FROM abonnements
            GROUP BY periode
            ORDER BY periode DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':fmt', $format);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }

    // --------------------------------------------------------
    // 3. CANDIDATURES / DEMANDES
    // --------------------------------------------------------

    public function totalCandidatures(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();
    }

    public function repartitionDepots(): array
    {
        $stmt = $this->db->query("
            SELECT type_depot, COUNT(*) AS total
            FROM candidatures
            GROUP BY type_depot
        ");
        $result = ['unitaire' => 0, 'abonnement' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['type_depot']] = (int)$row['total'];
        }
        return $result;
    }

    public function candidaturesParPeriode(string $periode = 'month', int $limite = 12): array
    {
        $format = match($periode) {
            'week'  => '%Y-%u',
            'year'  => '%Y',
            default => '%Y-%m',
        };
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(created_at, :fmt) AS periode,
                   COUNT(*) AS total,
                   SUM(CASE WHEN type_depot='abonnement' THEN 1 ELSE 0 END) AS via_abonnement,
                   SUM(CASE WHEN type_depot='unitaire'   THEN 1 ELSE 0 END) AS unitaires
            FROM candidatures
            GROUP BY periode
            ORDER BY periode DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':fmt', $format);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }

    // --------------------------------------------------------
    // 4. TRAITEMENT DES DEMANDES
    // --------------------------------------------------------

    public function traitementDemandes(): array
    {
        $stmt = $this->db->query("
            SELECT
                SUM(CASE WHEN statut IN ('acceptee','refusee','stage_en_cours') THEN 1 ELSE 0 END) AS traitees,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) AS non_traitees
            FROM candidatures
        ");
        return $stmt->fetch();
    }

    public function traitementParPeriode(string $periode = 'month', int $limite = 12): array
    {
        $format = match($periode) {
            'week'  => '%Y-%u',
            'year'  => '%Y',
            default => '%Y-%m',
        };
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(updated_at, :fmt) AS periode,
                   SUM(CASE WHEN statut IN ('acceptee','refusee','stage_en_cours') THEN 1 ELSE 0 END) AS traitees,
                   SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) AS non_traitees
            FROM candidatures
            WHERE statut != 'en_attente' OR updated_at != created_at
            GROUP BY periode
            ORDER BY periode DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':fmt', $format);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }

    // --------------------------------------------------------
    // 5. REVENUS
    // --------------------------------------------------------

    public function totalRevenus(): float
    {
        return (float)$this->db
            ->query("SELECT COALESCE(SUM(montant),0) FROM paiements WHERE statut='succes'")
            ->fetchColumn();
    }

    public function revenusParPeriode(string $periode = 'month', int $limite = 12): array
    {
        $format = match($periode) {
            'week'  => '%Y-%u',
            'year'  => '%Y',
            default => '%Y-%m',
        };
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(created_at, :fmt) AS periode,
                   SUM(montant) AS revenus,
                   COUNT(*) AS transactions
            FROM paiements
            WHERE statut = 'succes'
            GROUP BY periode
            ORDER BY periode DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':fmt', $format);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }
}
