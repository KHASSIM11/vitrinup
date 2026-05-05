/* ============================================================
   JavaScript Admin — Vitrinup
   ============================================================ */

document.addEventListener('DOMContentLoaded', function() {

    // ── Mise à jour stock (AJAX) ──
    document.querySelectorAll('.stock-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const tailleId = this.dataset.tailleId;
            const stock = parseInt(this.value) || 0;

            fetch(URL_ROOT + '/admin/stocks/modifierStock', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'taille_id=' + tailleId + '&stock=' + stock
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    input.classList.add('saved');
                    setTimeout(function() { input.classList.remove('saved'); }, 1000);
                } else {
                    alert('Erreur : ' + (data.error || 'Inconnue'));
                }
            })
            .catch(function() { alert('Erreur réseau'); });
        });
    });

    // ── Ajouter une taille (AJAX) ──
    document.querySelectorAll('.btn-add-taille').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const produitId = this.dataset.produitId;
            const tailleInput = document.getElementById('new-taille-' + produitId);
            const stockInput = document.getElementById('new-stock-' + produitId);
            const taille = tailleInput ? tailleInput.value.trim() : '';
            const stock = stockInput ? parseInt(stockInput.value) || 0 : 0;

            if (!taille) {
                alert('Veuillez saisir une taille');
                return;
            }

            fetch(URL_ROOT + '/admin/stocks/ajouterTaille', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'produit_id=' + produitId + '&taille=' + encodeURIComponent(taille) + '&stock=' + stock
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur : ' + (data.error || 'Inconnue'));
                }
            })
            .catch(function() { alert('Erreur réseau'); });
        });
    });

    // ── Supprimer une taille (AJAX) ──
    document.querySelectorAll('.btn-del-taille').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Supprimer cette taille ?')) return;

            const tailleId = this.dataset.tailleId;

            fetch(URL_ROOT + '/admin/stocks/supprimerTaille', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'taille_id=' + tailleId
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur : ' + (data.error || 'Inconnue'));
                }
            })
            .catch(function() { alert('Erreur réseau'); });
        });
    });

    // ── Charger les tailles (page entrée) ──
    const produitSelect = document.getElementById('produitSelect');
    const tailleSelect = document.getElementById('tailleSelect');

    if (produitSelect && tailleSelect && typeof taillesData !== 'undefined') {
        produitSelect.addEventListener('change', function() {
            const produitId = this.value;
            tailleSelect.innerHTML = '';

            if (!produitId) {
                tailleSelect.innerHTML = '<option value="">-- D\'abord choisir un produit --</option>';
                return;
            }

            const tailles = taillesData[produitId] || [];
            if (tailles.length === 0) {
                tailleSelect.innerHTML = '<option value="">-- Aucune taille disponible --</option>';
                return;
            }

            tailles.forEach(function(t) {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.taille + ' (stock actuel: ' + t.stock + ')';
                tailleSelect.appendChild(opt);
            });
        });
    }

});
