<?php
/**
 * @var array  $produits          Liste des produits actifs
 * @var array  $taillesParProduit Tailles par produit
 * @var array  $dernieresEntrees  Dernières entrées enregistrées
 * @var string $adminNom          Nom de l'admin connecté
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entrée de stock — Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/admin.css">
    <style>
        /* ═══════════════════════════════════════════════════════════
           ENTRÉE DE STOCK — Design Premium v3.0
           ═══════════════════════════════════════════════════════════ */

        /* ── Layout ─────────────────────────────────────────── */
        .entree-layout {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 28px;
            align-items: start;
        }
        @media (max-width: 1100px) {
            .entree-layout { grid-template-columns: 1fr; }
        }

        /* ── Carte principale ───────────────────────────────── */
        .card-entree {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            transition: box-shadow 0.3s ease;
        }
        .card-entree:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        }
        .card-entree .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-entree .card-title .title-badge {
            margin-left: auto;
            font-size: 0.7rem;
            background: #f0f0f0;
            color: #888;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* ── Sélecteur produit ──────────────────────────────── */
        .product-selector {
            position: relative;
        }
        .product-selector label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .product-selector select {
            width: 100%;
            padding: 16px 20px;
            padding-right: 48px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            color: #1a1a1a;
            outline: none;
            background: #fafafa;
            transition: all 0.25s ease;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='8' viewBox='0 0 14 8'%3E%3Cpath fill='%23999' d='M1 1l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
        }
        .product-selector select:hover {
            border-color: #ccc;
            background: #fff;
        }
        .product-selector select:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.10);
        }
        .product-selector .select-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            pointer-events: none;
            opacity: 0.4;
        }

        /* ── Panneau produit ────────────────────────────────── */
        .produit-panel {
            display: none;
            margin-top: 20px;
            animation: panelIn 0.35s ease;
        }
        .produit-panel.visible { display: block; }
        @keyframes panelIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .produit-header {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 16px 20px;
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        .produit-header .ph-img {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            object-fit: cover;
            background: #e8e8e8;
            flex-shrink: 0;
        }
        .produit-header .ph-img-placeholder {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e8e8e8, #ddd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }
        .produit-header .ph-info { flex: 1; }
        .produit-header .ph-info .ph-nom {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a1a1a;
        }
        .produit-header .ph-info .ph-marque {
            font-size: 0.82rem;
            color: #999;
            margin-top: 2px;
        }
        .produit-header .ph-stats {
            text-align: right;
            font-size: 0.75rem;
            color: #aaa;
        }
        .produit-header .ph-stats strong {
            font-size: 1.3rem;
            color: #1a1a1a;
            display: block;
        }

        /* ── Grille de tailles ──────────────────────────────── */
        .tailles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
        }

        .taille-card {
            background: #fff;
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 18px;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .taille-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--green);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .taille-card:hover {
            border-color: #ddd;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }
        .taille-card:hover::before { transform: scaleX(1); }

        .taille-card .tc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .taille-card .tc-head .tc-taille {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1a1a1a;
        }
        .taille-card .tc-head .tc-badge {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }
        .tc-badge.ok     { background: #e8f5e9; color: #2e7d32; }
        .tc-badge.faible { background: #fff3e0; color: #e65100; }
        .tc-badge.rupture{ background: #ffebee; color: #c62828; }

        /* ── Barre de stock ─────────────────────────────────── */
        .tc-bar {
            height: 5px;
            background: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .tc-bar .tc-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.6s ease, background 0.3s ease;
        }
        .tc-bar-fill.ok     { background: var(--green); }
        .tc-bar-fill.faible { background: var(--orange); }
        .tc-bar-fill.rupture{ background: var(--red); }

        .tc-stock {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.82rem;
            color: #888;
            margin-bottom: 14px;
        }
        .tc-stock .tc-stock-nb {
            font-size: 1.3rem;
            font-weight: 800;
        }
        .tc-stock .tc-stock-nb.ok     { color: #2e7d32; }
        .tc-stock .tc-stock-nb.faible { color: #e65100; }
        .tc-stock .tc-stock-nb.rupture{ color: #c62828; }

        /* ── Actions ────────────────────────────────────────── */
        .tc-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .tc-actions input[type="number"] {
            flex: 1;
            padding: 10px 12px;
            border: 2px solid #e8e8e8;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            outline: none;
            background: #fafafa;
            transition: all 0.2s ease;
            width: 70px;
        }
        .tc-actions input[type="number"]:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.10);
        }
        .tc-actions .btn-add {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #1a1a1a;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.25s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .tc-actions .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(201, 168, 76, 0.30);
        }
        .tc-actions .btn-add:active {
            transform: translateY(0);
        }
        .tc-actions .btn-add:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .tc-actions .btn-add .spinner-sm {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(0,0,0,0.2);
            border-top-color: #1a1a1a;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* ── État vide ──────────────────────────────────────── */
        .empty-tailles {
            text-align: center;
            padding: 48px 20px;
            color: #bbb;
        }
        .empty-tailles .empty-icon {
            font-size: 3.2rem;
            margin-bottom: 14px;
            opacity: 0.5;
        }
        .empty-tailles p {
            font-size: 0.95rem;
            margin-bottom: 4px;
        }
        .empty-tailles .empty-sub {
            font-size: 0.82rem;
            color: #ccc;
        }

        /* ── Ajout nouvelle taille ──────────────────────────── */
        .add-taille-wrap {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 2px dashed #e8e8e8;
        }
        .add-taille-wrap .at-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .add-taille-wrap .at-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .add-taille-wrap .at-row input {
            padding: 12px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            background: #fafafa;
            transition: all 0.2s ease;
        }
        .add-taille-wrap .at-row input:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.10);
        }
        .add-taille-wrap .at-row input[type="text"] { width: 110px; }
        .add-taille-wrap .at-row input[type="number"] { width: 80px; }
        .add-taille-wrap .at-row .btn-new-taille {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            background: #1a1a1a;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .add-taille-wrap .at-row .btn-new-taille:hover {
            background: #333;
            transform: translateY(-2px);
        }
        .add-taille-wrap .at-row .btn-new-taille:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* ═══════════════════════════════════════════════════════
           CONFIRMATION — Overlay vert "check"
           ═══════════════════════════════════════════════════════ */
        .confirm-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            animation: confirmFade 0.8s ease forwards;
        }
        @keyframes confirmFade {
            0%   { opacity: 0; }
            15%  { opacity: 1; }
            70%  { opacity: 1; }
            100% { opacity: 0; }
        }
        .confirm-box {
            background: #fff;
            border-radius: 20px;
            padding: 40px 48px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            animation: confirmPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes confirmPop {
            0%   { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .confirm-box .check-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            animation: checkPulse 0.6s ease;
        }
        @keyframes checkPulse {
            0%   { transform: scale(0); }
            50%  { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .confirm-box .check-circle svg {
            width: 36px;
            height: 36px;
            stroke: #2e7d32;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            animation: checkDraw 0.4s 0.2s ease forwards;
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
        }
        @keyframes checkDraw {
            to { stroke-dashoffset: 0; }
        }
        .confirm-box .confirm-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 4px;
        }
        .confirm-box .confirm-sub {
            font-size: 0.9rem;
            color: #888;
        }
        .confirm-box .confirm-sub strong {
            color: #1a1a1a;
        }

        /* ── Confettis ──────────────────────────────────────── */
        .confetti-container {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 99998;
            pointer-events: none;
            overflow: hidden;
        }
        .confetti {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            animation: confettiFall linear forwards;
        }
        @keyframes confettiFall {
            0%   { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        /* ═══════════════════════════════════════════════════════
           COLONNE DROITE — Dernières entrées "live"
           ═══════════════════════════════════════════════════════ */
        .card-feed {
            background: var(--white);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            transition: box-shadow 0.3s ease;
            position: sticky;
            top: 32px;
        }
        .card-feed:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        }
        .card-feed .feed-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-feed .feed-title .feed-counter {
            margin-left: auto;
            font-size: 0.7rem;
            background: #e8f5e9;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .card-feed .feed-title .feed-counter.pulse {
            animation: counterPulse 0.5s ease;
        }
        @keyframes counterPulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .feed-list {
            max-height: 520px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .feed-list::-webkit-scrollbar { width: 4px; }
        .feed-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 2px; }

        .feed-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            transition: all 0.25s ease;
            margin-bottom: 4px;
            border: 1px solid transparent;
            animation: feedSlide 0.35s ease;
        }
        @keyframes feedSlide {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .feed-item:hover {
            background: #fafafa;
            border-color: #f0f0f0;
        }
        .feed-item.feed-new {
            background: #e8f5e9;
            border-color: #a5d6a7;
        }
        .feed-item .feed-qte {
            font-size: 1.1rem;
            font-weight: 800;
            color: #2e7d32;
            min-width: 48px;
            text-align: center;
            padding: 6px 10px;
            background: #e8f5e9;
            border-radius: 8px;
        }
        .feed-item .feed-body {
            flex: 1;
            min-width: 0;
        }
        .feed-item .feed-body .feed-produit {
            font-weight: 600;
            font-size: 0.88rem;
            color: #1a1a1a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .feed-item .feed-body .feed-taille {
            font-size: 0.78rem;
            color: #999;
        }
        .feed-item .feed-body .feed-ref {
            font-size: 0.72rem;
            color: #bbb;
            margin-top: 2px;
        }
        .feed-item .feed-time {
            font-size: 0.72rem;
            color: #bbb;
            white-space: nowrap;
        }

        .feed-empty {
            text-align: center;
            padding: 60px 20px;
            color: #ccc;
        }
        .feed-empty .feed-empty-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            opacity: 0.4;
        }
        .feed-empty p { font-size: 0.9rem; }

        /* ── Flash messages ─────────────────────────────────── */
        .flash-message {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }
        .flash-success {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            color: #2e7d32;
        }
        .flash-error {
            background: #ffebee;
            border: 1px solid #ef9a9a;
            color: #c62828;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Animations globales ────────────────────────────── */
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Responsive ─────────────────────────────────────── */
        @media (max-width: 900px) {
            .card-entree { padding: 20px; }
            .card-feed { padding: 20px; }
            .tailles-grid { grid-template-columns: 1fr; }
            .produit-header { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">Vitrin<span>up</span></div>
    <div class="admin-info">
        <span class="avatar"><?= strtoupper(substr($adminNom ?? 'A', 0, 1)) ?></span>
        <?= htmlspecialchars($adminNom ?? '') ?>
    </div>
    <nav>
        <div class="nav-label">Navigation</div>
        <a href="<?= URL_ROOT ?>/admin"><span class="icon">📊</span> Dashboard</a>
        <a href="<?= URL_ROOT ?>/admin/produits"><span class="icon">👟</span> Produits</a>
        <a href="<?= URL_ROOT ?>/admin/categories"><span class="icon">🗂️</span> Catégories</a>
        <a href="<?= URL_ROOT ?>/admin/commandes"><span class="icon">📦</span> Commandes</a>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="active"><span class="icon">📋</span> Stocks</a>
        <a href="<?= URL_ROOT ?>" target="_blank"><span class="icon">🌐</span> Voir le site</a>
    </nav>
    <div class="logout"><a href="<?= URL_ROOT ?>/admin/logout"><span>🚪</span> <span>Déconnexion</span></a></div>
</aside>

<main class="main">
    <div class="page-header">
        <div>
            <h1>📥 Entrée de stock</h1>
            <div class="subtitle">Gérez les tailles et ajoutez du stock en un clic — interface temps réel</div>
        </div>
        <a href="<?= URL_ROOT ?>/admin/stocks" class="btn-back">← Retour aux stocks</a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-message flash-success">✅ <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash-message flash-error">❌ <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="entree-layout">
        <!-- ═══ COLONNE GAUCHE : Formulaire principal ═══ -->
        <div class="card-entree">
            <div class="card-title">
                🎯 Ajouter du stock
                <span class="title-badge">AJAX • Temps réel</span>
            </div>

            <div class="product-selector">
                <label>👟 Produit</label>
                <select id="produitSelect">
                    <option value="">— Sélectionnez un produit —</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= intval($p['id']) ?>" data-image="<?= htmlspecialchars($p['image'] ?? '') ?>">
                            <?= htmlspecialchars($p['nom']) ?>
                            <?= $p['marque'] ? ' (' . htmlspecialchars($p['marque']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Panneau produit -->
            <div class="produit-panel" id="produitPanel">
                <div class="produit-header">
                    <div class="ph-img-placeholder" id="phImgPlaceholder">👟</div>
                    <img class="ph-img" id="phImg" src="" alt="" style="display:none;">
                    <div class="ph-info">
                        <div class="ph-nom" id="phNom">—</div>
                        <div class="ph-marque" id="phMarque"></div>
                    </div>
                    <div class="ph-stats">
                        <strong id="phNbTailles">0</strong>
                        taille(s)
                    </div>
                </div>

                <div id="taillesContainer">
                    <div class="empty-tailles">
                        <div class="empty-icon">👟</div>
                        <p>Sélectionnez un produit</p>
                        <div class="empty-sub">pour gérer ses tailles et son stock</div>
                    </div>
                </div>

                <!-- Ajout nouvelle taille -->
                <div class="add-taille-wrap" id="addTailleWrap" style="display:none;">
                    <div class="at-label">➕ Nouvelle taille</div>
                    <div class="at-row">
                        <input type="text" id="newTailleInput" placeholder="Taille (ex: 42)" maxlength="20">
                        <input type="number" id="newStockInput" placeholder="Stock" min="0" value="0">
                        <button class="btn-new-taille" id="btnAddTaille">➕ Ajouter</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ COLONNE DROITE : Feed dernières entrées ═══ -->
        <div class="card-feed">
            <div class="feed-title">
                🔄 Dernières entrées
                <span class="feed-counter" id="feedCounter"><?= count($dernieresEntrees) ?></span>
            </div>

            <?php if (!empty($dernieresEntrees)): ?>
                <div class="feed-list" id="feedList">
                    <?php foreach ($dernieresEntrees as $e): ?>
                        <div class="feed-item">
                            <div class="feed-qte">+<?= intval($e['quantite']) ?></div>
                            <div class="feed-body">
                                <div class="feed-produit"><?= htmlspecialchars($e['produit_nom']) ?></div>
                                <div class="feed-taille">Taille <?= htmlspecialchars($e['taille']) ?></div>
                                <?php if ($e['reference']): ?>
                                    <div class="feed-ref"><?= htmlspecialchars($e['reference']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="feed-time"><?= date('d/m H:i', strtotime($e['created_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="feed-empty" id="feedEmpty">
                    <div class="feed-empty-icon">📭</div>
                    <p>Aucune entrée pour le moment</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
const URL_ROOT = '<?= URL_ROOT ?>';
const UPLOAD_URL = '<?= UPLOAD_URL ?>';
const STOCK_SEUIL_ALERTE = <?= STOCK_SEUIL_ALERTE ?>;
const taillesData = <?= json_encode($taillesParProduit) ?>;
const produitsData = <?= json_encode($produits) ?>;
</script>
<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
<script>
/* ═══════════════════════════════════════════════════════════
   ENTRÉE DE STOCK — Script Premium v3.0
   ═══════════════════════════════════════════════════════════ */
(function() {
    'use strict';

    // ── Éléments DOM ────────────────────────────────────────
    const $ = function(id) { return document.getElementById(id); };
    const produitSelect   = $('produitSelect');
    const produitPanel    = $('produitPanel');
    const phImg           = $('phImg');
    const phImgPlaceholder= $('phImgPlaceholder');
    const phNom           = $('phNom');
    const phMarque        = $('phMarque');
    const phNbTailles     = $('phNbTailles');
    const taillesContainer= $('taillesContainer');
    const addTailleWrap   = $('addTailleWrap');
    const newTailleInput  = $('newTailleInput');
    const newStockInput   = $('newStockInput');
    const btnAddTaille    = $('btnAddTaille');
    const feedList        = $('feedList');
    const feedEmpty       = $('feedEmpty');
    const feedCounter     = $('feedCounter');

    // ── Confettis ───────────────────────────────────────────
    function lancerConfettis() {
        var container = document.createElement('div');
        container.className = 'confetti-container';
        document.body.appendChild(container);
        var colors = ['#25D366','#c9a84c','#2e7d32','#ff9800','#e53935','#1565c0'];
        for (var i = 0; i < 40; i++) {
            var c = document.createElement('div');
            c.className = 'confetti';
            c.style.left = (Math.random() * 100) + '%';
            c.style.background = colors[Math.floor(Math.random() * colors.length)];
            c.style.width = (4 + Math.random() * 6) + 'px';
            c.style.height = (4 + Math.random() * 6) + 'px';
            c.style.animationDuration = (1.5 + Math.random() * 2) + 's';
            c.style.animationDelay = (Math.random() * 0.5) + 's';
            container.appendChild(c);
        }
        setTimeout(function() { container.remove(); }, 4000);
    }

    // ── Overlay de confirmation vert ────────────────────────
    function showConfirmOverlay(quantite, produitNom, taille) {
        var overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        overlay.innerHTML =
            '<div class="confirm-box">' +
            '<div class="check-circle">' +
            '<svg viewBox="0 0 24 24"><polyline points="4,12 10,18 20,6"/></svg>' +
            '</div>' +
            '<div class="confirm-title">+' + quantite + ' unité' + (quantite > 1 ? 's' : '') + '</div>' +
            '<div class="confirm-sub"><strong>' + produitNom + '</strong> — Taille ' + taille + '</div>' +
            '</div>';
        document.body.appendChild(overlay);
        setTimeout(function() { overlay.remove(); }, 1200);
        lancerConfettis();
    }

    // ── Toast ───────────────────────────────────────────────
    function showToast(message, type) {
        type = type || 'success';
        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        var icons = { success: '✅', error: '❌', info: 'ℹ️' };
        toast.innerHTML = '<span>' + (icons[type] || 'ℹ️') + '</span> ' + message;
        container.appendChild(toast);
        setTimeout(function() {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3500);
    }

    // ── Rendre les tailles d'un produit ────────────────────
    function renderTailles(produitId) {
        const tailles = taillesData[produitId] || [];

        if (tailles.length === 0) {
            taillesContainer.innerHTML = `
                <div class="empty-tailles">
                    <div class="empty-icon">📏</div>
                    <p>Aucune taille définie</p>
                    <div class="empty-sub">Ajoutez-en une ci-dessous</div>
                </div>
            `;
            addTailleWrap.style.display = 'block';
            phNbTailles.textContent = '0';
            return;
        }

        // Calculer le stock max pour la barre de progression
        var stocks = tailles.map(function(t) { return parseInt(t.stock) || 0; });
        var maxStock = Math.max.apply(null, stocks) || 1;

        var html = '<div class="tailles-grid">';
        tailles.forEach(function(t) {
            var stock = parseInt(t.stock) || 0;
            var pct = Math.min(100, Math.round((stock / maxStock) * 100));
            var cls = 'ok';
            var label = 'En stock';
            if (stock <= 0) { cls = 'rupture'; label = 'Rupture'; }
            else if (stock <= STOCK_SEUIL_ALERTE) { cls = 'faible'; label = 'Stock faible'; }

            html +=
                '<div class="taille-card" data-taille-id="' + t.id + '" data-produit-id="' + produitId + '">' +
                    '<div class="tc-head">' +
                        '<span class="tc-taille">Taille ' + t.taille + '</span>' +
                        '<span class="tc-badge ' + cls + '">' + label + '</span>' +
                    '</div>' +
                    '<div class="tc-bar">' +
                        '<div class="tc-bar-fill ' + cls + '" style="width:' + pct + '%"></div>' +
                    '</div>' +
                    '<div class="tc-stock">' +
                        '<span>Stock</span>' +
                        '<span class="tc-stock-nb ' + cls + '">' + stock + '</span>' +
                    '</div>' +
                    '<div class="tc-actions">' +
                        '<input type="number" class="qte-input" min="1" value="1" placeholder="Qte">' +
                        '<button class="btn-add" data-taille-id="' + t.id + '" data-produit-id="' + produitId + '">' +
                            '➕ Ajouter' +
                        '</button>' +
                    '</div>' +
                '</div>';
        });
        html += '</div>';
        taillesContainer.innerHTML = html;
        addTailleWrap.style.display = 'block';
        phNbTailles.textContent = tailles.length;

        // Attacher événements
        document.querySelectorAll('.btn-add').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tailleId = this.dataset.tailleId;
                var produitId = this.dataset.produitId;
                var card = this.closest('.taille-card');
                var qteInput = card.querySelector('.qte-input');
                var quantite = parseInt(qteInput.value) || 0;

                if (quantite <= 0) {
                    showToast('Veuillez saisir une quantité valide', 'error');
                    return;
                }

                ajouterStock(tailleId, produitId, quantite, card, qteInput);
            });
        });

        // Enter key
        document.querySelectorAll('.qte-input').forEach(function(input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    var card = this.closest('.taille-card');
                    var btn = card.querySelector('.btn-add');
                    if (btn) btn.click();
                }
            });
        });
    }

    // ── Ajouter du stock (AJAX) ────────────────────────────
    function ajouterStock(tailleId, produitId, quantite, card, qteInput) {
        var btn = card.querySelector('.btn-add');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-sm"></span>';

        fetch(URL_ROOT + '/admin/stocks/ajouterEntreeAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'taille_id=' + tailleId + '&produit_id=' + produitId + '&quantite=' + quantite
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var nouveauStock = data.nouveau_stock;
                var stockNb = card.querySelector('.tc-stock-nb');
                var barFill = card.querySelector('.tc-bar-fill');
                var badge = card.querySelector('.tc-badge');

                // Mettre à jour le nombre
                stockNb.textContent = nouveauStock;

                // Mettre à jour les classes et labels
                var cls = 'ok';
                var label = 'En stock';
                if (nouveauStock <= 0) { cls = 'rupture'; label = 'Rupture'; }
                else if (nouveauStock <= STOCK_SEUIL_ALERTE) { cls = 'faible'; label = 'Stock faible'; }

                stockNb.className = 'tc-stock-nb ' + cls;
                barFill.className = 'tc-bar-fill ' + cls;
                badge.className = 'tc-badge ' + cls;
                badge.textContent = label;

                // Animation barre
                var allStocks = [];
                document.querySelectorAll('.tc-stock-nb').forEach(function(s) {
                    allStocks.push(parseInt(s.textContent) || 0);
                });
                var maxS = Math.max.apply(null, allStocks) || 1;
                document.querySelectorAll('.tc-bar-fill').forEach(function(f, i) {
                    var s = parseInt(document.querySelectorAll('.tc-stock-nb')[i].textContent) || 0;
                    f.style.width = Math.min(100, Math.round((s / maxS) * 100)) + '%';
                });

                // Animation carte
                card.style.borderColor = '#25D366';
                card.style.background = '#e8f5e9';
                setTimeout(function() {
                    card.style.borderColor = '';
                    card.style.background = '';
                }, 800);

                // Réinitialiser
                qteInput.value = 1;

                // Overlay de confirmation vert
                var produitNom = phNom.textContent;
                var tailleLabel = card.querySelector('.tc-taille').textContent.replace('Taille ', '');
                showConfirmOverlay(quantite, produitNom, tailleLabel);

                // Ajouter au feed
                ajouterAuFeed(quantite, produitNom, tailleLabel);
            } else {
                showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
            }
        })
        .catch(function() {
            showToast('Erreur réseau', 'error');
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '➕ Ajouter';
        });
    }

    // ── Ajouter une entrée au feed ─────────────────────────
    function ajouterAuFeed(quantite, produitNom, taille) {
        if (!feedList) return;

        // Supprimer le message vide si présent
        if (feedEmpty) feedEmpty.style.display = 'none';

        var item = document.createElement('div');
        item.className = 'feed-item feed-new';
        item.innerHTML =
            '<div class="feed-qte">+' + quantite + '</div>' +
            '<div class="feed-body">' +
                '<div class="feed-produit">' + produitNom + '</div>' +
                '<div class="feed-taille">Taille ' + taille + '</div>' +
            '</div>' +
            '<div class="feed-time">À l\'instant</div>';

        feedList.insertBefore(item, feedList.firstChild);

        // Retirer la classe "new" après 2s
        setTimeout(function() {
            item.classList.remove('feed-new');
        }, 2000);

        // Limiter à 20 items
        while (feedList.children.length > 20) {
            feedList.removeChild(feedList.lastChild);
        }

        // Animer le compteur
        if (feedCounter) {
            var count = parseInt(feedCounter.textContent) || 0;
            feedCounter.textContent = count + 1;
            feedCounter.classList.add('pulse');
            setTimeout(function() { feedCounter.classList.remove('pulse'); }, 500);
        }
    }

    // ── Changement de produit ──────────────────────────────
    produitSelect.addEventListener('change', function() {
        var produitId = this.value;

        if (!produitId) {
            produitPanel.classList.remove('visible');
            return;
        }

        var produit = produitsData.find(function(p) {
            return String(p.id) === produitId;
        });

        if (produit) {
            phNom.textContent = produit.nom;
            phMarque.textContent = produit.marque || '';
            if (produit.image) {
                phImg.src = UPLOAD_URL + produit.image;
                phImg.style.display = 'block';
                phImgPlaceholder.style.display = 'none';
            } else {
                phImg.style.display = 'none';
                phImgPlaceholder.style.display = 'flex';
            }
        }

        produitPanel.classList.add('visible');
        renderTailles(produitId);
    });

    // ── Ajouter une nouvelle taille en LIVE (sans re-render) ──
    function ajouterTailleLive(produitId, taille, stock, newId) {
        // 1. Mettre à jour taillesData en mémoire
        if (!taillesData[produitId]) taillesData[produitId] = [];
        taillesData[produitId].push({ id: newId, produit_id: produitId, taille: taille, stock: stock });

        // 2. Supprimer le message "aucune taille" si présent
        var emptyMsg = taillesContainer.querySelector('.empty-tailles');
        if (emptyMsg) emptyMsg.remove();

        // 3. Créer la grille si elle n'existe pas
        var grid = taillesContainer.querySelector('.tailles-grid');
        if (!grid) {
            grid = document.createElement('div');
            grid.className = 'tailles-grid';
            taillesContainer.appendChild(grid);
        }

        // 4. Calculer le max stock pour les barres
        var allStocks = taillesData[produitId].map(function(t) { return parseInt(t.stock) || 0; });
        var maxStock = Math.max.apply(null, allStocks) || 1;
        var pct = Math.min(100, Math.round((stock / maxStock) * 100));
        var cls = 'ok';
        var label = 'En stock';
        if (stock <= 0) { cls = 'rupture'; label = 'Rupture'; }
        else if (stock <= STOCK_SEUIL_ALERTE) { cls = 'faible'; label = 'Stock faible'; }

        // 5. Créer la carte HTML
        var card = document.createElement('div');
        card.className = 'taille-card';
        card.dataset.tailleId = newId;
        card.dataset.produitId = produitId;
        card.innerHTML =
            '<div class="tc-head">' +
                '<span class="tc-taille">Taille ' + taille + '</span>' +
                '<span class="tc-badge ' + cls + '">' + label + '</span>' +
            '</div>' +
            '<div class="tc-bar">' +
                '<div class="tc-bar-fill ' + cls + '" style="width:' + pct + '%"></div>' +
            '</div>' +
            '<div class="tc-stock">' +
                '<span>Stock</span>' +
                '<span class="tc-stock-nb ' + cls + '">' + stock + '</span>' +
            '</div>' +
            '<div class="tc-actions">' +
                '<input type="number" class="qte-input" min="1" value="1" placeholder="Qte">' +
                '<button class="btn-add" data-taille-id="' + newId + '" data-produit-id="' + produitId + '">' +
                    '➕ Ajouter' +
                '</button>' +
            '</div>';

        // 6. Animation d'entrée
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        grid.appendChild(card);
        requestAnimationFrame(function() {
            card.style.transition = 'all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });

        // 7. Attacher événements sur la nouvelle carte
        card.querySelector('.btn-add').addEventListener('click', function() {
            var tId = this.dataset.tailleId;
            var pId = this.dataset.produitId;
            var c = this.closest('.taille-card');
            var qInput = c.querySelector('.qte-input');
            var qte = parseInt(qInput.value) || 0;
            if (qte <= 0) { showToast('Veuillez saisir une quantité valide', 'error'); return; }
            ajouterStock(tId, pId, qte, c, qInput);
        });
        card.querySelector('.qte-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                var c = this.closest('.taille-card');
                var b = c.querySelector('.btn-add');
                if (b) b.click();
            }
        });

        // 8. Mettre à jour les barres de toutes les tailles
        var maxS = Math.max.apply(null, taillesData[produitId].map(function(t) { return parseInt(t.stock) || 0; })) || 1;
        grid.querySelectorAll('.tc-bar-fill').forEach(function(fill) {
            var cardEl = fill.closest('.taille-card');
            var tId = cardEl.dataset.tailleId;
            var t = taillesData[produitId].find(function(t) { return String(t.id) === tId; });
            if (t) {
                var s = parseInt(t.stock) || 0;
                fill.style.width = Math.min(100, Math.round((s / maxS) * 100)) + '%';
            }
        });

        // 9. Mettre à jour le compteur de tailles
        phNbTailles.textContent = taillesData[produitId].length;

        // 10. Toast de confirmation
        showToast('✅ Taille "' + taille + '" ajoutée avec ' + stock + ' en stock', 'success');
    }

    // ── Ajouter une nouvelle taille (bouton) ────────────────
    btnAddTaille.addEventListener('click', function() {
        var produitId = produitSelect.value;
        var taille = newTailleInput.value.trim();
        var stock = parseInt(newStockInput.value) || 0;

        if (!produitId) {
            showToast('Veuillez d\'abord sélectionner un produit', 'error');
            return;
        }
        if (!taille) {
            showToast('Veuillez saisir une taille', 'error');
            return;
        }

        this.disabled = true;
        this.textContent = 'Ajout...';

        fetch(URL_ROOT + '/admin/stocks/ajouterTaille', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'produit_id=' + produitId + '&taille=' + encodeURIComponent(taille) + '&stock=' + stock
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                newTailleInput.value = '';
                newStockInput.value = '0';
                // Ajout LIVE sans re-render
                ajouterTailleLive(produitId, taille, stock, data.id);
            } else {
                showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
            }
        })
        .catch(function() {
            showToast('Erreur réseau', 'error');
        })
        .finally(function() {
            this.disabled = false;
            this.textContent = '➕ Ajouter';
        }.bind(this));
    });

    // Enter key
    newTailleInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') btnAddTaille.click();
    });
    newStockInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') btnAddTaille.click();
    });

})();
</script>
</body>
</html>
