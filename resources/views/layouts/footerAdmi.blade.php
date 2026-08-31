<footer style="background:#1a2d22; color:rgba(255,255,255,0.7); font-family:'DM Sans',sans-serif; margin-top:auto;">
    <div style="max-width:1100px; margin:0 auto; padding:32px 24px 20px;">

        <div style="display:grid; grid-template-columns:1.2fr 1fr 1fr; gap:32px; margin-bottom:24px;">

            {{-- Logo + Projet --}}
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                    <img src="{{ asset('assets/img/TERRITOIRE.png') }}" alt="Logo" style="height:28px; ">
                    {{-- <span style="font-family:'Syne',sans-serif; font-weight:700; font-size:14px; color:#fff;">SAMA TERRITOIRE</span> --}}
                </div>
                <p style="font-size:12px; line-height:1.6; color:rgba(255,255,255,0.5); margin:0;">
                    Plateforme de gestion territoriale — données spatiales, cartographie et statistiques pour le développement local.
                </p>
            </div>

            {{-- Liens rapides --}}
            <div>
                <div style="font-size:10px; letter-spacing:0.12em; text-transform:uppercase; color:#8FCBA5; margin-bottom:12px; font-weight:600;">Navigation</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <a href="{{ route('Dashboard') }}" style="font-size:12px; color:rgba(255,255,255,0.6); text-decoration:none; transition:color .2s;">Dashboard</a>
                    <a href="#" style="font-size:12px; color:rgba(255,255,255,0.6); text-decoration:none; transition:color .2s;">Cartographie</a>
                    <a href="#" style="font-size:12px; color:rgba(255,255,255,0.6); text-decoration:none; transition:color .2s;">Statistiques</a>
                    <a href="#" style="font-size:12px; color:rgba(255,255,255,0.6); text-decoration:none; transition:color .2s;">Données</a>
                </div>
            </div>

            {{-- Contact ARD --}}
            <div>
                <div style="font-size:10px; letter-spacing:0.12em; text-transform:uppercase; color:#8FCBA5; margin-bottom:12px; font-weight:600;">Contact</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="display:flex; align-items:center; gap:8px; font-size:12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:#8FCBA5; flex-shrink:0;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Agence Régionale de Développement — Kaolack</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; font-size:12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:#8FCBA5; flex-shrink:0;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
                        <span>contact@ard-kaolack.sn</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; font-size:12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:#8FCBA5; flex-shrink:0;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>+221 33 941 XX XX</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bas --}}
        <div style="border-top:1px solid rgba(255,255,255,0.1); padding-top:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <span style="font-size:11px; color:rgba(255,255,255,0.35);">&copy; {{ date('Y') }} SAMA TERRITOIRE — Tous droits réservés</span>
            <span style="font-size:10px; color:rgba(255,255,255,0.3);">ARD Kaolack · Données terrain</span>
        </div>
    </div>
</footer>
