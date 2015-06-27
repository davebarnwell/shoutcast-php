<?php
/**
* Class used to pull recently played list from a SHOUTcast DNAS
* @author Dave Barnwell <dave@freshsauce.co.uk>
*/
class ShoutCast {
  public $address;
  public $password;
  
  /**
   * Setup required params for this particular SHOUTcast DNAS
   *
   * @param string $streamAddress IP address or domain name plus port eg. 192.168.0.1:8888 or mystation.com:8888
   * @param string $password password set on the SHOUTcast DNAS
   */
  public function __construct($streamAddress, $password) {
    $this->address  = $streamAddress;
    $this->password = $password;
  }
  
  /**
   * Return the recently played list (Song History) as an array of ojects from the SHOUTcast DNAS
   * SHOUTcast DNAS XML spec is here http://wiki.shoutcast.com/wiki/SHOUTcast_DNAS_Server_2_XML_Reponses
   *
   * @return array of song objects (artistTitle, playedAt, artist, title)
   */
  public function getRecentlyPlayed() {
    $songs  = array();

    $buffer = self::getData('http://'.$this->address.'/admin.cgi?mode=viewxml&pass='.$this->password.'&page=4');

    $xml    = simplexml_load_string($buffer); 

    foreach ($xml->SONGHISTORY->SONG as $song) {
      $entry = array(
        'artistTitle' => trim($song->TITLE),
        'playedAt'    => date("r", (int) $song->PLAYEDAT),
        'artist'      => null,
        'title'       => null
      );

      if (substr($entry['artistTitle'], -2) == ' -') { // remove trailing space hypen
        $entry['artistTitle'] = substr($entry['artistTitle'], 0, -2);
      }
      if (preg_match("/^(wdj|ekr|ad|jingle|promo)\b/i", $entry['artistTitle'])) {
        //echo 'Skipped '.$entry['artistTitle']."\n";
        continue; // skip entries that start with these words
      }

      // now construct artist - title
      list($entry['artist'], $entry['title']) = explode(' - ', $entry['artistTitle'], 2);
      $songs[] = (object) $entry;
    }

    return $songs;
  }


   // returns content from the specified URL

   /**
    * @param string $url
    */
   private static function getData($url) {
    $ch = curl_init($url);
    curl_setopt_array(
      $ch,
      array(
        CURLOPT_HEADER         => false,
        CURLOPT_POST           => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)'
      )
    );
    $output      = curl_exec($ch);
    $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = ($http_code == 302) ? curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) : null;
    $error_no    = curl_errno($ch);
    if ($error_no != 0) {
      throw new \Exception(__METHOD__.':Data fetch failed curl error:'.$error_no);
    }
    if ($http_code != 200) {
      throw new \Exception(__METHOD__.':Data fetch failed http code:'.$http_code.(($redirectUrl) ? ', redirect: '.$redirectUrl : ''));
    }
    curl_close($ch);
    return $output;
  }
}

