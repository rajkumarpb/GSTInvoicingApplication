<?php
	include('config.php');
	$type = mysqli_real_escape_string($db,$_POST['type']);
	function transpose($array) {
		return array_map(null, ...$array);
	}
	
	function insertItemInvoice($data,$invId,$db)
	{
		$invStatus=1;
		deleteItemsfromInvoice($invId,$db);
		for ($i = 0; $i < count($data); $i++) {
			$insert_query = "insert into SALES_TRANSACTIONS (CGST, Discount, GSTRate, IGST, InvoiceNo, ItemID, Quantity, Rate, SGST, TaxableValue, TotalItemValue) values ('".$data[$i]['cgst']."','".$data[$i]['dis']."','".$data[$i]['gstrate']."','".$data[$i]['igst']."','".$invId."','".$data[$i]['itemID']."','".$data[$i]['qty']."','".$data[$i]['rate']."','".$data[$i]['sgst']."','".$data[$i]['taxableVal']."','".$data[$i]['itemtotal']."')";
			$runcheck=mysqli_query($db,$insert_query);
			if ( false===$runcheck ) {
				$invStatus = 0;
				printf("error: %s\n", mysqli_error($db));
			}
			$UPDATE_ITEM_QUERY = "Update ITEM_MASTER set Quantity= Quantity-'".$data[$i]['qty']."' where id=".$data[$i]['itemID'];
			$runcheck=mysqli_query($db,$UPDATE_ITEM_QUERY);
		}
		return $invStatus;
	}
	
	function deleteItemsfromInvoice($invId,$db)
	{
		$get_items="update sales_transactions st 
		INNER JOIN item_master im on im.ID=st.ItemID 
		set im.Quantity=im.Quantity+st.Quantity where st.InvoiceNo= '".$invId."'";
		$runcheck=mysqli_query($db,$get_items);
		$deleteItem_query="Delete from SALES_TRANSACTIONS where InvoiceNo = '".$invId."'";
		$runcheck=mysqli_query($db,$deleteItem_query);
	}
	
	function insertItemInvoicePurchase($data,$invId,$db)
	{
		$invStatus=1;
		deleteItemsfromInvoicePurchase($invId,$db);
		for ($i = 0; $i < count($data); $i++) {
			$insert_query = "insert into PURCHASE_TRANSACTIONS (CGST, Discount, GSTRate, IGST, InvoiceNo, ItemID, Quantity, Rate, SGST, TaxableValue, TotalItemValue) values ('".$data[$i]['cgst']."','".$data[$i]['dis']."','".$data[$i]['gstrate']."','".$data[$i]['igst']."','".$invId."','".$data[$i]['itemID']."','".$data[$i]['qty']."','".$data[$i]['rate']."','".$data[$i]['sgst']."','".$data[$i]['taxableVal']."','".$data[$i]['itemtotal']."')";
			$runcheck=mysqli_query($db,$insert_query);
			if ( false===$runcheck ) {
				$invStatus = 0;
				printf("error: %s\n", mysqli_error($db));
			}
			$UPDATE_ITEM_QUERY = "Update ITEM_MASTER set Quantity= Quantity+'".$data[$i]['qty']."' where id=".$data[$i]['itemID'];
			$runcheck=mysqli_query($db,$UPDATE_ITEM_QUERY);
		}
		return $invStatus;
	}
	
	function deleteItemsfromInvoicePurchase($invId,$db)
	{
		$get_items="update purchase_transactions st 
		INNER JOIN item_master im on im.ID=st.ItemID 
		set im.Quantity=im.Quantity-st.Quantity where st.InvoiceNo= '".$invId."'";
		$runcheck=mysqli_query($db,$get_items);
		$deleteItem_query="Delete from PURCHASE_TRANSACTIONS where InvoiceNo = '".$invId."'";
		$runcheck=mysqli_query($db,$deleteItem_query);
	}
	
	if($type=="1")
	{
		$query_type = mysqli_real_escape_string($db,$_POST['query_type']);
		$VendorName = mysqli_real_escape_string($db,$_POST['vendorName']);
		$GSTNUMBER = mysqli_real_escape_string($db,$_POST['custGST']);
		$State = mysqli_real_escape_string($db,$_POST['drpState']);
		$City = mysqli_real_escape_string($db,$_POST['custCity']);
		$contactPerson = mysqli_real_escape_string($db,$_POST['custContactName']);
		$MobileNumber = mysqli_real_escape_string($db,$_POST['custMobileNumber']);
		$PAN = mysqli_real_escape_string($db,$_POST['custPAN']);
		$Address = mysqli_real_escape_string($db,$_POST['custAddress']);
		$pincode = mysqli_real_escape_string($db,$_POST['custPin']);
		if($query_type == "-99")
		{
			
			$insert_query = "insert into CUSTOMER_MASTER (VendorName, GSTNUMBER, State, City, contactPerson, MobileNumber, PAN, Address, pincode) values ('".$VendorName."','".$GSTNUMBER."','".$State."','".$City."','".$contactPerson."','".$MobileNumber."','".$PAN."','".$Address."','".$pincode."')";
			$runcheck=mysqli_query($db,$insert_query);
			if ( false===$runcheck ) {
			  printf("error: %s\n", mysqli_error($db));
			}
			else {
			  echo '1';
			}
		}
		else
		{
			$update_query = "Update CUSTOMER_MASTER set VendorName= '".$VendorName."',GSTNUMBER= '".$GSTNUMBER."',State= '".$State."',City='".$City."',contactPerson='".$contactPerson."',MobileNumber='".$MobileNumber."',PAN='".$PAN."',Address='".$Address."',pincode='".$pincode."' where id=".$query_type;
			$runcheck=mysqli_query($db,$update_query);
			if ( false===$runcheck ) {
			  printf("error: %s\n", mysqli_error($db));
			}
			else {
			  echo '1';
			}
		}
	}
	else if($type=="2")
	{
		$sql12="SELECT ID,vendorName,gstnumber,state,Mobilenumber FROM CUSTOMER_MASTER";
		$result = mysqli_query($db,$sql12);
		$ID = Array();$vendorName = Array();$gstnumber = Array();$state = Array();$mobilenumber = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$ID[] = $row["ID"]; 
			$vendorName[] = $row["vendorName"];
			$gstnumber[] = $row["gstnumber"]; 
			$state[] = $row["state"];
			$mobilenumber[] = $row["Mobilenumber"]; 
		}
		//$row = mysqli_fetch_array($result);
		$res = array($ID, $vendorName,$gstnumber,$state,$mobilenumber);
		$res=transpose($res);
		echo json_encode($res);
	}

	else if($type=="3")
	{
		$custId = mysqli_real_escape_string($db,$_POST['id']);
		$total_row='select * from CUSTOMER_MASTER where id='.$custId;
		$result=mysqli_query($db,$total_row);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
		
	}
	else if($type=="4")
	{
		$query_type = mysqli_real_escape_string($db,$_POST['query_type']);
		$Description = mysqli_real_escape_string($db,$_POST['Description']);
		$ItemType = mysqli_real_escape_string($db,$_POST['ItemType']);
		$HSN = mysqli_real_escape_string($db,$_POST['HSN']);
		$ITEMCODE = mysqli_real_escape_string($db,$_POST['ITEMCODE']);
		$UNIT = mysqli_real_escape_string($db,$_POST['UNIT']);
		$TAXRATE = mysqli_real_escape_string($db,$_POST['TAXRATE']);
		$DISCOUNT = mysqli_real_escape_string($db,$_POST['DISCOUNT']);
		$CESSAMOUNT = str_replace(array(":", "-", "/", "*","'"), '',mysqli_real_escape_string($db,$_POST['CESSAMOUNT']));
		$PURCHASEPRICE = mysqli_real_escape_string($db,$_POST['PURCHASEPRICE']);
		$SELLINGPRICE = mysqli_real_escape_string($db,$_POST['SELLINGPRICE']);
		if($query_type == "-99")
		{
			
			$insert_query = "insert into ITEM_MASTER (Description, ItemType, HSN, ITEMCODE, UNIT, TAXRATE, DISCOUNT, CESSAMOUNT, PURCHASEPRICE, SELLINGPRICE, Quantity) values ('".$Description."','".$ItemType."','".$HSN."','".$ITEMCODE."','".$UNIT."','".$TAXRATE."','".$DISCOUNT."','".$CESSAMOUNT."','".$PURCHASEPRICE."','".$SELLINGPRICE."','0')";
			$runcheck=mysqli_query($db,$insert_query);
			if ( false===$runcheck ) {
			  printf("error: %s\n", mysqli_error($db));
			}
			else {
			  echo '1';
			}
		}
		else
		{
			$update_query = "Update ITEM_MASTER set Description= '".$Description."',ItemType= '".$ItemType."',HSN= '".$HSN."',ITEMCODE='".$ITEMCODE."',UNIT='".$UNIT."',TAXRATE='".$TAXRATE."',DISCOUNT='".$DISCOUNT."',CESSAMOUNT='".$CESSAMOUNT."',PURCHASEPRICE='".$PURCHASEPRICE."',SELLINGPRICE='".$SELLINGPRICE."' where id=".$query_type;
			$runcheck=mysqli_query($db,$update_query);
			if ( false===$runcheck ) {
			  printf("error: %s\n", mysqli_error($db));
			}
			else {
			  echo '1';
			}
		}
	}
	else if($type=="5")
	{
		$sql12="SELECT ID,Description,TAXRATE,SELLINGPRICE,PURCHASEPRICE,HSN, DISCOUNT, CONCAT(QUANTITY,' ',UNIT) as 'QUANTITY' FROM ITEM_MASTER";
		$result = mysqli_query($db,$sql12);
		$ID = Array();$Description = Array();$TAXRATE = Array();$SELLINGPRICE = Array();$PURCHASEPRICE = Array();$HSN = Array();$DISCOUNT = Array();$QUANTITY = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$ID[] = $row["ID"]; 
			$Description[] = $row["Description"];
			$TAXRATE[] = $row["TAXRATE"]; 
			$SELLINGPRICE[] = $row["SELLINGPRICE"];
			$PURCHASEPRICE[] = $row["PURCHASEPRICE"]; 
			$HSN[] = $row["HSN"]; 
			$DISCOUNT[] = $row["DISCOUNT"]; 
			$QUANTITY[] = $row["QUANTITY"]; 
		}
		//$row = mysqli_fetch_array($result);
		$res = array($ID, $Description, $HSN, $PURCHASEPRICE, $SELLINGPRICE, $TAXRATE, $DISCOUNT,$QUANTITY);
		$res=transpose($res);
		echo json_encode($res);
	}

	else if($type=="6")
	{
		$custId = mysqli_real_escape_string($db,$_POST['id']);
		$total_row='select * from ITEM_MASTER where id='.$custId;
		$result=mysqli_query($db,$total_row);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
		
	}
	else if($type=="7")
	{
		$total_row='select ID,VendorName from CUSTOMER_MASTER';
		$result=mysqli_query($db,$total_row);
		$ID = Array();$VendorName = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$ID[] = $row["ID"]; 
			$VendorName[] = $row["VendorName"];
			
		}
		//$row = mysqli_fetch_array($result);
		$res = array($ID, $VendorName);
		//$res=transpose($res);
		echo json_encode($res);
		//$rows = mysqli_fetch_assoc($result);
		//echo json_encode($rows);
		
	}
	else if($type=="8")
	{
		$custName = mysqli_real_escape_string($db,$_POST['customerName']);
		$select_gst="Select GSTNUMBER, ID,Address from CUSTOMER_MASTER where VendorName like '".$custName."'";
		$result=mysqli_query($db,$select_gst);
		$row = mysqli_fetch_array($result);
		$gst = $row["GSTNUMBER"];
		$cust_type = "B2C";
		$created_date = date('d-m-Y');
		if(strlen($gst) > 0)
		{
			$cust_type = "B2B";
		}
		$insert_query = "insert into SALES_MASTER (CustomerID, TYPE,InvoiceAddress) values ('".$row["ID"]."','".$cust_type."','".$row["Address"]."')";
		$runcheck=mysqli_query($db,$insert_query);
		if ( false===$runcheck ) {
			printf("error: %s\n", mysqli_error($db));
		}
		else {
			echo '1,'.$row["ID"].','.mysqli_insert_id($db);
		}
		
	}
	else if($type=="9")
	{
		$total_row='select Description from ITEM_MASTER';
		$result=mysqli_query($db,$total_row);
		$Description = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$Description[] = $row["Description"];
			
		}
		$res = array($Description);
		echo json_encode($res);
	}
	else if($type=="10")
	{
		$itemDesp = mysqli_real_escape_string($db,$_POST['Description']);
		$total_row="select * from ITEM_MASTER where Description like '".$itemDesp."'";
		$result=mysqli_query($db,$total_row);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
	}
	
	else if($type=="11")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$stateToShip = mysqli_real_escape_string($db,$_POST['stateToShip']);
		$itemBillDate = mysqli_real_escape_string($db,$_POST['itemBillDate']);	
		$taxableTotal = mysqli_real_escape_string($db,$_POST['taxableTotal']);
		$grandTotalCGST = mysqli_real_escape_string($db,$_POST['grandTotalCGST']);
		$grandTotalSGST = mysqli_real_escape_string($db,$_POST['grandTotalSGST']);
		$grandTotalIGST = mysqli_real_escape_string($db,$_POST['grandTotalIGST']);
		$grandTotal = mysqli_real_escape_string($db,$_POST['grandTotal']);
		$invoiceAddress = str_replace(array("/:", "/-", "//", "/*","/'"), ' ',mysqli_real_escape_string($db,$_POST['invoiceAddress']));	
		$note = str_replace(array("/:", "/-", "//", "/*","/'"), ' ',mysqli_real_escape_string($db,$_POST['note']));	
		$sql11 = "update SALES_MASTER set PlaceOfSupply ='".$stateToShip."', note = '".$note."', CreatedDate = '".$itemBillDate."', BillDate = '".$itemBillDate."', InvoiceAddress ='".$invoiceAddress."', TotalTaxable ='".$taxableTotal."', TOTALCGST ='".$grandTotalCGST."', TOTALSGST ='".$grandTotalSGST."', TOTALIGST ='".$grandTotalIGST."', GrandTotal ='".$grandTotal."', CreatedDate = '".$itemBillDate."' where InvoiceNo='".$invId."'";
		$runcheck=mysqli_query($db,$sql11);
		if ( false===$runcheck ) {
			printf("error: %s\n", mysqli_error($db));
		}
		else {
			echo '1';
		}
	}
	else if($type=="12")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$tableJSONObj =$_POST['JSONtableObject'];
		$action = mysqli_real_escape_string($db,$_POST['action']);
		$data = json_decode($tableJSONObj,true);
		$query_result = insertItemInvoice($data, $invId,$db);
		echo $query_result;
	}
	
	else if($type=="13")
	{
		$fromDate = mysqli_real_escape_string($db,$_POST['fromDate']);
		$toDate = mysqli_real_escape_string($db,$_POST['toDate']);
		$mode = mysqli_real_escape_string($db,$_POST['mode']);
		if($mode == "1")
		{
			$sql13="SELECT sm.BillDate as Date,CONCAT('000',sm.InvoiceNo) as InvoiceNo, cm.VendorName as custName,
			cm.GSTNUMBER as GST, sm.TotalTaxable as TotalTaxable, (sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as Tax,
			sm.GrandTotal as GrandTotal, sm.TYPE as custType
			FROM sales_master sm INNER JOIN customer_master cm ON sm.CustomerID = cm.ID";
		}
		else if($mode == "2")
		{
			$sql13="SELECT sm.BillDate as Date,CONCAT('000',sm.InvoiceNo) as InvoiceNo, cm.VendorName as custName,
			cm.GSTNUMBER as GST, sm.TotalTaxable as TotalTaxable, (sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as Tax,
			sm.GrandTotal as GrandTotal, sm.TYPE as custType
			FROM sales_master sm INNER JOIN customer_master cm ON sm.CustomerID = cm.ID where (str_to_date(sm.BillDate, '%d/%m/%Y') BETWEEN str_to_date('".$fromDate."', '%d/%m/%Y') AND str_to_date('".$toDate."', '%d/%m/%Y'))";
		}
		$result = mysqli_query($db,$sql13);
		$Date = Array();$InvoiceNo = Array();$custName = Array();$GST = Array();$TotalTaxable = Array();$Tax = Array();$GrandTotal = Array();$custType = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$Date[] = $row["Date"]; 
			$InvoiceNo[] = $row["InvoiceNo"];
			$custName[] = $row["custName"]; 
			$GST[] = $row["GST"];
			$TotalTaxable[] = $row["TotalTaxable"]; 
			$Tax[] = $row["Tax"]; 
			$GrandTotal[] = $row["GrandTotal"]; 
			$custType[] = $row["custType"]; 
		}
		//$row = mysqli_fetch_array($result);
		$res = array($Date, $InvoiceNo, $custName, $GST, $TotalTaxable, $Tax, $GrandTotal, $custType);
		$res=transpose($res);
		echo json_encode($res);
	}
	
	else if($type=="14")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$sql14="SELECT sm.Note as note,sm.BillDate as Date, sm.PlaceOfSupply as PlaceOfSupply, sm.InvoiceAddress as InvoiceAddress,sm.TOTALCGST as TOTALCGST,sm.TOTALSGST as TOTALSGST,sm.TOTALIGST as TOTALIGST, sm.InvoiceNo as InvoiceNo, cm.VendorName as custName,
		cm.GSTNUMBER as GST, sm.TotalTaxable as TotalTaxable, (sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as Tax,
		sm.GrandTotal as GrandTotal, sm.TYPE as custType
		FROM sales_master sm INNER JOIN customer_master cm ON sm.CustomerID = cm.ID where sm.InvoiceNo='".$invId."'";
		$result=mysqli_query($db,$sql14);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
		
	}
	else if($type=="15")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$sql15="select *,SALES_TRANSACTIONS.Quantity as iQty,im.Quantity as qtyLeft,im.Description as desp, im.HSN as hsn, im.UNIT as un from SALES_TRANSACTIONS INNER JOIN item_master im on im.ID=sales_transactions.ItemID where InvoiceNo='".$invId."'";
		$result = mysqli_query($db,$sql15);
		$CGST = Array();$SGST = Array();$IGST = Array();$TotalItemValue = Array();$TaxableValue = Array();$Discount = Array();$GSTRate = Array();$Rate = Array();$Quantity = Array();$ItemID = Array();$InvoiceNo = Array();$ID = Array();$qtyLeft = Array();$desp = Array();$hsn = Array();$un = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$CGST[] = $row["CGST"]; 
			$SGST[] = $row["SGST"];
			$IGST[] = $row["IGST"]; 
			$TotalItemValue[] = $row["TotalItemValue"];
			$TaxableValue[] = $row["TaxableValue"]; 
			$Discount[] = $row["Discount"]; 
			$GSTRate[] = $row["GSTRate"]; 
			$Rate[] = $row["Rate"]; 
			$Quantity[] = $row["iQty"]; 
			$ItemID[] = $row["ItemID"]; 
			$InvoiceNo[] = $row["InvoiceNo"]; 
			$ID[] = $row["ID"]; 
			$qtyLeft[] = $row["qtyLeft"]; 
			$desp[] = $row["desp"]; 
			$hsn[] = $row["hsn"]; 
			$un[] = $row["un"]; 
		}
		$res = array($CGST, $SGST, $IGST,$TotalItemValue,$TaxableValue,$Discount, $GSTRate, $Rate,$Quantity	,$ItemID, $ID, $qtyLeft, $desp, $hsn, $un);
		echo json_encode($res);
	}
	else if($type=="16")
	{
		$fromDate = mysqli_real_escape_string($db,$_POST['fromDate']);
		$toDate = mysqli_real_escape_string($db,$_POST['toDate']);
		$mode = mysqli_real_escape_string($db,$_POST['mode']);
		if($mode == "1")
		{
			$sql13="SELECT sm.BillDate as Date,sm.InvoiceNo as InvoiceNo, cm.VendorName as custName,
			cm.GSTNUMBER as GST, sm.TotalTaxable as TotalTaxable, (sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as Tax,
			sm.GrandTotal as GrandTotal, sm.TYPE as custType
			FROM purchase_master sm INNER JOIN customer_master cm ON sm.CustomerID = cm.ID";
		}
		else if($mode == "2")
		{
			$sql13="SELECT sm.BillDate as Date,sm.InvoiceNo as InvoiceNo, cm.VendorName as custName,
			cm.GSTNUMBER as GST, sm.TotalTaxable as TotalTaxable, (sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as Tax,
			sm.GrandTotal as GrandTotal, sm.TYPE as custType
			FROM purchase_master sm INNER JOIN customer_master cm ON sm.CustomerID = cm.ID where (str_to_date(sm.BillDate, '%d/%m/%Y') BETWEEN str_to_date('".$fromDate."', '%d/%m/%Y') AND str_to_date('".$toDate."', '%d/%m/%Y'))";

		}
		$result = mysqli_query($db,$sql13);
		$Date = Array();$InvoiceNo = Array();$custName = Array();$GST = Array();$TotalTaxable = Array();$Tax = Array();$GrandTotal = Array();$custType = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$Date[] = $row["Date"]; 
			$InvoiceNo[] = $row["InvoiceNo"];
			$custName[] = $row["custName"]; 
			$GST[] = $row["GST"];
			$TotalTaxable[] = $row["TotalTaxable"]; 
			$Tax[] = $row["Tax"]; 
			$GrandTotal[] = $row["GrandTotal"]; 
			$custType[] = $row["custType"]; 
		}
		//$row = mysqli_fetch_array($result);
		$res = array($Date, $InvoiceNo, $custName, $GST, $TotalTaxable, $Tax, $GrandTotal, $custType);
		$res=transpose($res);
		echo json_encode($res);
	}
	else if($type=="17")
	{
		$custName = mysqli_real_escape_string($db,$_POST['customerName']);
		$sql17="Select GSTNUMBER, ID from CUSTOMER_MASTER where VendorName like '".$custName."'";
		$result=mysqli_query($db,$sql17);
		$row = mysqli_fetch_array($result);
		$gst = $row["GSTNUMBER"];
		$cust_type = "B2C";
		$created_date = date('d-m-Y');
		if(strlen($gst) > 0)
		{
			$cust_type = "B2B";
		}
		$insert_query = "insert into PURCHASE_MASTER (CustomerID, TYPE) values ('".$row["ID"]."','".$cust_type."')";
		$runcheck=mysqli_query($db,$insert_query);
		if ( false===$runcheck ) {
			printf("error: %s\n", mysqli_error($db));
		}
		else {
			echo '1,'.$row["ID"].','.mysqli_insert_id($db);
		}
		
	}
	else if($type=="18")
    {
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$sql14="SELECT sm.Note as note,sm.BillDate as Date,sm.PurchaseNo as PurchaseNo, sm.PlaceOfSupply as PlaceOfSupply, sm.InvoiceAddress as InvoiceAddress,sm.TOTALCGST as TOTALCGST,sm.TOTALSGST as TOTALSGST,sm.TOTALIGST as TOTALIGST, sm.InvoiceNo as InvoiceNo, cm.VendorName as custName,
		cm.GSTNUMBER as GST, sm.TotalTaxable as TotalTaxable, (sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as Tax,
		sm.GrandTotal as GrandTotal, sm.TYPE as custType
		FROM purchase_master sm INNER JOIN customer_master cm ON sm.CustomerID = cm.ID where sm.InvoiceNo='".$invId."'";
		$result=mysqli_query($db,$sql14);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
		
	}
	else if($type=="19")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$sql15="select *,PURCHASE_TRANSACTIONS.Quantity as iQty,im.Quantity as qtyLeft,im.Description as desp, im.HSN as hsn, im.UNIT as un from PURCHASE_TRANSACTIONS INNER JOIN item_master im on im.ID=PURCHASE_TRANSACTIONS.ItemID where InvoiceNo='".$invId."'";
		$result = mysqli_query($db,$sql15);
		$CGST = Array();$SGST = Array();$IGST = Array();$TotalItemValue = Array();$TaxableValue = Array();$Discount = Array();$GSTRate = Array();$Rate = Array();$Quantity = Array();$ItemID = Array();$InvoiceNo = Array();$ID = Array();$qtyLeft = Array();$desp = Array();$hsn = Array();$un = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$CGST[] = $row["CGST"]; 
			$SGST[] = $row["SGST"];
			$IGST[] = $row["IGST"]; 
			$TotalItemValue[] = $row["TotalItemValue"];
			$TaxableValue[] = $row["TaxableValue"]; 
			$Discount[] = $row["Discount"]; 
			$GSTRate[] = $row["GSTRate"]; 
			$Rate[] = $row["Rate"]; 
			$Quantity[] = $row["iQty"]; 
			$ItemID[] = $row["ItemID"]; 
			$InvoiceNo[] = $row["InvoiceNo"]; 
			$ID[] = $row["ID"]; 
			$qtyLeft[] = $row["qtyLeft"]; 
			$desp[] = $row["desp"]; 
			$hsn[] = $row["hsn"]; 
			$un[] = $row["un"]; 
		}
		$res = array($CGST, $SGST, $IGST,$TotalItemValue,$TaxableValue,$Discount, $GSTRate, $Rate,$Quantity	,$ItemID, $ID, $qtyLeft, $desp, $hsn, $un);
		echo json_encode($res);
	}
	else if($type=="20")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$tableJSONObj =$_POST['JSONtableObject'];
		$action = mysqli_real_escape_string($db,$_POST['action']);
		$data = json_decode($tableJSONObj,true);
		$query_result = insertItemInvoicePurchase($data, $invId,$db);
		echo $query_result;
	}
	else if($type=="21")
	{
		$invId = mysqli_real_escape_string($db,$_POST['invId']);
		$purchaseNo = str_replace(array("/:", "/-", "//", "/*","/'"), ' ',mysqli_real_escape_string($db,$_POST['purchaseNo']));	
		$stateToShip = mysqli_real_escape_string($db,$_POST['stateToShip']);
		$itemBillDate = mysqli_real_escape_string($db,$_POST['itemBillDate']);	
		$taxableTotal = mysqli_real_escape_string($db,$_POST['taxableTotal']);
		$grandTotalCGST = mysqli_real_escape_string($db,$_POST['grandTotalCGST']);
		$grandTotalSGST = mysqli_real_escape_string($db,$_POST['grandTotalSGST']);
		$grandTotalIGST = mysqli_real_escape_string($db,$_POST['grandTotalIGST']);
		$grandTotal = mysqli_real_escape_string($db,$_POST['grandTotal']);
		$invoiceAddress = str_replace(array("/:", "/-", "//", "/*","/'"), ' ',mysqli_real_escape_string($db,$_POST['invoiceAddress']));	
		$note = str_replace(array("/:", "/-", "//", "/*","/'"), ' ',mysqli_real_escape_string($db,$_POST['note']));	
		
		$sql21 = "update PURCHASE_MASTER set PlaceOfSupply ='".$stateToShip."', note = '".$note."', CreatedDate = '".$itemBillDate."', BillDate = '".$itemBillDate."', InvoiceAddress ='".$invoiceAddress."', TotalTaxable ='".$taxableTotal."', TOTALCGST ='".$grandTotalCGST."', TOTALSGST ='".$grandTotalSGST."', TOTALIGST ='".$grandTotalIGST."', GrandTotal ='".$grandTotal."', CreatedDate = '".$itemBillDate."', PurchaseNo = '".$purchaseNo."' where InvoiceNo='".$invId."'";
		$runcheck=mysqli_query($db,$sql21);
		if ( false===$runcheck ) {
			printf("error: %s\n", mysqli_error($db));
		}
		else {
			echo '1';
		}
	}
	else if($type=="22")
	{
		$itemName = mysqli_real_escape_string($db,$_POST['itemName']);
		$fromDate = mysqli_real_escape_string($db,$_POST['fromDate']);
		$toDate = mysqli_real_escape_string($db,$_POST['toDate']);
		$sql22_1="select im.Description as s_desp,im.Quantity as s_qty,sm.BillDate as s_billdate,concat('-',(st.Quantity)) as s_trans,CONCAT('000',sm.InvoiceNo) as s_inv  from item_master im 
		INNER JOIN sales_transactions st on im.ID=st.ItemID
		INNER JOIN sales_master sm on st.InvoiceNo= sm.InvoiceNo
		where (str_to_date(sm.BillDate, '%d/%m/%Y') BETWEEN str_to_date('".$fromDate."', '%d/%m/%Y') AND str_to_date('".$toDate."', '%d/%m/%Y')) and im.Description = '".$itemName."' order by sm.BillDate desc";
		$result_1 = mysqli_query($db,$sql22_1);
		$sql22_2="select im.Description as p_desp,im.Quantity as p_qty,pm.BillDate as p_billdate,concat('+',(pt.Quantity)) as p_trans,pt.InvoiceNo as p_inv from item_master im 
		INNER JOIN purchase_transactions pt on im.ID=pt.ItemID
		INNER JOIN purchase_master pm on pt.InvoiceNo= pm.InvoiceNo
		where (str_to_date(pm.BillDate, '%d/%m/%Y') BETWEEN str_to_date('".$fromDate."', '%d/%m/%Y') AND str_to_date('".$toDate."', '%d/%m/%Y')) and im.Description = '".$itemName."' order by pm.BillDate desc";
		$result_2 = mysqli_query($db,$sql22_2);
		$ID = Array();$Description = Array();$TAXRATE = Array();$SELLINGPRICE = Array();$PURCHASEPRICE = Array();$HSN = Array();$DISCOUNT = Array();$QUANTITY = Array();
		$rows[] = array();
		$s_rows[] = array();
		$p_rows[] = array();
		$i=1;
		$row = array ($i,$itemName,' ',' ',' ',' ',' ','0');
		$rows[0] = $row;
		$s=0;
		while ($row = mysqli_fetch_array($result_1)) 
		{
			$s_rows[$s]=$row;
			$s=$s+1;
			//
		}
		$s_rows[$s]=array("s_billdate" => "30/12/2999");
		
		$s=0;
		while ($row = mysqli_fetch_array($result_2)) 
		{
			$p_rows[$s]=$row;
			$s=$s+1;
		}
		$p_rows[$s]=array("p_billdate" => "30/12/2999");
		$k=0;
		$len=count($s_rows);
		$len1=count($p_rows);
		$s=0;
		$i=0;$j=0;
		$DATE_INF=date('30/12/2999');
		for($k=1;$k<=($len1+$len);$k++)
		{
			$date1=date($p_rows[$i]['p_billdate']);
			$date2=date($s_rows[$j]['s_billdate']);
			if($date1 <= $date2)
			{
				if($date1 != $DATE_INF)
				{
					$s=(int)$s+(int)$p_rows[$i]['p_trans'];
					$r=array(($k+1),$p_rows[$i]['p_desp'],$p_rows[$i]['p_billdate'],'Purchase',$p_rows[$i]['p_inv'],$p_rows[$i]['p_trans'],' ',$s);
					$rows[$k] = $r;
					$i=$i+1;
				}
			}
			else
			{
				if($date2 != $DATE_INF)
				{
					$s=((int)$s+(int)$s_rows[$j]['s_trans']);
					$r=array(($k+1),$s_rows[$j]['s_desp'],$s_rows[$j]['s_billdate'],'Sales',$s_rows[$j]['s_inv'],' ' ,$s_rows[$j]['s_trans'],$s);
					$rows[$k] = $r;
					$j=$j+1;
				}
			}
		}
		$r = array (($k-1),' ',' ',' ',' ',' ','Grand Total',$s);
		$rows[$k-2] = $r;
		echo json_encode($rows);
	}
	else if($type=="23")
	{
		$fromDate = mysqli_real_escape_string($db,$_POST['fromDate']);
		$toDate = mysqli_real_escape_string($db,$_POST['toDate']);
		$sql23_1="select 'Sales' as type, count(InvoiceNo) as cnt,'".$fromDate."' as frmDt,'".$toDate."' as toDt, sum(sm.TotalTaxable) as amount, sum(sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as gst, sum(sm.GrandTotal) as netamount from sales_master sm where (str_to_date(sm.BillDate, '%d/%m/%Y') BETWEEN str_to_date('".$fromDate."', '%d/%m/%Y') AND str_to_date('".$toDate."', '%d/%m/%Y'))";
		$result_1 = mysqli_query($db,$sql23_1);
		$sql23_2="select 'Purchase' as type, count(InvoiceNo) as cnt,'".$fromDate."' as frmDt,'".$toDate."' as toDt, sum(sm.TotalTaxable) as amount, sum(sm.TOTALCGST+sm.TOTALSGST+sm.TOTALIGST) as gst, sum(sm.GrandTotal) as netamount from purchase_master sm where (str_to_date(sm.BillDate, '%d/%m/%Y') BETWEEN str_to_date('".$fromDate."', '%d/%m/%Y') AND str_to_date('".$toDate."', '%d/%m/%Y'))";
		$result_2 = mysqli_query($db,$sql23_2);
		$rows[] = array();
		while ($row = mysqli_fetch_array($result_1)) 
		{
			$row = array ('1',$row['type'],$row['cnt'],$row['frmDt'],$row['toDt'],round($row['amount'],2),round($row['gst'],2),round($row['netamount'],2)); 
			$rows[0] = $row;
		}

		while ($row = mysqli_fetch_array($result_2)) 
		{
			$row = array ('2',$row['type'],$row['cnt'],$row['frmDt'],$row['toDt'],round($row['amount'],2),round($row['gst'],2),round($row['netamount'],2)); 
			$rows[1] = $row;
		}
		echo json_encode($rows);
	}
	else if($type=="24")
	{
		$year = intval(mysqli_real_escape_string($db,$_POST['Year']));
		$sql24_1="SELECT 
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 1 THEN sm.GrandTotal ELSE 0 END) AS 'January',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 2 THEN sm.GrandTotal ELSE 0 END) AS 'February',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 3 THEN sm.GrandTotal ELSE 0 END) AS 'March',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 4 THEN sm.GrandTotal ELSE 0 END) AS 'April',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 5 THEN sm.GrandTotal ELSE 0 END) AS 'May',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 6 THEN sm.GrandTotal ELSE 0 END) AS 'June',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 7 THEN sm.GrandTotal ELSE 0 END) AS 'July',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 8 THEN sm.GrandTotal ELSE 0 END) AS 'August',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 9 THEN sm.GrandTotal ELSE 0 END) AS 'September',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 10 THEN sm.GrandTotal ELSE 0 END) AS 'October',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 11 THEN sm.GrandTotal ELSE 0 END) AS 'November',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 12 THEN sm.GrandTotal ELSE 0 END) AS 'December'
				FROM
					sales_master sm
				WHERE
					YEAR(str_to_date(sm.BillDate, '%d/%m/%Y')) = ".$year;
		$result_1 = mysqli_query($db,$sql24_1);
		$sql24_2="SELECT 
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 1 THEN sm.GrandTotal ELSE 0 END) AS 'January',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 2 THEN sm.GrandTotal ELSE 0 END) AS 'February',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 3 THEN sm.GrandTotal ELSE 0 END) AS 'March',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 4 THEN sm.GrandTotal ELSE 0 END) AS 'April',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 5 THEN sm.GrandTotal ELSE 0 END) AS 'May',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 6 THEN sm.GrandTotal ELSE 0 END) AS 'June',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 7 THEN sm.GrandTotal ELSE 0 END) AS 'July',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 8 THEN sm.GrandTotal ELSE 0 END) AS 'August',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 9 THEN sm.GrandTotal ELSE 0 END) AS 'September',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 10 THEN sm.GrandTotal ELSE 0 END) AS 'October',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 11 THEN sm.GrandTotal ELSE 0 END) AS 'November',
				  SUM(CASE MONTH(str_to_date(sm.BillDate, '%d/%m/%Y')) WHEN 12 THEN sm.GrandTotal ELSE 0 END) AS 'December'
				FROM
					purchase_master sm
				WHERE
					YEAR(str_to_date(sm.BillDate, '%d/%m/%Y')) = ".$year;
		$result_2 = mysqli_query($db,$sql24_2);
		$rows[] = array();
		$row = array ('Year', 'Sales', 'Purchase'); 
		$rows[0] = $row;
		$i=1;
		$r = mysqli_fetch_array($result_1);
		$r1 = mysqli_fetch_array($result_2);
		$row = array ('January',floatval($r['January']),floatval($r1['January'])); 
		$rows[$i++] = $row;
		$row = array ('February',floatval($r['February']),floatval($r1['February'])); 
		$rows[$i++] = $row;
		$row = array ('March',floatval($r['March']),floatval($r1['March'])); 
		$rows[$i++] = $row;
		$row = array ('April',floatval($r['April']),floatval($r1['April'])); 
		$rows[$i++] = $row;
		$row = array ('May',floatval($r['May']),floatval($r1['May'])); 
		$rows[$i++] = $row;
		$row = array ('June',floatval($r['June']),floatval($r1['June'])); 
		$rows[$i++] = $row;
		$row = array ('July',floatval($r['July']),floatval($r1['July'])); 
		$rows[$i++] = $row;
		$row = array ('August',floatval($r['August']),floatval($r1['August'])); 
		$rows[$i++] = $row;
		$row = array ('September',floatval($r['September']),floatval($r1['September'])); 
		$rows[$i++] = $row;
		$row = array ('October',floatval($r['October']),floatval($r1['October'])); 
		$rows[$i++] = $row;
		$row = array ('November',floatval($r['November']),floatval($r1['November'])); 
		$rows[$i++] = $row;
		$row = array ('December',floatval($r['December']),floatval($r1['December'])); 
		$rows[$i++] = $row;
		echo json_encode($rows);
	}
	else if($type=="25")
	{
		$cust_id = mysqli_real_escape_string($db,$_POST['id']);
		$total_row="select * from CUSTOMER_MASTER where ID = ".$cust_id;
		$result=mysqli_query($db,$total_row);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
	}
	else if($type=="26")
	{
		$item_id = mysqli_real_escape_string($db,$_POST['id']);
		$total_row="SELECT @s:=@s+1 serial_number,pm.BillDate as dt,cm.VendorName as vendornm,pst.InvoiceNo as invno,
		pst.Quantity as qty, pst.rate as rt, CONCAT(pst.GSTRate,'%') as gst 
		FROM (select @s:=0) as s,purchase_transactions pst
		INNER JOIN purchase_master pm ON pst.InvoiceNo = pm.InvoiceNo 
		INNER JOIN customer_master cm ON pm.CustomerID = cm.ID
		where pst.ItemID = ".$item_id;
		$result=mysqli_query($db,$total_row);
		$SR = Array();$Date = Array();$InvoiceNo = Array();$vendornm = Array();$GST = Array();$qty = Array();$rate = Array();
		while ($row = mysqli_fetch_array($result)) 
		{
			$SR[] = $row["serial_number"]; 
			$Date[] = $row["dt"]; 
			$InvoiceNo[] = $row["invno"];
			$vendornm[] = $row["vendornm"]; 
			$GST[] = $row["gst"];
			$qty[] = $row["qty"]; 
			$rate[] = $row["rt"]; 
		}
		//$row = mysqli_fetch_array($result);
		$res = array($SR, $Date, $vendornm, $InvoiceNo, $qty, $rate, $GST);
		$res=transpose($res);
		echo json_encode($res);
	}
	else if($type=="27")
	{
		$item_id = mysqli_real_escape_string($db,$_POST['id']);
		$total_row="select it.Quantity as qty,it.Description as nm from item_master it where it.ID =".$item_id;
		$result=mysqli_query($db,$total_row);
		$rows = mysqli_fetch_assoc($result);
		echo json_encode($rows);
	}
?>