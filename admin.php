
<?php 
    session_start();
    require_once('config.php');
    require_once('includes.php');
    require_once( 'includes/header.php' );
    $controller = new HomeController();

?>
<div id="outter-view-port">
    <div class="body-container">
        <div class="greeting-container"></div>
        <div class="content-container">
            <div class="section md-section white-background rounded register-section shadow">
                <div class="min-gap"></div>
                <h4 class="center-text">Search Employee Vehicle Registrations</h4>
                <div class="sm-padding">
                    <div class="container">
                        <div class="row">
                            <div class="form-group" style="width:29%">
                                <select id="searchCategory" class="form-control">
                                    <option value="license-plate" selected="selected">license Plate</option>
                                    <option value="owner-name">Owner Name</option>
                                    <option value="vehicle-make">Make</option>
                                    <option value="vehicle-model">Model</option>
                                    <option value="all">Anything</option>
                                </select>
                            </div>
                            <div class="form-group" style="width:69%">
                                <input placeholder="Search" id="vehicle-search" type="text" class="form-control">
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Begin typing to preform a search</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="min-gap"></div>
        <div class="section lg-section">
            <h4 class=""><span id="searchResultsText">Registered vehicles</span> (<span id="totalVehicles">--</span>)</h4>
            <p class="text-muted" id="help-intro"></p>
            <div id="searchHelp"></div>
        </div>
        <div class="section lg-section">
            <div class="admin-tile-container">

               <?php foreach($controller->getCars() as $key => $value ): ?>
                        <div class="tile parent stored-vehicle clickable text-capitalize search-block">
                            <h4>Registration Info</h4>
                            <div class="vehicle-make-logo">
                                <img class="stored-vehicle-image" data-make="<?=$value['make'];?>" src="~/Assets/loading.gif">
                            </div>
                            <p><b>Licence Plate:</b><span class="license-plate"><?=$value['licensePlate'];?></span></p>
                            <p><b>Owner Name:</b><span class="owner-name"><?=$value['fullName'];?></span></p>
                            <p><b>Registered State:</b><span class="vehicle-state"><?=$value['state'];?></span></p>
                            <h4>Vehicle Info</h4>
                            <p><b>Make:</b><span class="vehicle-make"><?=$value['make'];?></span></p>
                            <p><b>Model:</b><span class="vehicle-model"><?=$value['model'];?></span></p>
                            <p><b>Color:</b><span class="vehicle-color"><?=$value['color'];?></span></p>
                            <p><b>Year:</b><span class="vehicle-year"><?=$value['year'];?></span></p>
                        </div>

                 <?php endforeach; ?>


            </div>
        </div>
        <div class="gap"></div>
    </div>
</div>


<?php include('includes/footer.php');?>