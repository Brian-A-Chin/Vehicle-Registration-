<?php
class HomeController extends base{
    
    
    
    public function getCars(){
    
        $query = 'SELECT * FROM Vehicles ORDER BY `posted` DESC LIMIT 100';
        $statement = connection::make()->prepare($query);
        $statement->execute(); 
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function addCar( $data ){
            try{ 
                $smt = connection::make()->prepare("INSERT INTO `Vehicles` (`fullName`,`make`,`model`,`color`,`licensePlate`,`state`,`year`)VALUES(:fullName,:make,:model,:color,:licensePlate,:state,:year)");
                $smt->execute(array(
                    ':year' => $data['year'],
                    ':make' => $data['make'],   
                    ':model' => $data['model'],
                    ':color' => $data['color'],
                    ':state' => $data['state'],
                    ':fullName' => $data['fullName'],
                    ':licensePlate' => $data['plate']
                ));
            }catch(Exception $e){
                return array(
                    'result' =>  false,
                    'response' =>  'Faild to add check controller'
                ); 
            }
        
            return array(
                'result' =>  true,
                'response' =>  'Saved'
            ); 
                    
    }
    
    public function updateCar( $data ){
        try{ 
            connection::make()->prepare("UPDATE `Vehicles` SET make=?,model=?,color=?,licensePlate=?,state=?,year=? WHERE `id`=?")->execute($data);
        }catch(Exception $e){
            return array(
                'result' =>  false,
                'response' =>  'Faild to update check controller'
            ); 
        }
        return array(
            'result' =>  true,
            'response' =>  'Saved'
        ); 
        
    }
    
    public function deleteCar( $ID ){
        try{ 
            connection::make()->prepare("DELETE FROM Vehicles WHERE id=? LIMIT 1")->execute([$ID]);
        }catch(Exception $e){
            return array(
                'result' =>  false,
                'response' =>  'Faild to delete check controller'
            ); 
        }
        
        return array(
            'result' =>  true,
            'response' =>  'removed'
        ); 
    }
        
}