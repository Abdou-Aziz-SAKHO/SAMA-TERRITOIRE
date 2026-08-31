@extends('AppAdmi')
@section('content')
<div class="page" style="display:block; padding-top:var(--hdr); min-height:100vh;">
    <div style="max-width:700px; margin:40px auto; padding:0 20px;">
        <div class="page-title">Créer un utilisateur</div>
        <div class="page-sub">Remplissez les informations ci-dessous pour ajouter un nouvel utilisateur</div>

        @if ($errors->any())
            <div style="background:var(--red-lt); border:1px solid var(--red); border-radius:10px; padding:14px 18px; margin-bottom:20px;">
                <ul style="margin:0; padding-left:18px; color:var(--red); font-size:13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background:var(--white); border-radius:14px; box-shadow:var(--card-shadow); padding:30px;">
            <form action="{{ route('Register.post') }}" method="POST">
                @csrf

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required
                            style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                    <div>
                        <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required
                            style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                        onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" required
                            style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                    <div>
                        <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">CNI</label>
                        <input type="text" name="cni" value="{{ old('cni') }}"
                            style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Mot de passe</label>
                        <input type="password" name="password" required
                            style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                    <div>
                        <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" required
                            style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:15px; font-weight:600; color:var(--text-dim); margin-bottom:6px;">Rôle</label>
                    <select name="role" required
                        style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text); background:var(--surface2); outline:none; transition:.2s;"
                        onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                        {{-- Option Administrateur : visible par tous --}}
                        <option value="ADMINISTRATEUR" {{ old('role') == 'ADMINISTRATEUR' ? 'selected' : '' }}>Administrateur</option>
                        {{-- Option Super Administrateur : visible UNIQUEMENT si c'est un Super Admin qui est connecté --}}
                        @if (auth()->check() && auth()->user()->isSuperAdmin())
                            <option value="SUPER_ADMINISTRATEUR" {{ old('role') == 'SUPER_ADMINISTRATEUR' ? 'selected' : '' }}>Super Administrateur</option>
                        @endif
                    </select>
                </div>

                <button type="submit"
                    style="width:100%; padding:12px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:.2s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Créer l'utilisateur
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
