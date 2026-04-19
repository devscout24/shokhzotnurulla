@foreach(($scripts['body_end'] ?? []) as $script)
    {!! $script->code !!}
@endforeach