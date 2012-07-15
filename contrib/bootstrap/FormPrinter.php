<?php
/*
 * Tikapot Forms
 *
 */


require_once(home_dir . "framework/form_printers.php");

class BootstrapFormPrinter extends FormPrinter
{
	public function run($form, $action = "", $method = "", $submit_text = "", $classes = 'form-horizontal') {
		print $form->get_header($action, $method, ' class="'.$classes.'"');
		$formid = $form->get_form_id();
		foreach ($form->get_fieldsets() as $fieldset) {
			print '<fieldset>';
			if ($fieldset->get_legend() !== "")
				print '<legend>' . $fieldset->get_legend() . '</legend>';
			foreach ($fieldset->get_fields() as $name => $field) {
				if ($field->get_type() == "hidden") {
					print $field->get_input($fieldset->get_id($formid), $name);
				} else {
					print '<div class="control-group">';
					print $field->get_label($fieldset->get_id($formid), $name, ' class="control-label"');
					print '<div class="controls">' . $field->get_input($fieldset->get_id($formid), $name);
					$help = $field->get_error_html($fieldset->get_id($formid), $name);
					if (strlen($help) > 0) {
						$help = '<p class="help-block">' . $help . '</p>';
					}
					print  $help . '</div></div>';
				}
			}
			print '</fieldset>';
		}
		print '<fieldset>';
		print '<div class="form-actions">
			<button type="submit" class="btn btn-primary">' . (strlen($submit_text) > 0 ? $submit_text : $GLOBALS['i18n']['framework']["submit"]) . '</button>
		</div>';
		print '</fieldset>';
		print '</form>';
	}
}

class SimpleBootstrapFormPrinter extends FormPrinter
{
	public function run($form, $action = "", $method = "", $submit_text = "", $classes = '') {
		print $form->get_header($action, $method, ' class="'.$classes.'"');
		$formid = $form->get_form_id();
		foreach ($form->get_fieldsets() as $fieldset) {
			print '<fieldset>';
			if ($fieldset->get_legend() !== "")
				print '<legend>' . $fieldset->get_legend() . '</legend>';
			foreach ($fieldset->get_fields() as $name => $field) {
				if ($field->get_type() == "hidden") {
					print $field->get_input($fieldset->get_id($formid), $name);
				} else {
					print '<div class="control-group">';
					print $field->get_label($fieldset->get_id($formid), $name, ' class="control-label"');
					print '<div class="controls">' . $field->get_input($fieldset->get_id($formid), $name);
					$help = $field->get_error_html($fieldset->get_id($formid), $name);
					if (strlen($help) > 0) {
						$help = '<p class="help-block">' . $help . '</p>';
					}
					print  $help . '</div></div>';
				}
			}
			print '</fieldset>';
		}
		print '<fieldset>';
		print '<button type="submit" class="btn btn-primary">' . (strlen($submit_text) > 0 ? $submit_text : $GLOBALS['i18n']['framework']["submit"]) . '</button>';
		print '</fieldset>';
		print '</form>';
	}
}
?>
