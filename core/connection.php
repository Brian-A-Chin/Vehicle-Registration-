<?php
class connection{

    public static function make(){
        
        try{
            
            
            return new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME,
                DB_USER, 
                DB_PASSWORD
            );
            
            
        } catch (PDOException $e) {
            
            die($e->getMessage());
            
        }
        
        
    }
    
}



?>