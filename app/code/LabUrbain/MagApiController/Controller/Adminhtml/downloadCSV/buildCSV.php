<?php
$fileTitle = "coolid-OUTPUT.csv";
//exit("outcsv.php");
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$fileTitle);
////
$output = fopen('php://output', 'w');
//
//$userData = array("username" => "user", "password" => "saTvP7G1nkf9");
$userData = array("username" => "magapi", "password" => "V30bMabz7VcNM74o");
$ch = curl_init("http://coolid.ca/index.php/rest/V1/integration/admin/token");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

$token = curl_exec($ch);
$ch = curl_init("http://coolid.ca/index.php/rest/V1/orders?searchCriteria=[]");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

$result = curl_exec($ch);

$result = json_decode($result, 1);

$csv_input = array();


//echo "<pre>";
//print_r($result["items"][11]);
//die();
//print_r($result);

//$testitem = $result["items"][0];

//extra fees sku
$sku = array();
$sku[595]="991307";
$sku[995]="991307";


//status
$status["processing"]="P";
$status["pret_export"]="P";

$header = array("ORDER ID","CUSTOMER NUMBER","PRODUCT CODE","PRODUCT NAME","PRODUCT OPTION","PRICE","QUANTITY","TOTAL","DISCOUNT","COUPON CODE","COUPON DISCOUNT","ORDER TRACKING","SHIPPING COST","TAX","STATUS","DATE","PAYMENT METHOD","BILLING TITLE","BILLING FNAME","BILLING LNAME","BILLING ADDRESS 1","BILLING ADDRESS 2","BILLING CITY","BILLING STATE","BILLING COUNTRY","BILLING ZIP CODE","SHIPPING TITLE","SHIPPING FNAME","SHIPPING LNAME","SHIPPING ADDRESS 1","SHIPPING ADDRESS 2","SHIPPING CITY","SHIPPING STATE","SHIPPING COUNTRY","SHIPPING ZIP CODE","PHONE","EMAIL","LANGUAGE","SHIPPING METHOD","MEMBERSHIP"," INSURANCE"," LOGIN");
fputcsv($output, $header);

//echo "<br>";

foreach ($result["items"] as $testitem) {
	if ($testitem["status"]<>"pret_export") {
		continue;
	}

foreach ($testitem["items"] as $item) {
//foreach
$csv_output["ORDER ID"] = $testitem["increment_id"];
$csv_output["CUSTOMER NUMBER"] = "";
$csv_output["PRODUCT CODE"] = $item["sku"];	
$csv_output["PRODUCT NAME"] = cleanup($item["name"]);
$csv_output["PRODUCT OPTION"] = "";
$csv_output["PRICE"] = $item["price"];
$csv_output["QUANTITY"] = $item["qty_ordered"];
$csv_output["TOTAL"] = @number_format((float)$testitem["grand_total"], 2, '.', '');
$csv_output["DISCOUNT"] = abs($testitem["discount_amount"]);
$csv_output["COUPON CODE"] = @$testitem["coupon_code"];
$csv_output["COUPON DISCOUNT"] = ""; #$testitem["discount_amount"];
$csv_output["ORDER TRACKING"] = "";
$csv_output["SHIPPING COST"] = $testitem["shipping_amount"];
$csv_output["TAX"] = number_format((float)$testitem["tax_amount"], 2, '.', '');
$csv_output["STATUS"] = $status[$testitem["status"]];
$csv_output["DATE"] = date("d/m/Y H:i", strtotime($testitem["created_at"]));

if ($testitem["payment"]["method"]=="authnetcim") {
	$csv_output["PAYMENT METHOD"] = cleanup($testitem["payment"]["additional_information"][3]);
}elseif ($testitem["payment"]["method"]=="authorizenet_directpost") {
	$csv_output["PAYMENT METHOD"] = cleanup($testitem["payment"]["additional_information"][0]);
}elseif ($testitem["payment"]["method"]=="paypal_express") {
	$csv_output["PAYMENT METHOD"] = "Paypal";
}else{
	$csv_output["PAYMENT METHOD"] = "Other";
}

$csv_output["BILLING TITLE"] = @$testitem["billing_address"]["prefix"];
$csv_output["BILLING FNAME"] = cleanup($testitem["billing_address"]["firstname"]);
$csv_output["BILLING LNAME"] = cleanup($testitem["billing_address"]["lastname"]);
$csv_output["BILLING ADDRESS 1"] = cleanup($testitem["billing_address"]["street"][0]);
$csv_output["BILLING ADDRESS 2"] = cleanup(@$testitem["billing_address"]["street"][1]);
$csv_output["BILLING CITY"] = cleanup($testitem["billing_address"]["city"]);
$csv_output["BILLING STATE"] = cleanup($testitem["billing_address"]["region_code"]);
$csv_output["BILLING COUNTRY"] = cleanup($testitem["billing_address"]["country_id"]);
$csv_output["BILLING ZIP CODE"] = cleanup($testitem["billing_address"]["postcode"]);
$csv_output["SHIPPING TITLE"] = @$testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["prefix"];
$csv_output["SHIPPING FNAME"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["firstname"]);
$csv_output["SHIPPING LNAME"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["lastname"]);
$csv_output["SHIPPING ADDRESS 1"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["street"][0]);
$csv_output["SHIPPING ADDRESS 2"] = cleanup(@$testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["street"][1]);
$csv_output["SHIPPING CITY"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["city"]);
$csv_output["SHIPPING STATE"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["region_code"]);
$csv_output["SHIPPING COUNTRY"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["country_id"]);
$csv_output["SHIPPING ZIP CODE"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["postcode"]);
$csv_output["PHONE"] = cleanup(str_replace(")", "", str_replace("(", "", str_replace(" ", "", str_replace("-", "", $testitem["billing_address"]["telephone"])))));

if ($testitem["billing_address"]["email"] <> "order@excelcatalogues.com") {
	$csv_output["EMAIL"] = $testitem["billing_address"]["email"];
}else {
	$csv_output["EMAIL"] = '';
}


$csv_output["LANGUAGE"] = get_storeview_lang($testitem["store_id"], $token);
$csv_output["SHIPPING METHOD"] = cleanup($testitem["shipping_description"]);
$csv_output["MEMBERSHIP"] = "0";
$csv_output[" INSURANCE"] = "";
$csv_output[" LOGIN"] = "";
//
//
//echo implode(",", $csv_output);
fputcsv($output, $csv_output);

//echo "<br>";
}


if ($testitem["extension_attributes"]["amextrafee_fee_amount"] > 0) {
	//bonus item line
	$csv_output["ORDER ID"] = $testitem["increment_id"];
	$csv_output["CUSTOMER NUMBER"] = "";
	$csv_output["PRODUCT CODE"] = $sku[str_replace(".","",$testitem["extension_attributes"]["amextrafee_fee_amount"])];	
	$csv_output["PRODUCT NAME"] = "Extra P&H for the Bonus Item";
	$csv_output["PRODUCT OPTION"] = "";
	$csv_output["PRICE"] = $testitem["extension_attributes"]["amextrafee_fee_amount"];
	$csv_output["QUANTITY"] = 1;
	$csv_output["TOTAL"] = @number_format((float)$testitem["grand_total"], 2, '.', '');
	$csv_output["DISCOUNT"] = abs($testitem["discount_amount"]);
	$csv_output["COUPON CODE"] = @$testitem["coupon_code"];
	$csv_output["COUPON DISCOUNT"] = ""; #$testitem["discount_amount"];
	$csv_output["ORDER TRACKING"] = "";
	$csv_output["SHIPPING COST"] = $testitem["shipping_amount"];
	$csv_output["TAX"] = number_format((float)$testitem["tax_amount"], 2, '.', '');
	$csv_output["STATUS"] = "P";
	$csv_output["DATE"] = date("d/m/Y H:i", strtotime($testitem["created_at"]));

	if ($testitem["payment"]["method"]=="authnetcim") {
		$csv_output["PAYMENT METHOD"] = cleanup($testitem["payment"]["additional_information"][3]);
	}elseif ($testitem["payment"]["method"]=="authorizenet_directpost") {
		$csv_output["PAYMENT METHOD"] = cleanup($testitem["payment"]["additional_information"][0]);
	}elseif ($testitem["payment"]["method"]=="paypal_express") {
		$csv_output["PAYMENT METHOD"] = "Paypal";
	}else{
		$csv_output["PAYMENT METHOD"] = "Other";
	}

	$csv_output["BILLING TITLE"] = @$testitem["billing_address"]["prefix"];
	$csv_output["BILLING FNAME"] = cleanup($testitem["billing_address"]["firstname"]);
	$csv_output["BILLING LNAME"] = cleanup($testitem["billing_address"]["lastname"]);
	$csv_output["BILLING ADDRESS 1"] = cleanup($testitem["billing_address"]["street"][0]);
	$csv_output["BILLING ADDRESS 2"] = cleanup(@$testitem["billing_address"]["street"][1]);
	$csv_output["BILLING CITY"] = cleanup($testitem["billing_address"]["city"]);
	$csv_output["BILLING STATE"] = cleanup($testitem["billing_address"]["region_code"]);
	$csv_output["BILLING COUNTRY"] = cleanup($testitem["billing_address"]["country_id"]);
	$csv_output["BILLING ZIP CODE"] = cleanup($testitem["billing_address"]["postcode"]);
	$csv_output["SHIPPING TITLE"] = @$testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["prefix"];
	$csv_output["SHIPPING FNAME"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["firstname"]);
	$csv_output["SHIPPING LNAME"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["lastname"]);
	$csv_output["SHIPPING ADDRESS 1"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["street"][0]);
	$csv_output["SHIPPING ADDRESS 2"] = cleanup(@$testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["street"][1]);
	$csv_output["SHIPPING CITY"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["city"]);
	$csv_output["SHIPPING STATE"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["region_code"]);
	$csv_output["SHIPPING COUNTRY"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["country_id"]);
	$csv_output["SHIPPING ZIP CODE"] = cleanup($testitem["extension_attributes"]["shipping_assignments"][0]["shipping"]["address"]["postcode"]);
	$csv_output["PHONE"] = str_replace(")", "", str_replace("(", "", str_replace(" ", "", str_replace("-", "", cleanup($testitem["billing_address"]["telephone"])))));

	if ($testitem["billing_address"]["email"] <> "order@excelcatalogues.com") {
		$csv_output["EMAIL"] = $testitem["billing_address"]["email"];
	}else {
		$csv_output["EMAIL"] = '';
	}

	
	$csv_output["LANGUAGE"] = get_storeview_lang($testitem["store_id"], $token);
	$csv_output["SHIPPING METHOD"] = cleanup($testitem["shipping_description"]);
	$csv_output["MEMBERSHIP"] = "0";
	$csv_output[" INSURANCE"] = "";
	$csv_output[" LOGIN"] = "";


	//echo implode(",", $csv_output);
	fputcsv($output, $csv_output);

	//echo "<br>";
}

// change status
changeStatus($testitem['entity_id'], $testitem['increment_id'], "confirmed", $token);
	
}


function truncate_to_eight($nbr){
	$output = "";
	$zero = 0; //we increment this to 2 then stop removing 0s
	
	for ($i=0; $i < strlen($nbr); $i++) { 
		if ($nbr[$i] <> 0) {
			$output .= $nbr[$i];
		}else{
			if ($zero == 2) {
				$output .= $nbr[$i];
			}else{
				$zero++;
			}
		}
	}
	
	return $output;
}

function cleanup($string){
	$string = str_replace("-", " ", $string);
	$string = preg_replace("/[^0-9-\s\p{L}]/u", "", $string);
	return $string;
}

function get_storeview_lang($id, $token){
	$ch = curl_init("http://coolid.ca/index.php/rest/V1/store/storeConfigs");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
	$stores = curl_exec($ch);

	

	$stores = json_decode($stores, 1);
	foreach ($stores as $store) {
		if ($store["id"]==$id) {
			$lang = $store["locale"];
		}
	}
	
	@$lang = explode("_",@$lang);

	return @$lang[0];
}

function changeStatus($entity, $increment_id, $newstatus, $token){
	$ch = curl_init("http://coolid.ca/index.php/rest/V1/orders");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, '{"entity":{"entity_id":"'.$entity.'","increment_id":"'.$increment_id.'","status":"'.$newstatus.'"}}');
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
	$result = curl_exec($ch);
}


die();
echo "<br>";

echo "shopperid,quantity,description,unit_price,units,product_code,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,";
echo "<br>";
//foreach
$productdetails = array();
$productdetails["shopperid"] = $testitem["increment_id"];
$productdetails["quantity"] = $testitem["items"][0]["qty_ordered"];
$productdetails["description"] = $testitem["items"][0]["name"];
$productdetails["unit_price"] = $testitem["items"][0]["price"];
$productdetails["units"] = "";
$productdetails["product_code"] = $testitem["items"][0]["sku"];

echo implode(",", $productdetails);

print_r($csv_input);

echo '<pre>';print_r($result);




?>