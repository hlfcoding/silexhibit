<?php if (!defined('SITE')) exit('No direct script access allowed');


function getDailyHits()
{
    $timestamp = convertToStamp(getNow());
    
    $day        = substr($timestamp,6,2);
    $mn         = substr($timestamp,4,2);
    $yr         = substr($timestamp,0,4);
    
    $days = array('today', 'yesterday', '2 days ago', '3 days ago', '4 days ago', '5 days ago', '6 days ago');
    
    $i = 0;
    foreach ($days as $d) {
        
        $out['first'] = date('Y-m-d H:i:s', mktime('00', '00', '00', $mn, $day-$i, $yr));
        $out['second'] = date('Y-m-d H:i:s', mktime('23', '59', '59', $mn, $day-$i, $yr));
        
        $arr[$d] = array($out['first'],$out['second']);
        $i++;
    }
    
    return $arr;
}


function getWeekHits()
{
    // create week beginning on sunday
    $timestamp = (date("w") === 0) ? 6 : date("w") - 1; 
    $timestamp = date("Ymd", strtotime("-" .$timestamp. " days"));
    
    $day        = substr($timestamp,6,2);
    $mn         = substr($timestamp,4,2);
    $yr         = substr($timestamp,0,4);


    $weeks = array('this week', 'last week', '2 weeks ago', '3 weeks ago', '4 weeks ago');

    $i = 0;
    foreach ($weeks as $d) {
        
        $day = $day-$i;
        $oday = $day + 6;
        
        $out['first'] = date('Y-m-d H:i:s', mktime('00', '00', '00', $mn, $day, $yr));
        $out['second'] = date('Y-m-d H:i:s', mktime('23', '59', '59', $mn, $oday, $yr));
        $arr[$d] = array($out['first'],$out['second']);
        $i = 7;
    }
    
    return $arr;
}


function getMonthlyHits()
{
    $timestamp = convertToStamp(getNow());
    
    $mn         = substr($timestamp,4,2);
    $yr         = substr($timestamp,0,4);
    
    $months = array(
        'this month',
        'last month',
        '2 months ago',
        '3 months ago',
        '4 months ago',
        '5 months ago',
        '6 months ago',
        '7 months ago',
        '8 months ago',
        '9 months ago',
        '10 months ago',
        '11 months ago');
    
    $i = 0;
    foreach ($months as $d) {
        
        $out['first'] = date('Y-m', mktime('00', '00', '00', $mn-$i, '01', $yr));
        $id = ($i <= 1) ? $d : $out['first'];
        $arr[$id] = array($out['first']);
        $i++;
    }
    
    return $arr;
}
