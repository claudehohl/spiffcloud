<?php
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - insert()
 * - create_space()
 * - create_word()
 * - create_ip()
 * - get_space_id()
 * - get_word_id()
 * - get_timestamp()
 * - get_ip_id()
 * - get_words_in_space()
 * - get_words_in_space_cached()
 * - get_popular_spaces()
 * - get_spaces_by_search()
 * - _spiff_allowed()
 * - word_is_linked()
 * - rebuild_cache()
 * Classes list:
 * - Word_model extends CI_Model
 */

class Word_model extends CI_Model
{
	var $spaces = array();
	
	function __construct() 
	{
		parent::__construct();

		// Get all spaces to reduce queries
		$this->db->select('id, space_name');
		$this->db->from('spaces');
		$query = $this->db->get();
		$query_spaces = $query->result_array();
		foreach ($query_spaces as $key => $space) 
		{
			$this->spaces[$key] = $space['space_name'];
		}
	}
	
	function insert($word, $space) 
	{
		$space_id = $this->get_space_id($space);
		
		if ($space_id == '') 
		{
			$space_id = $this->create_space($space);
		}
		$word_id = $this->get_word_id($word);
		
		if ($word_id == '') 
		{
			$word_id = $this->create_word($word);
		}
		$ip_id = $this->get_ip_id($_SERVER['REMOTE_ADDR']);
		
		if ($ip_id == '') 
		{
			$ip_id = $this->create_ip($_SERVER['REMOTE_ADDR']);
		}
		
		if ($this->_spiff_allowed($space_id, $word_id)) 
		{
			$data = array(
				'word_id' => $word_id,
				'space_id' => $space_id,
				'ip_id' => $ip_id,
				'timestamp' => mktime() ,
			);
			$this->db->insert('_words_in_space', $data);

			// Update space popularity
			$this->db->select('popularity');
			$this->db->from('spaces');
			$this->db->where('space_name', $space);
			$query = $this->db->get();
			$result = $query->result_array();
			$data = array(
				'popularity' => $result[0]['popularity'] + 1,
				'timestamp' => mktime() ,
			);
			$this->db->where('space_name', $space);
			$this->db->update('spaces', $data);
		}
		else
		{
			return 'error';
		}
	}
	
	function create_space($space_name) 
	{
		$data = array(
			'space_name' => $space_name,
		);
		$this->db->insert('spaces', $data);
		return $this->db->insert_id();
	}
	
	function create_word($word) 
	{
		$data = array(
			'word' => $word,
		);
		$this->db->insert('words', $data);
		return $this->db->insert_id();
	}
	
	function create_ip($ip) 
	{
		$data = array(
			'ip' => $ip,
		);
		$this->db->insert('ips', $data);
		return $this->db->insert_id();
	}
	
	function get_space_id($space_name) 
	{
		$this->db->select('id');
		$this->db->from('spaces');
		$this->db->where('space_name', $space_name);
		$query = $this->db->get();
		$result = $query->result_array();
		return @$result[0]['id'];
	}
	
	function get_word_id($word) 
	{
		$this->db->select('id');
		$this->db->from('words');
		$this->db->where('word', $word);
		$query = $this->db->get();
		$result = $query->result_array();
		return @$result[0]['id'];
	}
	
	function get_timestamp($space_id, $word_id) 
	{
		$this->db->select('timestamp');
		$this->db->from('_words_in_space');
		$this->db->where('space_id', $space_id);
		$this->db->where('word_id', $word_id);
		$this->db->order_by('timestamp', 'DESC');
		$this->db->limit('1');
		$query = $this->db->get();
		$result = $query->result_array();
		return @$result[0]['timestamp'];
	}
	
	function get_ip_id($ip) 
	{
		$this->db->select('id');
		$this->db->from('ips');
		$this->db->where('ip', $ip);
		$query = $this->db->get();
		$result = $query->result_array();
		return @$result[0]['id'];
	}
	
	function get_words_in_space($space_name) 
	{
		$space_id = $this->get_space_id($space_name);
		$this->db->select('word_id, space_id, COUNT(word_id) as c, words.word as word, MAX(timestamp) as timestamp');
		$this->db->from('_words_in_space');
		$this->db->from('words');
		$this->db->where('words.id = word_id');
		$this->db->where('space_id', $space_id);
		$this->db->group_by('word_id');
		$this->db->order_by('c', 'DESC');
		$this->db->order_by('timestamp', 'DESC');
		$this->db->order_by('word_id', 'DESC');
		$query = $this->db->get();
		$result = $query->result_array();
		foreach ($result as $key => $word) 
		{
			$result[$key]['linked'] = $this->word_is_linked($word['word']);
		}
		return @$result;
	}
	
	function get_words_in_space_cached($space_name) 
	{
		$space_id = $this->get_space_id($space_name);
		$this->db->select('space_id, word_id, count as c, word, timestamp, linked');
		$this->db->from('_words_in_space_cachetable');
		$this->db->where('space_id', $space_id);
		$this->db->order_by('c', 'DESC');
		$this->db->order_by('timestamp', 'DESC');
		$this->db->order_by('word_id', 'DESC');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	function get_popular_spaces() 
	{
		$this->db->select('id, space_name, popularity, weight, timestamp');
		$this->db->from('spaces');
		$this->db->order_by('popularity', 'DESC');
		$this->db->order_by('timestamp', 'DESC');
		$this->db->order_by('id', 'DESC');
		$this->db->limit('200');
		$query = $this->db->get();
		$result = $query->result_array();
		foreach ($result as $key => $word) 
		{
			$result[$key]['word'] = $word['space_name'];
			$result[$key]['c'] = $word['popularity'] * $word['weight'];
			$result[$key]['popularity'] = $word['popularity'] * $word['weight'];
			$result[$key]['linked'] = false; // *hihi* - hier kann man einstellen ob blau oder grün! :)

			
		}
		sort($result);
		return @$result;
	}
	
	function get_spaces_by_search($term) 
	{
		$this->db->select('id, space_name, popularity, weight, timestamp');
		$this->db->from('spaces');
		$this->db->like('space_name', $term);
		$this->db->order_by('popularity', 'ASC');
		$this->db->limit('200');
		$query = $this->db->get();
		$result = $query->result_array();
		foreach ($result as $key => $word) 
		{
			$result[$key]['word'] = $word['space_name'];
			$result[$key]['c'] = $word['popularity'] * $word['weight'];
			$result[$key]['popularity'] = $word['popularity'] * $word['weight'];
			$result[$key]['linked'] = false; // *hihi* - hier kann man einstellen ob blau oder grün! :)

			
		}

		// Lade Wörter hinzu wenn weniger als 150 Spaces gefunden wurden
		
		if ($query->num_rows() < 100) 
		{
			$this->db->select('spaces.id, spaces.space_name, spaces.popularity, spaces.weight, spaces.timestamp');
			$this->db->distinct();
			$this->db->from('words');
			$this->db->join('_words_in_space', '_words_in_space.word_id = words.id');
			$this->db->join('spaces', '_words_in_space.space_id = spaces.id');
			$this->db->like('word', $term);
			$this->db->order_by('popularity');
			$this->db->limit('100');
			$query = $this->db->get();
			$result_words = $query->result_array();
			foreach ($result_words as $key => $word) 
			{
				$result_words[$key]['word'] = $word['space_name'];
				$result_words[$key]['c'] = $word['popularity'] * $word['weight'];
				$result_words[$key]['popularity'] = $word['popularity'] * $word['weight'];
				$result_words[$key]['linked'] = false; // *hihi* - hier kann man einstellen ob blau oder grün! :)

				
			}
			$result = array_merge($result, $result_words);

			// Dubletten bereinigen
			$space_ids = array();
			foreach ($result as $key => $word) 
			{
				
				if (in_array($word['id'], $space_ids)) 
				{
					unset($result[$key]);
				}
				else
				{
					array_push($space_ids, $word['id']);
				}
			}
		}

		// Sortieren
		$ids_popularity = array();
		foreach ($result as $key => $word) 
		{
			$ids_popularity[$key] = $word['popularity'];
		}
		asort($ids_popularity);
		$sorted_result = array();
		foreach ($ids_popularity as $key => $popularity) 
		{
			array_push($sorted_result, $result[$key]);
		}
		return @$sorted_result;
	}
	
	function _spiff_allowed($space_id, $word_id) 
	{
		$ip_id = $this->get_ip_id($_SERVER['REMOTE_ADDR']);
		$this->db->select('id');
		$this->db->from('_words_in_space');
		$this->db->where('space_id', $space_id);
		$this->db->where('word_id', $word_id);
		$this->db->where('ip_id', $ip_id);
		$query = $this->db->get();
		
		if ($query->num_rows() >= 5) 
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function word_is_linked($word) 
	{
		$space_name = space_name($word);
		
		if (in_array($space_name, $this->spaces)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function rebuild_cache() 
	{
		$spacewords = $this->get_words_in_space('Vornamen');
		foreach ($spacewords as $w) 
		{
			$this->db->insert('_words_in_space_cachetable', array(
				'space_id' => $w['space_id'],
				'word_id' => $w['word_id'],
				'count' => $w['c'],
				'word' => $w['word'],
				'timestamp' => $w['timestamp'],
				'linked' => $w['linked'],
			));
		}
	}
}
