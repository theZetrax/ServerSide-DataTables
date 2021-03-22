<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Method: GET');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: allow');
header('Content-Type: application/json');

$request = $_GET;
$num = $request['draw'];

$dsn = "mysql:dbname=lara_database;host=127.0.0.1";
$pdo = new PDO($dsn, "root", "");

$limit = $request['length'];
$offset = $request['start'];
$search = $request['search']['value'];
// $search = explode(' ', $search);
// $search = array_map(function ($keyword) {
// 	return "%{$keyword}%";
// }, $search);

$order = $request['columns'][$request['order'][0]['column']]['data'];
$order_dir = $request['order'][0]['dir'];

if (!empty($search)) {
	$search = "%$search%";
	$query = <<<SQL
		SELECT
			id, first_name, last_name, email
		FROM 
			customers
		WHERE 
			first_name LIKE :keyword
	SQL;

	if (!empty($order))
	{
		$query .= " ORDER BY {$order} {$order_dir}";
	}

	$query .= " LIMIT {$limit} OFFSET {$offset}";

	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':keyword', $search);
	$stmt->execute();

	$customers = $stmt->fetchAll(PDO::FETCH_OBJ);
	$mesg = $stmt->errorInfo();

	$query = <<<SQL
		SELECT COUNT(*) as customers_count FROM customers WHERE first_name LIKE :keyword
	SQL;

	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':keyword', $search);
	$stmt->execute();
	$customersCount = $stmt->fetchObject()->customers_count;

	$data = [
		'draw' => $num,
		'recordsTotal' => $customersCount,
		'recordsFiltered' => $customersCount,
		'data' => $customers,
		'input' => $_GET
	];
	$data = json_encode($data);

	http_response_code(200);
	echo $data;
	
} else {
	$query = <<<SQL
		SELECT id, first_name, last_name, email FROM customers
	SQL;

	if (!empty($order))
	{
		$query .= " ORDER BY {$order} {$order_dir}";
	}

	$query .= " LIMIT {$limit} OFFSET {$offset}";
	
	$stmt = $pdo->prepare($query);

	$response = $stmt->execute();
	$customers = $stmt->fetchAll(PDO::FETCH_OBJ);

	$query = <<<SQL
		SELECT COUNT(*) as customers_count FROM customers
	SQL;

	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$customersCount = $stmt->fetchObject()->customers_count;

	$data1 = [
		'draw' => $num,
		'recordsTotal' => $customersCount,
		'recordsFiltered' => $customersCount,
		'data' => $customers,
		'input' => $_GET
	];
	$data1 = json_encode($data1);

	http_response_code(200);
	echo $data1;
}
