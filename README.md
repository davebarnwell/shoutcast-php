shoutcast-php
=============

  [![Build Status](https://scrutinizer-ci.com/g/freshsauce/shoutcast-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/freshsauce/shoutcast-php/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/freshsauce/shoutcast-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/freshsauce/shoutcast-php/?branch=master)

  SHORTcast class to query a DNAS for such things as recently played tracks (Song History) 

  SHOUTcast DNAS XML spec is here http://wiki.shoutcast.com/wiki/SHOUTcast_DNAS_Server_2_XML_Reponses
  
  
  
Usage:
======

    $DNAS = new ShoutCast('mystatsion.com:8888','myDnasPassword');
    
    $recently_played = $DNAS->getRecentlyPlayed();
    
    var_dump($recently_played);