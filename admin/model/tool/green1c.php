<?php
class ModelToolGreen1c extends Model{

	

	public function category($fileXml){
		if(file_exists($fileXml)){
			$xmlImport = simplexml_load_file($fileXml);
			$category = array("id","category","date");
			$category['date'] = date('Y-m-d h:i:s');
			`rm -r ../image/data/import_files/`; // delete folder

			foreach ($xmlImport->Классификатор->Группы as $clasificator ) {
				foreach ($clasificator as $key => $value) {
					foreach($value as $data => $categories){
						if($data=="Ид"){
							$category['id'] = $categories;
						}
						if($data=="Наименование"){
							$category['category'] = $categories;
						}	
					}

				
					// Checkout
					$checkCategory = $this->db->query("SELECT green1c_id FROM ".DB_PREFIX."category WHERE green1c_id ='".$category['id']."'");
					if($checkCategory->num_rows == 0){ // none category
						//INSERT
						$this->insertCategory($category['id'], $category['category'], $category['date']);
					}else {
						// UPDATE
						$this->updateCategory($category['id'], $category['category'], $category['date']);
						//DELETE if this category isn't update today
						$this->deleteCategory($category['date']);
					}			
				} 
			}
		}
	}

	// INSERT
	public function insertCategory($id, $nameCategory, $dateAdded){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category` SET status = '1', green1c_id='".$id."', date_added='".$dateAdded."', date_modified ='".$dateAdded."'");
		$category_id = $this->db->query("SELECT MAX(category_id) category_id FROM ".DB_PREFIX."category");
		foreach($category_id->rows as $category_id){
			foreach ($category_id as $key => $value) {
				$id = $value;
			}
		}
		$this->db->query("INSERT INTO ".DB_PREFIX."category_description SET category_id='".$id."', name='".$nameCategory."', language_id='1'");
		$this->db->query("INSERT INTO ".DB_PREFIX."category_path SET category_id='".$id."' , path_id='".$id."', level = '1'");
		$this->db->query("INSERT INTO ".DB_PREFIX."category_to_store SET category_id='".$id."', store_id='0'");

	}

	//UPDATE
	public function updateCategory($green1c_id,$nameCategory, $dateModifeid){
		$this->db->query("UPDATE ".DB_PREFIX."category SET date_modified='".$dateModifeid."' WHERE green1c_id='".$green1c_id."'");
		$select = $this->db->query("SELECT category_id FROM ".DB_PREFIX."category WHERE green1c_id ='".$green1c_id."'");
		foreach($select->rows as $category_id){
			foreach($category_id as $key=>$id){
				$this->db->query("UPDATE ".DB_PREFIX."category_description SET name='".$nameCategory."' WHERE category_id='".$id."'");
			}
		}
	}
	//DELETE
	public function deleteCategory($date){
		global $id;
		$checkDate = $this->db->query("SELECT category_id, date_modified FROM ".DB_PREFIX."category ");
		foreach ($checkDate->rows as $chDate) {
			foreach($chDate as $key=>$dateCat){
				
				if($key=="category_id"){
					$id = $dateCat;
				}
				$date = explode(" ", $date);
				$date = $date[0];
				$dateCat = explode(" ", $dateCat);
				$dateCat = $dateCat[0];
				if($key=="date_modified" && $dateCat != $date){
					$this->db->query("DELETE FROM ".DB_PREFIX."category WHERE category_id ='".$id."'");
					$this->db->query("DELETE FROM ".DB_PREFIX."category_description WHERE category_id ='".$id."'");
					$this->db->query("DELETE FROM ".DB_PREFIX."category_path WHERE category_id ='".$id."'");
					$this->db->query("DELETE FROM ".DB_PREFIX."category_to_store WHERE category_id ='".$id."'");
				}else{
					//Nothing do
				}
			}
		}
	}

	//Product
	public function product($fileXmlImp){
		if(file_exists($fileXmlImp)){
			$xmlImport = simplexml_load_file($fileXmlImp);
			$product = array("id","sku","name","imagePath");
			$date = date('Y-m-d h:i:s');
			foreach($xmlImport->Каталог->Товары->Товар as $product){
				foreach($product as $key => $value){
					if($key=="Ид"){
						$product['id'] = $value;
					}
					if($key=="Наименование"){
						$product['sku'] =utf8_encode(substr($value,0,10));
						$product['name'] = $value;
					}
					if($key=="Картинка"){
						$product['imagePath'] = $value;
					}

				}
				//Checkout
				$checkProduct = $this->db->query("SELECT product_green1c_id FROM ".DB_PREFIX."product WHERE product_green1c_id='".$product['id']."'");
				if($checkProduct->num_rows == 0){
					//INSERT
					$this->insertProduct($product['id'], $product['sku'], $product['name'], $product['imagePath'], $date);
				}else{
					//UPDATE
					$this->updateProduct($product['id'], $product['sku'], $product['name'], $product['imagePath'], $date);
					//DELETE 
					$this->deleteProduct($date);
				}
			}
		}
	}
	public function productPrice($fileXmlOffers){
		$this->db->query("DELETE FROM ".DB_PREFIX."product_discount");
		global $actionID, $actionName;
		global $roznID, $roznName;
		global $zakupID, $zakupName;
		global $prodID, $quantityProd; // product
		global $mID, $price; // price
		$boolZakup = false;
		if(file_exists($fileXmlOffers)){
			$xmlOffers = simplexml_load_file($fileXmlOffers);
			foreach($xmlOffers->ПакетПредложений->ТипыЦен->ТипЦены as $price){
				foreach($price as $key => $value){
					if($key == "Ид"){
						$id = (string)$value;
					}
					if($key == "Наименование"){
						if($value == "Со скидкой!"){
							$actionID = $id;
							$actionName = $value;
						}
						if($value == "Розничные!"){
							$roznID = $id;
							$roznName = $value;
						}
						if($value == "Закупка"){
							$zakupID = $id;
							$zakupName = $value;
						}
					}
				}
			}
			foreach($xmlOffers->ПакетПредложений->Предложения->Предложение as $buy){ // take id product
				foreach($buy as $key => $value){
					if($key == "Ид"){
						$prodID = $value;
					}
					if($key == "Количество"){
						$quantityProd = $value;
					}
				}
				foreach($buy->Цены->Цена as $prPrice){ // PRICE !!!!
					foreach($prPrice as $key => $value){
						if($key == "ИдТипаЦены"){
							$mID = (string)$value;
							if($mID == $actionID){
								$boolZakup = true;
							}
							if($mID == $roznID){
								$boolZakup = true;
							}
							if($mID == $zakupID){
								$boolZakup = false;
							}
						}
						if($key == "ЦенаЗаЕдиницу"  && $boolZakup == true){
							$price = $value;
							if($mID == $roznID){
								$this->updatePrice($price,$prodID); //price
							}
							if($mID == $actionID){
								$idProd = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE product_green1c_id='".$prodID."'");
								foreach($idProd->rows as $val){
									foreach ($val as $key => $id) {
										$this->insertCustomer($price, $quantityProd, $id);
									}
								}
							}
						}
					}
				}
				$this->updateQuantity($quantityProd, $prodID);	//quantity
			}
		}
	}

	//update quantity
	private function updateQuantity($quantity, $green1c_id){
		$this->db->query("UPDATE ".DB_PREFIX."product SET quantity='".$quantity."'  WHERE product_green1c_id='".$green1c_id."'");
	}
	// update data from offers.xml
	private function updatePrice($price,$green1c_id){
		$this->db->query("UPDATE ".DB_PREFIX."product SET price='".$price."' WHERE product_green1c_id='".$green1c_id."'");
	}
	// INSERT Product
	private function insertProduct($id, $sku, $nameProduct, $imagePath, $dateAdded){
		$this->db->query("INSERT INTO ".DB_PREFIX."product SET product_green1c_id='".$id."', model='".$sku."', image='data/".$imagePath."', status='1', date_added='".$dateAdded."', date_modified='".$dateAdded."'");
		$product_id = $this->db->query("SELECT MAX( product_id ) product_id FROM ".DB_PREFIX."product");
		foreach($product_id->rows as $product_id){
			foreach ($product_id as $key => $value) {
				$prodId = $value;
			}
		}
		$this->db->query("INSERT INTO ".DB_PREFIX."product_description SET product_id='".$prodId."', language_id='1', name='".$nameProduct."'");
		$this->db->query("INSERT INTO ".DB_PREFIX."product_to_store SET product_id='".$prodId."', store_id='0'");
		if(is_dir('../export/import_files')){ // folder for insert
			`mv ../export/import_files ../image/data/`; // move folder
		}

	}
	// UPDATE Product
	private function updateProduct($id, $sku, $nameProduct, $imagePath, $dateModifeid){
		$this->db->query("UPDATE ".DB_PREFIX."product SET model='".$sku."', image='data/".$imagePath."', date_modified='".$dateModifeid."' WHERE product_green1c_id='".$id."'");
		$selectProduct = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE product_green1c_id='".$id."'");
		foreach($selectProduct->rows as $product_id){
			foreach($product_id as $key=>$id){
				$this->db->query("UPDATE ".DB_PREFIX."product_description SET name='".$nameProduct."' WHERE product_id='".$id."'");
				$this->db->query("UPDATE ".DB_PREFIX."product_to_store SET product_id='".$id."', store_id='0' WHERE product_id='".$id."'");
			}
		}
		if(is_dir('../export/import_files')){ // folder for update
			`mv ../export/import_files ../image/data/`; // move folder

		}
	}
	//DELETE
	private function deleteProduct($date){
		global $id;
		$checkDate = $this->db->query("SELECT product_id, date_modified FROM ".DB_PREFIX."product ");
		foreach ($checkDate->rows as $chDate) {
			foreach($chDate as $key=>$dateProd){
				
				if($key=="product_id"){
					$id = $dateProd;
				}
				$date = explode(" ", $date);
				$date = $date[0];
				$dateProd = explode(" ", $dateProd);
				$dateProd = $dateProd[0];
				if($key=="date_modified" && $dateProd != $date){
					$this->db->query("DELETE FROM ".DB_PREFIX."product WHERE product_id ='".$id."'");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_description WHERE product_id ='".$id."'");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_to_store WHERE product_id ='".$id."'");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_to_category WHERE product_id ='".$id."'");
				}else{
					//Nothing do
				}
			}
		}
					
		
	}

	//Customer group
	private function insertCustomer($price, $quantity, $id){
		$this->db->query("INSERT INTO ".DB_PREFIX."product_discount SET product_id='".$id."', price='".$price."', customer_group_id='2', quantity='".$quantity."', priority='1'");
	}

	// Relationship product & category
	public function cat_prod($fileXml){
		if(file_exists($fileXml)){
			$xmlImport = simplexml_load_file($fileXml);
			$catProd = array('prodId','catId');
			global $prodID, $catID;
			foreach($xmlImport->Каталог->Товары->Товар as $product){
				foreach($product as $key => $value){
					if($key=="Ид"){ 
						$catProd['prodId'] = $value;
					}
					foreach($value as $key=>$value){
						if($key == "Ид"){
							$categoryID = $value;
							$catProd['catId'] = $value;
						}
					}
				}
				if(isset($catProd['prodId'])){
					$checkProduct = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE product_green1c_id='".$catProd['prodId']."'"); // product
					if($checkProduct->num_rows == 0){
						// None product
						$prodID = NULL;
					}else{
						// Product isset
						foreach($checkProduct->rows as $prod_id){
							foreach ($prod_id as $key => $id) {
								$prodID = $id;
							}
						}
						//isset Category
						if(isset($catProd['catId'])){
							$checkCategory = $this->db->query("SELECT category_id FROM ".DB_PREFIX."category WHERE green1c_id='".$catProd['catId']."'"); // category
							if($checkCategory->num_rows==0){
								$catID = NULL;
							}else{
								foreach($checkCategory->rows as $cat_id){
									foreach($cat_id as $key => $id){
										$catID = $id;
									}
								}
							}
							// checkout product in oc_product_to_category
							$checkProdCat = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_to_category WHERE product_id='".$prodID."'");
							if($checkProdCat->num_rows == 0){
								$this->db->query("INSERT INTO ".DB_PREFIX."product_to_category SET product_id='".$prodID."', category_id='".$catID."'");
							}else{
								$this->db->query("UPDATE ".DB_PREFIX."product_to_category SET category_id='".$catID."' WHERE product_id='".$prodID."'");
							}
						}
					}
				}
			}
		}
	}
}
?>