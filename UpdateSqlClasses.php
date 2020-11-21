<?php
// From PhpApi

// Script Variables //////////////////////////////////////
if (!file_exists("./env.php")) {
	if (!file_exists("./../env.php")) {
		print "no env.php found. please place your env file in this directory or the parent directory";
		return;
	} else {
		include_once("./../env.php");
	}
} else {
	include_once("./env.php");
}
global $_dsn, $_dbUsername, $_dbPassword;

$dsnValues = array("alive" => "barely");
$dbParams = explode(";", $_dsn);
foreach ($dbParams as $dbParam) {
	if (strlen($dbParam) === 0) {
		continue;
	}
	$keyValuePair = explode("=", $dbParam);
	if (isset($keyValuePair[0]) && isset($keyValuePair[1])) {
		$dsnValues[$keyValuePair[0]] = $keyValuePair[1];
	} else {
		print("ERROR :: cannot assign dsnValue for values" . print_r($dbParam, true) . "\n");
	}
}

$dbName = $dsnValues['dbname'];
$pdo = new PDO($_dsn, $_dbUsername, $_dbPassword);
//////////////////////////////////////////////////////////

/*
What this script does:
	Classes representing sql tables:
		./DbClasses/{Table Name}.php
	And a script importing all Db classes:
		./DbClasses/DbClasses.php
*/

// translate sql data types to php data types
$PhpDataType = array(
	"varchar" => "string",
	"nvarchar" => "string",
	"char" => "string",
	"blob" => "string",
	"text" => "string",
	"tinytext" => "string",
	"smalltext" => "string",
	"mediumtext" => "string",
	"largetext" => "string",
	"tinyint" => "bool",
	"int" => "int",
	"smallint" => "int",
	"mediumint" => "int",
	"bigint" => "int",
	"decimal" => "float",
	"real" => "float",
	"double" => "float",
	"float" => "float",
	"json" => "object",
	"datetime" => "string",
	"date" => "string",
	"timestamp" => "string"
);

// used to designate a paramter as Nullable or not
$Nullable = array(
	"YES" => "?",
	"NO" => ""
);

// this will be the "this file was created automatically" message
$autoCreateMessage = "/* This file was created automatically
Do not alter this file as it may interfere with data handling in your database
If you wish to update this class, make your changes in the database
	then run CreateSqlClasses.php again
*/";

// Get array of table names
$sqlGetTables = "SELECT table_name 
	FROM information_schema.tables 
	WHERE table_schema = ?";
$stmtTables = $pdo->prepare($sqlGetTables);
$stmtTables->execute([$dbName]);
$tableArray = $stmtTables->fetchAll();

// Get array of column date
$sqlGetColumnDefinitions = "SELECT table_name, column_name, is_nullable, data_type 
	FROM information_schema.columns 
	WHERE table_schema = ?";
$stmtColumns = $pdo->prepare($sqlGetColumnDefinitions);
$stmtColumns->execute([$dbName]);
$columnArray = $stmtColumns->fetchAll();

// Create Db Classes
// extends FromArray abstract class
// this assumes you've arleady downloaded that file
foreach ($tableArray as $tableData) {
	$table = $tableData['TABLE_NAME'];
	if (!file_exists("./DbClasses/")) {
		mkdir("./DbClasses/");
	}
	$DbClassFile = fopen("./DbClasses/" . $table . ".php", "w");
	$classText = "<?php\n$autoCreateMessage\n
require_once(__DIR__ . '/../PhpApi/CanFromArray.php');
class " . $table . " extends CanFromArray 
{\n";
	foreach ($columnArray as $column) {
		if ($column['TABLE_NAME'] === $table) {
			$classText .= "\tpublic "
				. $Nullable[$column['IS_NULLABLE']]
				.  $PhpDataType[$column['DATA_TYPE']]
				. " $" . $column['COLUMN_NAME'] . ";\n";
		}
	}
	$classText .= "}\n";
	fwrite($DbClassFile, $classText);
	fclose($DbClassFile);
}

// Create DbClasses.php
$DbClassesFile = fopen("./DbClasses/DbClasses.php", "w");
$dbclassesFileText = "<?php \n$autoCreateMessage\n\n";
foreach ($tableArray as $tableData) {
	$table = $tableData['TABLE_NAME'];
	$dbclassesFileText .= "require_once(__DIR__ . '/" . $table . ".php');\n";
}
fwrite($DbClassesFile, $dbclassesFileText);
fclose($DbClassesFile);
