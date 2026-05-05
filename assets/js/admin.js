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

})();
