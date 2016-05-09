<?php
namespace assignments\composer_project;

use seekquarry\yioop\library\PhraseParser;
use seekquarry\yioop\locale\en_US\resources\Tokenizer;

require_once "vendor/autoload.php";
ini_set('error_reporting', E_ALL);
class Adt 
{
    /*
    ** The invertedIndex variable will hold the inverted index with the appropriate tokenization applied.
    ** The queryArray variable will hold the query string tokenized on the appropriate tokenization method.
    ** The static variable uvArray will hold u,v values for computing nextCover.
    */
    public $invertedIndex = [];
    public $queryArray = [];
    public $totalNoDocs = 0;
    static $cacheDocPos = [];
    static $cacheTermPos = [];
    static $cachePos = [];
    static $uvArray = [0,0];
    function getTotalNoOfDocs() 
    {
        return $this->totalNoDocs;
    }
    function getTokenizedQuery() 
    {
        return $this->queryArray;
    }
    function getInvertedIndex() 
    {
        return $this->invertedIndex;
    }
    function createTokenizedQueryArray($query,$tokenizationMethod)
    {
        $tempQueryContents = explode(' ',strtolower($query));
        $tempQueryContents = preg_replace("#[[:punct:]]#", "", $tempQueryContents);
        $query = [];
        foreach($tempQueryContents as $queryWord) {
            if ( $tokenizationMethod == 'stem' ) {
                    // Tokenize the given word by stemming it
                    $dummy = PhraseParser::segmentSegment("this is sparta", 'en-US');
                    $word = Tokenizer::stem($queryWord);
                    $query[] = $word;
            }
            elseif ( $tokenizationMethod == 'chargram' ) {
                    // Tokenize the given word by stemming it
                    foreach( PhraseParser::getNGramsTerm([$queryWord],5) as $word ) {
                            $query[] = $word;
                    }
            }
            elseif ( $tokenizationMethod == 'none' ) {
                $query[] = $queryWord;
            }
        }
        $this->queryArray = $query;
    }
    function createInvertedIndex($dirName, $tokenizationMethod)
    {
        $document_id = 0;
        $termFrequency = [];
        $termDocMapping = [];
        $termDocPositionMapping = [];
        echo  ("\n\nThis program strips all punctuation characters from the text and converts all text to lower case for the purpose of creating an inverted index.\n".PHP_EOL.PHP_EOL);
        $files = glob(rtrim($dirName)."/*.txt");
        if ( count($files) < 1) {
            echo ("There are no files in the given directory.\n\nProgram will exit now." . PHP_EOL . PHP_EOL );
            exit();
        }
        $this->totalNoDocs = count($files);
        foreach( $files as $fname ) {
            $fileContentsWithPunctuations = explode(' ',strtolower(file_get_contents($fname)));
            $fileContents = preg_replace("#[[:punct:]]#", "", $fileContentsWithPunctuations);
            $position = 0;
            $termDocPositions = [];
            foreach ($fileContents as $word) {
                if ( $tokenizationMethod == 'none' ) {
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
                elseif ( $tokenizationMethod == 'stem' ) {
                    // Tokenize the given word by stemming it
                    $dummy = PhraseParser::segmentSegment("this is sparta", 'en-US');
                    $word = Tokenizer::stem($word);
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
                elseif ( $tokenizationMethod == 'chargram' ) {
                    // Tokenize the given word by stemming it
                    $dummy = PhraseParser::segmentSegment("this is sparta", 'en-US');
                    $charGrammedWords = PhraseParser::getNGramsTerm([$word],5);
                    foreach ( $charGrammedWords as $word ) {
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
                    }
                    $position += 1;
                }
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
        // Sorting the array based on the terms for printing the inverted index
        ksort($termDocPositionMapping);
        $this->invertedIndex = $termDocPositionMapping;
    }
}