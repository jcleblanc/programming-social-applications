<?php
/*******************************************************************************
 * Class Name: 
 * Description: 
 * Useage: 
 *   $url = 'http://www.example.com/index.html';
 *   $graph = new OpenGraph($url);
 *   print_r($graph->get_one('title'));  //get only title element
 *   print_r($graph->get_all());         //return all Open Graph tags
 ******************************************************************************/
class OpenGraph{
    //the open graph associative array
    private static $og_content = array();
    
    /***************************************************************************
     * Function: Class Constructor
     * Description: Initiates the request to fetch OG data
     * Params: $url (string) - URL of page to collect OG tags from
     **************************************************************************/
    public function __construct($url){
        if ($url){
            self::$og_content = self::get_graph($url);    
        }
    }
    
    /***************************************************************************
     * Function: Get Open Graph
     * Description: Initiates the request to fetch OG data
     * Params: $url (string) - URL of page to collect OG tags from
     * Return: Object - associative array containing the OG data in format
     *                  property : content
     **************************************************************************/
    private function get_graph($url){
        //fetch html content from web source and filter to meta data
        $dom = new DOMDocument();
        @$dom->loadHtmlFile($url);
        $tags = $dom->getElementsByTagName('meta');
        
        //set open graph search tag and return object
        $og_pattern = '/^og:/';
        $graph_content = array();
        
        //for each open graph tag, store in return object as property : content 
        foreach ($tags as $element){
            if (preg_match($og_pattern, $element->getAttribute('property'))){
                $graph_content[preg_replace($og_pattern, '', $element->getAttribute('property'))] = $element->getAttribute('content');
            }
        }
        
        //store all open graph tags
        return $graph_content;
    }
    
    /***************************************************************************
     * Function: Get One Tag
     * Description: Fetches the content of one OG tag
     * Return: String - the content of one requested OG tag
     **************************************************************************/
    public function get_one($element){
        return self::$og_content[$element];
    }
    
    /***************************************************************************
     * Function: Get All Tags
     * Description: Fetches the content of one OG tag
     * Return: Object - The entire OG associative array
     **************************************************************************/
    public function get_all(){
        return self::$og_content;
    }
}
?>