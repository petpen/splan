<?php
/**
 * Shiftplanner
 * 
 * @author Peter Pensold
 * @version 1.2
 * @date 17.07.2011
 */
class HolidaySchedule {
	const MONTH_MIN = 1;
	const MONTH_MAX = 12;
	const MARKER_SIGN = 'X';
	
	const DAY = 86400; //Seconds for a day. Calculation: 60 * 60 * 24
	
	private $days = array('So','Mo','Di','Mi','Do','Fr','Sa');
	private $months = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	
	private $year = 0;
	private $shift_start_day;
	private $shift_start_pos = 0;
	private $monthRange = array();
	private $names = array();
	private $shift = '';
	private $shift_len = 0;
	private $holidays = array();
	
	private $monthNames = array();
	
	public function __construct($year = 0, $monthRange = array(), $shift = '', $shift_start_day, $names = array(), $holidays = array()) {
		$this->setYear($year);
		$this->setNames($names);
		$this->setShift($shift);
		$this->setShiftStartDay($shift_start_day);
		$this->setHolidays($holidays);
		$this->setMonthRange($monthRange);
		
		$this->calculateShiftStartPos();
	}
	
	/**
	 * Renders the holiday schedule
	 * 
	 * @return string 
	 */
	public function render() {
		$html = '';
		
		for($i = $this->monthRange['from']; $i <= $this->monthRange['to']; $i++) {
			$html .= $this->renderMonth($i);
		}
		
		return $html;
	}
	
	/**
	 * 
	 * @return string
	 */
	private function renderMonth($month) {
		$month_timestamp = mktime(0,0,0,$month,1,$this->year);
		$start_day_year = date('z',$month_timestamp); //First day of this month in this year
		$start_day_month = date('w',$month_timestamp);
		$start_pos = ($start_day_year % $this->shift_len) + $this->shift_start_pos;
		$month_name = $this->months[$month-1];
		$days_month = date('t',$month_timestamp);
		
		$month_cell = sprintf('<td rowspan="3" class="monat">%s<br/>%s</td>',$month_name,$this->year);
		
		$days_decimal = array();
		$days_name = array();
		$days_shift = array();
		$days_worker = array();
		
		for($i = 0;$i < $days_month; $i++) {
			$day_of_week = ($start_day_month + $i) % 7;
			$shift_pos = ($this->shift_len + $start_pos + $i) % $this->shift_len;
					
			$css_weekend = ($day_of_week == 0 || $day_of_week == 6) ? 'wochenende' : '';

			$days_decimal[] = sprintf('<td class="%s">%s</td>', $css_weekend, $i + 1);
			$days_name[] = sprintf('<td class="%s">%s</td>', $css_weekend, $this->days[$day_of_week]);
			$days_shift[] = sprintf('<td class="%s">%s</td>', $css_weekend, substr($this->shift, $shift_pos, 1));
			$days_worker[] =  sprintf('<td class="day %s"> </td>', $css_weekend);
		}
		
		$table_head = sprintf('<tr>%s%s</tr><tr class="linieunten">%s</tr><tr class="linieunten">%s</tr>',$month_cell,implode('',$days_decimal),implode('',$days_name),implode('',$days_shift));
		
		$table_worker = '';
		for($i = 0; $i < count($this->names); $i++) {
			$table_worker .= sprintf('<tr class="worker"><td class="person_name">%s</td>%s</tr>',$this->names[$i],implode($days_worker));
		}
		
		$html = sprintf('<table id="month_%d" class="schichtplan month">%s%s</table>',$month ,$table_head,$table_worker);
		
		return $html;
	}
	
	public function setYear($year = 0) {
		$year = intval($year);
		
		$this->year = ($year == 0) ? date('Y') : $year;
	}
	
	/**
	 * Calculates the shift start position in the shift array
	 * for the given year.
	 * 
	 * After the start position is calculated it is
	 * easier and faster to render the whole month
	 * by simply iterating the shift array.
	 */
	private function calculateShiftStartPos() {
		$day_difference = mktime(0,0,0,1,1,$this->year) - $this->shift_start_day;
		$days = $day_difference / self::DAY;
		
		$this->shift_start_pos = $days % $this->shift_len;
	}
	
	/**
	 * Sets the month range for displaying the calendar
	 * 
	 * The maximum range is from January to December
	 * The minimum is one month
	 * 
	 * The start month must be smaller or equal than the end month
	 * The end month cannot be greater than December
	 * 
	 * If wrong range given the default range will be used
	 * 
	 * Default range is January to December
	 * 
	 * @param from - starting month [1..12]
	 * @param to -end month [from .. 12]
	 */
	public function setMonthRange($monthRange = array(self::MONTH_MIN,self::MONTH_MAX)) {
		$from = intval($monthRange[0]);
		$to = intval($monthRange[1]);
		
		if($from >= self::MONTH_MIN && $from <= self::MONTH_MAX
		   && $to >= $from && $to <= self::MONTH_MAX)
		{
			$this->monthRange = array('from' => $from, 'to' => $to);
		} else {
			$this->monthRange = array('from' => 1, 'to' => 12);
		}
	}
	
	/**
	 * Sets the names of the workers
	 * 
	 * The order of the names is equal to the order in the list 
	 * 
	 * @param names - array of names
	 */
	public function setNames($names = array()) {
		$this->names = $names;
	}
	
	/**
	 * Sets the shift.
	 * 
	 * Rules:
	 * - The string length represents the whole shift
	 * - the first day is the day of the shiftstart
	 * - it will rollback if it reached the end
	 * - each character represents one day
	 * 
	 * @param shifts - string
	 */
	public function setShift($shift = '') {
		$this->shift = $shift;
		$this->shift_len = strlen($shift);
	}
	
	/**
	 * Sets the shift start day.
	 * 
	 * Rules:
	 * - The day must be placed before the requested year
	 * 
	 * @param day - string array
	 */
	public function setShiftStartDay($day) {
		$this->shift_start_day = intval($day);
	}
	
	/**
	 * TODO FUTURE USE
	 * 
	 * Not yet implemented
	 */
	public function setHolidays($holidays = array()) {
		
	}
}