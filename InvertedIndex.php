<?php

ini_set('error_reporting', E_ALL);

echo nl2br ("This program prints an inverted index for a set of documents.\n");
echo nl2br ("This program strips all punctuation characters from the text for the purpose of creating an inverted index.\n");

$document_id = 0;
$termFrequency = array();
$termDocMapping = array();
$termDocPositionMapping = array();
$termDocPositions = array();
	
function createInvertedIndex($dirName)
{
    global $document_id, $termFrequency, $termDocMapping, $termDocPositions, $termDocPositionMapping;	
	
    echo nl2br("\n\nThe directory name provided is - " . $dirName . "\n\n" );
    $files = glob($dirName."*");
	
    if ( count($files) < 1) {
	    echo nl2br("There are no files in the given directory.\n\nProgram will exit now.\n\n");
	    exit();
    }
	
    foreach( $files as $fname ) {
	    echo nl2br( "\n\nFilename - " . $fname . "\n\n") ;

	    $fileContentsWithPunctuations = explode(' ',file_get_contents($fname));
		
	    $fileContents = preg_replace("#[[:punct:]]#", "", $fileContentsWithPunctuations);
		
	    $position = 0;
	    $termDocPositions = array();
		
	    foreach ($fileContents as $word) {
			//echo ("Word : " . $word . "\tat pos : " . $position );
			
			// Get the term frequency
		    if ( !array_key_exists( $word,$termFrequency ) ) {
			    //$termDocMapping[$word]  = (string)$document_id;
			    $termFrequency[$word] = 1;
		    }
		    else {
			    $termFrequency[$word] += 1;
		    }
						
			// Get the term mapped with the document id, this will be used for finding the no of docs the term is associated with
		    if ( !array_key_exists( $word,$termDocMapping ) ) {
			    //$termDocMapping[$word]  = (string)$document_id;
			    $termDocMapping += array( $word => (string)$document_id  );
		    }
		    else {
			    $termDocMapping[$word]  = $termDocMapping[$word] . "," .(string)$document_id;
		    }
			
			// Get all positions where the word occurs in the document
		    if ( !array_key_exists( $word,$termDocPositions ) ) {
				//$termDocMapping[$word]  = (string)$document_id;
			    $termDocPositions += array( $word => (string)$position  );
		    }
		    else {
			    $termDocPositions[$word]  = $termDocPositions[$word] . "," .(string)$position;
		    }
			
		    $position += 1;
	    }
		
	    foreach( $termDocPositions as $terms => $positions ) {
			//echo nl2br("\nTerm : " . $terms);
			//echo (" - " . $termDocPositions[$terms] . ",");
			
		    if ( !array_key_exists( $terms,$termDocPositionMapping ) ) {
			    $termDocPositionMapping[$terms] = (array($document_id => array($termDocPositions[$terms]) ));
		    }
		    else {
			    $termDocPositionMapping[$terms] += (array($document_id => array($termDocPositions[$terms]) ));
		    }
			//var_dump($termDocPositionMapping);
	    }
		
	    $document_id += 1;
		
		//echo nl2br("The file content is : \n" . $string);
    }
	
    echo nl2br("\n\nGoing to print all term - doc - positions \n\n");
    foreach($termDocPositionMapping as $terms => $doc_pos) {
		//echo nl2br("\nTerm : " . $terms . " Num_Docs : " . count($doc_pos) . " Freq : " . $termFrequency[$terms] . " : ");
	    echo nl2br("\n" . $terms . " : " . count($doc_pos) . " : " . $termFrequency[$terms] . " : ");
	    foreach( $doc_pos as $doc => $positions) {
		    echo ( "( " . $doc . " , ");
		    $p = "";
		    foreach($positions as $pos) {
			    $p = $p . $pos . ", ";
				//echo nl2br( $pos. "," );
		    }
			//$p = rtrim($p,",");
			//echo ("th" . substr($p,0,-2) . "th");
		    echo ( substr($p,0,-2) . " ), " );
	    }
    }
	
	/*
	foreach($termDocMapping as $terms => $docid)
	{
		echo nl2br("\nTerm : " . $terms);
		echo ("    - " . $termDocMapping[$terms] . ",");
		
	}
	
	var_dump($termDocMapping);
	*/
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