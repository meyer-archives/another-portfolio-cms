<?php

if( !defined( "SITE_PATH" ) ) die( "Can't touch this." );

class Portfolio {
	private $sqlite;
	private $items_by_project;
	private $items_by_id;
	private $projects;
	private $meta;

	private static $instance;
 
	public function &get_instance() {
		if( self::$instance === null )
			self::$instance = new Portfolio();
		return self::$instance;
	}

	private function __construct(){
		$this->items_by_id = array();
		$this->items_by_project = array();
		$this->projects = array();
		$this->meta = array();

		// Initiate the DB
		try {
			$this->sqlite = DB::get_handle();

			$this->sqlite->exec("CREATE TABLE IF NOT EXISTS portfolio_meta (
				meta_id INTEGER PRIMARY KEY,
				meta_key TEXT UNIQUE,
				meta_value TEXT
			);");
			$this->sqlite->exec("CREATE TABLE IF NOT EXISTS portfolio_items (
				item_id INTEGER PRIMARY KEY,
				item_title TEXT,
				item_desc TEXT,
				item_title_src TEXT,
				item_desc_src TEXT,
				item_order INTEGER,
				item_project INTEGER,
				item_year INTEGER,
				date_added TIMESTAMP,
				last_updated TIMESTAMP
			);");
			$this->sqlite->exec("CREATE TABLE IF NOT EXISTS portfolio_projects (
				project_id INTEGER PRIMARY KEY,
				project_title TEXT,
				project_title_src TEXT,
				project_order INTEGER
			);");
			$now = time();
			$this->sqlite->exec("INSERT INTO portfolio_meta (meta_key, meta_value) VALUES ('last_updated','" . $now . "');");
			$this->sqlite->exec("INSERT INTO portfolio_meta (meta_key, meta_value) VALUES ('cache_age','" . $now . "');");
		} catch( PDOException $e ){ 
			die( "PDO Error: " . $e->getMessage() ); 
		}

		$item_results = $this->sqlite->query("SELECT * FROM portfolio_items ORDER BY item_order DESC;")->fetchAll();
		foreach( $item_results as $row ){
			$this->items_by_project[$row["item_project"]][] =
			$this->items_by_id[$row["item_id"]] = array(
				"id" => $row["item_id"],
				"project" => $row["item_project"],
				"title_src" => unescape( $row["item_title_src"] ),
				"title" => unescape( $row["item_title"], false ),
				"desc_src" => unescape( $row["item_desc_src"] ),
				"desc" => unescape( $row["item_desc"], false ),
				"order" => $row["item_order"],
				"project" => $row["item_project"],
				"date_added" => $row["date_added"],
				"last_updated" => $row["last_updated"],
				"year" => $row["item_year"]
			);
			if( empty( $project_count[$row["item_project"]] ) ) {
				$project_count[$row["item_project"]] = 1;
			} else {
				$project_count[$row["item_project"]]++;
			}
		}

		if( !empty( $project_count[0] ) )
			$this->projects[0] = array("title" => "Unpublished", "title_src" => "Unpublished");

		$project_results = $this->sqlite->query("SELECT * FROM portfolio_projects ORDER BY project_order DESC;")->fetchAll();
		foreach( $project_results as $row ){
			$this->projects[$row["project_id"]]["title"] = unescape($row["project_title"],false);
			$this->projects[$row["project_id"]]["title_src"] = unescape($row["project_title_src"]);
		}

		foreach( $this->items_by_project as $pid => $items ){
			// Move everything to items_by_project[0]
			if( empty( $this->projects[$pid] ) ){
				$this->items_by_project[0] += $this->items_by_project[$pid];
				unset($this->items_by_project[$pid]);
			}
		}

		$meta_results = $this->sqlite->query("SELECT * FROM portfolio_meta;")->fetchAll();
		foreach( $meta_results as $row ){
			$this->meta[$row["meta_key"]] = $row["meta_value"];
		}
	}

	private function db_version_update(){
		$this->meta("last_updated",time());
	}

	function meta( $k = false, $v = false ){
		if( empty( $k ) ) {
			return $this->meta;
		} else {
			if( empty($v) ) { // Get
				if( !empty( $this->meta[$k] ) )
					return $this->meta[$k];
				return false;
			} else { // Set
				if( !empty( $this->meta[$k] ) ){
					// Update
					$query = sprintf( "UPDATE portfolio_meta SET meta_value = '%s' WHERE meta_key = '%s';",
						escape($v),
						escape($k)
					);
				} else {
					// Insert
					$query = sprintf( "INSERT INTO portfolio_meta ( meta_key, meta_value ) VALUES ( '%s', '%s' );",
						escape($k),
						escape($v)
					);
				}
				$this->meta[$k] = $v;

				if( $k != "last_updated" )
					$this->db_version_update();

				return $this->sqlite->exec($query);
			}
		}
	}

	///////////////////////////////////////////////////////
	//  PORTFOLIO ITEMS - ADD, MODIFY, AND DELETE
	///////////////////////////////////////////////////////

	function item_add( $title, $desc, $project = "0" ){
		$project = (int) $project;

		$title_src = escape($title);
		$title = escape_typogrify($title);
		$desc_src = escape($desc);
		$desc = escape_typogrify( $desc );

		$query = sprintf( "INSERT INTO portfolio_items (
			item_title,
			item_title_src,
			item_desc,
			item_desc_src,
			item_project,
			item_order,
			item_year,
			last_updated
		) VALUES (
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'0',
			'2010',
			DATETIME('NOW') );",
			$title,
			$title_src,
			$desc,
			$desc_src,
			$project
		);
		$this->sqlite->exec( $query );

		$this->db_version_update();
		return $this->sqlite->lastInsertId();
	}

	function item_update( $id, $title, $desc, $project ){
		$id = (int) $id;
		if( $id > 0 ) {
			$query = "UPDATE portfolio_items SET %s = '%s' WHERE item_id = '$id';";

			$title_src = escape($title);
			$title = escape_typogrify($title);

			$this->sqlite->exec(sprintf($query,"item_title_src",$title_src));
			$this->sqlite->exec(sprintf($query,"item_title",$title));

			$desc_src = escape($desc);
			$desc = escape_typogrify($desc);

			$this->sqlite->exec(sprintf($query,"item_desc_src",$desc_src));
			$this->sqlite->exec(sprintf($query,"item_desc",$desc));

			$project = (int) $project;
			$this->sqlite->exec(sprintf($query,"item_project",$project));

			$this->db_version_update();
		} else {
			return false;
		}
	}

	function item_delete( $id ){
		$query = "DELETE FROM portfolio_items WHERE item_id = '$id'";
		$this->db_version_update();
		return $this->sqlite->exec($query);
	}


	///////////////////////////////////////////////////////
	//  PORTFOLIO CATEGORIES - ADD, MODIFY, AND DELETE
	///////////////////////////////////////////////////////

	function project_add( $title ){
		$title_src = escape($title);
		$title = typogrify(escape($title));

		$query = sprintf(
			"INSERT INTO portfolio_projects (
				project_title,
				project_title_src,
				project_order
			) VALUES ( '%s', '%s', '0' );",
			$title, $title_src
		);

		$this->db_version_update();
		return $this->sqlite->exec( $query );
	}

	function project_update( $id, $title ){
		$id = (int) $id;
		if( $id > 0 ) {
			$query = false;
			if( !empty($title) ){
				$query = "UPDATE portfolio_projects SET %s = '%s' WHERE project_id = '%d';";

				$title_src = escape($title);
				$title = escape_typogrify($title);

				$this->sqlite->exec(sprintf($query,"project_title",$title,$id));
				$this->sqlite->exec(sprintf($query,"project_title_src",$title_src,$id));

				$this->db_version_update();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function project_delete( $id ){
		$id = (int) $id;
		if( $id > 0 ){
			$query = "DELETE FROM portfolio_projects WHERE project_id = '$id';";
			$this->sqlite->exec($query);

			$query2 = "UPDATE portfolio_items SET item_project = '0' WHERE item_project = '$id';";
			$this->db_version_update();

			return $this->sqlite->exec($query2);
		}
	}

	///////////////////////////////////////////////////////
	//  CATEGORIES & ITEMS - GET
	///////////////////////////////////////////////////////

	function item($id){
		if( !empty( $this->items_by_id[$id] ) ){
			return $this->items_by_id[$id];
		} else {
			return false;
		}
	}

	function items($by_id = false){
		return $by_id ? $this->items_by_id : $this->items_by_project;
	}

	function project($id){
		if( !empty( $this->projects[$id] ) ){
			return $this->projects[$id];
		} else {
			$id = (int) $id;
			return "ERROR: Project $id not in projects[]";
		}
	}

	function projects(){
		return $this->projects;
	}
}

?>