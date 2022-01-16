<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Business Vehicle Registration</title>
    <link href="Content/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="Content/Site.css" rel="stylesheet" type="text/css" />
    <link href="Content/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="Content/JqueryUi.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" /> 
</head>
<body>
    <nav class="navbar navbar-expand-lg  navbar-dark transparent-bg absolute">
        <a class="navbar-brand image-parent" href="#">
            <!--<img src="logo.png" /> -->
        </a>
        <?php if (strpos($_SERVER['REQUEST_URI'], "admin") !== false): ?>
            <div class="admin-nav-button">
                <a href="https://brianaustinchin.com/portfolio/VR"><button class="btn btn-danger" type="button">Exit Admin View</button></a>
            </div>
        <?php else: ?>
            <div class="admin-nav-button">
                <a href="admin"><button class="btn btn-danger" type="button">Admin View</button></a>
            </div>
        <?php endif; ?>
    </nav>

    <div class="main-dialog">
        <div class="outer">
            <div class="middle">
                <div class="dialog-container" id="mainDialog">
                    <div class="title"><h4>Are you sure?</h4></div>
                    <div class="body"><p>This action cannot be undone</p></div>
                    <hr />
                    <button class="btn btn-danger dialog-close">Cancel</button>
                    <button class="btn btn-danger" id="dialog-confirm">Delete</button>
                </div>
            </div>
        </div>
    </div>