<?php

require_once 'functions.php';

function main(){
    $year = date('Y');
    
    // get month number
    $month = date('m');
    
    $files_in_dir = scandir('.');
    
    if (in_array('dates.csv', $files_in_dir)) {
        echo 'File exists.' . PHP_EOL;
        // break line
        // $file = fopen('dates.csv', 'r');
        $dates = [];
        $data = readCsv('dates.csv');
        // print_r($data);
        if ($data) {
            $missing_months = checkForMissingMonths($data, $year);
            foreach ($missing_months as $miss_month) {
            }
        } else {
            $missing_months = range(1, 12);
        }
        // if length of $missing_months is greater than 0
        if (count($missing_months) > 0) {
            // Fix: Pass $year as the first parameter and $missing_months as the second parameter
            // $dates[] = getDates($year, $missing_months);
            $dates = array_merge($dates, getDates($year, $missing_months));
            // print_r($dates);
            // append new rows to end of file
            writeDatesToCsv($dates);
            // print_r($dates);
        } else {
            echo 'All months already processed.' . PHP_EOL;
        }
        // write new rows to end of file

    } else {
        $file = fopen('dates.csv', 'w');
        $column_names = ['Year', 'Month', 'Payday', 'Bonus'];
        fputcsv($file, $column_names, ';');
        fclose($file);
        $dates = getDates($year, range(1, 12));
        // print_r($dates);
        writeDatesToCsv($dates);
    }
}

main();

?>
