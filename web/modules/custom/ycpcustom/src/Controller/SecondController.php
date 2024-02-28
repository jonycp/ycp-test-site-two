<?php
/**
* Generates markup to be displayed. Functionality in this Controller is
* wired to Drupal in ycpcustom.routing.yml.
*/

  namespace Drupal\ycpcustom\Controller; 

/**
* Add use statements next.
* In this example ControllerBase is an abstract class.
*/

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
class SecondController extends ControllerBase{

    public function showHandshakeJobsList() {
      $handshake = 'https://app.joinhandshake.com/external_feeds/17966/public.rss?token=VoCI9Q52nygV-CWnC9rn__oB7yQrmFfknNsatG31w3lA7SLpOk7zmw';
      $jobs = '';
      $ch=curl_init();
      if($ch){
        curl_setopt($ch, CURLOPT_URL, $handshake);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $feed=curl_exec($ch);
        $info=curl_getinfo($ch);
        if($info!==FALSE){
          if($info['http_code']==200){ 
            $rss = simplexml_load_string($feed);
            if($rss!==FALSE){
              foreach($rss->channel->item as $rssItem) {
                $jobs .= $this->output_job($rssItem);              
              } // foreach
            } // XML ok
          } // curl info 200
        } // curl info not false
        curl_close($ch);
       // echo $jobs;  
      } // curl initialized ok
      return [
        '#type' => 'markup',
        '#markup' => t($jobs),
      ];
    }
    
    private function output_job($item){

      $desc = substr(strval($item->description),0,200);
      $title = strval($item->title);
      $link = strval($item->link);

      $strJob='<p><a href="'.$link.'" style="font-weight:bold;">'.$title.'</a><br />'.$desc.'...</p>';

      return $strJob;
}

    public function showUser() {
        
      $user = \Drupal::currentUser();
      
      if($user->isAuthenticated()){
        $email = $user->getEmail();
        return [
          '#type' => 'markup',
          '#markup' => t('<h3>You are an authenticated user. Your email is ' . $email . '<h3>'),
        ];
      } else {
        return [
          '#type' => 'markup',
          '#markup' => t('<h3>User is not authenticated.<h3>'),
        ];
      }
    }

/**  Dr does not allow redirects by default.
  * Error message:
  * Redirects to external URLs are not allowed by default, 
  * use \Drupal\Core\Routing\TrustedRedirectResponse for it.
  */
    public function redirectWCOnline() { 
    
      $user = \Drupal::currentUser();
      
      if($user->isAuthenticated()){
        
         $email = $user->getEmail();  
        
        // $email = 'pmiller11@ycp.edu';
        
        $securityKey = 'vIt!899k@HAbjR$0Fxef';
       
        // date(I) is 1 during DST and 0 otherwise
        $hours = date('H') - date('I');

        // zero-pad the hours
        $hours = str_pad($hours,2,"0",STR_PAD_LEFT);

        // '' below are ' and ' together not " 
        $token = md5($securityKey.''. $hours .''.date('m').''.date('d').''.strtolower($email));

        $url = 'https://ycp.mywconline.com/backdoor.php?email=' . $email . '&token=' . $token;
        return $response = new TrustedRedirectResponse($url);
        
      } else {
        
          return [
            '#type' => 'markup',
            '#markup' => t('<h3>User is not authenticated.<h3>'),
          ];      
          
      }
    }
}
  
