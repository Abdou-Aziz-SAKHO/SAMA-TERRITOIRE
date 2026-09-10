@extends('AppAdmi')
@section('content')

<div class="page active" style="max-width:1100px; margin:0 auto;">

    {{-- ═══ Message de succès ═══ --}}
    @if(session('succes'))
    <div class="success-popup active" id="popup-succes" onclick="this.classList.remove('active')">
        <div class="success-card">
            <div class="success-icon"><i class="fa-solid fa-check"></i></div>
            <h4>Succès</h4>
            <p>{{ session('succes') }}</p>
        </div>
    </div>
    <script>setTimeout(function(){ var p=document.getElementById('popup-succes'); if(p) p.classList.remove('active'); }, 3000);</script>
    @endif

    <div class="page-title">
        Commentaires & Plaintes
        @if($commentaires->total() > 0)
            <span style="background:var(--primary); color:#fff; font-size:12px; padding:3px 10px; border-radius:12px; margin-left:10px;">{{ $commentaires->total() }}</span>
        @endif
    </div>
    <div class="page-sub">Commentaires, suggestions et plaintes des utilisateurs du territoire</div>

    {{-- ═══ Filtres ═══ --}}
    <form method="GET" action="{{ url('/CommentairesAdmi') }}" class="stats-filters">
        <div class="stats-filters-inner">
            <div class="filter-group">
                <label for="type">Type</label>
                <select name="type" id="type" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="commentaire" {{ $type === 'commentaire' ? 'selected' : '' }}>💬 Commentaire</option>
                    <option value="suggestion" {{ $type === 'suggestion' ? 'selected' : '' }}>🟢 Suggestion</option>
                    <option value="plainte" {{ $type === 'plainte' ? 'selected' : '' }}>🔴 Plainte</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="statut">Statut</label>
                <select name="statut" id="statut" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ $statut === 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                    <option value="lu" {{ $statut === 'lu' ? 'selected' : '' }}>👁️ Lu</option>
                    {{-- <option value="traite" {{ $statut === 'traite' ? 'selected' : '' }}>✅ Traité</option> --}}
                </select>
            </div>
            <div class="filter-group">
                <label for="mois">Mois</label>
                <input type="month" name="mois" id="mois" value="{{ $mois }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    {{-- ═══ Barre d'export PDF ═══ --}}
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
        <div class="export-info">
            <span id="export-count-badge" class="export-count">0 sélectionné(s)</span>
            <span class="export-hint">Cochez des lignes puis générez le document PDF.</span>
        </div>
        <button type="button" id="btn-export" class="btn-export" onclick="openExportModal()" disabled>
            <i class="fa-solid fa-file-pdf"></i> Télécharger en PDF
        </button>
    </div>

    {{-- ═══ Tableau ═══ --}}
    <div class="chart-card" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:2px solid var(--border,#e8efe9);">
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600; width:36px;">
                        <input type="checkbox" id="selall" class="export-check" onclick="toggleAll(this)" title="Tout sélectionner">
                    </th>
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600; width:40px;">#</th>
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600;">Auteur</th>
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600;">Type</th>
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600;">Message</th>
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600;">Statut</th>
                    <th style="text-align:left; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600;">Date</th>
                    <th style="text-align:right; padding:10px 12px; color:var(--text-dim,#8aaa95); font-weight:600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commentaires as $c)
                <tr style="border-bottom:1px solid var(--border,#e8efe9); {{ $c->statut === 'en_attente' ? 'background:rgba(192,123,40,0.04);' : '' }}">
                    <td style="padding:10px 12px;">
                        <input type="checkbox" name="ids[]" value="{{ $c->id }}" form="export-form" class="export-check row-check" onchange="updateExportBar()" title="Sélectionner pour export">
                    </td>
                    <td style="padding:10px 12px; color:var(--text-dim,#8aaa95); font-size:11px;">{{ $c->id }}</td>
                    <td style="padding:10px 12px;">
                        <div style="font-weight:600; color:var(--text,#1a2d22);">{{ $c->nom ?: 'Anonyme' }}</div>
                        <div style="font-size:11px; color:var(--text-dim,#8aaa95);">{{ $c->email }}</div>
                    </td>
                    <td style="padding:10px 12px;">
                        @if($c->type === 'plainte')
                            <span style="background:#c4403022; color:#c44030; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">🔴 Plainte</span>
                        @elseif($c->type === 'suggestion')
                            <span style="background:#267a4722; color:#267a47; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">🟢 Suggestion</span>
                        @else
                            <span style="background:#8aaa9522; color:#8aaa95; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">💬 Commentaire</span>
                        @endif
                    </td>
                    <td style="padding:10px 12px; max-width:250px;">
                        <span style="color:var(--text,#1a2d22); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block;">{{ Str::limit($c->message, 60) }}</span>
                    </td>
                    <td style="padding:10px 12px;">
                        @if($c->statut === 'en_attente')
                            <span style="background:#c07b2822; color:#c07b28; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">⏳ En attente</span>
                        @elseif($c->statut === 'lu')
                            <span style="background:#2e7fbb22; color:#2e7fbb; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">👁️ Lu</span>
                        @else
                            {{-- <span style="background:#267a4722; color:#267a47; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">✅ Traité</span> --}}
                        @endif
                    </td>
                    <td style="padding:10px 12px; color:var(--text-dim,#8aaa95); font-size:11px; white-space:nowrap;">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding:10px 12px; text-align:right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end; flex-wrap:nowrap;">
                            {{-- Voir --}}
                            <button type="button" class="btn-action" title="Voir le détail"
                                onclick="voirCommentaire({{ $c->id }}, '{{ addslashes($c->nom ?: 'Anonyme') }}', '{{ addslashes($c->email) }}', '{{ addslashes($c->type) }}', '{{ addslashes($c->statut) }}', '{{ addslashes($c->message) }}', '{{ $c->created_at->format('d/m/Y H:i') }}')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            {{-- Marquer lu --}}
                            @if($c->statut === 'en_attente')
                            <form method="POST" action="{{ url('/Commentaires/' . $c->id . '/lu') }}" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-action btn-action-ok" title="Marquer lu">
                                    <i class="fa-solid fa-check" style="color:#267a47;"></i>
                                </button>
                            </form>
                            @endif
                            {{-- Supprimer --}}
                            <form method="POST" action="{{ url('/Commentaires/' . $c->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Supprimer ce commentaire ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-danger" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:30px; color:var(--text-dim,#8aaa95);">
                        <i class="fa-solid fa-comments" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                        Aucun commentaire indépendant trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ═══ Formulaire d'export PDF (les cases cochées y sont rattachées via form="export-form") ═══ --}}
    <form id="export-form" method="POST" action="{{ url('/Commentaires/export-pdf') }}">
        @csrf
        <input type="hidden" name="destinataire" id="export-destinataire" value="">
    </form>

    {{-- ═══ Modal destinataire ═══ --}}
    <div class="modal-overlay" id="modal-export">
        <div class="modal-card" style="max-width:460px;">
            <div class="modal-card-header">
                <h3>Exporter en PDF</h3>
                <button class="modal-close" onclick="document.getElementById('modal-export').classList.remove('active')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-card-body">
                <p style="font-size:13px; color:var(--text-dim,#8aaa95); margin-bottom:16px;" id="export-modal-count">
                    Le document PDF sera généré à partir des éléments sélectionnés.
                </p>
                <div class="field-group">
                    <label for="export-dest-name">Destinataire <span style="font-weight:400; font-size:11px;">(mairie, autorité, personne concernée…)</span></label>
                    <input type="text" id="export-dest-name" name="dest" placeholder="Ex. : Mairie de Kaolack, service technique…"
                           style="width:100%; padding:9px 12px; font-size:13px; font-family:'DM Sans',sans-serif; border:1px solid var(--border,#e8efe9); border-radius:8px; outline:none; background:var(--surface2,#f4f7f5); color:var(--text,#1a2d22);">
                </div>
                <div style="display:flex; gap:8px; margin-top:18px; justify-content:flex-end;">
                    <button class="btn-cancel" onclick="document.getElementById('modal-export').classList.remove('active')">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </button>
                    <button class="btn-primary" onclick="submitExport()" style="display:inline-flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-file-pdf"></i> Générer le PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Pagination ═══ --}}
    @if($commentaires->hasPages())
    <div style="margin-top:16px; text-align:center;">
        {{ $commentaires->withQueryString()->links() }}
    </div>
    @endif

</div>

{{-- ═══ Modal détail commentaire ═══ --}}
<div class="modal-overlay" id="modal-commentaire">
    <div class="modal-card" style="max-width:560px;">
        <div class="modal-card-header">
            <h3>Détail du commentaire</h3>
            <button class="modal-close" onclick="document.getElementById('modal-commentaire').classList.remove('active')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                <div class="field-group">
                    <label>Auteur</label>
                    <div id="vc-nom" style="font-size:13px; font-weight:600; color:var(--text,#1a2d22);">—</div>
                </div>
                <div class="field-group">
                    <label>Email</label>
                    <div id="vc-email" style="font-size:13px; color:var(--text,#1a2d22);">—</div>
                </div>
                <div class="field-group">
                    <label>Type</label>
                    <div id="vc-type">—</div>
                </div>
                <div class="field-group">
                    <label>Statut</label>
                    <div id="vc-statut">—</div>
                </div>
                <div class="field-group">
                    <label>Date</label>
                    <div id="vc-date" style="font-size:13px; color:var(--text,#1a2d22);">—</div>
                </div>
            </div>
            <div class="field-group">
                <label>Message</label>
                <div id="vc-message" style="font-size:13px; color:var(--text,#1a2d22); line-height:1.6; padding:12px; background:var(--surface2,#f4f7f5); border-radius:8px; white-space:pre-wrap;">—</div>
            </div>
            <div style="display:flex; gap:8px; margin-top:16px; justify-content:flex-end;">
                <button class="btn-cancel" onclick="document.getElementById('modal-commentaire').classList.remove('active')">
                    <i class="fa-solid fa-xmark"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-action {
        display:inline-flex; align-items:center; justify-content:center;
        width:30px; height:30px; border-radius:8px; border:1px solid var(--border,#e8efe9);
        background:var(--surface2,#f4f7f5); color:var(--text-dim,#8aaa95);
        cursor:pointer; transition:.15s; font-size:12px;
    }
    .btn-action:hover { border-color:var(--primary,#267a47); color:var(--primary,#267a47); background:#fff; }
    .btn-action-ok { border-color:#267a4740; background:#267a4711; }
    .btn-action-danger:hover { border-color:#c44030; color:#c44030; }
    .stats-filters { margin-bottom:16px; }
    .stats-filters-inner { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; justify-content:center; max-width:620px; margin:0 auto; padding:10px 14px; background:var(--surface,#fff); border:1px solid var(--border,#e8efe9); border-radius:12px; box-shadow:0 1px 4px rgba(23,45,33,.05); }
    .filter-group { display:flex; flex-direction:column; gap:4px; min-width:0; flex:1 1 140px; }
    .filter-group label { font-size:10px; font-weight:600; color:var(--text-dim,#8aaa95); text-transform:uppercase; letter-spacing:.3px; }
    .filter-group select { padding:7px 10px; font-size:12px; font-family:'DM Sans',sans-serif; border:1px solid var(--border,#e8efe9); border-radius:8px; background:var(--surface2,#f4f7f5); color:var(--text,#1a2d22); outline:none; width:100%; }
    .filter-group select:focus { border-color:var(--primary,#267a47); box-shadow:0 0 0 3px rgba(38,122,71,.12); }
    .filter-group input[type="month"] { padding:7px 10px; font-size:12px; font-family:'DM Sans',sans-serif; border:1px solid var(--border,#e8efe9); border-radius:8px; background:var(--surface2,#f4f7f5); color:var(--text,#1a2d22); outline:none; width:100%; box-sizing:border-box; }
    .filter-group input[type="month"]:focus { border-color:var(--primary,#267a47); box-shadow:0 0 0 3px rgba(38,122,71,.12); }
    .export-info { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .export-count { background:#267a4711; color:var(--primary,#267a47); font-size:12px; font-weight:700; padding:5px 12px; border-radius:20px; border:1px solid #267a4740; }
    .export-hint { font-size:12px; color:var(--text-dim,#8aaa95); }
    .btn-export { display:inline-flex; align-items:center; gap:8px; padding:9px 16px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; color:#fff; background:var(--primary,#267a47); border:none; border-radius:10px; cursor:pointer; transition:.15s; box-shadow:0 2px 8px rgba(38,122,71,.2); }
    .btn-export:hover:not(:disabled) { background:#1d5f39; }
    .btn-export:disabled { opacity:.4; cursor:not-allowed; box-shadow:none; }
    .export-check { width:16px; height:16px; accent-color:var(--primary,#267a47); cursor:pointer; }
</style>

<script>
function voirCommentaire(id, nom, email, type, statut, message, date) {
    document.getElementById('vc-nom').textContent = nom;
    document.getElementById('vc-email').textContent = email;
    document.getElementById('vc-message').textContent = message;
    document.getElementById('vc-date').textContent = date;

    var typeBadge = {
        plainte: '<span style="background:#c4403022; color:#c44030; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">🔴 Plainte</span>',
        suggestion: '<span style="background:#267a4722; color:#267a47; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">🟢 Suggestion</span>',
        commentaire: '<span style="background:#8aaa9522; color:#8aaa95; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">💬 Commentaire</span>'
    };
    document.getElementById('vc-type').innerHTML = typeBadge[type] || typeBadge.commentaire;

    var statutBadge = {
        en_attente: '<span style="background:#c07b2822; color:#c07b28; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">⏳ En attente</span>',
        lu: '<span style="background:#2e7fbb22; color:#2e7fbb; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">👁️ Lu</span>',
        traite: '<span style="background:#267a4722; color:#267a47; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;">✅ Traité</span>'
    };
    document.getElementById('vc-statut').innerHTML = statutBadge[statut] || statutBadge.en_attente;

    document.getElementById('modal-commentaire').classList.add('active');
}

// ── Export PDF ──
function updateExportBar() {
    var boxes = document.querySelectorAll('.export-check.row-check:checked');
    var count = boxes.length;
    var badge = document.getElementById('export-count-badge');
    var btn = document.getElementById('btn-export');
    if (badge) badge.textContent = count + ' sélectionné(s)';
    if (btn) btn.disabled = count === 0;
    var selall = document.getElementById('selall');
    if (selall) {
        var all = document.querySelectorAll('.export-check.row-check');
        selall.checked = all.length > 0 && count === all.length;
        selall.indeterminate = count > 0 && count < all.length;
    }
}

function toggleAll(el) {
    document.querySelectorAll('.export-check.row-check').forEach(function(cb) {
        cb.checked = el.checked;
    });
    updateExportBar();
}

function openExportModal() {
    var checked = document.querySelectorAll('.export-check.row-check:checked').length;
    document.getElementById('export-modal-count').textContent = checked + ' élément(s) sélectionné(s) seront exportés dans un document officiel.';
    document.getElementById('export-dest-name').value = '';
    document.getElementById('modal-export').classList.add('active');
}

function submitExport() {
    document.getElementById('export-destinataire').value = document.getElementById('export-dest-name').value.trim();
    document.getElementById('export-form').submit();
}

document.addEventListener('DOMContentLoaded', updateExportBar);
</script>

@endsection
