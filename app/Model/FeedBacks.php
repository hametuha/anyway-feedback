<?php

namespace AFB\Model;


class FeedBacks extends Base
{

	/**
	 * Database version
	 *
	 * @var string
	 */
	protected $version = '1.0';

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $name = 'feedbacks';

	/**
	 * Key name
	 *
	 * @var string
	 */
	public $key = 'afb_db_version';

	/**
	 * Retrieve data from table
	 * @param int $object_id
	 * @param string $post_type
	 * @return object
	 */
	public function get($object_id, $post_type = "post"){
		$query = <<<SQL
			SELECT * FROM {$this->table}
			WHERE post_type = %s
			  AND object_id = %d
SQL;

		return $this->db->get_row($this->db->prepare($query, $post_type, $object_id));
	}

	/**
	 * Add new data
	 *
	 * @param int $object_id
	 * @param string $post_type (optional)
	 * @param boolean $affirmative (optional) if false,negative. default true.
	 * @return int
	 */
	public function add($object_id, $post_type = "post", $affirmative = true){
		$data = array(
			"object_id" => $object_id,
			"post_type" => $post_type,
			"updated" => current_time("mysql")
		);
		if( $affirmative ){
			$data["positive"] = 1;
		}else{
			$data["negative"] = 1;
		}
		$result = $this->db->insert($this->table, $data, array("%d", "%s", "%s", "%d") );
		if( $result ){
			return $this->db->insert_id;
		}else{
			return 0;
		}
	}


	/**
	 * Update data
	 *
	 * @param int $object_id
	 * @param string $post_type (optional)
	 * @param  boolean $affirmative (optional) if false,negative. default true.
	 * @return boolean
	 */
	public function update($object_id, $post_type = "post", $affirmative = true){
		$column = $affirmative ? "positive" : "negative";
		$query = <<<SQL
			UPDATE {$this->table}
			SET
				`$column` = {$column} + 1,
				`updated` = %s
			WHERE `post_type` = %s
			  AND `object_id` = %d
SQL;
		// Try updating and get updated rows.
		$result = $this->db->query($this->db->prepare($query, current_time("mysql"), $post_type, $object_id));
		return (boolean) $result;
	}

	/**
	 * Delete record
	 *
	 * @param int $object_id
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function delete($object_id, $post_type = 'post'){
		$query = <<<SQL
			DELETE FROM {$this->table} WHERE post_type = %s AND object_id = %d
SQL;
		return (boolean) $this->db->prepare($query, $post_type, $object_id);

	}

	/**
	 * Retrieve recorded post_types
	 *
	 * @return array
	 */
	public function recorded_post_types(){
		$recorded = array();
		$sql = <<<EOS
			SELECT DISTINCT post_type FROM {$this->table}
			GROUP BY post_type
EOS;
		foreach($this->db->get_results($sql) as $r){
			$recorded[] = $r->post_type;
		}
		sort($recorded);
		return $recorded;
	}

	/**
	 * Search
	 *
	 * @param array $args
	 * @param int $page
	 * @param int $per_page
	 *
	 * @return mixed
	 */
	public function search( array $args = array(), $page = 1, $per_page = 10){
		$args = wp_parse_args($args, array(
			's'=> '',
			'post_type' => '',
			'post_status' => '',
			'order' => 'ASC',
			'orderby' => 'positive',
		));
		$per_page = intval($per_page);
		$offset = (max(1, $page) - 1) * $per_page;
		$where_clause = array();
		if( 'comment' == $args['post_type'] ){
			// Comment
			$query = <<<SQL

SQL;

		}else{
			// Post
			$where_clause[] = $this->db->prepare("p.post_type = %s", $args['post_type']);
			if( !empty($args['s']) ){
				$search_query = <<<SQL
				  	(p.post_title LIKE %s OR p.post_content LIKE %s )
SQL;
				$s = '%'.(string)$args['s'].'%';

				$where_clause[] = $this->db->prepare($search_query, $s, $s);
			}
			if( $args['post_status'] ){
				$status_query = <<<SQL
					(p.post_status = %s)
SQL;
				$where_clause[] = $this->db->prepare($status_query, $args['post_status']);
			}else{
				$where_clause[] = "( p.post_status IN ('future', 'draft', 'trash', 'publish', 'private') )";
			}
			// Build query
			if( empty($where_clause) ){
				$where_clause = '';
			}else{
				$where_clause = 'WHERE '.implode(' AND ', $where_clause);
			}
			// Order
			$order = $args['order'] == 'ASC' ? 'ASC' : 'DESC';
			switch($args['orderby']){
				case 'date':
				case 'post_date':
					$orderby = 'p.post_date';
					break;
				case 'updated':
					$orderby = 'afb.updated';
					break;
				case 'positive':
				case 'negative':
					$orderby = 'afb.'.$args['orderby'];
					break;
				case 'title':
				case 'post_title':
					$orderby = 'p.post_title';
					break;
				default:
					$orderby = 'total';
					break;
			}
			$query = <<<SQL
				SELECT SQL_CALC_FOUND_ROWS
					p.*,
					afb.positive, afb.negative, afb.updated,
					(afb.positive + afb.negative) AS total
				FROM {$this->db->posts} AS p
				LEFT JOIN {$this->table} AS afb
				ON afb.object_id = p.ID
				{$where_clause}
				ORDER BY {$orderby} {$order}
				LIMIT {$offset}, {$per_page}
SQL;
			$results = $this->db->get_results($query);
			return $results;
		}
	}

	/**
	 * Get total count
	 *
	 * @return int
	 */
	public function total(){
		return (int)$this->db->get_var("SELECT FOUND_ROWS()");
	}


	/**
	 * Get static
	 *
	 * @param $post_type
	 *
	 * @return mixed
	 */
	public function get_ratio( $post_type ){
		$query = <<<SQL
			SELECT SUM(positive) AS positive, SUM(negative) AS negative
			FROM {$this->table}
			WHERE post_type = %s
SQL;
		return $this->db->get_row($this->db->prepare($query, $post_type));
	}

	/**
	 * Get ranking
	 *
	 * @param string $post_type
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function get_ranking($post_type, $limit = 10){
		$query = <<<SQL
			SELECT p.post_title, afb.positive, afb.negative, (afb.positive + afb.negative) AS total
			FROM {$this->table} AS afb
			INNER JOIN {$this->db->posts} AS p
			ON afb.object_id = p.ID
			WHERE afb.post_type = %s
			ORDER BY total DESC
			LIMIT %d
SQL;
		return $this->db->get_results($this->db->prepare($query, $post_type, $limit));
	}


}
