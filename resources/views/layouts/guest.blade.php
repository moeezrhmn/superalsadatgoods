<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> sadaatgoods | {{Route::currentRouteName()}} </title>

    <script src="assets/js/libs/jquery-3.1.1.min.js"></script>

    <!-- <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" /> -->
    <link href="assets/css/loader.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/loader.js"></script>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/plugins.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components/custom-modal.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->


</head>

<body>

    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="px-3 pt-2 fixed-top">
        <header class="header navbar navbar-expand-sm expand-header">
            <a href="/" class="sidebarCollapse" data-placement="bottom">
                Super Al Sadat Goods
            </a>

        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN CONTENT AREA  -->
    <div  class=" d-flex justify-content-center align-items-center min-vh-100 min-vw-100 main-content ">
            @yield('content')
    </div>
    <!--  END CONTENT AREA  -->


    <script src="bootstrap/js/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
         App.init();
     });
    </script>
    <script src="assets/js/custom.js"></script>
    <script src="plugins/highlight/highlight.pack.js"></script>

</body>

</html>