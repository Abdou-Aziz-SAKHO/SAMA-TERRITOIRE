@extends('AppUser')

@section('content')
<div id="page-actualites" class="page active">

  <section class="actus-page">
    @include('PageUser.partials.actualites-mosaique', [
        'actualites'     => $actualites->items(),
        'actusTitre'     => 'Toutes les actualités',
        'actusSousTitre' => 'Retrouvez ici l\'ensemble des actualités publiées sur le territoire.',
        'actusPagination'=> $actualites,
    ])
  </section>

</div>

<style>
  .actus-page { padding: 28px 0 64px; }
  @media (max-width: 700px) {
    .actus-page { padding-top: 12px; }
  }
</style>
@endsection