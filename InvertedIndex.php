<?php

ini_set('error_reporting', E_ALL);

echo  ("This program prints an inverted index for a set of documents".PHP_EOL.PHP_EOL);
echo  ("This program strips all punctuation characters from the text for the purpose of creating an inverted index.\n".PHP_EOL.PHP_EOL);

$document_id = 0;
$termFrequency = [];
$termDocMapping = [];
$termDocPositionMapping = [];
    
function createInvertedIndex($dirName)
{
    global $document_id, $termFrequency, $termDocMapping, $termDocPositions, $termDocPositionMapping;    
    
    echo (PHP_EOL . PHP_EOL . "The directory name provided is - " . $dirName . PHP_EOL . PHP_EOL  );
    $files = glob(rtrim($dirName)."/*.txt");
    
    if ( count($files) < 1) {
        echo ("There are no files in the given directory.\n\nProgram will exit now." . PHP_EOL . PHP_EOL );
        exit();
    }
    
    foreach( $files as $fname ) {
        echo ( "\n\nFilename - " . $fname . PHP_EOL . PHP_EOL) ;

        $fileContentsWithPunctuations = explode(' ',file_get_contents($fname));
        
        $fileContents = preg_replace("#[[:punct:]]#", "", $fileContentsWithPunctuations);
        
        $position = 0;
        $termDocPositions = [];
        
        foreach ($fileContents as $word) {          
            // Get the term frequency
            if ( !array_key_exists( $word,$termFrequency ) ) {
                $termFrequency[$word] = 1;
            }
            else {
                $termFrequency[$word] += 1;
            }
                        
            // Get the term mapped with the document id, this will be used for finding the no of docs the term is associated with
            if ( !array_key_exists( $word,$termDocMapping ) ) {
                $termDocMapping[$word] = [$document_id]  ;
            }
            else {
                $termDocMapping[$word][]  = $document_id;
            }
            // Get all positions where the word occurs in the document
            if ( !array_key_exists( $word,$termDocPositions ) ) {
                $termDocPositions[$word] = (string)$position ;
            }
            else {
                $termDocPositions[$word]  .= "," . (string)$position ;
            }            
            $position += 1;
        }
        foreach( $termDocPositions as $terms => $positions ) {
            if ( !array_key_exists( $terms,$termDocPositionMapping ) ) {
                $termDocPositionMapping[$terms] = [$document_id => $termDocPositions[$terms]];
            }
            else {
                $termDocPositionMapping[$terms] += [$document_id => $termDocPositions[$terms]];
            }
        }
        $document_id += 1;
    }
    echo (PHP_EOL . PHP_EOL . "Please find below the inverted index" .PHP_EOL . PHP_EOL );

    foreach($termDocPositionMapping as $terms => $docs) {
        echo (PHP_EOL . $terms . ":" . count($docs) . ":" . $termFrequency[$terms] . ":");
        $docPositions = "";
        foreach($docs as $doc => $positions) {
            $docPositions .= ( "(" . $doc . "," . $positions . ")," );
        }
        echo (rtrim($docPositions,','));
    }
}

// Read the directory name from the command line
if (PHP_SAPI === 'cli') {
    $argument1 = $argv[1];
}
else {
    $argument1 = ($_GET['argument1']);
}

// Call this function to read all files inside the provided directory
createInvertedIndex($argument1);

?>
