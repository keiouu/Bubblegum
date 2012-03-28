<?php
/*
 * Tikapot error View
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/views/html.php");

class ErrorView extends BasicHTMLView {
	public function __construct($url = "", $title = "", $style = "", $script = "", $meta = "") {
		$style .= '<style type="text/css">
			body {
				margin: 0;
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
				font-size: 13px;
				font-weight: normal;
				line-height: 18px;
				color: #404040;
			}
			.container {width: 50%; margin: auto auto; text-align: center;}
			.left, .stack {text-align: left;}
			
			.stack {line-height: 6px; padding-bottom: 10px;}
			.stack ul {line-height: 17px;}
		</style>';
		parent::__construct($url, $title, $style, $script, $meta);
	}
	
	public function render($request, $error) {
		print '<div class="container">';
		print '<h1>' . $request->i18n['framework']["stack_title"] . '</h1>';
		print '<h3>' . $request->i18n['framework']["stack_desc"] . '</h3>';
		print '<p>' . $request->i18n['framework']["stack_err"] . '<br /><strong>' . $error->getMessage() . '</strong></p>';
		print '<h2 class="left">' . $request->i18n['framework']["stack"] . '</h2>';
		foreach ($error->getTrace() as $issue) {
			print '<div class="stack">';
			print '<p class="file"><strong>' . $request->i18n['framework']["stack_file"] . '</strong> ' . $issue["file"] . ' (' . $request->i18n['framework']["stack_line"] . ' '. $issue["line"].')</p>';
			print '<p class="func"><strong>' . $request->i18n['framework']["stack_func"] . '</strong> ' . $issue["function"] . '</p>';
			print '<p class="args"><strong>' . $request->i18n['framework']["stack_args"] . '</strong><ul>';
			foreach ($issue["args"] as $arg) {
				print '<li>';
				if (is_array($arg)) {
					print_r($arg);
				} else {
					if (!is_object($arg) || (is_object($arg) && method_exists($arg, '__toString')))
						print $arg;
					else
						print $request->i18n['framework']['stack_objtyp'] . ' "' . get_class($arg) . '"';
				}
				print '</li>';
			}
			print '</ul></p>';
			print '</div>';
		}
		print '</div>';
	}
}

?>
