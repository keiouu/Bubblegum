<?php
/**
 * Statistics Models
 * 
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class Statistics_Visit extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("location", new CharField(500, ""));
		$this->add_field("ip", new CharField(16, "0.0.0.0"));
		$this->add_field("browser", new CharField(500, ""));
		$this->add_field("browser_version", new CharField(500, ""));
		$this->add_field("cookies_enabled", new BooleanField(true));
		$this->add_field("javascript_enabled", new BooleanField(true));
		$this->add_field("platform", new CharField(500, ""));
		$this->add_field("timestamp", new DateTimeField(true));
		$this->add_field("spider", new BooleanField(false));
	}
	
	public static function generate() {
		// TODO - cache
		$stats = array(
			"os" => array(),
			"browsers" => array(),
			"js" => array("enabled" => 0, "disabled" => 0),
			"cookies" => array("enabled" => 0, "disabled" => 0),
		);
		foreach (Statistics_Visit::objects() as $object) {
			// Browsers
			if (!isset($stats["browsers"][$object->browser]))
				$stats["browsers"][$object->browser] = 0;
			$stats["browsers"][$object->browser]++;
			
			// OS
			if (!isset($stats["os"][$object->platform]))
				$stats["os"][$object->platform] = 0;
			$stats["os"][$object->platform]++;
			
			// JS
			if ($object->javascript_enabled)
				$stats["js"]["enabled"]++;
			else
				$stats["js"]["disabled"]++;
			
			// Cookies
			if ($object->cookies_enabled)
				$stats["cookies"]["enabled"]++;
			else
				$stats["cookies"]["disabled"]++;
		}
		return $stats;
	}
	
	public static function as_percentage($dataset, $name) {
		if (!isset($dataset[$name]))
			return 0;
		
		$total = 0;
		foreach ($dataset as $_name => $count)
			$total += $count;
		return round(((float)$dataset[$name] / (float)$total) * 100.0, 2);
	}
}

class Statistics_Error extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("location", new CharField(500, ""));
		$this->add_field("timestamp", new DateTimeField(true));
	}
}

?>

