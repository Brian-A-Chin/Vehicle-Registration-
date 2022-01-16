<?php
class base{
    

    function crypto( $string, $action = 'e' ) {

        $key = substr(hash('sha256', SECRET_KEY), 0, 32);
        $iv = substr(hash('sha256', SALT), 0, 16);
        $padwith = '`';
        $blocksize = 32;
        $method = "AES-256-CFB";
        if($action === 'e'){
            $padded_secret = $string . str_repeat($padwith, ($blocksize - strlen($string) % $blocksize));
            $encrypted_string = openssl_encrypt($padded_secret, $method, $key, OPENSSL_RAW_DATA, $iv);
            $encrypted_secret = base64_encode($encrypted_string);
            return $encrypted_secret;
        }else{
            $decoded_secret = base64_decode($string);
            $decrypted_secret = openssl_decrypt($decoded_secret, $method, $key, OPENSSL_RAW_DATA, $iv);
            return rtrim($decrypted_secret, $padwith);
        }
    }
    
    function create_token(){
        
        $date = new DateTime();
        $unix_time_stamp = $date->getTimestamp();
        $token = $this->crypto(ceil(($unix_time_stamp * 1996) / 24));
        return $token;
        
    }
    
    function validate_token($token){
        //BASED ON A 7 SEC TTL 
        $date = new DateTime();
        $current_time = $date->getTimestamp();
        $tokenTime = $this->crypto($token,'d');
        return (ceil(($current_time * 1996) / 24) - 4 <= $tokenTime) && ($tokenTime <= (ceil(($current_time * 1996) / 24)) + 4); 
    }
    
    
    public static function IP(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    

    
    function post_clean($object){
        $object = filter_var($object, FILTER_SANITIZE_STRING);
        $object = filter_var($object, FILTER_SANITIZE_SPECIAL_CHARS);
        return $object;
    }
    
    function clean($object){
        
        $object = filter_var($object, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        return $object;
    }
    
    function isHTML($string){
        if($string != strip_tags($string)){
            // is HTML
            return true;
        }else{
            // not HTML
        return false;
        }
    }
    
    function file_filter($object){
        $ext = PATHINFO($object, PATHINFO_EXTENSION);
        $object = substr($object, 0, strrpos($object, "."));
        $object = preg_replace('/[^a-zA-Z0-9\s+]/', '', $object);
        $object = str_replace(' ', '_', $object);
        $object = preg_replace('/\s+/', '_', $object);
        return $object.'.'.$ext;
    }


    
    function underlineSpaces($object){
        
        $object = str_replace(' ', '_', $object);
        $object = preg_replace('/\s+/', '_', $object);
        return $object;
    }
    
    function addSpaces($object){
        $object = str_replace('_', ' ', $object);
        
        return $object;
    }
    
    function indexify($object){
        
        $object = str_replace(' ', ',', $object);
        $object = preg_replace('/\s+/', ',', $object);
        
        return $object;
    }
    
    function generate_int_code($length){
        if($length == 1){
            return rand(0,9);
        }else{
          $int = '';
            for($i = 0;$i < $length; $i++){
                $int .= 9;
            } 
            return rand($int,$int.'9');
        }
    }
    
    function lower($object){
        
        return strtolower($object);
        
    }

    
    function rmSpaces($object){
        
        $object = str_replace(' ', '', $object);
        $object = preg_replace('/\s+/', '', $object);
        return $object;
    }
    
    function cleanArray($array){
        $array = array_unique($array);
        $array = array_filter($array);
        return $array;
    }
    
    function filter($object){
        $object = $this->clean($object);
        $object = preg_replace("/[^A-Za-z0-9' -.]/","",$object);
        return $object;
    }
    
    public function scriptFilter( $string ){
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $string);
    }
    
    
    function getTime(){
        $now_stmt = connection::make()->prepare("SELECT NOW() AS `time`");
        $now_stmt->execute([]); 
        $time = $now_stmt->fetch();  
        return $time['time'];
    }

     function is_email($email){
        $email= trim($email);
		if (filter_var($email, FILTER_VALIDATE_EMAIL)){
			return true;
        }else{
			return false;
        }
    }
    
    
    function redirect(){
        header("location:/404");
        exit;
    }
    
    function kick($override){
        unset($_SESSION['authenticationToken']);
        $this->generate_auth_form_key();
        $_SESSION['kickout_protocol'] = 'true';
        if(!is_bool($override)){
            $this->setResponse($override);
        }
        header('location:/control/login/action/authenticate');
        exit;
    }
    
  
    
    function is_authenticated(){
        if(!isset($_SESSION['AUTH_SET_ATTEMPTS']))
            $_SESSION['AUTH_SET_ATTEMPTS'] = 0;
        
        if($_SESSION['AUTH_SET_ATTEMPTS'] <= 7){
            $pdo_stmt = connection::make()->prepare("SELECT `IP` FROM `IPS` WHERE `IP`=? LIMIT 1");
            $pdo_stmt->execute([ $this->crypto($this::IP()) ]); 
            $user = $pdo_stmt->fetch();  
            if($this->crypto($user['IP'],'d') === $this::IP()){
                $_SESSION['signature'] = $this->crypto($this::IP());
                return true;
            }else{
                $_SESSION['AUTH_SET_ATTEMPTS']++;
                return false;
            }
        }
        return false;
        
            
    }
    


    
    
    function randomString($length) {
	   $str = "";
	   $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	   $max = count($characters) - 1;
	   for ($i = 0; $i < $length; $i++) {
		  $rand = mt_rand(0, $max);
		  $str .= $characters[$rand];
	   }
	   return $str;
        
    }
    

    function register($device,$nickname,$token){
        $smt = connection::make()->prepare("INSERT INTO `IPS` (`IP`, `Nickname`) VALUES (:IP, :Nickname)"); 
        $register = $smt->execute(array(
            "IP" =>  $device,
            "Nickname" => $nickname
        ));
        return array(
            'result' =>  true,
            'action' => 'ndr',
            'token' => $token,
            'response' =>  'Device Registered'
        );
    }
    
    function setLink($params){
        $smt = connection::make()->prepare("UPDATE `Links` SET URL=:URL WHERE Name=:Name"); 
        $smtExe = $smt->execute(array(
            "Name" =>  $this->clean($params['Name']),
            "URL" => $this->clean($params['URL'])
        ));
        return array(
            'result' =>  true,
            'response' =>  'Updated'
        );
    }
    
    function getLink($name){
        
        $stmt = connection::make()->prepare("SELECT URL FROM Links WHERE Name=?");
        $stmt->execute([ $this->clean($name) ]); 
        return $stmt->fetch()['URL'];
        
    }
    
    function deleteDevice($deviceID,$restrictedDevice){
        $deviceID = $this->crypto($deviceID,'d');
        $deviceID = $this->clean($deviceID);
        if($restrictedDevice){
            $sql = "DELETE FROM Restricted WHERE id=? LIMIT 1";
        }else{
            $sql = "DELETE FROM IPS WHERE id=? LIMIT 1";
        }
        connection::make()->prepare($sql)->execute([$deviceID]);
        return array(
            'result' =>  true,
            'response' =>  'Device Removed'
        );
    }
    
    function get_devices($restrictedDevice){
        $data = [];
        if($restrictedDevice){
            $query = 'SELECT *,DATE_FORMAT(Created, "%m/%d/%Y at %h:%i %p") AS Created FROM Restricted';
        }else{
            $query = 'SELECT *,DATE_FORMAT(Created, "%m/%d/%Y at %h:%i %p") AS Created FROM IPS';
        }
        $statement = connection::make()->prepare($query);
        $statement->execute(); 
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    function get_logs($DeviceID){
        $query = 'SELECT Message,DATE_FORMAT(Created, "%m/%d/%Y at %h:%i %p") AS posted FROM Logs WHERE DeviceID=? ORDER BY Created DESC LIMIT 40';
        $statement = connection::make()->prepare($query);
        $statement->execute([$DeviceID]); 
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    function block($accessPoint,$accessLevel){
        $smt = connection::make()->prepare("INSERT INTO `Restricted` (`AccessPoint`, `AccessLevel`) VALUES (:AccessPoint, :AccessLevel)"); 
        $register = $smt->execute(array(
            "AccessPoint" =>  $this->crypto($accessPoint),
            "AccessLevel" => $this->crypto($accessLevel)
        ));
        return array(
            'result' =>  true,
            'action' => 'restricted',
            'response' =>  'Saved'
        );    
    }
    
    function get_FR_Stats($DeviceID){
        
        $statement = connection::make()->prepare('SELECT * FROM FR_Stats WHERE DeviceID=?');
        $statement->execute([ $DeviceID ]); 
        return $statement->fetchAll(PDO::FETCH_ASSOC)[0];
        
        
    }
    
    
}




?>
