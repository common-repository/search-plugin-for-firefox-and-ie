<?php 
/*
Plugin Name: Search Plugin for Firefox and IE
Plugin URI: http://www.informaniaci.it/2009/06/14/search-firefox-ie-plugin-wordpress/
Description:This plugin allows your visitors to easily install the search bar of your site in their browser. The plugin also allowsyou to customize the search bar with the name and image on your web site.
Version: 1.0
Author: Davide Alocci
Author URI: http://www.informaniaci.it

Copyright (C) 2009 Informaniaci.it



This program is free software; you can redistribute it and/or modify

it under the terms of the GNU General Public License as published by

the Free Software Foundation; either version 3 of the License, or

(at your option) any later version.



This program is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

GNU General Public License for more details.



You should have received a copy of the GNU General Public License

along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
add_action("widgets_init", array('SearchForBrowser', 'register'));
register_activation_hook( __FILE__, array('SearchForBrowser', 'activate'));
register_deactivation_hook( __FILE__, array('SearchForBrowser', 'deactivate'));

// Widget Class
class SearchForBrowser {
	
  function activate(){
    $data = array( 'title' => '' ,'image_button_search' => '');
    if ( ! get_option('SearchForBrowser')){
      add_option('SearchForBrowser' , $data);
    } else {
      update_option('SearchForBrowser' , $data);
    }
  }
  function deactivate(){
    delete_option('SearchForBrowser');
  }
  function control(){
  		$ind_base = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 	 
	    $ind_button = $ind_base."search_button.gif";
		$data = get_option('SearchForBrowser');
	
		if (isset($_POST['search_submit']))
	       {
		      $img_button = stripslashes($_POST['img_button']);
		      $wid_title = stripslashes($_POST['title']);
		      if(strcmp($img_button, "") != 0  )
	 	        {
			        $data['title'] = $wid_title;
			        $data['image_button_search'] = $img_button;
    		        update_option('SearchForBrowser', $data);
				}
		      else
		        {	
				    $data['title'] = $wid_title;
    		        $data['image_button_search'] = $ind_button;
			        update_option('SearchForBrowser', $data);
			     }
	       }?>
	
                <div >
                <label  style="line-height:35px;display:block;">Title: <input type="text"  name="title" value="<?php echo $data['title']; ?>" style="width: 95%" /></label><br />
				<label  style="line-height:35px;display:block;">Image path: <input type="text"  name="img_button" value="<?php echo $data['image_button_search']; ?>" style="width: 95%" /></label><br />
                <label  style="line-height:15px;display:block;">Enter the url of the image to display in the widget or leave blank for use the default image </label><br />
                 <input type="hidden" name="search_submit"  id="search_submit" value="1" />
		        </div>  
		<?php
  }
  
  function widget($args){
  
   if ( !defined('WP_CONTENT_URL') )

    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

   if ( ! defined( 'WP_PLUGIN_URL' ) )

      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

	$ind_base = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 	 
	$ind_file = $ind_base."searchplugin.xml";
	$data = get_option('SearchForBrowser');
	echo $args['before_widget'];
  	$title = $data['title'];
 	if(strcmp($title, "") != 0  ){
         echo $args['before_title'] . $title . $args['after_title'];
	               }
  
	?> <table align="center">
       <tr>
       <td><a href="javascript:if (window.external) {window.external.AddSearchProvider( '<?php echo $ind_file; ?>' )} else {alert('Your browser do not support search plugin');};;"><img src="<?php echo $data['image_button_search']; ?>" alt="Search for Firefox and IE" ></a></td>
       </tr>
       <tr>
       <td align="right"><div class="search_for_browser" style="float:right; font-size:9px;"> <a href="http://www.informaniaci.it"> by 1N </a></div></td>
      </tr>
      </table>

<?
  
    echo $args['after_widget'];
  }
  function register(){
    register_sidebar_widget('Search For Browser', array('SearchForBrowser', 'widget'));
    register_widget_control('Search For Browser', array('SearchForBrowser', 'control'));
  }
}

add_action('admin_menu', 'search_admin_menu');  
function search_admin_menu()  
{  
	if (function_exists('add_options_page')) {
	add_options_page('Search Browser', 'Search Browser', 5, basename(__FILE__), 'search_options_page');
		}
}  

// Funzione per creare pagina di amministrazione



function search_options_page()
{
 	$updated = false;
	
	if ( !defined('WP_CONTENT_URL') )

    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

	if ( ! defined( 'WP_PLUGIN_URL' ) )

      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	
 	if (isset($_POST['submit']))  
        
		{  
 
	 	$shortname = ($_POST['shortname']);
	 	$desc = ($_POST['desc']);
	 	$tag = ($_POST['tag']);
	 	$urlsite = str_replace("/wp-content/plugins","",WP_PLUGIN_URL); 
	 	$urlsite = stripslashes($urlsite."/?s={searchTerms}");
	 	$urlimage = stripslashes(($_POST['urlimage']));
 	 	if (isset($_POST['adultcontent']))
			{
			$adultcontent = 'true';
			}
		else
			{
			$adultcontent = 'false';
			}
		
	 	$search_file = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 	 
	 	$search_file = $search_file."searchplugin.xml";
	 	$ind_file = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$ind_antipixel = $ind_file ."pixelbutton.gif";
	 	$ind_file = $ind_file ."searchplugin.xml";
	 
	 	$xml = simplexml_load_file($search_file);
	 	$xml->Url[0]['template'] = $urlsite;
	 	$xml->ShortName = $shortname;
	 	$xml->Description = $desc;
	 	$xml->Tags = $tag;
	 	$xml->LongName = $shortname;
	 	$xml->Image[1] = $urlimage;
	 	$xml->AdultContent = $adultcontent;
 	 	writeXML($search_file, $xml);
	 	$updated = true;  
	 	}

 if ($updated)  
         {  
                 ?>  
              <div class="updated">
              <p><strong> Options Update</strong></p><br />
              <p><strong> How insert the plugin code in your wordpress theme without using the widget :</strong></p>
              <p>Add  the code below in the &quot;header.php&quot; file.  When someone visit your web site, a message advises him that there is a &quot;Search Plugin&quot; for is browser. </p>
                 <p><strong>&lt;link rel=&quot;search&quot; href=&quot;<?php echo $ind_file; ?>&quot; type=&quot;application/opensearchdescription+xml&quot; title=&quot;<?php echo $shortname; ?>&quot; /&gt;</strong></p>
                 
                 <p>Add  the code below in your template. When a visitor click on the link, a message advises him that there is a &quot;Search Plugin&quot; for is browser. </p>
               <p><strong>&lt;a href=&quot;javascript:if (window.external) {window.external.AddSearchProvider( '<?php echo  $ind_file; ?>' )} else {alert('Type Here a message of error like: Your browser do not support search plugin');};;&quot;&gt; Insert a text or image Here&lt;/a&gt;</strong></p>
                 
                 
                 
                 
                 
</div>
                  
                  
        <?php  
       }  

	$ind_antipixel = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$ind_antipixel = $ind_antipixel ."pixelbutton.gif";
	  
   
?>
         <div class="wrap">  
                <h2>Search Plugin </h2>  
                 <form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">  
    
                         <fieldset class="options">  
   
                                 <legend>Search Plugin for Firefox and IE</legend> <br /> 
                                
                                 
                                 <table width="100%" cellspacing="2" cellpadding="5"  class="editform">  
                         <tr valign="middle">  
                                         <th width="18%" scope="row">Site Name:</th>  
                                         <td width="82%"><input name="shortname" type="text" width="250"/>  
                                    Insert the name of your web site</td>  
                                 		
                                 </tr>  
                                 <tr valign="middle">  
                                         <th width="18%" scope="row">Description:</th>  
                                         <td><input name="desc" type="text" width="250" value=""/>                                          
                                            Insert the description of your web site
</td>  
                                 		
                                 </tr>  
                                 <tr valign="middle">  
                                         <th width="18%" scope="row">Tags:</th>  
                                         <td><input name="tag" type="text" width="250"/>                                           
                                           Insert some Tags of your web site separated by commas ( Ex: tag1,tag2,tag3)</td> 
                                                               		
                                 </tr>                                
                           <tr valign="middle">  
                                         <th width="18%" scope="row">Image Url:</th>  
                                         <td><input name="urlimage" type="text" width="250" value="http://"/> 
                                         Enter the location of your favicon  
                                   ( height=&quot;16&quot; width=&quot;16&quot; )</td> 
                              	
                                 </tr>  
                                 <tr valign="middle">  
                                         <th width="18%" scope="row">Adult Content:</th>  
                                         <td valign="middle"><input name="adultcontent" type="checkbox" width="250" value="false"/> 
                                         Check the box if your web site contains Adult 
                                         Contents</td>  
                         		  </table>  
                         </fieldset> <br />
   
   
                         <p class="submit">  
                           <input type="submit" name="submit" value="Update Options " />  
                         </p>  
                 </form><br />
                 Copyright 2009, <a href="http://www.informaniaci.it" > <img src="<?php echo $ind_antipixel; ?>"  /></a>  Release under GNU General Public License 
    
         </div>
         
 <?php } 
       
function writeXML($nome_file, $oggetto_xml)
{
 file_put_contents($nome_file, $oggetto_xml->asXML());
}



?>