<?php
/**
 * Page d'export PDF des stocks — standalone, optimisée A4
 * @var array  $produits          Produits avec stock_total, tailles
 * @var array  $taillesParProduit Tailles détaillées par produit
 * @var array  $stats             Stats globales (avec_stock, stock_faible, rupture)
 * @var int    $total             Nombre total de produits
 * @var string $adminNom         Nom de l'admin
 * @var string $search           Filtre recherche actif
 * @var string $statut           Filtre statut actif
 */
$dateGeneration = date('d/m/Y à H:i');
$seuil = STOCK_SEUIL_ALERTE;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Stocks — <?= htmlspecialchars(SITE_NAME) ?> — <?= date('d/m/Y') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ── Base ─────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #1a1a2e;
            background: #f5f5f5;
        }

        /* ── Conteneur A4 ─────────────────────────────── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            padding: 14mm 14mm 12mm 14mm;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }

        /* ── Barre d'actions (non imprimée) ───────────── */
        .print-toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 999;
            background: #1a1a2e;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .print-toolbar .toolbar-title {
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .print-toolbar .toolbar-actions { display: flex; gap: 10px; }
        .btn-print {
            background: #C8A96E;
            color: #1a1a2e;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.2s;
        }
        .btn-print:hover { background: #b8944e; }
        .btn-close {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-close:hover { background: rgba(255,255,255,0.1); }

        /* Espace pour la toolbar fixe */
        body.has-toolbar { padding-top: 50px; }

        /* ── En-tête du document ──────────────────────── */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10mm;
            border-bottom: 3px solid #C8A96E;
            margin-bottom: 7mm;
        }

        .brand-block { display: flex; flex-direction: column; gap: 4px; }

        .brand-logo {
            font-size: 28pt;
            font-weight: 900;
            letter-spacing: 2px;
            color: #1a1a2e;
            line-height: 1;
        }
        .brand-logo span {
            color: #C8A96E;
        }
        .brand-tagline {
            font-size: 8pt;
            color: #888;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .doc-meta {
            text-align: right;
            font-size: 9pt;
            color: #555;
            line-height: 1.7;
        }
        .doc-meta strong {
            display: block;
            font-size: 14pt;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
        }
        .doc-meta .meta-badge {
            display: inline-block;
            background: #1a1a2e;
            color: #C8A96E;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* ── Bandeaux filtres actifs ──────────────────── */
        .active-filters {
            background: #f8f4eb;
            border-left: 3px solid #C8A96E;
            border-radius: 0 4px 4px 0;
            padding: 5px 10px;
            font-size: 9pt;
            color: #555;
            margin-bottom: 6mm;
        }

        /* ── Stats résumé ─────────────────────────────── */
        .stats-row {
            display: flex;
            gap: 5mm;
            margin-bottom: 7mm;
        }
        .stat-box {
            flex: 1;
            border-radius: 6px;
            padding: 6mm 5mm;
            text-align: center;
        }
        .stat-box .s-value {
            font-size: 22pt;
            font-weight: 900;
            line-height: 1;
            display: block;
        }
        .stat-box .s-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
            display: block;
        }
        .stat-box.total   { background: #1a1a2e; color: #C8A96E; }
        .stat-box.ok      { background: #e8f5e9; color: #2e7d32; }
        .stat-box.faible  { background: #fff8e1; color: #e65100; }
        .stat-box.rupture { background: #fce4ec; color: #c62828; }

        /* ── Tableau des stocks ───────────────────────── */
        .section-title {
            font-size: 11pt;
            font-weight: 700;
            color: #1a1a2e;
            padding-bottom: 3mm;
            border-bottom: 1.5px solid #e0d9c5;
            margin-bottom: 4mm;
            letter-spacing: 0.3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        thead tr {
            background: #1a1a2e;
            color: #C8A96E;
        }
        thead th {
            padding: 5px 8px;
            text-align: left;
            font-weight: 700;
            font-size: 8.5pt;
            letter-spacing: 0.4px;
        }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody tr { border-bottom: 1px solid #eee; }
        tbody td {
            padding: 5px 8px;
            vertical-align: middle;
        }

        /* Stock badges */
        .badge-stock {
            display: inline-block;
            border-radius: 4px;
            padding: 2px 7px;
            font-size: 8.5pt;
            font-weight: 700;
        }
        .badge-ok      { background: #e8f5e9; color: #2e7d32; }
        .badge-faible  { background: #fff8e1; color: #e65100; }
        .badge-rupture { background: #fce4ec; color: #c62828; }

        /* Tailles détaillées */
        .tailles-wrap { display: flex; flex-wrap: wrap; gap: 3px; }
        .t-chip {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            border-radius: 3px;
            padding: 1px 5px;
            font-size: 7.5pt;
            font-weight: 600;
            border: 1px solid;
        }
        .t-ok      { background: #f1f8e9; border-color: #a5d6a7; color: #2e7d32; }
        .t-faible  { background: #fff8e1; border-color: #ffcc80; color: #e65100; }
        .t-rupture { background: #fce4ec; border-color: #ef9a9a; color: #c62828; }

        /* Statut produit */
        .statut-actif   { color: #2e7d32; font-weight: 600; }
        .statut-inactif { color: #999;    font-weight: 400; }

        /* ── Pied de page ─────────────────────────────── */
        .doc-footer {
            margin-top: 8mm;
            padding-top: 5mm;
            border-top: 1.5px solid #e0d9c5;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 8pt;
            color: #888;
        }
        .doc-footer .footer-brand {
            font-weight: 700;
            color: #1a1a2e;
            font-size: 9pt;
        }
        .doc-footer .legend { display: flex; gap: 8px; align-items: center; }
        .legend-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
        }

        /* ── Saut de page ─────────────────────────────── */
        .page-break { page-break-after: always; }

        /* ── Media Print ──────────────────────────────── */
        @media print {
            body { background: #fff !important; padding-top: 0 !important; }
            .print-toolbar { display: none !important; }
            .page {
                width: 100%;
                margin: 0;
                padding: 10mm 12mm 10mm 12mm;
                box-shadow: none;
            }
            table { font-size: 8.5pt; }
            thead th, tbody td { padding: 4px 6px; }
            .stat-box .s-value { font-size: 18pt; }

            /* Éviter coupure à l'intérieur d'une ligne */
            tbody tr { page-break-inside: avoid; }

            /* Répéter l'en-tête du tableau sur chaque page */
            thead { display: table-header-group; }
        }
    </style>
</head>
<body class="has-toolbar">

<!-- Barre d'outils (masquée à l'impression) -->
<div class="print-toolbar">
    <span class="toolbar-title">📄 Rapport Stocks — <?= htmlspecialchars(SITE_NAME) ?></span>
    <div class="toolbar-actions">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimer / Enregistrer PDF</button>
        <button class="btn-close" onclick="window.close()">✕ Fermer</button>
    </div>
</div>

<!-- Page A4 -->
<div class="page">

    <!-- En-tête -->
    <div class="doc-header">
        <div class="brand-block">
            <div class="brand-logo">
                VITRIN<span>UP</span>
            </div>
            <div class="brand-tagline">Boutique Chaussures · Panel Administrateur</div>
        </div>
        <div class="doc-meta">
            <strong>Rapport des Stocks</strong>
            Généré le <?= $dateGeneration ?><br>
            Par : <?= htmlspecialchars($adminNom) ?><br>
            Seuil d'alerte : <strong><?= $seuil ?> unités</strong><br>
            <span class="meta-badge">CONFIDENTIEL</span>
        </div>
    </div>

    <!-- Filtres actifs -->
    <?php if (!empty($search) || !empty($statut)): ?>
    <div class="active-filters">
        <strong>Filtres appliqués :</strong>
        <?php if (!empty($search)): ?>
            Recherche : "<?= htmlspecialchars($search) ?>"
        <?php endif; ?>
        <?php if (!empty($statut)): ?>
            Statut : <?php
                echo match($statut) {
                    'faible'  => '⚠️ Stock faible',
                    'rupture' => '🚫 En rupture',
                    'ok'      => '✅ Stock OK',
                    default   => $statut
                };
            ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Statistiques résumées -->
    <div class="stats-row">
        <div class="stat-box total">
            <span class="s-value"><?= intval($total) ?></span>
            <span class="s-label">Produits total</span>
        </div>
        <div class="stat-box ok">
            <span class="s-value"><?= intval($stats['avec_stock'] ?? 0) ?></span>
            <span class="s-label">Stock OK</span>
        </div>
        <div class="stat-box faible">
            <span class="s-value"><?= intval($stats['stock_faible'] ?? 0) ?></span>
            <span class="s-label">Stock faible</span>
        </div>
        <div class="stat-box rupture">
            <span class="s-value"><?= intval($stats['rupture'] ?? 0) ?></span>
            <span class="s-label">En rupture</span>
        </div>
    </div>

    <!-- Tableau des stocks -->
    <div class="section-title">Inventaire détaillé — <?= intval($total) ?> produit<?= $total > 1 ? 's' : '' ?></div>

    <?php if (empty($produits)): ?>
        <p style="color:#888;text-align:center;padding:20px;">Aucun produit à afficher.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width:28%">Produit</th>
                <th style="width:14%">Marque</th>
                <th style="width:9%">Statut</th>
                <th style="width:11%">Stock total</th>
                <th>Tailles détaillées</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $p):
                $stockTotal = intval($p['stock_total']);
                if ($stockTotal === 0) {
                    $stockClass = 'badge-rupture';
                    $stockLabel = 'Rupture';
                } elseif ($stockTotal <= $seuil) {
                    $stockClass = 'badge-faible';
                    $stockLabel = 'Faible';
                } else {
                    $stockClass = 'badge-ok';
                    $stockLabel = 'OK';
                }
                $tailles = $taillesParProduit[$p['id']] ?? [];
                $statutClass = $p['statut'] === 'actif' ? 'statut-actif' : 'statut-inactif';
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($p['nom']) ?></strong></td>
                <td><?= htmlspecialchars($p['marque'] ?? '—') ?></td>
                <td><span class="<?= $statutClass ?>"><?= ucfirst($p['statut']) ?></span></td>
                <td>
                    <span class="badge-stock <?= $stockClass ?>">
                        <?= $stockTotal ?> u.
                    </span>
                </td>
                <td>
                    <?php if (!empty($tailles)): ?>
                        <div class="tailles-wrap">
                            <?php foreach ($tailles as $t):
                                $s = intval($t['stock']);
                                $tc = $s === 0 ? 't-rupture' : ($s <= $seuil ? 't-faible' : 't-ok');
                            ?>
                                <span class="t-chip <?= $tc ?>">
                                    <?= htmlspecialchars($t['taille']) ?>: <?= $s ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span style="color:#bbb">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Pied de page -->
    <div class="doc-footer">
        <div>
            <div class="footer-brand"><?= htmlspecialchars(SITE_NAME) ?></div>
            Document généré automatiquement · <?= $dateGeneration ?>
        </div>
        <div>
            <div class="legend">
                <span class="legend-dot" style="background:#2e7d32;"></span> Stock OK (&gt;<?= $seuil ?>)
                &nbsp;
                <span class="legend-dot" style="background:#e65100;"></span> Stock faible (1–<?= $seuil ?>)
                &nbsp;
                <span class="legend-dot" style="background:#c62828;"></span> Rupture (0)
            </div>
        </div>
    </div>

</div><!-- /.page -->

<script>
// Déclencher l'impression automatiquement après chargement
window.addEventListener('load', function() {
    // Petit délai pour que les styles soient bien appliqués
    setTimeout(function() {
        window.print();
    }, 600);
});
</script>
</body>
</html>
