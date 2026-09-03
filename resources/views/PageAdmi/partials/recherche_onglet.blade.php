{{-- Champ de recherche sur le tableau de l'onglet actif (page Données) --}}
<form method="GET" action="{{ route('DonneesAdmi') }}" style="position:relative; flex:0 0 auto;">
    <input type="hidden" name="tab" value="{{ $tab }}">
    @if(!empty($regionId)) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
    @if(!empty($departementId)) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
    @if(!empty($communeId)) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
    @if(!empty($secteurId)) <input type="hidden" name="secteur_id" value="{{ $secteurId }}"> @endif
    @if(!empty($localiteId)) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
    @if(!empty($docType)) <input type="hidden" name="type_document" value="{{ $docType }}"> @endif
    @if(!empty($infrastructureId)) <input type="hidden" name="infrastructure_id" value="{{ $infrastructureId }}"> @endif
    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Rechercher {{ $placeholder ?? '...' }}..."
        style="padding:8px 30px 8px 32px; font-size:13px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); outline:none; width:220px; font-family:'DM Sans',sans-serif; transition:border-color .15s, box-shadow .15s;"
        onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,.12)';"
        onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
    @if(!empty($q))
        <a href="{{ route('DonneesAdmi', array_filter(['tab' => $tab, 'region_id' => $regionId ?? null, 'departement_id' => $departementId ?? null, 'commune_id' => $communeId ?? null, 'secteur_id' => $secteurId ?? null, 'localite_id' => $localiteId ?? null, 'type_document' => $docType ?? null, 'infrastructure_id' => $infrastructureId ?? null])) }}"
           title="Effacer la recherche"
           style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px; text-decoration:none;"><i class="fa-solid fa-xmark"></i></a>
    @endif
</form>
