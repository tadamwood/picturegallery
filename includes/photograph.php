<?php 

require_once(LIB_PATH.DS.'database.php');

class photograph extends DatabaseObject {

	protected static $table_name="photographs";
	protected static $db_fields = array('id', 'filename', 'type', 'size', 'caption');
	public $id;
	public $filename;
	public $type;
	public $size;
	public $caption;

	private $temp_path;
	protected $upload_dir="images";
	public $errors=array();

	protected $upload_errors = array (
		UPLOAD_ERR_OK => "No errors.",
		UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize.",
		UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
		UPLOAD_ERR_PARTIAL => "Partial upload.",
		UPLOAD_ERR_NO_FILE => "No file.",
		UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
		UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
		UPLOAD_ERR_EXTENSION => "File upload stopped by extension.",
	);

	//Pass in $_FILE(['uploaded_file']) as an argument
	public function attach_file($file) {
		//Perform error checking on the form parameters
		if(!$file || empty($file) || !is_array($file)) {
			//error: nothing uploaded or wrong argument usage
			$this->errors[] = "No file was uploaded";
			return false;
		} else if ($file['error'] != 0) {
			//error: report what PHP says went wrong
			$this->errors[] = $this->upload_errors[$file['error']];
			return false;
		} else {
			//Set object attributes to the for parameters
			$this->temp_path = $file['tmp_name'];
			$this->filename = basename($file['name']);
			$this->type = $file['type'];
			$this->size = $file['size'];

			return true;
		}
	}

	public function save() {
		if(isset($this->id)) {
			//to update the caption
			$this->update();
		} else {
			//make sure there are no errors
			//can't save if there are preexisting errors
			if(!empty($this->errors)) { return false; }

			//make sure the caption isn't too long
			if(strlen($this->caption) > 255) {
				$this->errors[] = "The caption can only be 255 characters long.";
				return false;
			}

			//can't save without the filename and temp location
			if(empty($this->filename) || empty($this->temp_path)) {
				$this->errors[] = "The file location was not available.";
				return false;
			}

			//determine the target_path
			$target_path = SITE_ROOT.DS.'public.'.DS.$this->upload_dir.DS.$this->filename;

			//make sure the file doesn't already exist
			if(file_exists($target_path)) {
				$this->errors[] = "The file {$this->filename} already exists.";
				return false;
			}

			//attempt to move the file
			if(move_uploaded_file($this->temp_path, $target_path)) {
				//success
				//save a corresponding entry to the database
				if($this->create()) {
					//done with temp_path, the file isn't there anymore
					unset($this->temp_path);
					return true;
				}
			} else {
				//failure
				$this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
				return false;
			}
		}
	}

	public function destroy() {
		//first remove the database entry
		if($this->delete()) {
			//then remove the file
			$target_path = SITE_ROOT.DS.'public'.DS.$this->image_path();
			return unlink($target_path) ? true : false;
		} else {
			//database delete failed
			return false;
		}
	}

	public function image_path() {
		return $this->upload_dir.DS.$this->filename;
	}

	public function size_as_text() {
		if($this->size < 1024) {
			return "{$this->size} bytes";
		} elseif($this->size < 1048576) {
			$size_kb = round($this->size/1024);
			return "{$size_kb} KB";
		} else {
			$size_mb = round ($this->size/1048576, 1);
			return "{$size_mb} MB";
		}
	}

	public function comments() {
		return Comment::find_comments_on($this->id);
	}

	//Common Database Methods

	//static makes it a class method
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
	}

	//used to call one instance
	public static function find_by_id($id=0) {
		global $db;
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id=".$db->escape_value($id)." LIMIT 1");
		//checking to see if the array is empty
		//array shift will shift an element off the beginning of an array
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	//used to find a subset of sql
	public static function find_by_sql($sql="") {
		global $db;
		$result_set = $db->query($sql);
		$object_array = array();
		while ($row = $db->fetch_array($result_set)) {
			//creates an array holding all the information held in $object
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	private static function instantiate($record) {
		//creating a new instance of itself (the user class)
		$object = new self;
		/*
		$object->id 			= $record['id'];
		$object->username 		= $record['username'];
		$object->password 		= $record['password'];
		$object->first_name 	= $record['first_name'];
		$object->last_name 		= $record['last_name'];*/

		//more dynamic approach to this
		foreach($record as $attribute=>$value) {
			if($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}

	//private functions just create an instance
	private function has_attribute($attribute) {
		//get_object_vars returns an associative array with all attributes as the keys and their current values as value
						//this refers to get_object_vars
		// $object_vars = $this->attributes();
		$object_vars = get_object_vars($this);
		//checks to see if the key exists

		//returns true of false
		//does the key attribute exists in the array object vars?
		return array_key_exists($attribute, $object_vars);
	}

	/*protected function attributes() {
		//return an array of atrribute keys and their values
		$attributes = array();
		foreach(self::$db_fields as $field) {
			if(property_exists($this, $field)) {
				$attributes[$field] = $this->field;
			}
		}
		return $attributes;
	}

	protected function sanitized_attributes() {
		global $db;
		$clean_attributes = array();
		//sanitize the values before submitting
		foreach($this->attributes() as $key => $value) {
			$clean_attributes[$key] = $db->escape_values($value);
		}
		return $clean_attributes;
	}*/

	//replaced with a custom save
	/*public function save() {
		return isset($this->id) ? $this->update() : $this->create();
	}*/

	public function create() {
		global $db;
		// $attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$table_name." (filename, type, size, caption) VALUES ('$this->filename', '$this->type', '$this->size', '$this->caption')";
		if($db->query($sql)) {
			$this->id = $db->insert_id();
			return true;
		} else {
			return flase;
		}
	}

	public function update() {
		global $db;
		$sql = "UPDATE ".self::$table_name." SET filename='$this->filename', size='$this->size', type='$this->type', caption='$this->caption' WHERE id='$this->id'";
		$db->query($sql);
		return ($db->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $db;
		$sql = "DELETE FROM ".self::$table_name." WHERE id='$this->id' LIMIT 1";
		$db->query($sql);
		return ($db->affected_rows() == 1) ? true : false;
	}

}

 ?>