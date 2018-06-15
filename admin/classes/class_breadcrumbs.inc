<?php
namespace Contentomat;

Class Breadcrumbs {
	
	public function __construct() {
		
	}
	
	public function createBreadcrumbs($params=array()) {
		
		$defaultParams = array(
			'separator' => '/',
			'source' => '',
			'linkSuffix' => '',
			'linkLastCrumb' => false
		);
		$params = array_merge($defaultParams, $params);
		
		$string = preg_replace(
			array(
				'/^' . preg_quote($params['separator'], $params['separator']).'/',
				'/' . preg_quote($params['separator'], $params['separator']).'$/'
			),
			'',
			$params['string']
		);
		
		$breadcrumbs = explode($params['separator'], $string);
	
		// last element empty?
		if (!$breadcrumbs[count($breadcrumbs)-1]) {
			array_pop($breadcrumbs);
		}
		
		if (!$breadcrumbs[0] && count($breadcrumbs) == 1) {
			$breadcrumbs[0] = $string;
		}

		$links = array();
		$crumbs = array();
		
		$linkString = '';
		$crumbsCounter = 1;
		$crumbsLength = count($breadcrumbs);
		
		foreach($breadcrumbs as $key => $crumb) {
			
			if ($crumbsCounter < $crumbsLength || ($crumbsCounter == $crumbsLength && $params['linkLastCrumb'])) {
				$links[$key] = $linkString . $crumb . $params['separator'] . $params['linkSuffix'];
				$linkString .= $crumb . $params['separator'];
			}
			$crumbs[] = $crumb;
			
			
			$crumbsCounter++;
		}
		
		return array(
			'breadcrumbs' => $crumbs,
			'links' => $links
		);
		
	}
	
	public function prepareForParser($data) {
		
		$preparedData = array();
		
		foreach((array)$data['breadcrumbs'] as $key=>$breadcrumb) {
			
			$preparedData[] = array(
				'breadcrumb' => $data['breadcrumbs'][$key],
				'link' => $data['links'][$key]
			);
			
		}
		
		return $preparedData;
	}
}
?>