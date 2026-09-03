@extends('AppAdmi')
@section('content')
<div style="display:flex; height:calc(100vh - var(--hdr)); padding-top:var(--hdr);">

    {{-- ═══ SIDEBAR GAUCHE ═══ --}}
    <aside class="app-sidebar" style="background:var(--white); border-right:1px solid var(--border); padding:20px 16px; display:flex; flex-direction:column; gap:6px; overflow-y:auto;">
        <div class="sidebar-title" style="font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:var(--text); padding:0 8px 12px; border-bottom:1px solid var(--border);">
            <span><i class="fa-solid fa-users" style="margin-right:8px; color:var(--primary);"></i> Utilisateurs</span>
        </div>
        <div class="sidebar-nav-label" style="font-size:11px; color:var(--text-muted); padding:10px 8px 4px; text-transform:uppercase; letter-spacing:.5px; font-weight:600;">
            Navigation
        </div>
        <button type="button" class="onglet-btn sidebar-item active" data-onglet="comptes" title="Comptes" style="text-align:left; padding:12px; border:none; border-radius:9px; background:var(--primary); color:#fff; font-size:13px; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; gap:9px;">
            <i class="fa-solid fa-address-book" style="width:16px; text-align:center;"></i>
            <span class="sidebar-label">Comptes</span>
            <span class="sidebar-onglet-badge" style="margin-left:auto; background:rgba(255,255,255,.25); border-radius:20px; padding:1px 8px; font-size:11px;">{{ $utilisateurs->count() }}</span>
        </button>
        <button type="button" class="onglet-btn sidebar-item" data-onglet="commentateurs" title="Commentateurs" style="text-align:left; padding:12px; border:none; border-radius:9px; background:var(--surface2); color:var(--text-dim); font-size:13px; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; gap:9px;">
            <i class="fa-solid fa-comment-dots" style="width:16px; text-align:center;"></i>
            <span class="sidebar-label">Commentateurs</span>
            <span class="sidebar-onglet-badge" style="margin-left:auto; background:var(--border); border-radius:20px; padding:1px 8px; font-size:11px;">{{ $commentateurs->count() }}</span>
        </button>

        <div style="flex:1;"></div>
        <div class="sidebar-user" style="font-size:11px; color:var(--text-muted); padding:10px 8px 0; border-top:1px solid var(--border);">
            @if($connecte)
                Connecté : <strong style="color:var(--text);">{{ $connecte->nomComplet() }}</strong>
                <br><span style="display:inline-block; margin-top:4px; padding:2px 8px; border-radius:6px; background:{{ $connecte->isSuperAdmin() ? '#eef4ff' : '#eefaf1' }}; color:{{ $connecte->isSuperAdmin() ? '#1d4ed8' : '#1d8a4e' }}; font-size:11px; font-weight:600;">{{ $connecte->libelleRole() }}</span>
            @else
                Non connecté (lecture seule)
            @endif
        </div>
    </aside>

    {{-- ═══ ZONE PRINCIPALE ═══ --}}
    <main style="flex:1; overflow-y:auto; background:var(--bg);">
        <div style="width:100%; max-width:1100px; margin:0 auto; padding:28px 36px;">

        {{-- Bandeau d'erreurs --}}
        @if($errors->any() || session('error'))
            <div style="background:#fdecea; border:1px solid #f5c6c2; border-radius:10px; padding:14px 18px; margin-bottom:20px;">
                <ul style="margin:0; padding-left:18px; color:#b3261e; font-size:13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @if(session('error'))<li>{{ session('error') }}</li>@endif
                </ul>
            </div>
        @endif

        {{-- ════ ONGLET 1 : COMPTES ════ --}}
        <section id="onglet-comptes" class="onglet-panel">
            {{-- En-tête + bouton ajouter --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                   <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Gestion des utilisateurs</div>
                    <div class="page-sub" style="margin:4px 0 0;">Comptes administrateurs et super administrateurs</div>
                </div>
                <a href="{{ route('Register') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 22px; background:var(--primary); color:#fff; border-radius:10px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none; transition:.15s;">
                    <i class="fa-solid fa-plus"></i>
                    Ajouter un utilisateur
                </a>
            </div>

            {{-- Recherche + filtres (compact centré) --}}
            <form method="GET" action="{{ route('UtilisateursAdmi') }}" style="display:flex; gap:10px; align-items:center; background:var(--white); border:1px solid var(--border); border-radius:12px; padding:10px 16px; margin:0 auto 20px; max-width:760px; flex-wrap:wrap;">
                <input type="text" name="q" value="{{ $recherche }}" placeholder="Rechercher nom, prénom ou email..."
                    style="flex:1; min-width:180px; padding:8px 11px; font-size:13px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); outline:none; font-family:'DM Sans',sans-serif;">
                <select name="role" style="padding:8px 11px; font-size:13px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); outline:none; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <option value="">Tous les rôles</option>
                    <option value="ADMINISTRATEUR" {{ $roleFiltre === 'ADMINISTRATEUR' ? 'selected' : '' }}>Administrateur</option>
                    <option value="SUPER_ADMINISTRATEUR" {{ $roleFiltre === 'SUPER_ADMINISTRATEUR' ? 'selected' : '' }}>Super Administrateur</option>
                </select>
                <select name="statut" style="padding:8px 11px; font-size:13px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); outline:none; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ $statutFiltre === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="bloque" {{ $statutFiltre === 'bloque' ? 'selected' : '' }}>Bloqué</option>
                </select>
                <button type="submit" style="padding:8px 16px; border:none; border-radius:8px; background:var(--text); color:#fff; font-size:13px; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif;">
                    <i class="fa-solid fa-filter"></i> Filtrer
                </button>
                @if($recherche || $roleFiltre || $statutFiltre)
                    <a href="{{ route('UtilisateursAdmi') }}" style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; color:var(--text-dim); font-size:12px; text-decoration:none; display:inline-flex; gap:5px; align-items:center;">
                        <i class="fa-solid fa-xmark"></i> Effacer
                    </a>
                @endif
            </form>

            {{-- Tableau comptes --}}
            <div style="background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="background:var(--surface2); text-align:left;">
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Utilisateur</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Email</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Téléphone</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Rôle</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Statut</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utilisateurs as $u)
                            @php
                                $isSelf = $connecte && $connecte->id === $u->id;
                                $peutModifier = $connecte && ($connecte->isSuperAdmin() || !$u->isSuperAdmin());
                                $peutBloquer = $connecte && $connecte->isSuperAdmin() && !$isSelf;
                                $dataEdit = json_encode(['prenom' => $u->prenom, 'nom' => $u->nom, 'email' => $u->email, 'telephone' => $u->telephone, 'cni' => $u->cni], JSON_UNESCAPED_UNICODE);
                            @endphp
                            <tr style="border-bottom:1px solid var(--border2);" data-user="{{ $u->id }}" data-edit="{{ $dataEdit }}">
                                <td style="padding:12px 16px;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:#fff; background:{{ $u->isSuperAdmin() ? '#1d4ed8' : '#2d9b5f' }};">
                                            {{ strtoupper(substr($u->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($u->nom ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:var(--text);">{{ $u->nomComplet() }}</div>
                                            @if($isSelf)<div style="font-size:11px; color:var(--primary); font-weight:600;">(vous)</div>@endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:12px 16px; color:var(--text-dim);">{{ $u->email }}</td>
                                <td style="padding:12px 16px; color:var(--text-dim);">{{ $u->telephone }}</td>
                                <td style="padding:12px 16px;">
                                    <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:{{ $u->isSuperAdmin() ? '#eef4ff' : '#eefaf1' }}; color:{{ $u->isSuperAdmin() ? '#1d4ed8' : '#1d8a4e' }};">
                                        {{ $u->libelleRole() }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;">
                                    <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:{{ $u->isActif() ? '#eefaf1' : '#fdecec' }}; color:{{ $u->isActif() ? '#1d8a4e' : '#b3261e' }};">
                                        {{ $u->isActif() ? 'Actif' : 'Bloqué' }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;">
                                    <div style="display:flex; gap:6px;">
                                        @if($peutModifier)
                                            <button type="button" onclick="openModifier({{ $u->id }})" title="Modifier"
                                                style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:13px;">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                        @else
                                            <span title="Vous n'avez pas le droit de modifier ce compte" style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text-muted); opacity:.5; display:inline-flex; align-items:center; justify-content:center; font-size:13px; cursor:not-allowed;">
                                                <i class="fa-solid fa-pen"></i>
                                            </span>
                                        @endif

                                        @if($peutBloquer)
                                            <button type="button"
                                                onclick="confirmerStatut({{ $u->id }}, '{{ $u->isActif() ? "bloquer" : "debloquer" }}', '{{ addslashes($u->nomComplet()) }}')"
                                                title="{{ $u->isActif() ? 'Bloquer' : 'Débloquer' }}"
                                                style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:13px; color:{{ $u->isActif() ? 'var(--red)' : 'var(--primary)' }};">
                                                <i class="fa-solid {{ $u->isActif() ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        @else
                                            <span title="Action réservée au Super Administrateur" style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text-muted); opacity:.5; display:inline-flex; align-items:center; justify-content:center; font-size:13px; cursor:not-allowed;">
                                                <i class="fa-solid {{ $u->isActif() ? 'fa-ban' : 'fa-check' }}"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:32px; text-align:center; color:var(--text-muted); font-size:13px;">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="font-size:11px; color:var(--text-muted); margin-top:10px;">{{ $utilisateurs->count() }} compte(s) affiché(s)</div>
        </section>

        {{-- ════ ONGLET 2 : COMMENTATEURS ════ --}}
        <section id="onglet-commentateurs" class="onglet-panel" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                     <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Commentateurs</div>
                    <div class="page-sub" style="margin:4px 0 0;">Utilisateurs ayant commenté les actualités</div>
                </div>
            </div>

            <div style="background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="background:var(--surface2); text-align:left;">
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Nom</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Email</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Commentaires</th>
                            <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Dernière activité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commentateurs as $c)
                            <tr style="border-bottom:1px solid var(--border2);">
                                <td style="padding:12px 16px; font-weight:600; color:var(--text);">{{ $c->nom }}</td>
                                <td style="padding:12px 16px; color:var(--text-dim);">{{ $c->email }}</td>
                                <td style="padding:12px 16px;">
                                    <span style="display:inline-block; min-width:26px; text-align:center; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600; background:var(--surface2); color:var(--text);">{{ $c->total_commentaires }}</span>
                                </td>
                                <td style="padding:12px 16px; color:var(--text-dim);">{{ $c->derniere_date ? \Carbon\Carbon::parse($c->derniere_date)->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:32px; text-align:center; color:var(--text-muted); font-size:13px;">
                                    Aucun commentaire pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        </div>
    </main>
</div>

{{-- ════════════════════════════════════════ MODAL MODIFIER ════════════════════════════════════════ --}}
<div id="modal-modifier" class="modal-overlay">
    <div class="modal-card" style="max-width:560px;">
        <div class="modal-card-header">
            <h3>Modifier l'utilisateur</h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-modifier')">&times;</button>
        </div>
        <div class="modal-card-body">
            <form id="edit-user-form" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="field-row">
                    <div class="field-group">
                        <label>Prénom</label>
                        <input type="text" id="ed-prenom" name="prenom" required>
                    </div>
                    <div class="field-group">
                        <label>Nom</label>
                        <input type="text" id="ed-nom" name="nom" required>
                    </div>
                </div>
                <div class="field-group">
                    <label>Email</label>
                    <input type="email" id="ed-email" name="email" required>
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label>Téléphone</label>
                        <input type="text" id="ed-telephone" name="telephone" required>
                    </div>
                    <div class="field-group">
                        <label>CNI</label>
                        <input type="text" id="ed-cni" name="cni">
                    </div>
                </div>
                <div class="field-group">
                    <label>Mot de passe <span style="font-weight:400; color:var(--text-muted);">(laisser vide pour ne pas le changer)</span></label>
                    <input type="password" id="ed-password" name="password" placeholder="••••••••">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:6px;">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-modifier')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════ CONFIRMATION STATUT ════════════════════════════════════════ --}}
<div id="modal-statut" class="modal-overlay">
    <div class="modal-card" style="max-width:440px;">
        <div class="modal-card-header">
            <h3 id="statut-titre">Confirmation</h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-statut')">&times;</button>
        </div>
        <div class="modal-card-body">
            <p id="statut-message" style="font-size:13px; color:var(--text); margin:0 0 20px;"></p>
            <form id="statut-form" method="POST" action="">
                @csrf
                @method('PUT')
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-statut')">Annuler</button>
                    <button type="submit" id="statut-confirmer" class="btn-submit"><i class="fa-solid fa-ban"></i> Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ POPUP SUCCÈS ═══ --}}
<div id="successPopup" class="success-popup">
    <div class="success-card">
        <div class="success-icon"><i class="fa-solid fa-check"></i></div>
        <h4>Succès</h4>
        <p id="successMessage">{{ session('success', '') }}</p>
        <button class="btn-submit" onclick="document.getElementById('successPopup').classList.remove('active')">Fermer</button>
    </div>
</div>

{{-- ═══ SCRIPT ═══ --}}
<script>
    // ── Onglets ──
    document.querySelectorAll('.onglet-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const nom = btn.getAttribute('data-onglet');
            document.querySelectorAll('.onglet-btn').forEach(function(b) {
                b.style.background = 'var(--surface2)';
                b.style.color = 'var(--text-dim)';
                b.classList.remove('active');
            });
            btn.style.background = 'var(--primary)';
            btn.style.color = '#fff';
            btn.classList.add('active');
            document.getElementById('onglet-comptes').style.display = (nom === 'comptes') ? '' : 'none';
            document.getElementById('onglet-commentateurs').style.display = (nom === 'commentateurs') ? '' : 'none';
        });
    });

    function escHtml(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    function openFormModal(id) { document.getElementById(id).classList.add('active'); }

    // ── Modifier (les données sont injectées dans des data-attributes) ──
    function openModifier(id) {
        const row = document.querySelector('#onglet-comptes tr[data-user="' + id + '"]');
        if (!row) return;
        const data = JSON.parse(row.getAttribute('data-edit').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, '&'));
        document.getElementById('ed-prenom').value = data.prenom;
        document.getElementById('ed-nom').value = data.nom;
        document.getElementById('ed-email').value = data.email;
        document.getElementById('ed-telephone').value = data.telephone;
        document.getElementById('ed-cni').value = data.cni || '';
        document.getElementById('ed-password').value = '';
        document.getElementById('edit-user-form').action = '/Utilisateurs/' + id;
        openFormModal('modal-modifier');
    }

    // ── Confirmation bloquer / débloquer ──
    function confirmerStatut(id, action, nom) {
        const bloquer = action === 'bloquer';
        document.getElementById('statut-titre').textContent = bloquer ? 'Bloquer l\'utilisateur' : 'Débloquer l\'utilisateur';
        document.getElementById('statut-message').textContent = bloquer
            ? 'Voulez-vous vraiment bloquer le compte de « ' + nom + ' » ? Il ne pourra plus se connecter.'
            : 'Voulez-vous réactiver le compte de « ' + nom + ' » ?';
        const btn = document.getElementById('statut-confirmer');
        btn.innerHTML = bloquer ? '<i class="fa-solid fa-ban"></i> Bloquer' : '<i class="fa-solid fa-check"></i> Débloquer';
        document.getElementById('statut-form').action = '/Utilisateurs/' + id + '/statut';
        openFormModal('modal-statut');
    }

    // ── Popup succès ──
    @if(session('success'))
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('successMessage').textContent = {!! json_encode(session('success')) !!};
            document.getElementById('successPopup').classList.add('active');
        });
    @endif
</script>
@endsection
