<?php
declare(strict_types=1);

namespace Staff\Services;

use Staff\Core\Database;
use PDO;

/**
 * MailService — PHPMailer + Gmail SMTP
 * Installer PHPMailer : composer require phpmailer/phpmailer
 * OU télécharger manuellement dans /vendor/phpmailer/
 */
class MailService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Envoi principal via PHPMailer ────────────────────

    private function envoyer(string $to, string $subject, string $htmlBody): bool
    {
        // Vérifier si PHPMailer est disponible
        $phpmailerPath = BASE_PATH . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';

        if (file_exists($phpmailerPath)) {
            return $this->envoyerPhpMailer($to, $subject, $htmlBody);
        }

        // Fallback : mail() natif
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
        return @mail($to, $subject, $htmlBody, $headers);
    }

    private function envoyerPhpMailer(string $to, string $subject, string $htmlBody): bool
    {
        require_once BASE_PATH . '/vendor/phpmailer/phpmailer/src/Exception.php';
        require_once BASE_PATH . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once BASE_PATH . '/vendor/phpmailer/phpmailer/src/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('Erreur mail: ' . $e->getMessage());
            return false;
        }
    }

    // ── Templates emails ──────────────────────────────────

    public function envoyerConfirmationAbonnement(int $userId, int $abonnementId): void
    {
        $stmt = $this->db->prepare("
            SELECT u.email, COALESCE(c.prenom,'') AS prenom, a.debut, a.fin
            FROM users u
            LEFT JOIN chercheurs c ON c.user_id = u.id
            JOIN abonnements a ON a.id = ?
            WHERE u.id = ?
        ");
        $stmt->execute([$abonnementId, $userId]);
        $data = $stmt->fetch();
        if (!$data) return;

        $fin = (new \DateTime($data['fin']))->format('d/m/Y');
        $this->envoyer(
            $data['email'],
            'STAFF — Abonnement activé ✅',
            $this->template('confirmation_abo', [
                '{{PRENOM}}' => htmlspecialchars($data['prenom'] ?: 'Cher(e) abonné(e)'),
                '{{FIN}}'    => $fin,
                '{{PRIX}}'   => number_format(ABONNEMENT_PRIX, 0, ',', ' ') . ' FCFA',
            ])
        );
    }

    public function envoyerRelanceAbonnement(array $abo): void
    {
        $fin = (new \DateTime($abo['fin']))->format('d/m/Y');
        $this->envoyer(
            $abo['email'],
            'STAFF — Votre abonnement expire bientôt ⏰',
            $this->template('relance_abo', [
                '{{PRENOM}}' => htmlspecialchars($abo['prenom'] ?: 'Cher(e) abonné(e)'),
                '{{FIN}}'    => $fin,
                '{{URL}}'    => BASE_URL . '/abonnement',
            ])
        );
    }

    public function envoyerEbook(int $userId): void
    {
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $email = $stmt->fetchColumn();
        if (!$email) return;

        $this->db->prepare("UPDATE email_contacts SET ebook_sent=1, ebook_sent_at=NOW() WHERE user_id=?")
                 ->execute([$userId]);

        $this->envoyer(
            $email,
            'STAFF — Votre livre numérique offert 🎁',
            $this->template('ebook', ['{{URL_EBOOK}}' => BASE_URL . '/ebook/download'])
        );
    }

    public function envoyerResetPassword(string $email, string $token): void
    {
        $url = BASE_URL . '/reset-password?token=' . urlencode($token);
        $this->envoyer($email, 'STAFF — Réinitialisation mot de passe 🔐', $this->template('reset_password', ['{{URL}}' => $url]));
    }

    public function envoyerNotificationParrainage(array $parrainage): void
    {
        $this->envoyer(
            $parrainage['email'],
            'STAFF — Commission parrainage validée 💰',
            $this->template('parrainage', [
                '{{PRENOM}}'     => htmlspecialchars($parrainage['prenom'] ?: 'Cher parrain'),
                '{{COMMISSION}}' => number_format((float)$parrainage['commission'], 0, ',', ' ') . ' FCFA',
                '{{URL}}'        => BASE_URL . '/chercheur/parrainage',
            ])
        );
    }

    // ── Templates HTML ────────────────────────────────────

    private function template(string $name, array $vars = []): string
    {
        $wrap = fn(string $content) => '
<div style="font-family:\'DM Sans\',sans-serif;max-width:520px;margin:auto;background:#111827;color:#e2e8f0;border-radius:12px;overflow:hidden">
  <div style="background:#00d4aa;padding:20px 28px">
    <div style="font-family:sans-serif;font-weight:800;font-size:1.5rem;color:#0a0e1a;letter-spacing:-0.04em">STAFF</div>
  </div>
  <div style="padding:28px">' . $content . '</div>
  <div style="padding:16px 28px;border-top:1px solid #1e2d45;font-size:0.75rem;color:#64748b">
    STAFF Platform — Stages &amp; Emploi au Cameroun<br>
    <a href="' . BASE_URL . '" style="color:#00d4aa">staff.cm</a>
  </div>
</div>';

        $templates = [
            'confirmation_abo' => $wrap('
<h2 style="color:#00d4aa;margin-bottom:12px">✅ Abonnement activé !</h2>
<p>Bonjour <strong>{{PRENOM}}</strong>,</p>
<p style="margin-top:10px">Votre abonnement STAFF est actif jusqu\'au <strong>{{FIN}}</strong>.</p>
<div style="background:#0a0e1a;border-radius:8px;padding:16px;margin:16px 0">
  Montant : <strong style="color:#00d4aa">{{PRIX}}</strong>
</div>
<p>Vous pouvez maintenant postuler à toutes les offres !</p>
<a href="' . BASE_URL . '/chercheur/dashboard" style="display:inline-block;background:#00d4aa;color:#0a0e1a;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;margin-top:14px">Accéder à mon espace →</a>'),

            'relance_abo' => $wrap('
<h2 style="color:#f59e0b;margin-bottom:12px">⏰ Abonnement bientôt expiré</h2>
<p>Bonjour <strong>{{PRENOM}}</strong>,</p>
<p style="margin-top:10px">Votre abonnement expire le <strong>{{FIN}}</strong>.</p>
<p style="margin-top:8px;color:#94a3b8">Renouvelez maintenant pour continuer à postuler.</p>
<a href="{{URL}}" style="display:inline-block;background:#00d4aa;color:#0a0e1a;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;margin-top:14px">Renouveler →</a>'),

            'ebook' => $wrap('
<h2 style="color:#00d4aa;margin-bottom:12px">🎁 Votre cadeau de bienvenue</h2>
<p>Merci pour votre inscription sur STAFF !</p>
<p style="margin-top:10px;color:#94a3b8">Voici votre livre numérique offert :</p>
<a href="{{URL_EBOOK}}" style="display:inline-block;background:#00d4aa;color:#0a0e1a;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;margin-top:14px">Télécharger le livre →</a>'),

            'reset_password' => $wrap('
<h2 style="color:#00d4aa;margin-bottom:12px">🔐 Réinitialisation mot de passe</h2>
<p>Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.</p>
<p style="color:#64748b;font-size:0.83rem;margin-top:6px">Ce lien est valable 1 heure.</p>
<a href="{{URL}}" style="display:inline-block;background:#00d4aa;color:#0a0e1a;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;margin-top:14px">Réinitialiser →</a>
<p style="color:#64748b;font-size:0.78rem;margin-top:16px">Si vous n\'avez pas demandé cela, ignorez cet email.</p>'),

            'parrainage' => $wrap('
<h2 style="color:#f59e0b;margin-bottom:12px">💰 Commission validée !</h2>
<p>Bonjour <strong>{{PRENOM}}</strong>,</p>
<p style="margin-top:10px">Un de vos filleuls vient de s\'abonner. Votre commission de <strong style="color:#f59e0b">{{COMMISSION}}</strong> a été validée.</p>
<p style="margin-top:8px;color:#94a3b8">Le virement sera effectué automatiquement sur votre Mobile Money.</p>
<a href="{{URL}}" style="display:inline-block;background:#00d4aa;color:#0a0e1a;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;margin-top:14px">Voir mon parrainage →</a>'),
        ];

        $html = $templates[$name] ?? '<p>Email STAFF</p>';
        return str_replace(array_keys($vars), array_values($vars), $html);
    }
}
