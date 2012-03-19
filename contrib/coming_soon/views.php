<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");

class ComingSoonView extends TemplateView
{
	public function setup($request, $args) {
		$days = "??"; $hours = "??"; $minutes = "??"; $seconds = "??";

		try {
			$date = DateTime::createFromFormat("d/m/Y H:i", ConfigManager::get_app_config("coming_soon", "release_date"));
			$dnow = new DateTime("now");
			if ($date && ($date > $dnow)) {
				$timediff = $date->diff($dnow);
				$days = $timediff->days;
				$hours = $timediff->h;
				$minutes = $timediff->i;
				$seconds = $timediff->s;
			} else {
				if (!$date)
					console_log("Warning: coming soon release date (" . ConfigManager::get_app_config("coming_soon", "release_date") . ") must be in the following format: dd/mm/yyyy hh:mm");
			}
		} catch (Exception $e) {}

		$this->register_var("days", $days);
		$this->register_var("hours", $hours);
		$this->register_var("minutes", $minutes);
		$this->register_var("seconds", $seconds);
		
		return parent::setup($request, $args);
	}
}
?>

