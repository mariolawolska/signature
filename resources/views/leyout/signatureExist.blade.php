@php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
$domainName = $_SERVER['HTTP_HOST'] . '/';
@endphp
<div class="col-md-12 mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card_">
            <div class="card-body ">

                Your signature: 

                <img  width="130" height="90"class="img-fluid img-thumbnail signatureBox m-8" src="/storage/app/public/ratrak/{{Auth::user()->id}}/thumbnail/{{ $signature }}" alt="Signature Thumbnail">
                <div class="col-md-12" style="margin:20px 0 ;">
                    <a href="{{ route('toPdf') }}"role="button">
                        <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" style="font-size:16px;" type="submit">
                            Generate report
                        </button>
                    </a>
                </div>
                <div>
                    @if($comment!='You can not change you signature any more, please contact with admin')
                    <button  userId="{{Auth::user()->id}}"  class="btn btn-delete" id="redefineSignature">Re-defineSignature</button>

                    @endif
                    <p style="font-size:12px; color:red">{{$comment}}</p>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(document).on("click", "#redefineSignature", function () {
            var userId = $(this).attr('userId');
            redefineSignature(userId);
        });
    });


    /**
     * Load Comment Table END
     */


    function redefineSignature(userId) {

        if (confirm('Are you sure you want to redefine signature?')) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "PUT",
                url: "/public/redefineSignature",
                data: {
                    'userId': userId
                },
                beforeSend: function () {
//                    $('#loaderBox').modal('show');
                },
                success: function (data) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
    }

</script>