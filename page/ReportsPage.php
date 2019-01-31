<?php
class ReportsPage extends Page {
    
    public function get($f3) {
    	checkAuth($f3);
        $f3->set('title', 'Отчёты');
        
        $page = $this->resolveParam('page', 1);
        
        $delid= $this->resolveParam('delid', 0);
        
        if($delid){
        	debug_message("<<< in delid=".$delid);
        	$deleteReport = deleteReport($f3, $delid);
        	debug_message($deleteReport,1);
        	if($deleteReport !== true){
        		throw new Exception('Ошибка: отчет не удален: '. $deleteReport);
        	}
        	debug_message(">>>");
        	$f3->reroute ( "/reports/$page");
        	return;
        }
        
        $reportsCount = getReportersCount($f3);
        $pages = (int)($reportsCount/$f3->get ( 'PER_PAGE' ));
        
        $reports = getReports($f3, $page);
        
        $f3->set('reports', $reports);
        $f3->set('reportsCount', $reportsCount);
        $f3->set('page', $page);
        $f3->set('pages', $pages);
        
        echo \Template::instance()->render('view/reportList.htm');
    }
    
    public function post($f3) {
    	checkAuth($f3);
    }
    
}