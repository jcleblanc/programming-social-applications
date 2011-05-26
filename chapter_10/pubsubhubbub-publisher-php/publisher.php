<?php
/* Copyright 2011 Jonathan LeBlanc
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * Class: PubSubHubbub Publisher
 * Description: Allows for the publishing of new updates to the hub
 */
class Publisher{
    private $regex_url = '|^https?://|i';       //simple URL string validator
    private $hub = '';                          //hub URL
    
    //constructor that stores the hub and callback URLs for the subscriber
    public function __construct($hub){
        if (preg_match($this->regex_url, $hub)){ $this->hub = $hub; }
        else{ throw new Exception('Invalid hub URL supplied'); }
    }
    
    //makes request to hub to subscribe / unsubscribe
    public function publish($feeds){
        //set up POST string with mode
        $post_string = 'hub.mode=publish';
        
        //loop through each feed provided
        foreach ($feeds as $feed){
            //if feed is valid, add to POST string
            if (preg_match($this->regex_url, $feed)){
                $post_string .= '&hub.url=' . urlencode($feed);
            } else {
                throw new Exception('Invalid hub URL supplied');
            }
        }
        
        //set up cURL request
        $ch = curl_init($this->hub);
        $options = array(
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $post_string,
            CURLOPT_CUSTOMREQUEST => 'POST'
        );
        curl_setopt_array($ch, $options);
            
        //make request to hub
        $response = curl_exec($ch);
        curl_close($ch);
            
        //return response
        return $response;
    }
}
?>