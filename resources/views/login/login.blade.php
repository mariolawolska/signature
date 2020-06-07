<!DOCTYPE html>
<html>
    @include('layout.head_login')
    
    <body>
        <div class="container-fluid bg">
            <div class="row no-gutter">
                <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
                <div class="col-md-8 col-lg-6">
                    <div class="login d-flex align-items-center py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-9 col-lg-8 mx-auto">
                                    <h3 class="login-heading mb-4">Welcome</h3>
                                    <form action="{{url('post-login')}}" method="POST" id="logForm">

                                        {{ csrf_field() }}

                                        <div class="form-label-group">
                                            <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" >
                                            <label for="inputEmail">Email address</label>

                                            @if ($errors->has('email'))
                                            <span class="error">{{ $errors->first('email') }}</span>
                                            @endif    
                                        </div> 

                                        <div class="form-label-group">
                                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password">
                                            <label for="inputPassword">Password</label>

                                            @if ($errors->has('password'))
                                            <span class="error">{{ $errors->first('password') }}</span>
                                            @endif  
                                        </div>

                                        @if (\Session::has('success'))
                                        <div class="alert alert-success">
                                            <ul>
                                                <li>{!! \Session::get('success') !!}</li> 		
                                            </ul>
                                        </div>
                                        @endif

                                        <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Sign In</button>
                                        <div class="text-center">If you have an account?
                                            <a class="small" href="{{url('registration')}}">Sign Up</a></div>
                                            
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>