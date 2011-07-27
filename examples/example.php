<?php

// Error Reporting
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');

/** @see VKontakte */
require_once '../library/vkontakte.php';

// Create new VKontakte instance.
$vkontakte = new VKontakte(array('testMode'   => true,    // false by default
                                 'fileUpload' => false)); // false by default

// ID of the user that is viewing the application.
$viewerId = $vkontakte->getViewerId();

// Returns advanced information about users. 
$profiles = $vkontakte->getProfiles(array('uids'   => $viewerId,
                                          'fields' => 'photo,sex'));

// $profiles = $vkontakte->api(array('method' => 'getProfiles',
//                                   'uids'   => $viewerId,
//                                   'fields' => 'photo,sex'));

// $profiles = $vkontakte->api('getProfiles', array('uids'   => $viewerId,
//                                                  'fields' => 'photo,sex'));

foreach ($profiles as $profile) {
    printf('<img src="%s" alt="" /><br /><pre>%s</pre>',
           $profile['photo'], 
           print_r($profile, true));
}

// Returns a list of identifiers of the current user's friends
// or advanced information about the user's friends (when using
// the fields parameter). 
$friends = $vkontakte->get('friends', array('fields' => 'photo'));

// $friends = $vkontakte->api(array('method' => 'friends.get',
//                                  'fields' => 'photo'));

// $friends = $vkontakte->api('friends.get', array('fields' => 'photo'));

// $friends = $vkontakte->get(array('method' => 'friends',
//                                  'fields' => 'photo'));

foreach ($friends as $friend) {
    printf('<img src="%s" alt="" /><br /><pre>%s</pre>',
           $friend['photo'], 
           print_r($friend, true));
}