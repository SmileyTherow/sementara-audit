@props(['text' => ''])
<div class="prose catalog-description">
    @foreach(preg_split('/\R\s*\R/u', trim($text ?? ''), -1, PREG_SPLIT_NO_EMPTY) as $paragraph)
        <p>{{ $paragraph }}</p>
    @endforeach
</div>
