<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	public function hash($password) {
		return hash("sha512", $password . config_item("encryption_key"));
	}
	
	public function uploadImage($role, $fields = "user_photo") {
		$return_photo = 'defualt.png';
		$old_user_photo = $this->input->post('old_user_photo');
		if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
			$config['upload_path'] = './uploads/images/' . $role . '/';
			$config['allowed_types'] = '*';
			$config['overwrite'] = FALSE;
			$config['encrypt_name'] = TRUE;
			$this->upload->initialize($config);
			if ($this->upload->do_upload("$fields")) {
	            // need to unlink previous photo
	            if (!empty($old_user_photo)) {
	            	$unlink_path = 'uploads/images/' . $role . '/';
	                if (file_exists($unlink_path . $old_user_photo)) {
	                    @unlink($unlink_path . $old_user_photo);
	                }
	            }
				$return_photo = $this->upload->data('file_name');
			}
		} else {
			if (!empty($old_user_photo)){
				$return_photo = $old_user_photo;
			}
		}
		return $return_photo;
	}

    public function get($table, $where_array = NULL, $single = false, $branch = false, $columns = '*')
    {
        $this->db->select($columns);
        if (is_array($where_array)){
            $this->db->where($where_array);
        }
        if ($branch == true) {
	        if (!is_superadmin_loggedin()) {
	            $this->db->where("branch_id", get_loggedin_branch_id());
	        }
        }
        if ($single == true) {
            $method = 'row_array';
        } else {
            $method = 'result_array';
            $this->db->order_by('id', 'ASC');
        }
        $result = $this->db->get($table)->$method();

		if (empty($result) && $single == true) {
		    $config = array();
		    $r = $this->db->list_fields($table);
		    foreach ($r as $key => $value) {
		        $config[$value] = "";
		    }
		    return $config;
		}
		
		return $result;
    }

    public function getSingle($table, $id = NULL, $single = false)
    {
        if ($single == true) {
            $method = 'row';
        } else {
            $method = 'result';
        }
        $q = $this->db->query("SELECT * FROM " . $table . " WHERE id = " . $this->db->escape($id));
		return $q->$method();
    }

    public function fileupload($media_name, $upload_path = "", $old_file = '', $enc = true)
    {
        if (file_exists($_FILES[$media_name]['tmp_name']) && !$_FILES[$media_name]['error'] == UPLOAD_ERR_NO_FILE) {
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = '*';
            if ($enc == true) {
                $config['encrypt_name'] = true;
            } else {
                $config['overwrite'] = FALSE;
            }
            $this->upload->initialize($config);
            if ($this->upload->do_upload($media_name)) {
                if (!empty($old_file)) {
                    $file_name = $config['upload_path'] . $old_file;
                    if (file_exists($file_name)) {
                        unlink($file_name);
                    }
                }
                return $this->upload->data('file_name');
            }
        } else {
			if (!empty($old_file)){
				return $old_file;
			} else {
				return "";
			}
        }
    } 
}
