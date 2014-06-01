<?php
// Omitted items: URL;VALUE=URI: /*escapeString($uri)*/ 
// Variables used in this script:
//   $summary     - text title of the event
//   $datestart   - the starting date (in seconds since unix epoch)
//   $dateend     - the ending date (in seconds since unix epoch)
//   $address     - the event's address
//   $uri         - the URL of the event (add http://)
//   $description - text description of the event
//   $filename    - the name of this file for saving (e.g. my-event-name.ics)
//
// Notes:
//  - the UID should be unique to the event, so in this case I'm just using
//    uniqid to create a uid, but you could do whatever you'd like.
//
//  - iCal requires a date format of "yyyymmddThhiissZ". The "T" and "Z"
//    characters are not placeholders, just plain ol' characters. The "T"
//    character acts as a delimeter between the date (yyyymmdd) and the time
//    (hhiiss), and the "Z" states that the date is in UTC time. Note that if
//    you don't want to use UTC time, you must prepend your date-time values
//    with a TZID property. See RFC 5545 section 3.3.5
//
//  - The Content-Disposition: attachment; header tells the browser to save/open
//    the file. The filename param sets the name of the file, so you could set
//    it as "my-event-name.ics" or something similar.
//
//  - Read up on RFC 5545, the iCalendar specification. There is a lot of helpful
//    info in there, such as formatting rules. There are also many more options
//    to set, including alarms, invitees, busy status, etc.
//
//      https://www.ietf.org/rfc/rfc5545.txt

// 2. Define helper functions

// Converts a unix timestamp to an ics-friendly format
// NOTE: "Z" means that this timestamp is a UTC timestamp. If you need
// to set a locale, remove the "\Z" and modify DTEND, DTSTAMP and DTSTART
// with TZID properties (see RFC 5545 section 3.3.5 for info)
//
// Also note that we are using "H" instead of "g" because iCalendar's Time format
// requires 24-hour time (see RFC 5545 section 3.3.12 for info).
function dateToCal($timestamp) 
{
  return date('Ymd\THis\Z', $timestamp);
}

// Escapes a string of characters
function escapeString($string) 
{
  return preg_replace('/([\,;])/','\\\$1', $string);
}

// Converts a mysql time string into seconds 
// that can be added to one of our start or end dates. 
function time2seconds($time='00:00:00')
{
    list($hours, $mins, $secs) = explode(':', $time);
    return ($hours * 3600 ) + ($mins * 60 ) + $secs;
}

// 3. Assemble the ics file's contents
function md2_get_ics_data($dr)
{
  $dtz = md2_get_default_tz();
  $ics="BEGIN:VCALENDAR\n";
  $ics.="VERSION:2.0\n";
  $ics.="PRODID:-//MD2//WebSite v1.0//EN\n";
  $ics.="CALSCALE:GREGORIAN\n";
  $ics.="BEGIN:VEVENT\n";
  $tmp_date = date_create($dr->date_of_meet,$dtz);
  $meet_date_start = clone $tmp_date;
  $meet_date_start->modify("+" . time2seconds($dr->time_meet_start) ." seconds");
  $ics.="DTSTART:".dateToCal(date_format($meet_date_start, 'U')) . "\n";
  $meet_date_end = clone $tmp_date;
  $meet_date_end->modify("+" . time2seconds($dr->time_meet_end) ." seconds");
  $ics.="DTEND:".dateToCal(date_format($meet_date_end, 'U')) . "\n";
  $ics.="SUMMARY:Grand Rounds Call " . date_format($meet_date_start, "F, Y") . "\n";
  $ics.="UID:". uniqid() . "\n";
  $ics.="DTSTAMP:".dateToCal(time()) . "\n";
  $ics.="LOCATION:Telephone conference\n";
  $ics.="DESCRIPTION:Join us for the quarterly Grand Rounds Call.\\n";
  $ics.="Dial {$dr->phone_number} and use the code {$dr->meeting_id} to connect.\\n";
  $ics.= str_replace("\r\n", "\\n", $dr->meeting_note) . "\\n" . "\n";
  $ics.='ORGANIZER;CN="Laurie Krisman":mailto:krisman@md2.com'."\n";
  $ics.="END:VEVENT\n";
  $ics.="END:VCALENDAR\n";
  
  return $ics;
}