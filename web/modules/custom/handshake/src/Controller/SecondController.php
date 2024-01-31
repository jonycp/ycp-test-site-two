<?php
/**
* Generates markup to be displayed. Functionality in this Controller is
* wired to Drupal in handshake.routing.yml.
*/

  namespace Drupal\handshake\Controller; 

/**
* Add use statements next.
* In this example ControllerBase is an abstract class.
*/

use Drupal\Core\Controller\ControllerBase; 

class SecondController extends ControllerBase{

    public function showHandshakeJobsList() {
      $handshake = 'https://app.joinhandshake.com/external_feeds/17966/public.rss?token=VoCI9Q52nygV-CWnC9rn__oB7yQrmFfknNsatG31w3lA7SLpOk7zmw';
      $eventsListItems = '';
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
                $eventsListItems .= $this->outputEvent($rssItem);              
              } // foreach
            } // XML ok
          } // curl info 200
        } // curl info not false
        curl_close($ch);
       // echo $eventsListItems;  
      } // curl initialized ok
      return [
        '#type' => 'markup',
        '#markup' => t($eventsListItems),
      ];
    }
    
    private function outputEvent($item){
      $brtag = '<br/>';
      $br2tag = '<br/><br/>';
      $brtagpos = 0;
      $br2tagpos = 0;
      $dtpos = 0;
      $descpos=0;
      $descendpos=0;

    // Get employer and expires positions to parse these from the description.
    // Use divs instead of h's or p's due to existing styles' padding.
    // Expires length is Expires: 04/01/2024 17 

      $emprPos = strpos($item->description, 'Employer:');
      $exprPos = strpos($item->description, 'Expires');
      $outEmpr = substr($item->description, $emprPos, $exprPos - 4);
      $outExpr = substr($item->description, $exprPos, 17);
      $outDesc = substr($item->description, $exprPos + 23);
      
      $strEvent  = '<div style="margin-top:15px;"><a href="' . trim(strval($item->link)) . '" style="font-weight:500;">' . trim(strval($item->title)) . '</a></div>';
      $strEvent .= '<div>' . trim(strval($outDesc)) . '...</div>';
      
      return $strEvent;
    }

}
