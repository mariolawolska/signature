@if($error == 'error')
<div class="text-danger" style="padding: 15px" role="alert">
    This is a danger alert—check it out! {{ $error }}
</div>
@endif