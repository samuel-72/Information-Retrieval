<?php
namespace assignments\composer_project;

use seekquarry\yioop\library\PhraseParser;
use seekquarry\yioop\locale\en_US\resources\Tokenizer;

require_once("create_inverted_index.php");
require_once("helper_functions.php");
require_once "vendor/autoload.php";
ini_set('error_reporting', E_ALL);

// This variable '$invertedIndex' holds the inverted index that has been created after the tokenization method provided in the command line has been applied on the documents
$invertedIndex;
$totalNoDocs;
if ($argc != 3) {
    echo ("\nInvalid number of arguements passed to the program. Please see usage details below.\n");
    echo ("\nUsage : php index_program.php path_to_folder_to_index index_filename\n");
    exit();
}
$tokenizationMethod = "stem";
$dirName = $argv[1];
$indexFilename = $argv[2];

//Creating an inverted index for all the docs in the given directory
$adt = new Adt();
$adt->createInvertedIndex($dirName, $tokenizationMethod);
$invertedIndex = $adt->getInvertedIndex();
$totalNoDocs = $adt->getTotalNoOfDocs();
$files = globFiles($dirName,"txt");
//print_r($invertedIndex);

mapTermToDocid($files,$invertedIndex);
mapDocidToDS($dirName);
?>
