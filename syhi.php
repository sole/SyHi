<?php
/*
Plugin Name: SyHi
Version: 0.0.4
Plugin URI: http://soledadpenades.com/projects/wordpress/syhi
Author: Soledad Penadés
Author URI: http://soledadpenades.com
Description: Minimalistic Syntax Highlighter plug-in which also makes sure code can still be copied and pasted into your favourite compiler!

This plug-in is totally designed for developers.

You know, that sort of people who post code snippets in their blog regularly and hardly use the Visual Editor,
because they write the HTML themselves. But they don't like WordPress messing with their quotes and dashes,
so this plug-in makes sure no one touches one of their lovingly crafted lines of code.

It's also very minimalistic - less than 10k, not counting the GeSHi library which is also [partially] included.

In addition, it's also lazy. It will try to avoid doing as much work as possible.
That is good, because it will not eat resources like other plug-ins out there.

Please have a look at the included readme.txt file for more details on features, usage, etc.

*/

/*  Copyright 2009-2010 Soledad Penades

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if(!class_exists('SyHi'))
{

class SyHi
{
	protected $code_blocks;
	protected $geshi_instance = null;

	public function __construct()
	{
		// Execute pre and post process functions before and after each post content is processed
		add_filter('the_content', array(&$this, 'pre_process'), 2);
		add_filter('the_content', array(&$this, 'post_process'), 1000);

		// Same for each comment
		add_filter('comment_text', array(&$this, 'pre_process'), 2);
		add_filter('comment_text', array(&$this, 'post_process'), 1000);

		// Add the css stylesheet link to the <head>
		add_action('wp_head', array(&$this, 'add_css'), 1);
	}

	public function install()
	{
		if (version_compare(phpversion(),"5.0.0","<"))
		{
			deactivate_plugins(__FILE__); // Make sure it doesn't show up in the activated plug-ins list
			trigger_error(__('This plug-in requires php5 or higher', 'SyHi'), E_USER_ERROR); // And crash!
		}
	}

	public function pre_process($text)
	{
		$this->code_blocks = array();
		
		if(stripos($text, '<code') === false)
		{
			// Be lazy as a plug-in can be
			return $text;
		}

		while(true)
		{
			// The reason why I'm doing this sort of '2 phase matching' is due to a nasty PCRE 'bug'/behaviour
			// where if there's a huge (like ~8kb) match with recursivity involved, you're likely to get
			// nasty php segmentation faults in 99% of hosts out there (actually, I still haven't seen one which
			// doesn't have this "feature")
			
			if(!preg_match('@<code( lang="(.+?)")?>@i', $text, $matches))
			{
				break;
			}

			@ list($start_tag, , $lang) = $matches;

			if(!preg_match('@</code>@i', $text, $matches))
			{
				break;
			}

			list($end_tag) = $matches;

			$start_len = strlen($start_tag);
			$end_len = strlen($end_tag);
			$start_pos = stripos($text, $start_tag);
			$end_pos = stripos($text, $end_tag) + $end_len;

			$code_block = substr($text, $start_pos, $end_pos - $start_pos);
			$block_contents = substr($text, $start_pos + $start_len, strlen($code_block) - $end_len - $start_len );
			$hash = md5($code_block . count($this->code_blocks));
			
			$text = str_replace($code_block, '<p>'.$hash.'</p>', $text);

			$block_contents = $this->geshi_format(trim($block_contents), $lang);
			$formatted_block = sprintf('<div class="syhi_block"><code>%s</code></div>', $block_contents);

			// The reason why the hash is surrounded by <p></p> is because we do not want the final code
			// to get wrapped by a pair of <p></p> when wp_texturize is executed later on
			$this->code_blocks['<p>'.$hash.'</p>'] = $formatted_block;
		}

		return $text;
	}

	public function geshi_format($block, $lang)
	{
		$geshi_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libs';
		// This check looks kind of silly, but maybe another plug-in included another GeSHi already!
		// If we do not get to include our version, fingers crossed it has a compatible API :-P
		if(!class_exists('Geshi'))
		{
			require_once($geshi_path . DIRECTORY_SEPARATOR . 'geshi.php');
		}

		// Deal with the lang, make sure it exists. GeSHi has an unbelievable tendency to bork otherwise.
		// Also make sure no one enters a compromising value
		$lang = strtolower(preg_replace('/([^\w])/', '_', $lang));
		if(empty($lang)) { $lang = 'text'; }
		else
		{
			if('html' == $lang) { $lang = 'html4strict'; }
			else
			{
				$ruleset_file = $geshi_path . DIRECTORY_SEPARATOR . 'geshi' . DIRECTORY_SEPARATOR .  $lang . '.php';

				if(!file_exists($ruleset_file))
				{
					$lang = 'text';
				}
			}
		}

		// Reuse, reuse, reuse!
		if(is_null($this->geshi_instance))
		{
			$this->geshi_instance = new Geshi($block, $lang);
			$this->geshi_instance->set_header_type('GESHI_HEADER_NONE');
		}
		else
		{
			$this->geshi_instance->set_source($block);
			$this->geshi_instance->set_language($lang);
		}

		return $this->geshi_instance->parse_code();
	}

	public function post_process($text)
	{
		// Pick the code blocks we set apart before and place them into the final page
		// Hopefully no one else will touch them!
		$text = str_replace(array_keys($this->code_blocks), array_values($this->code_blocks), $text);
		return $text;
	}

	public function add_css($eh)
	{
		$css = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'syhi.css');
		echo '<link rel="stylesheet" href="'.$css.'" type="text/css" media="screen" />', "\n";
	}
}

}

if (class_exists('SyHi'))
{
	$syhi_instance = new SyHi();
	register_activation_hook( __FILE__, array(&$syhi_instance, 'install') );
}

?>
