@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($errors->all() as $error)
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: "{{ $error }}",
            });
        @endforeach
    });
</script>
@endif