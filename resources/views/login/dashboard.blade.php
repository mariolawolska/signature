<!DOCTYPE html>
<html>
    <head>
        @include('layout.head')
    </head>
    <body style="background:{{ isset($settingsPdfReport['colorPage'])?$settingsPdfReport['colorPage'] : '#BC9B78' }} ">
        <div class="container-fluid">
            <div class="row no-gutter">
                <div class="d-none d-md-flex col-md-4 col-lg-6 " style=" background-size: 100% 100% ;background-image: url({{  $settingsPdfReport['background']?$settingsPdfReport['background']:'https://s3-us-west-2.amazonaws.com/s.cdpn.io/28963/form-bk.jpg' }}); height: 1000px;"></div>
                <div class="col-md-8 col-lg-6">
                    <div class="login d-flex align-items-center py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-10 mx-auto">

                                    <div class="mb-4" style="background:{{  $settingsPdfReport['colorPage']?$settingsPdfReport['colorPage']:'#BC9B78' }} ; padding: 15px;">
                                        <h3 class="login-heading" style="margin-bottom: 50px;">Welcome {{ ucfirst(Auth()->user()->name) }}!</h3>
                                        <div class="row ">

                                            @if($signature)
                                            @include('layout.signatureExist')
                                            @endif

                                            @if(!$signature)
                                            @include('layout.signatureNew')
                                            @endif
                                        </div>
                                        <div class="row pt-5">
                                            <div class="col-md-4">
                                                <a  href="{{url('logout')}}">Logout</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>