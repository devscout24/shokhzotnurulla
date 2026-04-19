<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Validation / Error Bag
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error("{{ $error }}", "Error");
        @endforeach
    @endif
    // 2. Session-based Errors
    @if(session('error'))
        toastr.error("{{ session('error') }}", "Error");
    @endif
    @if(session('auth_error'))
        toastr.error("{{ session('auth_error') }}", "Authentication Failed");
    @endif
    // 3. Success Messages
    @if(session('success'))
        toastr.success("{{ session('success') }}", "Success");
    @endif
    // 4. Info Messages
    @if(session('info'))
        toastr.info("{{ session('info') }}", "Info");
    @endif
    // 5. Warning Messages
    @if(session('warning'))
        toastr.warning("{{ session('warning') }}", "Warning");
    @endif

    // 6. Resent Verification Email
    @if(session('resent'))
        toastr.success("{{ __('A fresh verification link has been sent to your email address.') }}", "Email Sent");
    @endif

    // 7. Resent Verification Email
    @if(session('status'))
        toastr.success("{{ session('status') }}", "Email Sent");
    @endif
});
</script>