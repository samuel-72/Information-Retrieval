<?php
namespace assignments\composer_project;

use seekquarry\yioop\library\PhraseParser;
use seekquarry\yioop\locale\en_US\resources\Tokenizer;

function globFiles($dirName,$typeOfFiles) 
{
    $listOfFiles = glob(rtrim($dirName)."/*.".$typeOfFiles."*");
    $files = [];
    foreach ($listOfFiles as $filename) 
    {
        $files[] += explode('.',(explode("/",$filename)[1]))[0] ;
    }
    natsort($files);
    return($files);
}
    
function mapTermToDocid($files,$invertedIndex)
{
    $termDocMapping = [];
    foreach($files as $docId)
    {
        foreach($invertedIndex as $term => $docNo)
        {
            if ( !array_key_exists( $term,$termDocMapping ) ) {
                if (array_key_exists( $docId,$invertedIndex[$term] )) {
                    $termDocMapping[$term] = [str_pad((string)$docId,2,"0",STR_PAD_LEFT)];
                }
            }
            else {
                if (array_key_exists( $docId,$invertedIndex[$term] )) {
                    array_push($termDocMapping[$term],str_pad((string)$docId,2,"0",STR_PAD_LEFT)); 
                }
            }
        }
    }
    ksort($termDocMapping);
    print_r($termDocMapping);
}

function mapDocidToDS($dirName)
{
    $files = glob(rtrim($dirName)."/*.txt");
    if ( count($files) < 1) {
        echo ("There are no files in the given directory.\n\nProgram will exit now." . PHP_EOL . PHP_EOL );
        exit();
    }
    natsort($files);
    $docIdMapping = [];
    $tokenizationMethod = 'stem';
    foreach( $files as $fname ) {
        $fileContentsWithPunctuations = explode(' ',strtolower(file_get_contents($fname)));
        $fileContents = preg_replace("#[[:punct:]]#", "", $fileContentsWithPunctuations);
        $position = 0;
        $termFrequency = [];
        /*
        echo("Fname - ".$fname."\n");
        print_r($fileContents);
        */
        foreach ($fileContents as $word) {
            if ( $tokenizationMethod == 'stem' ) {
                // Tokenize the given word by stemming it
                $dummy = PhraseParser::segmentSegment("this is sparta", 'en-US');
                $word = Tokenizer::stem($word);
                if ( !array_key_exists( $word,$termFrequency ) ) {
                    $termFrequency[$word] = 1;
                }
                else {
                    $termFrequency[$word] += 1;
                }         
                $position += 1;
            }
        }
        /*
        print_r($termFrequency);
        echo("Pos : ".$position."\n");
        print_r($docIdMapping);
        echo("\nDoc id - ".$documentId."\n");
        */
        $documentId = explode('.',(explode("/",$fname)[1]))[0] ;
        $docIdMapping[$documentId] = [$position, $termFrequency];
        
    }
    echo("\n\nThis is the final OP.\n\n");
    print_r($docIdMapping);
}
?>