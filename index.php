<?php
$default_names = "Alvin\nBernd\nChristian\nDaniel\nEmil";

//Analyse input
$year_in = isset($_GET['year']) ? sprintf('%d',$_GET['year']) : date('Y')+1;
$month_begin_in = isset($_GET['monthb']) ? sprintf('%d',$_GET['monthb']) : 1;
$month_end_in = isset($_GET['monthe']) ? sprintf('%d',$_GET['monthe']) : 12;
$names_in = isset($_GET['names']) ? sprintf('%s',$_GET['names']) : $default_names;

$year = ($year_in > 2000 && $year_in < 2100) ? $year_in : date('Y');
$month_begin = ($month_begin_in > 1 && $month_begin_in <= 12) ? $month_begin_in : 1;
$month_end = ($month_end_in >= $month_begin && $month_end_in <= 12) ? $month_end_in : 12;

//Prepare names
$names = explode("\n",$names_in);

//Start script
require_once("HolidaySchedule.php");

//setlocale(LC_TIME, "de_DE");
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

$shifts = 'FFFFFFFSSSSS  NNNNNNN       ';
 //' ' = Free, F = Morning, S = Late, N = Night
$shift_start_day = mktime(0,0,0,1,24,2011); //Problems between April and September!

$schedule = new HolidaySchedule($year, array($month_begin,$month_end), $shifts, $shift_start_day, $names);

$month_names = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml' lang="de" xml:lang="de">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>Schichtplan <?php echo $year;?></title>
<link rel="stylesheet" type="text/css" media="screen" href="/static/main.css" />
<link rel="alternate stylesheet" type="text/css" href="/static/druck.css" title="Druck" />
<link rel="stylesheet" type="text/css" media="print, embossed" href="/static/druck.css" />
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
  <fieldset>
    <legend>Schichtplan</legend>
    	<table style="table-layout:fixed; width:660px;">
        	<tr>
            	<td align="left">
                    <label for="year">Jahr: </label>
                    <input name="year" id="year" type="text" value="<?php echo $year; ?>" />
                </td>
                <td align="center">
                    <label for="monthb">Von: </label>
                    <select name="monthb" id="monthb" size="1">
                    	<?php
                    		for($i = 1; $i <= 12; $i++) {
                    			$selected = ($i == $month_begin) ? ' selected="selected"' : '';
                    			echo sprintf('<option value="%d"%s>%s</option>',$i,$selected,$month_names[$i-1]);
                    		}
                    	?>
                    </select>
                </td>
                <td align="right"> 
                    <label for="monthe">Bis: </label>
                    <select name="monthe" id="monthe" size="1">
                    	<?php
                    		for($i = 1; $i <= 12; $i++) {
                    			$selected = ($i == $month_end) ? ' selected="selected"' : '';
                    			echo sprintf('<option value="%d"%s>%s</option>',$i,$selected,$month_names[$i-1]);
                    		}
                    	?>
                    </select>
				</td>
			</tr>
            <tr>
            	<td colspan="2" style="font-size:0.9em;">
            	    <div class="shifts">F = Früh, S = Spät, N = Nacht, '&nbsp;' = Frei</div>
            	    <div>
                        <label for="names">Personen: </label>
                        <textarea class="names" name="names" id="names"><?php echo join("\n", $names); ?></textarea>
            	    </div>
            	 </td>
            	<td align="right" valign="bottom" style="height:40px;">
		            <input type="submit" value="Anzeigen" />
		            <input type="button" onClick="javascript: window.print()" value="Drucken"/>
				</td>
			</tr>
			<tr style="display: none">
				<td colspan="3">
					Freie Tage <input type="button" id="data_load" value="laden"/> oder <input type="button" id="data_save" value="speichern"/>
				</td>
			</tr>
		</table>
  </fieldset>
</form>

<?php echo $schedule->render(); ?>
<textarea id="output" style="display: none;"></textarea>
<script type="text/javascript" src="/static/jquery.min.js"></script>
<script type="text/javascript" src="/static/js.js"></script>
</body>
</html>
