<?php
namespace app\index\controller;
use think\Controller;

class Center extends Controller
{
	public function getindex()
	{
		return $this->fetch('center');
	}
	
}
?>