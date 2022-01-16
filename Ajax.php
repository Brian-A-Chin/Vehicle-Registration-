
<?php 
    if ( empty($_POST) ) { 
        header("location:../../404");
        exit;
    }
    session_start();
    require_once('config.php');
    require_once('includes.php');
    require_once('core/3rdParty/RandomNames/randomNameGenerator.php');
    $controller = new HomeController();
    
    
    if(isset($_POST['addCar'])){
        $NameGenerator = new randomNameGenerator();
        $result = $controller->addCar(array(
            'fullName' => $NameGenerator->getName(),
            'make' => $controller->clean($_POST['addCar']),
            'model' => $controller->clean($_POST['Model']),
            'color' => $controller->clean($_POST['Color']),
            'plate' => $controller->clean($_POST['Plate']),
            'state' => $controller->clean($_POST['State']),
            'year' => $controller->clean($_POST['Year']),
        ));
        
        echo json_encode($result);
    }

    if(isset($_POST['updateCar'])){
        $result = $controller->updateCar([
            $controller->clean($_POST['Make']),
            $controller->clean($_POST['Model']),
            $controller->clean($_POST['Color']),
            $controller->clean($_POST['Plate']),
            $controller->clean($_POST['State']),
            $controller->clean($_POST['Year']),
            $controller->clean($_POST['updateCar']),
        ]);
        
        echo json_encode($result);
    }

    if(isset($_POST['deleteCar'])){
        $result = $controller->deleteCar($controller->clean($_POST['deleteCar']));
        echo json_encode($result);
    }

?>