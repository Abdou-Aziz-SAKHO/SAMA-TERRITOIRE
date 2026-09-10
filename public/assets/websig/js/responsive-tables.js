/**
 * Tableaux responsives génériques (pages Admin & User)
 * ----------------------------------------------------
 * Transforme automatiquement TOUTE table possédant un <thead> en
 * « cartes » lisibles sur petit écran (<=700px), sans aucune classe à
 * ajouter dans les vues.
 *
 * Fonctionnement :
 *  1. À l'init, chaque colonne (<th>) devient un libellé.
 *  2. Chaque <td> reçoit un attribut data-label correspondant.
 *  3. Sur mobile, le CSS masque la ligne d'en-têtes et affiche chaque
 *     ligne comme une carte : libellé à gauche, valeur à droite.
 *  4. Les colonnes d'actions (entête « Action(s) ») sont alignées à
 *     droite et sans libellé.
 *  5. Un MutationObserver traite automatiquement tout tableau inséré
 *     dynamiquement plus tard (AJAX, onglets, etc.).
 */
(function () {
    'use strict';

    function responsiveTablesInit(root) {
        root = root || document;
        var theadRows = root.querySelectorAll('table thead tr');

        theadRows.forEach(function (theadRow) {
            var table = theadRow.closest('table');
            if (!table || table.classList.contains('tbl-card')) return;

            // On ne traite que les tables avec un vrai en-tête de colonnes
            var cols = theadRow.querySelectorAll('th');
            if (!cols.length) return;

            var labels = [];
            cols.forEach(function (th) {
                labels.push(th.textContent.trim());
            });

            table.classList.add('tbl-card');

            table.querySelectorAll('tbody tr').forEach(function (row) {
                var cells = row.querySelectorAll('td');
                cells.forEach(function (td, i) {
                    // Lignes « vide » (colspan) : pas de label, centrage seul
                    if (td.hasAttribute('colspan')) return;

                    var lbl = labels[i] || '';
                    td.setAttribute('data-label', lbl);

                    // Colonne d'actions : alignée à droite, sans libellé
                    if (/^action/i.test(lbl)) {
                        td.setAttribute('data-actions', '1');
                    }
                });
            });
        });
    }

    // Lancement au chargement
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            responsiveTablesInit();
        });
    } else {
        responsiveTablesInit();
    }

    // Traite automatiquement les tableaux ajoutés dynamiquement
    var timer = null;
    var observer = new MutationObserver(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            responsiveTablesInit();
        }, 150);
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();