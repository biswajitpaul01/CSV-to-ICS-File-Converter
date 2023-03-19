<?php
require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

// Carbon::now()->timezone('UTC');

// Replace 'example.csv' with the path to your CSV file
$csvFile = __DIR__ . '/source/IPLT20-2023.csv';

// Replace 'example.ics' with the name you want to give to your ICS file
$icsFile = __DIR__ . '/destination/' . basename($csvFile, '.csv') . '.ics';

// Read the CSV file
$fileHandleCsv = fopen($csvFile, 'r');
// $data = fgetcsv($fileHandle);

// Create the ICS file
$fileHandle = fopen($icsFile, 'w');

// Write the ICS file header
fwrite($fileHandle, "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\n");

// Skip the first row of the CSV file
fgetcsv($fileHandleCsv);

// Loop through the CSV file and create an event for each row
while (($data = fgetcsv($fileHandleCsv)) !== false) {
    // Replace the array keys with the column names in your CSV file
    $summary = '🏏 ' . $data[5] . ' vs ' . $data[6];
    $location = $data[7];
    $description = '';
    $time = Carbon::createFromFormat('d-M-y g:i A', $data[3] . ' ' . $data[4]);
    $start_time = $time->format('Ymd\THis');
    $end_time = $time->addHours(3)->addMinutes(30)->format('Ymd\THis');

    $event = [
        'summary'     => $summary,
        'location'    => $location,
        'description' => $description,
        'start'       => $start_time,
        'end'         => $end_time,
    ];

    // Create the ICS event
    $icsEvent = "BEGIN:VEVENT\n";
    $icsEvent .= "SUMMARY:" . $event['summary'] . "\n";
    $icsEvent .= "LOCATION:" . $event['location'] . "\n";
    $icsEvent .= "DESCRIPTION:" . $event['description'] . "\n";
    $icsEvent .= "DTSTART:" . $event['start'] . "\n";
    $icsEvent .= "DTEND:" . $event['end'] . "\n";
    $icsEvent .= "END:VEVENT\n";

    // Write the ICS event to the file
    fwrite($fileHandle, $icsEvent);
}

// Write the ICS file footer
fwrite($fileHandle, "END:VCALENDAR\n");

// Close the file handles
fclose($fileHandle);

// Output a message to the user
echo "Conversion complete. The ICS file has been created at $icsFile";
