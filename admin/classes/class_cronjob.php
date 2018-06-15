<?php 

namespace Contentomat;

class Cronjob {
	
	protected $db;
	private $cronTable = 'cmt_crontab';
	
	public function __construct() {
		
		$this->db = new DBCex();
	}
	
	public function find() {
		
		$now = time();
		
		// get all cron jobs
		$this->db->query("SELECT * FROM `" . $this->cronTable . "` WHERE is_active = '1'");
		$r = $this->db->getAll();
		
		$executeCronJobs = array();
		
		// filter them
		foreach ($r as $id => $cronJob) {

			// check minute
			if ($cronJob['minute'] == intval(date('i')) || $cronJob['minute'] == '*') {
				$executeCronJobs[$id] = $cronJob ;
			} else {
				continue;
			}
			
			// hour
			if ($cronJob['hour'] == date('G') || $cronJob['hour'] == '*') {
				$executeCronJobs[$id] = $cronJob;
			} else {
				unset($executeCronJobs[$id]);
				continue;
			}
				
			// check day of week
			// IMPORTANT: we start with Monday = 1 and end with Sunday = 7
			if ($cronJob['day_of_week'] == (date('w') + 1) || $cronJob['day_of_week'] == '*') {
				$executeCronJobs[$id] = $cronJob ;
			} else {
				unset($executeCronJobs[$id]);
				continue;
			}

			// check day of month
			if ($cronJob['day_of_month'] == date('j') || $cronJob['day_of_month'] == '*') {
				$executeCronJobs[$id] = $cronJob ;
			} else {
				unset($executeCronJobs[$id]);
				continue;
			}
			
			// check month
			if ($cronJob['month'] == date('n') || $cronJob['month'] == '*') {
				$executeCronJobs[$id] = $cronJob ;
			} else {
				unset($executeCronJobs[$id]);
				continue;
			}
		}
		
		// eliminate double entries
		//$executeCronJobs = array_keys(array_flip($executeCronJobs));
		
		return $executeCronJobs;
	}
	
	
}