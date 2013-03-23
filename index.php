<?php

$host = $_SERVER['HTTP_HOST'];

$inputs = array();

if ($host === 'localhost') {
	$inputs[] = '[["kvm_local","vm_name","1",2048,"\/vm","takehara-test01.img","running"],["kvm_local","vm_name2","2",2048,"\/vm","takehara-test02.img","not running"]]';
	$inputs[] = '[["kvm_local2","vm_name","1",2048,"\/vm","takehara-test01.img","not running"],["kvm_local2","vm_name2","2",2048,"\/vm","takehara-test02.img","not running"]]';
} else {
//	$inputs[] = file_get_contents('http://localhost:8080/get_vm_web.php');
	$inputs[] = file_get_contents('http://172.16.8.3:8080/get_vm_web.php');
	$inputs[] = file_get_contents('http://172.16.8.4:8080/get_vm_web.php');
}

// インプットデータをまとめる処理
$data = array();
foreach ($inputs as $input) {
	foreach (json_decode($input, 1) as $each) {
		$hostName     = $each[0];
		$instanceName = $each[1];
		$each[3]      = round($each[3],0);

		if (isset($data[$instanceName]) === false) {
			$data[$instanceName] = array();
		}

		$data[$instanceName][] = $each;
	}
}

$runningNodes = array();

$tableData = array();

// データを表示する処理
foreach ($data as $vmName => $status) {
	$notRunningStatus = true;
	$runningHostName = $each[0];

	foreach ($status as $each) {
		if ($each[6] === 'running') {
			$notRunningStatus = false;
			$runningHostName = $each[0];
			$runningNodes[$vmName] = $each;
		}
	}

	if ($notRunningStatus === true) {
		//echo ' - ' . $vmName . ' is not running on any host' . "\n";
		$tableData[] = $data[$vmName][0];
	} else {
		//echo $runningHostName . ' ' . $vmName . ' is running' . "\n";
		$tableData[] = $runningNodes[$vmName];
	}
}

include('web_ui.php');

?>
