/* ============================================================
   Admin JS Premium — Vitrinup v2.0
   Toast notifications, modales, AJAX, auto-refresh
   ============================================================ */

(function() {
    'use strict';

    // ── Toast System ───────────────────────────────────────
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

    // ── Modal System ───────────────────────────────────────
    function showModal(title, message, confirmText, confirmClass, callback) {
        var overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.innerHTML =
            '<div class="modal">' +
            '<h3>' + title + '</h3>' +
            '<p>' + message + '</p>' +
            '<div class="modal-actions">' +
            '<button class="btn-cancel" id="modalCancel">Annuler</button>' +
            '<button class="' + (confirmClass || 'btn-confirm') + '" id="modalConfirm">' + (confirmText || 'Confirmer') + '</button>' +
            '</div>' +
            '</div>';
        document.body.appendChild(overlay);
        document.getElementById('modalCancel').addEventListener('click', function() {
            overlay.remove();
        });
        document.getElementById('modalConfirm').addEventListener('click', function() {
            overlay.remove();
            if (typeof callback === 'function') callback();
        });
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) overlay.remove();
        });
    }

    // ── Mise à jour stock (AJAX) ──
    document.querySelectorAll('.stock-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var tailleId = this.dataset.tailleId;
            var stock = parseInt(this.value) || 0;
            var oldVal = this.defaultValue;

            if (stock === parseInt(oldVal)) return;

            var self = this;
            fetch(URL_ROOT + '/admin/stocks/modifierStock', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'taille_id=' + tailleId + '&stock=' + stock
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    self.defaultValue = stock;
                    self.classList.add('saved');
                    setTimeout(function() { self.classList.remove('saved'); }, 1000);
                    showToast('Stock mis à jour !', 'success');
                    // Mettre à jour les stats si présentes
                    if (typeof refreshStats === 'function') refreshStats();
                } else {
                    self.value = oldVal;
                    showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
                }
            })
            .catch(function() {
                self.value = oldVal;
                showToast('Erreur réseau', 'error');
            });
        });
    });

    // ── Ajouter une taille (AJAX) ──
    document.querySelectorAll('.btn-add-taille').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var produitId = this.dataset.produitId;
            var tailleInput = document.getElementById('new-taille-' + produitId);
            var stockInput = document.getElementById('new-stock-' + produitId);
            var taille = tailleInput ? tailleInput.value.trim() : '';
            var stock = stockInput ? parseInt(stockInput.value) || 0 : 0;

            if (!taille) {
                showToast('Veuillez saisir une taille', 'error');
                return;
            }

            var self = this;
            self.disabled = true;
            self.innerHTML = '<span class="spinner"></span>';

            fetch(URL_ROOT + '/admin/stocks/ajouterTaille', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'produit_id=' + produitId + '&taille=' + encodeURIComponent(taille) + '&stock=' + stock
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('Taille "' + taille + '" ajoutée !', 'success');
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    self.disabled = false;
                    self.innerHTML = '+ Ajouter';
                    showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
                }
            })
            .catch(function() {
                self.disabled = false;
                self.innerHTML = '+ Ajouter';
                showToast('Erreur réseau', 'error');
            });
        });
    });

    // ── Supprimer une taille (AJAX) ──
    document.querySelectorAll('.btn-del-taille').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tailleId = this.dataset.tailleId;
            var tailleLabel = this.parentElement.querySelector('span') ?
                this.parentElement.querySelector('span').textContent : 'cette taille';

            showModal(
                'Supprimer la taille ?',
                'Êtes-vous sûr de vouloir supprimer ' + tailleLabel + ' ? Cette action est irréversible.',
                'Supprimer',
                'btn-danger',
                function() {
                    fetch(URL_ROOT + '/admin/stocks/supprimerTaille', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'taille_id=' + tailleId
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            showToast('Taille supprimée', 'success');
                            setTimeout(function() { location.reload(); }, 500);
                        } else {
                            showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
                        }
                    })
                    .catch(function() { showToast('Erreur réseau', 'error'); });
                }
            );
        });
    });

    // ── Charger les tailles (page entrée) ──
    var produitSelect = document.getElementById('produitSelect');
    var tailleSelect = document.getElementById('tailleSelect');

    if (produitSelect && tailleSelect && typeof taillesData !== 'undefined') {
        produitSelect.addEventListener('change', function() {
            var produitId = this.value;
            tailleSelect.innerHTML = '';

            if (!produitId) {
                tailleSelect.innerHTML = '<option value="">-- D\'abord choisir un produit --</option>';
                return;
            }

            var tailles = taillesData[produitId] || [];
            if (tailles.length === 0) {
                tailleSelect.innerHTML = '<option value="">-- Aucune taille disponible --</option>';
                return;
            }

            tailles.forEach(function(t) {
                var opt = document.createElement('option');
                opt.value = t.id;
                var stockClass = parseInt(t.stock) <= 0 ? '🔴' :
                    (parseInt(t.stock) <= STOCK_SEUIL_ALERTE ? '🟡' : '🟢');
                opt.textContent = t.taille + '  ' + stockClass + ' (stock: ' + t.stock + ')';
                tailleSelect.appendChild(opt);
            });
        });
        // Déclencher si déjà sélectionné
        if (produitSelect.value) {
            var event = new Event('change');
            produitSelect.dispatchEvent(event);
        }
    }

    // ── Confirmation des actions (sortie) ──
    document.querySelectorAll('.btn-confirmer, .btn-annuler').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var msg = this.getAttribute('data-confirm') ||
                (this.classList.contains('btn-confirmer') ?
                    'Confirmer cette commande ? 1 unité sera déduite du stock.' :
                    'Annuler cette commande ?');
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // ── Auto-refresh des stats ──
    function refreshStats() {
        // Sera implémenté si nécessaire
    }

    // ── Preview Images (produits/form) ─────────────────────
    window.previewImages = function(input) {
        var container = document.getElementById('newPreviews');
        if (!container) return;
        container.innerHTML = '';
        Array.from(input.files).forEach(function(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var div = document.createElement('div');
                div.className = 'preview-img';
                div.innerHTML = '<img src="' + e.target.result + '" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">';
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    };

    // ── Hamburger Menu (Mobile) ────────────────────────────
    var hamburger = document.querySelector('.sidebar .hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            var nav = document.querySelector('.sidebar nav');
            var logout = document.querySelector('.sidebar .logout');
            var adminInfo = document.querySelector('.sidebar .admin-info');
            var isOpen = nav ? nav.classList.contains('open') : false;

            if (nav) nav.classList.toggle('open');
            if (logout) logout.classList.toggle('open');
            if (adminInfo) adminInfo.classList.toggle('open');

            // Changer l'icône du hamburger
            this.textContent = isOpen ? '☰' : '✕';
        });

        // Fermer le menu quand on clique sur un lien
        document.querySelectorAll('.sidebar nav a').forEach(function(link) {
            link.addEventListener('click', function() {
                var nav = document.querySelector('.sidebar nav');
                var logout = document.querySelector('.sidebar .logout');
                var adminInfo = document.querySelector('.sidebar .admin-info');
                if (nav) nav.classList.remove('open');
                if (logout) logout.classList.remove('open');
                if (adminInfo) adminInfo.classList.remove('open');
                if (hamburger) hamburger.textContent = '☰';
            });
        });
    }

    // ── Export CSV ─────────────────────────────────────────
    var exportBtn = document.getElementById('exportCsv');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var table = document.querySelector('table');
            if (!table) return;
            var rows = table.querySelectorAll('tr');
            var csv = [];
            rows.forEach(function(row) {
                var cols = row.querySelectorAll('td, th');
                var rowData = [];
                cols.forEach(function(col) {
                    var text = col.textContent.trim().replace(/,/g, ';').replace(/\n/g, ' ');
                    rowData.push('"' + text + '"');
                });
                csv.push(rowData.join(','));
            });
            var blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'export_stocks_' + new Date().toISOString().slice(0,10) + '.csv';
            link.click();
            showToast('Export CSV téléchargé !', 'success');
        });
    }

    // ── Recherche en direct ──
    var searchInput = document.getElementById('searchLive');
    if (searchInput) {
        var timer;
        searchInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                var q = searchInput.value.trim();
                if (q.length >= 2 || q.length === 0) {
                    window.location.href = URL_ROOT + '/admin/stocks?search=' + encodeURIComponent(q);
                }
            }, 500);
        });
    }

    // ── Highlight search terms ──
    var urlParams = new URLSearchParams(window.location.search);
    var searchTerm = urlParams.get('search');
    if (searchTerm) {
        var cells = document.querySelectorAll('td');
        cells.forEach(function(cell) {
            var html = cell.innerHTML;
            var regex = new RegExp('(' + searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            if (regex.test(html)) {
                cell.innerHTML = html.replace(regex, '<mark>$1</mark>');
            }
        });
    }

    // ── ENTRÉE DE STOCK — Script Premium v3.0 ──────────────
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

        // ── Ajouter une nouvelle taille en LIVE ────────────────
        function ajouterTailleLive(produitId, taille, stock, newId) {
            if (!taillesData[produitId]) taillesData[produitId] = [];
            taillesData[produitId].push({ id: newId, produit_id: produitId, taille: taille, stock: stock });

            var emptyMsg = taillesContainer.querySelector('.empty-tailles');
            if (emptyMsg) emptyMsg.remove();

            var grid = taillesContainer.querySelector('.tailles-grid');
            if (!grid) {
                grid = document.createElement('div');
                grid.className = 'tailles-grid';
                taillesContainer.appendChild(grid);
            }

            var allStocks = taillesData[produitId].map(function(t) { return parseInt(t.stock) || 0; });
            var maxStock = Math.max.apply(null, allStocks) || 1;
            var pct = Math.min(100, Math.round((stock / maxStock) * 100));
            var cls = 'ok';
            var label = 'En stock';
            if (stock <= 0) { cls = 'rupture'; label = 'Rupture'; }
            else if (stock <= STOCK_SEUIL_ALERTE) { cls = 'faible'; label = 'Stock faible'; }

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

            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            grid.appendChild(card);
            requestAnimationFrame(function() {
                card.style.transition = 'all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });

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

            phNbTailles.textContent = taillesData[produitId].length;
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
})();
