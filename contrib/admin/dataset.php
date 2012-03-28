<?php
/*
 * Tikapot Datasets
 */

require_once(home_dir . "framework/utils.php");

class DataSet
{
	protected $model, $queryset, $headings, $linked_headings;
	
	public function __construct($model, $queryset, $headings = null, $linked_headings = null) {
		if (is_array($this->queryset))
			throw new Exception($GLOBALS["i18n"]["admin"]["dataset_error0"]);
		$this->model = $model;
		$this->queryset = $queryset;
		$this->headings = $headings;
		$this->linked_headings = $linked_headings;
	}
	
	public function get_value($data, $heading) {
		if (starts_with($heading, "call_")) {
			$func = substr($heading, strlen("call_"));
			return $data->$func();
		}
		return $data->get_field($heading);
	}
	
	public function get_count() {
		return count($this->queryset);
	}
	
	public function get_pages($limit = 25) {
		return max(ceil($this->get_count() / $limit), 1);
	}
	
	public function get_page($number = 1, $limit = 25) {
		if ($this->get_pages($limit) < $number || $number < 0) {
			$number = $this->get_pages($limit);
		}
		$data = $this->get_data();
		return array_slice($data, ($number - 1) * $limit, $limit);
	}
	
	public function get_headings() {
		return $this->headings;
	}
	
	public function get_linked_headings() {
		return $this->linked_headings;
	}
	
	public function get_data() {
		return $this->queryset->all();
	}
}
?>
