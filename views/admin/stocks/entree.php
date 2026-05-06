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
        /* ── Styles spécifiques à la page entrée ── */
        .entree-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        @media (max-width: 1100px) {
            .entree-layout { grid-template-columns: 1fr; }
        }

        /* ── Sélecteur produit amélioré ── */
        .product-selector {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .product-selector select {
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 500;
            outline: none;
            background: var(--white);
            transition: var(--transition);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }
        .product-selector select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
        }
        .product-selector select option {
            padding: 8px;
        }

        /* ── Tailles grid ── */
        .tailles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        .taille-card {
            background: #fafafa;
            border: 2px solid #eee;
            border-radius: var(--radius-sm);
            padding: 16px;
            transition: var(--transition);
            position: relative;
        }
        .taille-card:hover {
            border-color: #ddd;
            background: #f5f5f5;
        }
        .taille-card .taille-label {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .taille-card .stock-actuel {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        .taille-card .stock-actuel strong {
            font-size: 1.2rem;
        }
        .taille-card .stock-actuel strong.ok { color: var(--green-text); }
        .taille-card .stock-actuel strong.faible { color: var(--orange-text); }
        .taille-card .stock-actuel strong.rupture { color: var(--red-text); }

        .taille-card .qte-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .taille-card .qte-row input[type="number"] {
            flex: 1;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            outline: none;
            transition: var(--transition);
            width: 80px;
        }
        .taille-card .qte-row input[type="number"]:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.12);
        }
        .taille-card .qte-row button {
            padding: 10px 18px;
            border: none;
            border-radius: var(--radius-sm);
            background: var(--gold);
            color: var(--dark);
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
        }
        .taille-card .qte-row button:hover {
            background: var(--gold-light);
            transform: translateY(-1px);
        }
        .taille-card .qte-row button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .taille-card .qte-row button .spinner-sm {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(0,0,0,0.2);
            border-top-color: var(--dark);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* ── Ajout nouvelle taille ── */
        .add-taille-section {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px dashed #ddd;
        }
        .add-taille-section .add-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        .add-taille-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .add-taille-row input {
            padding: 10px 14px;
            border: 2px solid #e0e0e0;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            outline: none;
            transition: var(--transition);
        }
        .add-taille-row input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.12);
        }
        .add-taille-row input[type="text"] { width: 100px; }
        .add-taille-row input[type="number"] { width: 80px; }
        .add-taille-row button {
            padding: 10px 20px;
            border: none;
            border-radius: var(--radius-sm);
            background: var(--dark);
            color: var(--white);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .add-taille-row button:hover {
            background: #333;
            transform: translateY(-1px);
        }
        .add-taille-row button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Info produit sélectionné ── */
        .produit-info {
            display: none;
            background: var(--white);
            border: 2px solid #eee;
            border-radius: var(--radius);
            padding: 24px;
            margin-top: 16px;
        }
        .produit-info.visible { display: block; }
        .produit-info .pi-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .produit-info .pi-header img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            background: #eee;
        }
        .produit-info .pi-header .pi-nom {
            font-size: 1.2rem;
            font-weight: 700;
        }
        .produit-info .pi-header .pi-marque {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .produit-info .pi-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }
        .produit-info .pi-empty .pi-empty-icon {
            font-size: 3rem;
            margin-bottom: 12px;
        }

        /* ── Animation succès ── */
        .taille-card.saved {
            border-color: var(--green);
            background: var(--green-bg);
            animation: pulse-green 0.5s ease;
        }
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.3); }
            50% { box-shadow: 0 0 0 8px rgba(37, 211, 102, 0); }
        }

        /* ── Badge stock inline ── */
        .stock-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .stock-indicator.ok { background: var(--green); }
        .stock-indicator.faible { background: var(--orange); }
        .stock-indicator.rupture { background: var(--red); }

        /* ── Message flash amélioré ── */
        .flash-message {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }
        .flash-success {
            background: var(--green-bg);
            border: 1px solid #a5d6a7;
            color: var(--green-text);
        }
        .flash-error {
            background: var(--red-bg);
            border: 1px solid #ef9a9a;
            color: var(--red-text);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Dernières entrées compact ── */
        .last-entries {
            max-height: 500px;
            overflow-y: auto;
        }
        .last-entries .entry-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .last-entries .entry-item:last-child { border-bottom: none; }
        .last-entries .entry-item .entry-qte {
            font-weight: 700;
            color: var(--green-text);
            min-width: 50px;
        }
        .last-entries .entry-item .entry-detail {
            flex: 1;
            font-size: 0.85rem;
        }
        .last-entries .entry-item .entry-date {
            font-size: 0.75rem;
            color: var(--text-muted);
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
            <div class="subtitle">Ajoutez du stock ou créez de nouvelles tailles pour vos produits</div>
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
        <!-- Colonne gauche : Formulaire principal -->
        <div class="card">
            <h2>🎯 Ajouter du stock</h2>

            <div class="product-selector">
                <label style="font-weight:600;font-size:0.85rem;color:#555;">Sélectionnez un produit</label>
                <select name="produit_id" id="produitSelect">
                    <option value="">-- Choisir un produit --</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= intval($p['id']) ?>" data-image="<?= htmlspecialchars($p['image'] ?? '') ?>">
                            <?= htmlspecialchars($p['nom']) ?>
                            <?= $p['marque'] ? ' (' . htmlspecialchars($p['marque']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Info produit + tailles -->
            <div class="produit-info" id="produitInfo">
                <div class="pi-header" id="piHeader">
                    <img id="piImage" src="" alt="">
                    <div>
                        <div class="pi-nom" id="piNom"></div>
                        <div class="pi-marque" id="piMarque"></div>
                    </div>
                </div>

                <div id="taillesContainer">
                    <div class="pi-empty">
                        <div class="pi-empty-icon">👟</div>
                        <p>Sélectionnez un produit pour gérer ses tailles et stocks</p>
                    </div>
                </div>

                <!-- Ajout nouvelle taille -->
                <div class="add-taille-section" id="addTailleSection" style="display:none;">
                    <div class="add-title">➕ Ajouter une nouvelle taille</div>
                    <div class="add-taille-row">
                        <input type="text" id="newTailleInput" placeholder="Taille (ex: 42)" maxlength="20">
                        <input type="number" id="newStockInput" placeholder="Stock" min="0" value="0">
                        <button id="btnAddTaille">Ajouter</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Dernières entrées -->
        <div class="card">
            <h2>🕐 Dernières entrées</h2>
            <?php if (!empty($dernieresEntrees)): ?>
                <div class="last-entries">
                    <?php foreach ($dernieresEntrees as $e): ?>
                        <div class="entry-item">
                            <span class="entry-qte">+<?= intval($e['quantite']) ?></span>
                            <div class="entry-detail">
                                <strong><?= htmlspecialchars($e['produit_nom']) ?></strong>
                                <span style="color:var(--text-muted)">— Taille <?= htmlspecialchars($e['taille']) ?></span>
                                <?php if ($e['reference']): ?>
                                    <br><small style="color:var(--text-muted)"><?= htmlspecialchars($e['reference']) ?></small>
                                <?php endif; ?>
                            </div>
                            <span class="entry-date"><?= date('d/m H:i', strtotime($e['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-msg">
                    <div class="empty-icon">📭</div>
                    <p>Aucune entrée pour le moment</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
const URL_ROOT = '<?= URL_ROOT ?>';
const STOCK_SEUIL_ALERTE = <?= STOCK_SEUIL_ALERTE ?>;
const taillesData = <?= json_encode($taillesParProduit) ?>;
const produitsData = <?= json_encode($produits) ?>;
</script>
<script src="<?= URL_ROOT ?>/assets/js/admin.js"></script>
<script>
/* ── Script spécifique à la page entrée ── */
(function() {
    'use strict';

    const produitSelect = document.getElementById('produitSelect');
    const produitInfo = document.getElementById('produitInfo');
    const piImage = document.getElementById('piImage');
    const piNom = document.getElementById('piNom');
    const piMarque = document.getElementById('piMarque');
    const taillesContainer = document.getElementById('taillesContainer');
    const addTailleSection = document.getElementById('addTailleSection');
    const newTailleInput = document.getElementById('newTailleInput');
    const newStockInput = document.getElementById('newStockInput');
    const btnAddTaille = document.getElementById('btnAddTaille');

    // ── Toast helper ──
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

    // ── Afficher les tailles d'un produit ──
    function renderTailles(produitId) {
        const tailles = taillesData[produitId] || [];

        if (tailles.length === 0) {
            taillesContainer.innerHTML = `
                <div class="pi-empty">
                    <div class="pi-empty-icon">📏</div>
                    <p>Aucune taille définie pour ce produit.</p>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin-top:4px;">Utilisez le formulaire ci-dessous pour ajouter une taille.</p>
                </div>
            `;
            addTailleSection.style.display = 'block';
            return;
        }

        let html = '<div class="tailles-grid">';
        tailles.forEach(function(t) {
            const stock = parseInt(t.stock) || 0;
            let cls = 'ok';
            let label = 'En stock';
            if (stock <= 0) { cls = 'rupture'; label = 'Rupture'; }
            else if (stock <= STOCK_SEUIL_ALERTE) { cls = 'faible'; label = 'Stock faible'; }

            html += `
                <div class="taille-card" data-taille-id="${t.id}" data-produit-id="${produitId}">
                    <div class="taille-label">
                        <span>Taille ${t.taille}</span>
                        <span class="stock-badge stock-${cls}">${label}</span>
                    </div>
                    <div class="stock-actuel">
                        Stock actuel : <strong class="${cls}">${stock}</strong>
                    </div>
                    <div class="qte-row">
                        <input type="number" class="qte-input" min="1" value="1" placeholder="Qte">
                        <button class="btn-add-stock" data-taille-id="${t.id}" data-produit-id="${produitId}">
                            ➕ Ajouter
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        taillesContainer.innerHTML = html;
        addTailleSection.style.display = 'block';

        // Attacher les événements aux boutons "Ajouter"
        document.querySelectorAll('.btn-add-stock').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const tailleId = this.dataset.tailleId;
                const produitId = this.dataset.produitId;
                const card = this.closest('.taille-card');
                const qteInput = card.querySelector('.qte-input');
                const quantite = parseInt(qteInput.value) || 0;

                if (quantite <= 0) {
                    showToast('Veuillez saisir une quantité valide', 'error');
                    return;
                }

                ajouterStock(tailleId, produitId, quantite, card, qteInput);
            });
        });

        // Enter key sur les inputs quantité
        document.querySelectorAll('.qte-input').forEach(function(input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const card = this.closest('.taille-card');
                    const btn = card.querySelector('.btn-add-stock');
                    if (btn) btn.click();
                }
            });
        });
    }

    // ── Ajouter du stock (AJAX) ──
    function ajouterStock(tailleId, produitId, quantite, card, qteInput) {
        const btn = card.querySelector('.btn-add-stock');
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
                // Mettre à jour l'affichage du stock
                const stockStrong = card.querySelector('.stock-actuel strong');
                const badge = card.querySelector('.stock-badge');
                const nouveauStock = data.nouveau_stock;

                stockStrong.textContent = nouveauStock;
                let cls = 'ok';
                let label = 'En stock';
                if (nouveauStock <= 0) { cls = 'rupture'; label = 'Rupture'; }
                else if (nouveauStock <= STOCK_SEUIL_ALERTE) { cls = 'faible'; label = 'Stock faible'; }
                stockStrong.className = cls;
                badge.className = 'stock-badge stock-' + cls;
                badge.textContent = label;

                // Animation
                card.classList.add('saved');
                setTimeout(function() { card.classList.remove('saved'); }, 1000);

                // Réinitialiser quantité
                qteInput.value = 1;

                showToast('✅ +' + quantite + ' unité' + (quantite > 1 ? 's' : '') + ' ajoutée' + (quantite > 1 ? 's' : ''), 'success');
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

    // ── Changement de produit ──
    produitSelect.addEventListener('change', function() {
        const produitId = this.value;

        if (!produitId) {
            produitInfo.classList.remove('visible');
            return;
        }

        // Trouver le produit dans les données
        const produit = produitsData.find(function(p) {
            return String(p.id) === produitId;
        });

        if (produit) {
            piNom.textContent = produit.nom;
            piMarque.textContent = produit.marque || '';
            if (produit.image) {
                piImage.src = URL_ROOT + '/' + produit.image;
                piImage.style.display = 'block';
            } else {
                piImage.src = '';
                piImage.style.display = 'none';
            }
        }

        produitInfo.classList.add('visible');
        renderTailles(produitId);
    });

    // ── Ajouter une nouvelle taille ──
    btnAddTaille.addEventListener('click', function() {
        const produitId = produitSelect.value;
        const taille = newTailleInput.value.trim();
        const stock = parseInt(newStockInput.value) || 0;

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
                showToast('✅ Taille "' + taille + '" ajoutée avec ' + stock + ' en stock', 'success');
                newTailleInput.value = '';
                newStockInput.value = '0';
                // Recharger les tailles
                renderTailles(produitId);
            } else {
                showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
            }
        })
        .catch(function() {
            showToast('Erreur réseau', 'error');
        })
        .finally(function() {
            this.disabled = false;
            this.textContent = 'Ajouter';
        }.bind(this));
    });

    // Enter key sur les inputs d'ajout de taille
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
