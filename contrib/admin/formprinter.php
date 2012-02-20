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
				print '<div class="clearfix'.($field->has_error() ? ' error' : '').'">';
				print $field->get_label($fieldset->get_id($formid), $name);
				$options = $field->get_options();
				$classes = "";
				if (isset($options['xlarge']) && $options['xlarge'])
					$classes .= "xlarge";
				print '<div class="input">' . $field->get_input($fieldset->get_id($formid), $name, $classes);
				if (get_class($field) == "FKFormField") {
					$model_string = $field->get_model_string();
					list($m_app, $n, $m_class) = partition($model_string, '.');
					if (AdminManager::is_registered($m_app, $m_class)) {
						print '&nbsp; <a href="'.home_url.'admin/'.$m_app.'/'.$m_class.'/add/" target="_blank"><img src="'.home_url.'contrib/admin/media/images/add.png" alt="add" class="fkadd" /></a>';
					}
				}
				if ($field->has_error())
					print '<span class="help-inline">' . $field->get_error_html($fieldset->get_id($formid), $name) . '</span>';
				if ($field->has_helptext())
					print '<span class="help-block">' . $field->get_helptext() . '</span>';
				print '</div></div>';
			}
			
			if ($fieldset_name !== "control")
				print '</fieldset>';
		}
		print '<div class="actions">';
		if ($buttons === "") {
			print '<input type="submit" name="submit" value="'.$GLOBALS["i18n"]["admin_submit1"].'" class="btn primary" /> ';
			print '<input type="submit" name="submit_stay" value="'.($edit_mode ? $GLOBALS["i18n"]["admin_submit4"] : $GLOBALS["i18n"]["admin_submit3"]).'" class="btn" /> ';
			print '<button type="reset" class="btn">'.$GLOBALS["i18n"]["admin_reset"].'</button>';
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
