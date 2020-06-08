<!DOCTYPE html>

<html>
    @include('leyout.head')

    <body style="margin-top: 10px; background-image:url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/28963/form-bk.jpg'); background-size: 100%">
        <div class="container">
            <div class="row">
                <div class="col-12" style="padding-bottom: 40px">

                    @include('comment.table')

                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    @if(Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                        {{ Session::forget('success') }}
                    </div>
                    @endif

                    @include('comment.form')

                </div>
            </div>
        </div>

    </body>

</html>