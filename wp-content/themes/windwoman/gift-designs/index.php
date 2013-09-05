<?

//Pull server date info
$month=date('M');
//$month='Jan';
$date=date('j');
//$date='30';
$time=date('G');
//$time='14';


//$start_date=29;
//$end_date=1;

//$time_start=12;
//$time_end=12;

//$month_end='Feb';
//$month_start='Jan';


// Wordpress Custom Post Feilds
$date_start = get_post_meta($post->ID, 'date_start', true);
$date_end = get_post_meta($post->ID, 'date_end', true);
$month_end = get_post_meta($post->ID, 'month_end', true);
$month_start = get_post_meta($post->ID, 'month_start', true);
$time_end = get_post_meta($post->ID, 'time_end', true);
$time_start = get_post_meta($post->ID, 'time_start', true);
$file_name = get_post_meta($post->ID, 'file_name', true);
$img_type = get_post_meta($post->ID, 'img_type', true);
$purchase_url = get_post_meta($post->ID, 'purchase_url', true);
$run_length = get_post_meta($post->ID, 'run_length', true);

 if ($date_end < $date_start){
	$ed=$date_end + $run_length; 
 }
	else { $ed=$date_end;
 }

if ( ! post_password_required() ) {
if ((($date == $date_start) && ($time >= $time_start)) || (($date == $date_end) && ($time <= $time_end)) || ($date > $date_start && $date < $ed)) {
		echo '<br><center><a href="/friday/'.$file_name.'.zip"><img src="/friday/'.$file_name.'.'.$img_type.'"><br>Download Here</a></center>';
		}
		else { echo '';}
}


// debug
/*
echo '<br><br><h3>Script Debug</h3><br>';
echo 'current date: '.$date;
echo "<br>";
echo 'current time: '.$time;
echo "<br>";
echo 'current month: '.$month;

echo "<br>";
echo 'start date: '.$date_start;
echo "<br>";
echo 'end date: '.$date_end;
echo "<br>";
echo 'end month: '.$month_end;
echo "<br>";
echo 'start month: '.$month_start;
echo "<br>";
echo 'time end: '.$time_end;
echo "<br>";
echo 'time start: '.$time_start;
echo '<br>';
echo 'Run lenght: '.$run_length;
*/
?>
