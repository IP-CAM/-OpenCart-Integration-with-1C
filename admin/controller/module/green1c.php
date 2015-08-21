<?php
class ControllerModuleGreen1C extends Controller {
	private $error = array();

	public function  install(){
		$this->db->query("ALTER TABLE ".DB_PREFIX."category ADD COLUMN green1c_id varchar(255) AFTER category_id");
		$this->db->query("ALTER TABLE ".DB_PREFIX."product ADD COLUMN product_green1c_id varchar(255) AFTER product_id");
	}

	public function uninstall() {
		$this->db->query("ALTER TABLE ".DB_PREFIX."category DROP 'green1c_id'");
		$this->db->query("ALTER TABLE ".DB_PREFIX."product DROP 'product_green1c_id'");
	}
	public function index(){

		$this->load->language('module/green1c');
		$this->load->model('tool/image');
		$this->load->model('tool/green1c');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('green1c', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['token'] = $this->session->data['token'];
		$this->data['success'] = $this->language->get('none');
		$this->data['upload_message'] = $this->language->get('none');
		$this->data['upload_error'] = $this->language->get('none');
		$this->data['error_imp'] = $this->language->get('none');
		

		$this->data['button_back'] = $this->language->get('button_back');

		$this->data['tab1'] = $this->language->get('tab1');
		$this->data['tab2'] = $this->language->get('tab2');

		$this->data['upload'] = $this->language->get('upload');
		$this->data['send'] = $this->language->get('send');

		$this->template = 'module/green1c.tpl';
		$this->children = array(
			'common/header',
			'common/footer'	
		);

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_home'),
			'href'		=> $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> false
		);

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_module'),
			'href'		=> $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/exchange1c', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		// Button back
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$this->response->setOutput($this->render());
	}
	public function upload(){
		$this->load->model('tool/image');
		$this->load->model('tool/green1c');
		$this->load->language('module/green1c');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['token'] = $this->session->data['token'];
		$this->data['success'] = $this->language->get('none');
		$this->data['upload_message'] = $this->language->get('none');
		$this->data['upload_error'] = $this->language->get('none');
		$this->data['error_imp'] = $this->language->get('none');
		

		$this->data['button_back'] = $this->language->get('button_back');

		$this->data['tab1'] = $this->language->get('tab1');
		$this->data['tab2'] = $this->language->get('tab2');

		$this->data['upload'] = $this->language->get('upload');
		$this->data['send'] = $this->language->get('send');
		$this->template = 'module/green1c.tpl';
		$this->children = array(
			'common/header',
			'common/footer'	
		);
		

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_home'),
			'href'		=> $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> false
		);

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_module'),
			'href'		=> $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/exchange1c', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		// Button back
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if(isset($_POST)){	
			if($_FILES['file_name']['error'] ==0){
				$temp = $_FILES['file_name']['tmp_name'];
				$name_file = $_FILES['file_name']['name'];
				if(move_uploaded_file($temp, "../export/".$name_file)){
					$this->data['upload_message'] = $this->language->get('upload_message');
				}else{
					$this->data['upload_error'] = $this->language->get('upload_error');
				}
			}
		}
		$this->response->setOutput($this->render());

		
	}
	public function importData(){
		$this->load->model('tool/image');
		$this->load->model('tool/green1c');
		$this->load->language('module/green1c');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['token'] = $this->session->data['token'];
		$this->data['success'] = $this->language->get('none');
		$this->data['upload_message'] = $this->language->get('none');
		$this->data['upload_error'] = $this->language->get('none');
		$this->data['error_imp'] = $this->language->get('none');

		$this->data['button_back'] = $this->language->get('button_back');

		$this->data['tab1'] = $this->language->get('tab1');
		$this->data['tab2'] = $this->language->get('tab2');

		$this->data['upload'] = $this->language->get('upload');
		$this->data['send'] = $this->language->get('send');
		$this->template = 'module/green1c.tpl';
		$this->children = array(
			'common/header',
			'common/footer'	
		);
		

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_home'),
			'href'		=> $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> false
		);

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_module'),
			'href'		=> $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/green1c', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		// Button back
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		if(isset($_POST)){
			// zip operation
			/*$zip = new ZipArchive;
			$res = $zip->open('../export/1cbitrix.zip');
			if($res === true){		
				$zip->extractTo('../export/');
				$zip->close();
			}else{
			}*/

			if(file_exists('../export/import.xml') && file_exists('../export/offers.xml')){
				$this->model_tool_green1c->category('../export/import.xml'); // category
				$this->model_tool_green1c->product('../export/import.xml'); // product
				$this->model_tool_green1c->productPrice('../export/offers.xml'); // product price
				$this->model_tool_green1c->cat_prod('../export/import.xml'); // relationship product & category
				$this->data['success'] = $this->language->get('success');
			}else{
				$this->data['error_imp'] = $this->language->get('error_imp');
			}
		}
		$this->response->setOutput($this->render());
	}

}		

?>