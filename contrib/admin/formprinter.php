<?php
/*
 * Tikapot Admin Form Printer
 */

require_once(home_dir . "framework/form_printers.php");
require_once(home_dir . "framework/utils.php");
require_once(home_dir . "contrib/admin/core.php");

class AdminFormPrinter extends FormPrinter
{
	public function run($form, $edit_mode = false, $buttons = "") {
		print $form->get_header();
		$formid = $form->get_form_id();
		foreach ($form->get_fieldsets() as $fieldset_name => $fieldset) {
			if ($fieldset_name !== "control")
				print '<fieldset>' . ($fieldset->get_legend() !== "" ? '<legend>' . $fieldset->get_legend() . '</legend>' : '');
			
			foreach ($fieldset->get_fields() as $name => $field) {
				if ($field->get_type() == "hidden") {
					print $field->get_input($fieldset->get_id($formid), $name);
					continue;
				}
				
				print '<div class="control-group'.($field->has_error() ? ' error' : '').'"><div class="controls">';
				
				// Print our label
				if (get_class($field) == "CheckedFormField")
					print '<label class="checkbox" for="'.$fieldset->get_id($formid).'_'.$name.'">';
				else
					print $field->get_label($fieldset->get_id($formid), $name);
				
				// Print the input
				$options = $field->get_options();
				$classes = "";
				if (isset($options['xlarge']) && $options['xlarge'])
					$classes .= "xlarge";
				print $field->get_input($fieldset->get_id($formid), $name, $classes);
				
				// If its an FK field, give the option to add a new object
				if (get_class($field) == "FKFormField") {
					$model_string = $field->get_model_string();
					list($m_app, $n, $m_class) = partition($model_string, '.');
					if (AdminManager::is_registered($m_app, $m_class)) {
						print '&nbsp; <a href="'.home_url.'admin/'.$m_app.'/'.$m_class.'/add/" target="_blank"><i class="icon-plus"></i></a>';
					}
				}
				
				// Error messages
				if ($field->has_error())
					print '<span class="help-inline">' . $field->get_error_html($fieldset->get_id($formid), $name) . '</span>';
				
				// Help Text
				if ($field->has_helptext())
					print '<span class="help-inline">' . $field->get_helptext() . '</span>';
				
				// Close the label if this is a checkbox
				if (get_class($field) == "CheckedFormField")
					print prettify($field->get_name()) . '</label>';
				
				print '</div></div>';
			}
			
			if ($fieldset_name !== "control")
				print '</fieldset>';
		}
		print '<div class="form-actions">';
		if ($buttons === "") {
			print '<input type="submit" name="submit" value="'.$GLOBALS["i18n"]["admin_submit1"].'" class="btn btn-primary" /> ';
			print '<input type="submit" name="submit_stay" value="'.($edit_mode ? $GLOBALS["i18n"]["admin_submit4"] : $GLOBALS["i18n"]["admin_submit3"]).'" class="btn" /> ';
		} else {
			foreach ($buttons as $key => $button) {
				print '<input type="submit" name="'.$key.'" value="'.prettify($GLOBALS["i18n"]["admin_".$button]).'" class="btn primary" /> ';
			}
		}
		print '</div>';
		print '</form>';
	}
}
?>
