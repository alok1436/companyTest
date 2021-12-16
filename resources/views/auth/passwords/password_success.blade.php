<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Confirm your email</title>
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
<!--div class="home-btn d-none d-sm-block">
  <a href="index.html"><i class="fas fa-home h2 text-dark"></i></a>
</div-->
<div class="account-pages mt-5 mb-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-success">{{ __('Password changed') }}</div>

                <div class="card-body">
                    <div class="col-md-12">
                              <div class="alert alert-success" role="alert">
                             Thank you, your password has been changes successfully.
                        </div>
                    </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    {{ __('Back to login') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
    <!-- end page -->

    <!-- Vendor js -->
    <script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.min.js') }}"></script>
  </body>
</html>
 