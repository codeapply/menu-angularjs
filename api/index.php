<?php

interface API_interface
{
function create($link, $title, $after_id, $depth);
function read($format);
function update();
function delete($id);
}

class API implements API_interface {


public function __construct()
{
      $this->assignToken($_SERVER['HTTP_HOST'], $_GET['rk']);
      
      if (isset($_GET) && isset($_GET['action']))     
      $action = $_GET['action'];
      
      switch ($action) {
      case "read": $this->read($_GET['format']); break;
      case "create": $this->create($_GET['href'], $_GET['title'], $_GET['after'], $_GET['depth']); break;
      case "delete": $this->delete($_GET['id']); break;
      case "update": $this->update($_GET['id'], $_GET['arg'], $_GET['value']); break;
      }                    
      $this->expireToken();                                           
}
                       
function genKeys($domain,$accesslevel) {
//gen key domain-wise

}         
     
     
function assignToken($domain,$accesslevel) {
//asg time token as http-cookie ncrypted server-time, with random request id by client - limit by time or n. of execution

}         
     
function expireToken() {

}

function isAuthorized() {                
      $validKeys = array (0 => array('msflk4n3tjljnknjk2j','localhost','readonly'), 
                          0 => array('flwnj4jnjk34nj34jnk','localhost','write'));
      
      return true; 
      //simple key0permissions-domain assoc and check      
}
function notAuthorized() {  
      header("HTTP/1.1 401 Unauthorized");
      die('Not Authorized');
}                              

function url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function redirect() {  
       //header('HTTP/1.1 301 Moved Permanently'); 
       //header('Location: ' . $this->url_origin($_SERVER, false)); 
       exit(0);
}

function read($format = 'json') {
      if (!$this->isAuthorized()) $this->notAuthorized();
      
      $json = file_get_contents('menuitems.json');
      $menu_arr = json_decode($json, true);
    
      
      if ($format == 'xml') {                    
      //output xml
      header('Content-type: text/xml');
      $xml = new SimpleXMLElement('<root/>');
      $this->array2xml($xml, $menu_arr);    
      $dom = new DOMDocument("1.0");
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($xml->asXML());
      echo $dom->saveXML();
      } 
      else {
      //assume json & output
      header('Content-type: application/json');
      echo $json;
      exit(0);
      }
      

}


function update() {
      if (!$this->isAuthorized()) $this->notAuthorized();
      
      $json = file_get_contents('menuitems.json');
      
      $menu_arr = json_decode($json, true);
      
      $menu_arr = $this->createIds($menu_arr);
      
      file_put_contents('menuitems.json', json_encode($menu_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      $this->redirect();

}                                

function create($link, $title, $after_id, $depth) {
      if (!$link) $this->redirect();
      if (!$title) $this->redirect();
      
      if (!$this->isAuthorized()) $this->notAuthorized();
      
      $menu_arr = json_decode(file_get_contents('menuitems.json'), true);
      
      $new_item = array ('href'=>$link, 'title'=>$title);
      
      $menu_arr = $this->walk_recursive_add($menu_arr, $after_id, $new_item);
      
      $menu_arr = $this->createIds($menu_arr);      
      
      
      file_put_contents('menuitems.json', json_encode($menu_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      $this->redirect();
      
}                  

function walk_recursive_add(array $array, $id, $new_item) { 
    
    
    foreach ($array as $k => $v) {
        if (is_array($v)) { 
            $array[$k] = $this->walk_recursive_add($array[$k], $id, $new_item);  
            if ($v['itemId'] == $id) {         
                echo $v['itemId'] . '  itemid';
                $tmp_pr = $new_item;
                echo count($array);
                echo 'ca k:'.$k.':k';
                for ($i = $k; $i < count($array); $i++) {
                
                echo 'i:'.$i . ' i:k '.$k;
                   $tmp_nr = $array[$i+1];
                   if (array_key_exists($i+1,$array))
                   $array[$i+1] = $tmp_pr;
                   else
                   {
                   $array[$i+1] = $tmp_pr;
                   break;
                   }
                   $tmp_pr = $tmp_nr;
                   
                }
                var_dump( $array[$i+1]);
        
    }
    }
    }
    
    return $array; 
} 

                                            
function delete($id) {
      if (!$this->isAuthorized()) $this->notAuthorized();
      $menu_arr = json_decode(file_get_contents('menuitems.json'), true);
      $menu_arr = $this->walk_recursive_remove($menu_arr, $id); //function($itemid) use ($id) { if ($itemid == $id) return true; else return false; });
      $menu_arr = $this->createIds($menu_arr);
      file_put_contents('menuitems.json', json_encode($menu_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      $this->redirect();
}

function walk_recursive_remove(array $array, $id) { 
    foreach ($array as $k => $v) { 
        if (is_array($v)) { 
            $array[$k] = $this->walk_recursive_remove($v, $callback);  
            if ($v['itemId'] == $id) 
            unset($array[$k]); 
            }
    } 
    return $array; 
} 

function createIds(array $array) {
    $new_a = array();
    if (!$this->n) $this->n = 0;
    foreach ($array as $k => $v) {
        if (!is_array($v)) {
        $new_a["itemId"] = $this->n;
        if ($k == 'title')
        $this->n = $this->n + 1;
        $new_a[$k] = $v;
        }
        else {
            // directory node -- recurse  
            $new_a[$k] = $this->createIds($v, $this->n);
        }
    }
    return $new_a;
}

function array2xml($obj, $array)
{
      foreach ($array as $key => $value) {
      if(is_numeric($key))
      $key = 'entry' . $key;
      
      if (is_array($value)) {
      $node = $obj->addChild($key);
      $this->array2xml($node, $value);
      } 
      else 
      $obj->addChild($key, htmlspecialchars($value));
      }
}


                        
}

//load and save?

error_reporting (E_ALL ^ E_NOTICE);

$api = new API();



?>