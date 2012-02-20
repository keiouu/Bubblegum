<?php
/*
 * Tikapot Twitter App
 *
 */

require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/template_tags/tag.php");

class Twitter
{
	public function get($screen_name, $count=3, $useSSL=true) {
		$url = "http".($useSSL ? "s" : "")."://api.twitter.com/1/statuses/user_timeline.json?screen_name=".$screen_name."&count=" . $count;
		$str = file_get_contents($url);
		return json_decode($str);
	}
	
	public function display($screen_name, $count=3, $useSSL=true) {
		$json = $this->get($screen_name, $count, $useSSL);
		foreach($json as $obj) {
			$tweet = htmlentities($obj->text, ENT_QUOTES);
			$tweet = preg_replace('/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="http://$1" target="_blank">http://$1</a>', $tweet);
			$tweet = preg_replace('/@([a-z0-9_]+)/i', '<a href="http'.($useSSL ? "s" : "").'://twitter.com/$1" target="_blank">@$1</a>', $tweet);
			print '<div class="tweet-container"><a href="http'.($useSSL ? "s" : "").'://www.twitter.com/#!/'.$obj->user->screen_name.'"><img src="'.($useSSL ? $obj->user->profile_image_url_https : $obj->user->profile_image_url).'" alt="Twitter Profile Image" class="twitter_profile_img" /></a><p class="tweet">' . $tweet . '</p></div>';
		}
	}
}

function twitter_init($request) {
	$request->twitter = new Twitter();
}

SignalManager::hook("page_load_setup", "twitter_init");

class TwitterTag extends TplTag
{
	public function render($request, $args, $page) {
		preg_match_all('/{% twitter "(?P<username>[\s[:punct:]\w]+?)" (?P<max>[0-9]+?) %}/', $page, $matches, PREG_SET_ORDER);
		foreach($matches as $val) {
			$result = $request->twitter->get($val['username'], $val['max']);
			$page = preg_replace('/{% twitter "'.$val['username'].'" '.$val['max'].' %}/', $result, $page);
		}
		return $page;
	}
}

TwitterTag::register();
?>
