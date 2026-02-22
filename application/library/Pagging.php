<?php
class Pagging{
	public $rowcount=20;
	public $page_count;
	public $cur_page;
	public $offset;
	public $total_data;
	public $customUrl='';
	public $custom_start = False;
	public $custom_start_data = Null;

	function __construct($total_data, $rowcount=20, $url=''){
		$this->total_data  	= $total_data;
		if ($this->total_data =0 )
			return
		$this->customUrl 	= $url; 
		$this->rowcount   	= $rowcount;
		$this->total_data 	= $total_data;
		$this->page_count 	= ceil(intval ($total_data) / $this->rowcount);
		if (isset($_GET['page'])){
			$this->cur_page 	= $_GET['page'];
			$this->offset  		= ($this->cur_page-1) * $this->rowcount;
		}
		else{
			$this->offset  	= 0;
			$this->cur_page = 1;
		}
		if (isset($_GET['cstm'])){
			$this->offset = $_GET['cstm'];
		}
	}

	function getLimit(){
		if ($this->total_data =0 )
			return '';
		
		return "LIMIT {$this->offset}, {$this->rowcount}";
	}

	function customOffset($offset){
		$this->custom_start = true;
		$this->custom_start_data= $offset;
	}

	function getUrl(){
		global $debug;

		$url = explode("/", $_SERVER['REQUEST_URI']);
		if (!isset($url[2])) {

			$url = 'main?';
			$url .= 'action=index';
		}
		else{
			$url = $url[2];
		}

		$url = explode("?", $url);
		$base_url = $url[0];
		$get =	isset($url[1])?$url[1]:'';
		$get = explode("&", $get);
		$other_get = [];
		foreach($get as $str){
			if (!in_string($str, "page=")){
				$other_get[] = $str;
			}
			
		}

		
		$other_get = implode("&", $other_get);
		if ($other_get){
			$url = "{$base_url}?{$other_get}";
			$url.= "&page=";
		}
		else {
			$url = "{$base_url}?page=";
		}


		return $url;
	}

	function getPageNavigation(){
		if ($this->total_data =0 )
			return '';
		$url = $this->getUrl();
		
		
		$navigation = '';
		if($this->cur_page>1) 
			$navigation.= "<a href='{$url}1'><i class='fa fa-fast-backward'></i></a> <a href='{$url}".($this->cur_page-1)."'><i class='fa fa-backward'></i></a> ";

		$navigation.= $this->cur_page.' of '.$this->page_count." "; 
		if($this->cur_page<$this->page_count) {
			$url_next = "{$url}{$this->page_count}";
			if ($this->custom_start)
				$url_next .="&cstm={$this->custom_start_data}";

			$navigation.= "<a href='{$url}".($this->cur_page+1)."'><i class='fa fa-forward'></i></a> <a href='$url'><i class='fa fa-fast-forward'></i></a>";
		}
		return $navigation;
	}

	function getAllVar(){
		global $debug;
		printpre(['rowcount' 	=> $this->rowcount, 
				'page_count'	=> $this->page_count,
				'cur_page' 		=> $this->cur_page,
				'offset' 		=> $this->offset,
				'total_data' 	=> $this->total_data, ]);
	}

}
?>