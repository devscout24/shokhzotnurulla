@php $scripts = $scripts ?? collect(); @endphp

@foreach(($scripts['head'] ?? []) as $script)
    {!! $script->code !!}
@endforeach