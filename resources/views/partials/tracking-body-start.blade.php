@foreach(($scripts['body_start'] ?? []) as $script)
    {!! $script->code !!}
@endforeach