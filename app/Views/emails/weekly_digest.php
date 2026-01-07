<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haftalık Özet</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0f172a;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background-color: #1e293b; border-radius: 16px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);">
                            <h1 style="margin: 0 0 8px; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Haftalık Özet
                            </h1>
                            <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 16px;">
                                Merhaba <?= esc($user['name']) ?>, takip ettiklerinizden yeni projeler var!
                            </p>
                        </td>
                    </tr>

                    <!-- Projects -->
                    <tr>
                        <td style="padding: 32px;">
                            <h2 style="margin: 0 0 24px; color: #f1f5f9; font-size: 18px; font-weight: 600;">
                                Bu Hafta Eklenen Projeler (<?= count($projects) ?>)
                            </h2>

                            <?php foreach ($projects as $project): ?>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px; background-color: #334155; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <!-- Project Title -->
                                        <a href="<?= $baseUrl ?>projects/<?= esc($project['slug']) ?>" style="text-decoration: none;">
                                            <h3 style="margin: 0 0 8px; color: #f1f5f9; font-size: 18px; font-weight: 600;">
                                                <?= esc($project['title']) ?>
                                            </h3>
                                        </a>

                                        <!-- Author & Category -->
                                        <p style="margin: 0 0 12px; color: #94a3b8; font-size: 14px;">
                                            <span style="color: #c4b5fd;"><?= esc($project['user_name']) ?></span>
                                            &bull;
                                            <?= esc($project['category_name']) ?>
                                        </p>

                                        <!-- Description -->
                                        <p style="margin: 0 0 16px; color: #cbd5e1; font-size: 14px; line-height: 1.5;">
                                            <?= esc(mb_substr($project['description'], 0, 150)) ?><?= strlen($project['description']) > 150 ? '...' : '' ?>
                                        </p>

                                        <!-- AI Tools -->
                                        <?php if (!empty($project['ai_tools'])): ?>
                                        <div style="margin-bottom: 16px;">
                                            <?php foreach (array_slice($project['ai_tools'], 0, 3) as $tool): ?>
                                            <span style="display: inline-block; padding: 4px 12px; margin-right: 8px; margin-bottom: 4px; background-color: rgba(139, 92, 246, 0.2); color: #c4b5fd; font-size: 12px; border-radius: 9999px;">
                                                <?= esc($tool['name']) ?>
                                            </span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>

                                        <!-- CTA Button -->
                                        <a href="<?= $baseUrl ?>projects/<?= esc($project['slug']) ?>" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 500; border-radius: 8px;">
                                            Projeyi Gör
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <?php endforeach; ?>

                            <!-- View All Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-top: 16px;">
                                        <a href="<?= $baseUrl ?>feed" style="display: inline-block; padding: 14px 32px; background-color: #475569; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 500; border-radius: 8px;">
                                            Tüm Akışı Gör
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #1e293b; border-top: 1px solid #334155;">
                            <p style="margin: 0 0 8px; color: #64748b; font-size: 12px; text-align: center;">
                                Bu e-postayı takip ettiğiniz kullanıcıların yeni projelerini size bildirmek için gönderiyoruz.
                            </p>
                            <p style="margin: 0; color: #64748b; font-size: 12px; text-align: center;">
                                E-posta almak istemiyorsanız, <a href="<?= $baseUrl ?>user/<?= $user['id'] ?>" style="color: #8b5cf6;">profil ayarlarınızdan</a> kapatabilirsiniz.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
