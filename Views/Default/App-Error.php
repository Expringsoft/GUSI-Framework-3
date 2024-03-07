<?php

use App\Core\Framework\Classes\Strings;
use App\Core\Server\Actions;

?>

<!DOCTYPE html>
<html lang="<?php echo Actions::getDisplayLang() ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex" />
	<title><?php echo Actions::printLocalized(Strings::INTERNAL_SERVER_ERROR) ?></title>
	<link rel="stylesheet" href="<?php echo Actions::printCSS("core.css") ?>">
</head>

<body>
	<h1><?php echo Actions::printLocalized(Strings::INTERNAL_SERVER_ERROR) ?></h1>
	<h4><?php echo Actions::printLocalized(Strings::APP_INTERNAL_SERVER_ERROR_EXPLAIN) ?></h4>
</body>

</html>