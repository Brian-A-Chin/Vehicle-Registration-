
<?php 
    session_start();
    require_once('config.php');
    require_once('includes.php');
    require_once( 'includes/header.php' );
    $controller = new HomeController();

?>
<div id="outter-view-port">
    <div class="side-container shadow white-background sm-padding">
        <div class="text-right clickable close-side-menu danger-hover">
            <i class="fa fa-times text-muted" aria-hidden="true"></i>
        </div>
        <h4 class="center-text">Edit Vehicle</h4>
        <form id="editVehicleForm">
            <p>Make</p>
            <input type="text" id="make" placeholder="Make" class="form-control text-capitalize selected-vehicle-make vehicle-make dependent">
            <a href="#"><small class="loadAllMakes">Can&rsquot find your manufacture?</small></a>
            <p>Model</p>
            <input type="text" id="model" class="form-control vehicle-model text-capitalize" placeholder="Model">
            <p>Color</p>
            <input type="text" id="color" class="form-control vehicle-color text-capitalize" placeholder="Color">
            <p>License Plate</p>
            <input type="text" id="plate" class="form-control classifier text-uppercase" data-type="plate" placeholder="License Plate" min="5" max="10">
            <p>State</p>
            <input type="text" class="form-control vehicle-state text-capitalize" id="state" placeholder="State" data-type="state" />
            <p>Year</p>
            <input type="text" id="year" class="form-control classifier numeric" placeholder="Vehicle Year" data-type="year" min="4" max="4" />
            <hr>
            <div class="center-center parent sm-padding inline-btns">
                <button type="button" class="btn btn-outline-primary core-action" data-action="edit">Update</button>
                <button type="button" class="btn btn-outline-danger core-action" data-action="delete">Delete</button>
            </div>
        </form>
    </div>

    <div class="body-container">
        <div class="greeting-container"></div>
        <div class="content-container">
            <div class="section md-section white-background rounded register-section shadow">
                <div class="min-gap"></div>
                <h4 class="center-text">Vehicle Registration Demo</h4>
                <div class="sm-padding">
                    <form id="newVehicleForm">

                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <input type="text" placeholder="Make" class="form-control new-vehicle-make vehicle-make dependent">
                                    <a href="#"><small class="loadAllMakes">Can&rsquo;t find your manufacture?</small></a>
                                </div>
                                <div class="col-sm">
                                    <input type="text" class="form-control vehicle-model disabled" placeholder="Model" disabled="disabled">
                                </div>
                                <div class="col-sm">
                                    <input type="text" class="form-control vehicle-color disabled" placeholder="Color" disabled="disabled">
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <input type="text" class="form-control classifier text-uppercase" data-type="plate" placeholder="License Plate" min="5" max="10">
                                </div>
                                <div class="col-sm">
                                    <input type="text" class="form-control vehicle-state" placeholder="State" data-type="state" />
                                </div>
                                <div class="col-sm">
                                    <input type="text" class="form-control classifier numeric" placeholder="Vehicle Year" data-type="year" min="4" max="4" />
                                </div>
                            </div>
                        </div>

                        <div class="center-center parent sm-padding">
                            <button type="button" class="btn btn-primary core-action" data-action="add">Register</button>
                        </div>



                    </form>
                </div>
            </div>
        </div>
        <div class="min-gap"></div>
        <div class="section md-section">
            <h4 class="">My vehicles (<span id="totalVehicles">--</span>)</h4>
            <p class="text-muted" id="help-intro"></p>
        </div>
        <div class="section md-section">
            <div class="tile-container">

                <?php foreach($controller->getCars() as $key => $value ): ?>

                <div class="tile parent stored-vehicle clickable text-capitalize" data-ID="<?=$value['id'];?>" data-state="<?=$value['state'];?>" data-plate="<?=$value['licensePlate'];?>" data-color="<?=$value['color'];?>" data-year="<?=$value['year'];?>" data-make="<?=$value['make'];?>" data-model="<?=$value['model'];?>">
                    <div class="core-action text-muted remove-vehicle" data-action="delete" data-RID="@row.CarID">
                        <i class="fas fa-times"></i>
                    </div>
                    <p class="text-muted stored-vehicle-year"><?=$value['year'];?></p>
                    <p><span class="stored-vehicle-make"><?=str_replace('_',' ',$value['make']);?></span> - <span class="stored-vehicle-model"><?=$value['model'];?></span></p>
                    <img class="stored-vehicle-image pending-logo" src="Assets/loading.gif">
                </div>
                
            <?php endforeach; ?>


            </div>
        </div>
        <div class="gap"></div>
    </div>
</div>
<?php include('includes/footer.php');?>