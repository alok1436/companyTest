<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Vendor | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico')}}">

    <!-- App css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
  </head>
  <body class="authentication-bg">
    <!-- <div class="home-btn d-none d-sm-block">
      <a href="index.html"><i class="fas fa-home h2 text-dark"></i></a>
    </div> -->
    <div class="account-pages mt-5 mb-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            
            <div class="card customloginform">
              <div class="card-body p-4">
                <div class="text-center logosection">
                        <a href="index.html">
                          <span>
                            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="100">
                          </span>
                        </a>
                      </div>
                <div class="text-center mb-4">
                  <h4 class="text-uppercase mt-0">Vendor Login</h4>
                </div>
                                
                @if( session('success'))
                    <div class="alert alert-success pt10 pb10 mt10 mb10">
                        {{ session('success') }}
                    </div>
                @endif

                @if( session('error'))
                    <div class="alert alert-danger mt10 mb10  pt10 pb10">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                  @csrf
                  <div class="form-group mb-3">
                    <label for="emailaddress">Email address</label>
                    <input type="email" name="email" id="emailaddress" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email"/>
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter your password"/>
                    <input type="hidden" name="auth_role" value="{{ \Crypt::encryptString('vendor') }}">
                    @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group mb-3">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input checkboxblack" id="checkbox-signin" checked>
                      <label class="custom-control-label" for="checkbox-signin">Remember me</label>
                    </div>
                  </div>
                  <div class="form-group mb-0 text-center">
                    <input type="hidden" name="decice_token" id="decice_token">
                    <button class="btn btn-primary btn-block" type="submit"> Log In </button>
                  </div>
                </form>
                <!-- New registration start -->
                  <p class="mt-3 customloginbtn"><a href="{{ url('register') }}" class="text-muted1 coloblack ml-1"><i class="fa fa-lock mr-1"></i>New Registration</a></p>
                  <p class="mt-3 customloginbtn"><a href="{{ url('password/reset') }}" class="text-muted1 coloblack ml-1"><i class="fa fa-lock mr-1"></i>Reset password</a></p>
                <!-- New registration end -->
              </div> <!-- end card-body -->

            </div>
            <!-- end card -->

            <!-- <div class="row mt-3">
                <div class="col-12 text-center">
                  <p> <a href="#" class="text-muted ml-1"><i class="fa fa-lock mr-1"></i>New Registration</a></p>
          
                </div>
            </div> -->
            <!-- end row -->
          </div> <!-- end col -->
        </div>
        <!-- end row -->
      </div>
      <!-- end container -->
    </div>
    <!-- end page -->
   <style>
    body.authentication-bg {
        background-image: url({{url('backend/assets/images/big/loginbg2.jpg')}});
    }
    .logosection img {
      max-width: 88px;
      height: auto;
      margin-bottom: 20px;
    }
    .customloginform .form-group input.form-control {
        border: none;
        border-bottom: 1px solid #333;
        border-radius: 0px;
        padding-left: 0px;
        padding-top: 0px;
        padding-bottom: 3px;
        height: auto;
    }
    .customloginform .form-group input.form-control::placeholder { 
      color: #333;
      opacity: 1; 
    }

    .customloginform .form-group input.form-control:-ms-input-placeholder { 
      color: #333;
    }

    .customloginform .form-group input.form-control::-ms-input-placeholder { 
      color: #333;
    }

    .btn-primary {
      color: #fff;
      background-color: #333;
      border-color: #333;
  }
  .custom-control-input:checked~.custom-control-label::before {
      color: #333;
      border-color: #333;
      background-color: #333;
  }
  .coloblack{
    color: #333;
  }
  .btn-primary:hover {
      color: #fff;
      background-color: #6c6e6f;
      border-color: #6c6e6f;
  }
   </style>
    <!-- Vendor js -->
    <script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.min.js') }}"></script>
  </body>
</html>