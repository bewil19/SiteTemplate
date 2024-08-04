<?php

use bewil19\Site\Config;
use bewil19\Site\DatabaseType;

$config = Config::getInstance();
if ([] !== $_POST) {
    $result = $config->install($_POST);
    var_dump($result);
}

$config = $config->getConfig();
?>
<form method="post">
    <div class="form">
    <div class="formbuilder-text form-group field-installPassword">
<label for="installPassword" class="formbuilder-text-label">Install Password<span>*</span></label>
<input type="password" class="form-control" name="installPassword" id="installPassword" required="required" aria-required="true">
</div>
<h1>Database Settings</h1>
<div class="formbuilder-select form-group field-<?php echo DatabaseType::dbType; ?>">
<label for="<?php echo DatabaseType::dbType; ?>" class="formbuilder-select-label">Select database type:</label>
<select class="form-control" name="<?php echo DatabaseType::dbType; ?>" id="<?php echo DatabaseType::dbType; ?>">
<option value="<?php echo DatabaseType::mysql; ?>" selected="true" id="<?php echo DatabaseType::dbType; ?>-0"><?php echo DatabaseType::mysql; ?></option>
</select>
</div>
<div class="formbuilder-text form-group field-<?php echo DatabaseType::dbHost; ?>">
<label for="<?php echo DatabaseType::dbHost; ?>" class="formbuilder-text-label">Host:</label>
<input type="text" class="form-control" name="<?php echo DatabaseType::dbHost; ?>" id="<?php echo DatabaseType::dbHost; ?>" value="<?php echo $config[DatabaseType::dbHost]; ?>">
</div>
<div class="formbuilder-text form-group field-<?php echo DatabaseType::dbPort; ?>">
<label for="<?php echo DatabaseType::dbPort; ?>" class="formbuilder-text-label">Port:</label>
<input type="text" class="form-control" name="<?php echo DatabaseType::dbPort; ?>" id="<?php echo DatabaseType::dbPort; ?>" value="<?php echo $config[DatabaseType::dbPort]; ?>">
</div>
<div class="formbuilder-text form-group field-<?php echo DatabaseType::dbUsername; ?>">
<label for="<?php echo DatabaseType::dbUsername; ?>" class="formbuilder-text-label">Username:</label>
<input type="text" class="form-control" name="<?php echo DatabaseType::dbUsername; ?>" id="<?php echo DatabaseType::dbUsername; ?>" value="<?php echo $config[DatabaseType::dbUsername]; ?>">
</div>
<div class="formbuilder-text form-group field-<?php echo DatabaseType::dbPassword; ?>">
<label for="<?php echo DatabaseType::dbPassword; ?>" class="formbuilder-text-label">Password:</label>
<input type="password" class="form-control" name="<?php echo DatabaseType::dbPassword; ?>" id="<?php echo DatabaseType::dbPassword; ?>" value="">
</div>
<div class="formbuilder-text form-group field-<?php echo DatabaseType::dbName; ?>">
<label for="<?php echo DatabaseType::dbName; ?>" class="formbuilder-text-label">Name:</label>
<input type="text" class="form-control" name="<?php echo DatabaseType::dbName; ?>" id="<?php echo DatabaseType::dbName; ?>" value="<?php echo $config[DatabaseType::dbName]; ?>">
</div>
<div class="formbuilder-button form-group field-submit">
<button type="submit" class="btn-default btn" name="submit" id="submit">Submit</button>
</div>
    </div>
</form>