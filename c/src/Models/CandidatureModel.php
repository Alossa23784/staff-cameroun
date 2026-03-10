<?php
declare(strict_types=1);

namespace Staff\Models;

use Staff\Core\Database;
use PDO;

/**
 * Modèle Candidature
 * Gère candidatures, notation stagiaires, stages en cours
 */
class CandidatureModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Candidatures entreprise ──────────────────────────

    public function getParEntreprise(int $entrepriseId, string $statut = ''): array
    {
        $sql = "
            SELECT c.*, 
                   u.email AS chercheur_email,
                   ch.nom, ch.prenom, ch.photo, ch.note_moyenne
            FROM candidatures c
            JOIN chercheurs ch ON ch.id = c.chercheur_id
            JOIN users u ON u.id = ch.user_id
            WHERE c.entreprise_id = ?
        ";
        $params = [$entrepriseId];

        if ($statut) {
            $sql .= " AND c.statut = ?";
            $params[] = $statut;
        }

        $sql .= " ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Candidatures d'un chercheur */
    public function getParChercheur(int $chercheurId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, e.nom AS entreprise_nom, e.logo AS entreprise_logo
            FROM candidatures c
            JOIN entreprises e ON e.id = c.entreprise_id
            WHERE c.chercheur_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$chercheurId]);
        return $stmt->fetchAll();
    }

    // ── Changer statut ───────────────────────────────────

    public function changerStatut(int $candidatureId, int $entrepriseId, string $statut): bool
    {
        $stmt = $this->db->prepare("
            UPDATE candidatures
            SET statut = ?, updated_at = NOW()
            WHERE id = ? AND entreprise_id = ?
        ");
        $stmt->execute([$statut, $candidatureId, $entrepriseId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Marquer comme stagiaire actif :
     * - Passe le statut à 'stage_en_cours'
     * - Retire de 'acceptee'
     */
    public function marquerStagiaire(int $candidatureId, int $entrepriseId): bool
    {
        return $this->changerStatut($candidatureId, $entrepriseId, 'stage_en_cours');
    }

    // ── Notation ─────────────────────────────────────────

    /**
     * Noter un stagiaire (1 seule note par couple entreprise/chercheur)
     * Seules les entreprises avec stage_en_cours peuvent noter
     */
    public function noterStagiaire(int $candidatureId, int $entrepriseId, int $note): array
    {
        // Vérifier que la candidature est bien en stage_en_cours pour cette entreprise
        $stmt = $this->db->prepare("
            SELECT id, chercheur_id, note_attribuee
            FROM candidatures
            WHERE id = ? AND entreprise_id = ? AND statut = 'stage_en_cours'
            LIMIT 1
        ");
        $stmt->execute([$candidatureId, $entrepriseId]);
        $candidature = $stmt->fetch();

        if (!$candidature) {
            return ['success' => false, 'message' => 'Candidature introuvable ou stage non en cours.'];
        }

        if ($candidature['note_attribuee'] !== null) {
            return ['success' => false, 'message' => 'Vous avez déjà noté ce stagiaire.'];
        }

        if ($note < 1 || $note > 5) {
            return ['success' => false, 'message' => 'La note doit être entre 1 et 5.'];
        }

        // Enregistrer la note
        $this->db->prepare("
            UPDATE candidatures
            SET note_attribuee = ?, note_at = NOW()
            WHERE id = ?
        ")->execute([$note, $candidatureId]);

        // Recalculer la note moyenne du chercheur
        $this->recalculerMoyenne((int)$candidature['chercheur_id']);

        return ['success' => true, 'message' => 'Note enregistrée avec succès.'];
    }

    /** Recalcule et met à jour la note moyenne du chercheur */
    private function recalculerMoyenne(int $chercheurId): void
    {
        $stmt = $this->db->prepare("
            SELECT AVG(note_attribuee) AS moyenne, COUNT(*) AS total
            FROM candidatures
            WHERE chercheur_id = ? AND note_attribuee IS NOT NULL
        ");
        $stmt->execute([$chercheurId]);
        $row = $stmt->fetch();

        $this->db->prepare("
            UPDATE chercheurs
            SET note_moyenne = ?, note_count = ?
            WHERE id = ?
        ")->execute([
            round((float)$row['moyenne'], 2),
            (int)$row['total'],
            $chercheurId,
        ]);
    }

    // ── Postuler ─────────────────────────────────────────

    public function postuler(int $chercheurId, int $entrepriseId, array $data): array
    {
        // Vérifier doublon
        $stmt = $this->db->prepare("
            SELECT id FROM candidatures
            WHERE chercheur_id = ? AND entreprise_id = ?
            LIMIT 1
        ");
        $stmt->execute([$chercheurId, $entrepriseId]);
        if ($stmt->fetchColumn()) {
            return ['success' => false, 'message' => 'Vous avez déjà postulé à cette entreprise.'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO candidatures
                (chercheur_id, entreprise_id, offre_id, type_depot, cv_path, lettre_path, message)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $chercheurId,
            $entrepriseId,
            $data['offre_id']    ?? null,
            $data['type_depot']  ?? 'unitaire',
            $data['cv_path']     ?? null,
            $data['lettre_path'] ?? null,
            $data['message']     ?? null,
        ]);

        return ['success' => true, 'message' => 'Candidature envoyée avec succès.'];
    }
}
