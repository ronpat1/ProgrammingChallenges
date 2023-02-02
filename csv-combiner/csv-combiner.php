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

# Read/Translate each row of each CSV file
# Note: 

# Note: had to copy full path due to some errors - change as necessary
$path = "/Users/ronakp/Desktop/PMG/ProgrammingChallenges/csv-combiner/fixtures/";

$combinedCSV = [];
$headers = [];
$rowCount = 0;
foreach ($fileNames as $fName) {

    $f = fopen($path.$fName, 'r');
    if ($f === false) {
        exit("Unable to open file: ".$fName);
    }

    $i = 0;
    # Note: declared tempHeaders here since it is used in an if-else branch 
    $tempHeaders = [];
    # Row iteration
    while (($row = fgetcsv($f)) !== false) {
        $fileContents[$fName][] = $row;
        
        if ($i == 0) {
            # Header row - add to total headers and record temp headers
            $headers = array_unique(array_merge($headers, $row));
            $tempHeaders = $row;
        }
        else {
            # Create/Translate row data
            $combinedCSV['filename'][] = $fName;

            # Iterate through indexed data to find assoc. column header
            foreach ($row as $index => $value) {
                # if col. header exists
                if (in_array($tempHeaders[$index], array_keys($combinedCSV))) {
                    $combinedCSV[$tempHeaders[$index]][] = $value;
                }
                # if col. header does NOT exist
                else {
                    $nils = 0;
                    while ($nils < $rowCount) {
                        $combinedCSV[$tempHeaders[$index]][] = "NIL";
                        $nils += 1;
                    }
                    $combinedCSV[$tempHeaders[$index]][] = $value;
                }
                # Find leftover columns to add NIL value to
                foreach ($headers as $h) {
                    if (! in_array($h, $tempHeaders)) {
                        $combinedCSV[$h][] = "NIL";
                    }
                }
            }

            $rowCount += 1;
        }

        $i += 1;
    }

    fclose($f);
}

# Write data as CSV to stdout
$fstdout = fopen("php://stdout", 'w');

$headers = array_keys($combinedCSV);
fputcsv($fstdout, $headers);

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