<?php
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - index()
 * - cloud()
 * - x()
 * Classes list:
 * - Home extends CI_Controller
 */

class Home extends CI_Controller
{
	
	function __construct() 
	{
		parent::__construct();
		$this->load->helper('cloud');
        $this->output->enable_profiler(1);
	}
	
	function index($action = '') 
	{
		$this->load->model('word_model');
		$this->load->library('tagcloud');
		
		if (isset($_POST['spacename']) && $_POST['spacename'] != '') 
		{
			redirect(space_name($_POST['spacename']));
		}
		
		if (isset($_POST['search']) && $_POST['search'] != '') 
		{
			$words = $this->word_model->get_spaces_by_search($_POST['search']);
		}
		else
		{
			$words = $this->word_model->get_popular_spaces();
		}
		$words = $this->tagcloud->generate($words);
		$this->load->view('html_header', array(
			'title' => 'Spiffcloud - connecting minds.'
		));
		$this->load->view('fronthead', array(
			'action' => $action
		));
		$this->load->view('tagcloud', array(
			'words' => $words
		));
		$this->load->view('html_footer');
	}
	
	function cloud() 
	{
		$this->load->model('word_model');
		$this->load->library('tagcloud');
		$space_name = $this->uri->segment(1);
		
		if (isset($_POST['word']) && $_POST['word'] != '') 
		{
			$status = $this->word_model->insert($_POST['word'], $space_name);
			
			if ($status == 'error') 
			{
				redirect($space_name . '/error');
			}
			redirect($space_name);
		}
		$words = $this->word_model->get_words_in_space($space_name);
		$words = $this->tagcloud->generate($words);
		$this->load->view('html_header', array(
			'title' => 'Spiffcloud - ' . $space_name
		));
		$this->load->view('cloudhead', array(
			'space_name' => $space_name
		));
		$this->load->view('tagcloud', array(
			'words' => $words,
			'input' => true
		));
		$this->load->view('html_footer');

		//$output = $this->output->get_output();
		//$output = preg_replace("{\t|\n|\r}", "", $output);

		//$this->output->set_output($output);

		
	}
	
	function x() 
	{
		$action = $this->uri->segment(2);
		
		if ($action == 'resetdb') 
		{
			$this->load->model('db_forge');
			$this->db_forge->delete_tables();
			$this->db_forge->create_tables();
		}
		else 
		if ($action == 'create') 
		{
			$this->index('create');
		}
		else 
		if ($action == 'ajax') 
		{
			$what = $this->uri->segment(3);
			
			if ($what == 'tagcloud') 
			{
				$this->load->model('word_model');
				$this->load->library('tagcloud');
				$space_name = $this->uri->segment(4);
				
				if ($space_name == 'aafront') 
				{
					$words = $this->word_model->get_popular_spaces();
				}
				else
				{
					$words = $this->word_model->get_words_in_space($space_name);
				}
				$words = $this->tagcloud->generate($words);
				$this->load->view('tagcloud', array(
					'words' => $words,
					'ajax' => true
				));
			}
			else 
			if ($what == 'xml') 
			{ // Ugly hack

				$this->load->model('word_model');
				$this->load->library('tagcloud');
				$space_name = $this->uri->segment(4);
				$words = $this->word_model->get_words_in_space($space_name);
				$words = $this->tagcloud->generate($words, true);
				$this->load->view('xml', array(
					'words' => $words
				));
			}
		}
		else 
		if ($action == 'backupdb') 
		{

			//protection
			$user = $this->config->item('backup_user');
			$pass = $this->config->item('backup_pass');
			
			if ($user == '' || $pass == '' || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != $user || $_SERVER['PHP_AUTH_PW'] != $pass) 
			{
				header('WWW-Authenticate: Basic realm="Backup"');
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}

			// Load the DB utility class
			$this->load->dbutil();

			// Backup your entire database and assign it to a variable
			$backup = & $this->dbutil->backup();

			// Load the download helper and send the file to your desktop
			$this->load->helper('download');
			force_download('spiffcloud.sql.gz', $backup);
		}
	}
}
