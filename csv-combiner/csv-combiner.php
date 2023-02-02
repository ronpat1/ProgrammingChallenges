#!/opt/homebrew/bin/php

<?php
# CSV Combiner for PMG Programming Challenge #
# Note(s):
# for loop counts determined beforehand since count evaluates at every iteration of loop

# Exit program if no CSV files are specified
if ($argc == 1) {
    exit("No CSV files specified!");
}

# Otherwise, extract filenames
# Note: assumes folder and that only filenames are inputted
$fileNames = [];
for ($i = 1; $i < $argc; $i++) {
    $explodedFileName = explode('/', $argv[$i]);
    $fileNames[] = $explodedFileName[count($explodedFileName)-1];
}

# Read/Write each CSV file into an array
# Note: may not be the most memory-efficient way to go about this
$fileContents = [];
foreach ($fileNames as $fName) {
    # Note: had to copy full path due to some errors - change as necessary
    $path = "/Users/ronakp/Desktop/PMG/ProgrammingChallenges/csv-combiner/fixtures/";

    $f = fopen($path.$fName, 'r');
    if ($f === false) {
        exit("Unable to open file: ".$fName);
    }

    $fileContents[$fName] = [];
    while (($row = fgetcsv($f)) !== false) {
        $fileContents[$fName][] = $row;
    }

    fclose($f);
}

# DESCRIPTION COMMENT
# Note: assumes files are not empty?? 
$combinedCSV = [];
# Iterating through each file and retrieving filename
foreach ($fileContents as $file => $content) {
    # Iterating through each row of a file's contents
    $iCount = count($content);
    for ($i = 1; $i < $iCount; $i++) {
        #record filename
        $combinedCSV['filename'][] = $file;
        #iterate through/record each element in row
        $jCount = count($content[$i]);
        for ($j = 0; $j < $jCount; $j++) {
            $combinedCSV[$content[0][$j]][] = $content[$i][$j];
        }
    }
}

# Write data as CSV to stdout
$fstdout = fopen("php://stdout", 'w');

$headers = array_keys($combinedCSV);
fputcsv($fstdout, $headers);
#HARD INDEXING - change later
$iCount = count($combinedCSV[$headers[0]]);

for ($i = 0; $i < $iCount; $i++) {
    $row = [];
    foreach ($headers as $h) {
        $row[] = $combinedCSV[$h][$i];
    }
    fputcsv($fstdout, $row);
}

fclose($fstdout);
?>