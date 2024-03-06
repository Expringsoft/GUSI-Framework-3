<?php

use App\Core\Framework\Classes\Strings;
use App\Core\Server\Actions;
?>

<!DOCTYPE html>
<html lang="<?php echo Actions::getDisplayLang() ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo Actions::printLocalized(Strings::APP_NAME) ?></title>
	<link rel="stylesheet" href="<?php echo Actions::printCSS("core.css") ?>">
</head>

<body>
	<img src="<?php echo Actions::printResource("Images/App_Logo.svg") ?>" alt="GUSI Framework v3">
	<h1><?php echo Actions::printLocalized(Strings::APP_NAME) ?></h1>
	<h4><?php echo Actions::printLocalized(Strings::APP_DESCRIPTION_NO_NAME) ?></h4>
</body>

</html>