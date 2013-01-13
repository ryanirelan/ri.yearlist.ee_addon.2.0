<?php
/*
================================================================
	Year List
	for EllisLab ExpressionEngine - by Ryan Irelan	
----------------------------------------------------------------
	Copyright (c) 2009 Mijingo, LLC
================================================================
	THIS IS COPYRIGHTED SOFTWARE. PLEASE
	READ THE LICENSE AGREEMENT.
----------------------------------------------------------------
	This software is based upon and derived from
	EllisLab ExpressionEngine software protected under
	copyright dated 2005 - 2008. Please see
	http://expressionengine.com/docs/license.html
----------------------------------------------------------------
	USE THIS SOFTWARE AT YOUR OWN RISK. WE ASSUME
	NO WARRANTY OR LIABILITY FOR THIS SOFTWARE AS DETAILED
	IN THE LICENSE AGREEMENT.
================================================================
	File:			pi.yearlist.php
----------------------------------------------------------------
	Version:		2.1
----------------------------------------------------------------
	Purpose:	  Returns list of years in which there are entries
----------------------------------------------------------------
	Compatibility:	EE 2.0, MSM
----------------------------------------------------------------
	Created:		2009-04-03
	Updated:		2012-07-19 (cherrypj)
================================================================
*/

// -----------------------------------------
//	Information array
// -----------------------------------------

$plugin_info = array(
                 'pi_name'          => 'Year List',
                 'pi_version'       => '2.1',
                 'pi_author'        => 'Ryan Irelan',
                 'pi_author_url'    => 'http://eeinsider.com',
                 'pi_description'   => 'Returns list of years in which there are entries',
                 'pi_usage'         => Yearlist::usage()
               );

// -----------------------------------------
//	Begin class
// -----------------------------------------

class Yearlist
{
    var $return_data;
    var $category;
    var $channel;
    var $site_id;

    // -------------------------------
    // Constructor
    // -------------------------------
    
    function Yearlist ()
    {
		// super object
		$this->EE =& get_instance();		
		
		// --------------------------
		// get the channel parameter
		// --------------------------
		$channel = $this->EE->TMPL->fetch_param('channel');
		if (!$this->EE->TMPL->fetch_param('channel'))
		{
			$error = "You did not provide a channel name, so this will not work!";
			return $error;
		} 	                                                                      
		// ---------------------------
		// get the category parameter
		// ---------------------------
		if ($this->EE->TMPL->fetch_param('category') !=false)
		{
			$category = $this->EE->TMPL->fetch_param('category');
		}
		else
		{
			$category = 'all';
		}                                              
		
		// ---------------------------
		// get the site_id parameter
		// ---------------------------
		if ($this->EE->TMPL->fetch_param('site') !=false)
		{
			$site_id = $this->EE->TMPL->fetch_param('site');
		}
		else
		{
			$site_id = 1;
		}                                              
		
		// ---------------------------
		// Query the database
		// ---------------------------
		$query = $this->EE->db->select('channel_id');
		$query = $this->EE->db->get_where('exp_channels', array('channel_name' => $channel, 'site_id' => $site_id));
		
		// ----------------------------
		// Is this a real channel name?
		// ----------------------------
		if ($query->num_rows == 0)
		{
			$error = "The channel name you provided does not exist. Please check your channel name and try again.";
			return $error;
		}
		
		// ------------------------------
		// Build the query to get years
		// ------------------------------  
		foreach ($query->result() as $row)
		{
			$channel = $row->channel_id;
		}
		
		if ($category == 'all')
		{
			$wheres = array('channel_id' => $channel, 'site_id' => $site_id);
			$this->EE->db->select('year')->distinct()->from('exp_channel_titles')->where($wheres)->order_by('year', 'desc');			
		}
		else
		{
			// --------------------------------------------------
			// if the category is set to something besides all
			// we need to query for only entries that are in that
			// category
			// ---------------------------------------------------                               
			$wheres = array('exp_channel_titles.channel_id' => $channel, 'exp_category_posts.cat_id' => $category, 'exp_channel_titles.site_id' => $site_id);
			
			$this->EE->db->select('exp_channel_titles.year')->distinct()->from('exp_channel_titles')->join('exp_category_posts', 'exp_channel_titles.entry_id = exp_category_posts.entry_id', 'inner')->where($wheres)->order_by('year', 'desc');
		}

		// do the query 
		$query = $this->EE->db->get();   
		
		// ----------------------------
		// Return query and parse tags
		// ----------------------------
		if ($query->num_rows() == 0)
		{
			$this->return_data = "";
		}                           
		else
		{
			foreach ($query->result() as $row)
			{  
				$tagdata = $this->EE->TMPL->tagdata;
				
				foreach ($this->EE->TMPL->var_single as $key => $val)
				{
					if ($key == 'year')
					{
						$tagdata = $this->EE->TMPL->swap_var_single($key, $row->year, $tagdata);
					}
				}                                                                     
				$this->return_data .= $tagdata;
			}
		}
    }
    // END
	
	// -------------------------------
    // Usage
    // -------------------------------

	function usage()
	{
		ob_start(); 
?>
The Year Listing plugin is a simple way to get a distinct 4 digit year for your entries. This way you can list out years for archives.

{exp:yearlist channel="yourchannel" category="1"}

{year}

{/exp:yearlist}

That will return an array of years. Use {year} to print them to the screen and wrap in any markup needed. There are currently no linebreaks or HTML associated with this plugin.

The category parameter is optional and if you leave it out, the plugin will search across all categories. There is currently no support for having multiple categories in the category parameter (e.g. category="3|8|10"). This may come later.

<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 

		return $buffer;
	}
	// END
	
}
// END CLASS
?>