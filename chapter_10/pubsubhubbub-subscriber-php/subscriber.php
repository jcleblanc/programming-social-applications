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
 * Class: Subscriber
 * Description: Provides ability to subscribe / unsubscribe from hub feeds
 */
class Subscriber{
    private $regex_url = '|^https?://|i';       //simple URL string validator
    private $hub = '';                          //hub URL
    private $callback = '';                     //callback URL
    
    //constructor that stores the hub and callback URLs for the subscriber
    public function __construct($hub, $callback){
        if (preg_match($this->regex_url, $hub)){ $this->hub = $hub; }
        else{ throw new Exception('Invalid hub URL supplied'); }
        
        if (preg_match($this->regex_url, $callback)){ $this->callback = $callback; }
        else{ throw new Exception('Invalid callback URL supplied'); }
    }
    
    //initiates a request to subscribe to a feed
    public function subscribe($feed){
        return $this->change_subscription('subscribe', $feed);
    }
    
    //initiates a request to unsubscribe from a feed
    public function unsubscribe($feed){
        return $this->change_subscription('unsubscribe', $feed);
    }
    
    //makes request to hub to subscribe / unsubscribe
    public function change_subscription($mode='subscribe', $feed){
        //check if provided feed is a valid URL
        if (preg_match($this->regex_url, $feed)){
            //set the post string for subscribe / unsubscribe
            $post_string = "hub.mode=$mode&hub.callback={$this->callback}&hub.verify=async&hub.topic=$feed";
            
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
        } else {
            throw new Exception('Invalid feed URL supplied');
        }
    }
}
?>