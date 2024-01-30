<?php

// payday: last working day of the month if it's not a weekend, otherwise the last weekday of the month
function getPayday($year, $month) {
    $last_day = date('t', strtotime($year . '-' . $month));
    $last_day_of_month = date('l', strtotime($year . '-' . $month . '-' . $last_day));
    if ($last_day_of_month == 'Saturday') {
        $payday = date('Y-m-d', strtotime($year . '-' . $month . '-' . ($last_day - 1)));
    } elseif ($last_day_of_month == 'Sunday') {
        $payday = date('Y-m-d', strtotime($year . '-' . $month . '-' . ($last_day - 2)));
    } else {
        $payday = date('Y-m-d', strtotime($year . '-' . $month . '-' . $last_day));
    }
    return $payday;
}

// unit test function for getPayday, test all months of a year, use months as an array of numbers
function testGetPayday($year) {
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[] = $i;
    }
    foreach ($months as $month) {
        $payday = getPayday($year, $month);
        echo $year . ' ' . $month . ': ' . $payday . PHP_EOL;
    }
}

// bonus day: 15th of the month if it's not a weekend, otherwise the first wednesday after the 15th
function getBonusDay($year, $month) {
    $bonus_day = date('Y-m-d', strtotime($year . '-' . $month . '-15'));
    $bonus_day_of_month = date('l', strtotime($bonus_day));
    if ($bonus_day_of_month == 'Saturday') {
        $bonus = date('Y-m-d', strtotime($year . '-' . $month . '-17'));
    } elseif ($bonus_day_of_month == 'Sunday') {
        $bonus = date('Y-m-d', strtotime($year . '-' . $month . '-16'));
    } else {
        $bonus = $bonus_day;
    }
    return $bonus;
}

// unit test function for getBonusDay, test all months of a year
function testGetBonusDay($year) {
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[] = $i;
    }
    foreach ($months as $month) {
        $bonus = getBonusDay($month, $year);
        echo $year . ' ' . $month . ': ' . $bonus . '\n';
    }
}

// function to create a 2d array of year, month, payday, bonus
function getDates($year, $months) {
    // for each month, get month name, payday, bonus day
    $dates = [];
    foreach ($months as $month) {
        $month_name = date('F', mktime(0, 0, 0, $month, 1, $year)); // Fix: Add $year as the last parameter
        $payday = getPayday($year, $month);
        $bonus = getBonusDay($year, $month);
        $dates[] = [$year, $month_name, $payday, $bonus];
    }
    return $dates;
    // print_r($dates);
}


// unit test function for getPaydays
function testGetDates($years, $months) {
    $years = [];
    // check years 2016-2030
    for ($i = 2016; $i <= 2030; $i++) {
        $years[] = $i;
    }
    foreach ($years as $year) {
        $dates = getDates($year, $months);
        foreach ($dates as $date) {
            echo $date[0] . ' ' . $date[1] . ': ' . $date[2] . ' ' . $date[3] . PHP_EOL;
        }
    }
}

// function to write a given 2d array to a csv file with columns of year, month, payday, bonus. it should be able to append to an existing file, or create a new file if it doesn't exist
function writeDatesToCsv($dates) {
    $fp = fopen('dates.csv', 'w');
    $column_names = ['Year', 'Month', 'Payday', 'Bonus'];
    fputcsv($fp, $column_names, ';');
    foreach ($dates as $date) {
        fputcsv($fp, $date, ';');
    }
    fclose($fp);
}

// unit test function for writeDatesToCsv
function testWriteDatesToCsv($years, $months) {
    $dates = getDates($years, $months);
    writeDatesToCsv($dates);
    $fp = fopen('dates.csv', 'r');
    $column_names = fgetcsv($fp);
    if ($column_names == ['Year', 'Month', 'Payday', 'Bonus']) {
        echo 'Column names are correct.' . PHP_EOL;
    } else {
        echo 'Column names are incorrect.' . PHP_EOL;
    }
    fclose($fp);
}



// function to read a csv file and return a 2d array of the data, skipping the first row. the seperator is ;
function readCsv($file) {
    $fp = fopen($file, 'r');
    $data = [];
    $column_names = fgetcsv($fp, 0, ';');
    // check if file is empty
    if (!$column_names) {
        echo 'File is empty.\n';
    }
    elseif ($column_names != ['Year', 'Month', 'Payday', 'Bonus']) {
        echo 'Column names are incorrect.' . PHP_EOL;
        return false;
    }
    while ($line = fgetcsv($fp, 0, ';')) {
        $data[] = $line;
    }
    fclose($fp);
    return $data;

}
// unit test function for readCsv
function testReadCsv($file) {
    $data = readCsv($file);
    foreach ($data as $line) {
        echo $line[0] . ' ' . $line[1] . ': ' . $line[2] . ' ' . $line[3] . '\n';
    }
}

// function to check if a row for the current year and rest othe months from a given month number already exists in a 2d array
function checkIfRowExists($data, $year, $month) {
    $month_name = date('F', mktime(0, 0, 0, $month, 1));
    foreach ($data as $line) {
        if ($line[0] == $year && $line[1] == $month_name) {
            return true;
        }
    }
    return false;
}

// test unit function for checkIfRowExists
function testCheckIfRowExists($data, $year, $month) {
    $exists = checkIfRowExists($data, $year, $month);
    if ($exists) {
        echo 'Row exists.' . PHP_EOL;
    } else {
        echo 'Row does not exist.' . PHP_EOL;
    }
}


// function to check for months of a year that are missing in a 2d array
function checkForMissingMonths($data, $year) {
    if (!$data) {
        echo 'All months are missing.\n';
        return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ,11, 12];
    }
    $missing_months = [];
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[] = $i;
    }
    foreach ($months as $month) {
        $check = checkIfRowExists($data, $year, $month);
        if (!$check) {
            $missing_months[] = $month;
        }
    }
    return $missing_months;
}

// unit test function for checkForMissingMonths
function testCheckForMissingMonths($data, $year) {
    $missing_months = checkForMissingMonths($data, $year);
    foreach ($missing_months as $month) {
        echo $month . PHP_EOL;
    }
}