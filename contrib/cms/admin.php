<?php
/*
 * Admin
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(dirname(__FILE__) . "/models.php");
require_once(dirname(__FILE__) . "/forms.php");

AdminModel::register("CMS", new CMS_Template(), new CMSTemplateAddForm(), null, array("id", "title", "created", "created_by", "updated", "updated_by"), array("id", "title"));
AdminModel::register("CMS", new CMS_Page(), null, null, array("id", "template", "title", "url", "published", "created", "created_by", "updated", "updated_by"), array("id", "title"));
?>

