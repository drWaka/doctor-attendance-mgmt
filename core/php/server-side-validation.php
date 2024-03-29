<?php
class form_validation {
  var $value;
  var $valid;
  var $err_msg;

  function __construct($data, $type, $label, $required) {
    if ($this -> test_empty($data, $label, $type, $required)) {
      if ($type == 'str') {
        $this -> test_str($data, $label);
      } else if ($type == 'int') {
        $this -> test_int($data, $label);
      } else if ($type == 'double') {
        $this -> test_double($data, $label);
      } else if ($type == 'str-int') {
        $this -> test_str_int($data, $label);
      } else if ($type == 'raw-str-int') {
        $this -> test_raw_str_int($data, $label);
      } else if ($type == 'email') {
        $this -> test_email($data, $label);
      } else if ($type == 'bool' || $type == 'boolean') {
        $this -> test_bool($data, $label);
      } else if ($type == 'date') {
        $this -> test_date($data, $label);
      } else if ($type == 'time') {
        $this -> test_time($data, $label);
      } else if ($type == 'password') {
        $this -> test_password($data, $label);
      } else if ($type == 'mobile_number') {
        $this -> test_mobile_number($data, $label);
      } else if ($type == 'telephone_number') {
        $this -> test_telephone_number($data, $label);
      } else if ($type == 'account_number') {
        $this -> test_account_number($data, $label);
      } else if (substr($type, 0, 4) == 'file') {
        $this -> test_file($data, $label, $type);
      } else if ($type == 'array') {
        $this -> test_array($data, $label, $type);
      }
    }
  }

  function test_file($data, $label, $type) {
    $valid_extensions = "";
    if (substr($type, 5, strlen($type)) === 'image') {
      $valid_extensions = array('jpeg', 'jpg', 'gif', 'png');
    } else if (substr($type, 5, strlen($type)) == 'doc') {
      $valid_extensions = array('doc', 'docx', 'pdf');
    }
      
    $file_extension = explode('.' , $data['name']);
    $file_extension = end($file_extension);
    if ($this -> test_file_extension($valid_extensions, $file_extension)) {
      if ($data['size'] <= 15000000000 && $data['error'] != 1) {
        $this -> valid = 1;
        $this -> value = $data;
      } else {
        $this -> valid = 0;
        $this -> err_msg = $label . ' size must not exceed on 1.5MB';
      }
    } else {
      $this -> valid = 0;
      $this -> err_msg = $label . ' must be a file type of ';

      foreach ($valid_extensions as $extension) {
        if (end($valid_extensions) == $extension) {
          $this -> err_msg .= 'or ' . $extension;
        } else {
          $this -> err_msg .= $extension . ', ';
        }
      }
    }
  }

  function test_file_extension($valid_extensions, $file_extension) {
    $file_valid = false;
    foreach ($valid_extensions as $extension) {
      if (strtolower($extension) == strtolower($file_extension)) {
        $file_valid = true;
        break;
      }
    }
    return $file_valid;
  }

  function test_int($data, $label) {
    if ($data < 0) {
      $this -> valid = 0;
      $this -> err_msg = $label . ' must not be negative';
    } else {
      if (filter_var($data, FILTER_VALIDATE_INT) || $data == '0') {
        $this -> valid = 1;
        $this -> value = $this -> filter_data($data);
      } else {
        if (filter_var($data, FILTER_VALIDATE_FLOAT)) {
          $this -> valid = 1;
          $this -> value = $this -> filter_data($data);
        } else {
          $this -> valid = 0;
          $this -> err_msg = 'Please enter a valid ' . $label;
        }
      }
    }
  }

  function test_str($data, $label) {
    for ($index = 0 ; $index <= strlen($data) ; $index++) {
      if (filter_var(substr($data, $index, 1), FILTER_VALIDATE_INT)) {
        $this -> valid = 0;
        $this -> err_msg = $label . ' must not contain numbers';
        break;
      } else {
        $this -> valid = 1;
        $this -> value = $this -> filter_data($data);
      }
    }
  }

  function test_array($data, $label) {
    if (is_array($data)) {
      $this -> valid = 1;
      $this -> value = $data;
    } else {
      $this -> valid = 0;
      $this -> err_msg = $label . 'is invalid';
    }
  }

  function test_str_int($data, $label) {
    $this -> valid = 1;
    $this -> value = $this -> filter_data($data);
  }

  function test_raw_str_int($data, $label) {
    $this -> valid = 1;
    $this -> value = $data;
  }

  function test_password($data, $label) {
    $this -> valid = 1;
    $this -> value = $data;

    // Must be 8 characters long
    if (strlen($this -> value) < 8) {
      $this -> valid = 0;
      $this -> err_msg = "{$label} must be atleast 8 characters";
    }

    if ($this -> valid == 1) {
      $flags = array(
        "hasCapital" => 0,
        "hasSmall" => 0,
        "hasNumber" => 0
      );
      $charSet = array(
        "capital" => array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'),
        "small" => array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'),
        "number" => array('0','1','2','3','4','5','6','7','8','9')
      );
      $dataPerChar = str_split($this -> value);
      foreach ($dataPerChar as $char) {
        // Check for Capital Letter
        if (array_search($char, $charSet['capital']) !== false) {
          $flags['hasCapital'] = 1;
        }

        // Check for Small Letter
        if (array_search($char, $charSet['small']) !== false) {
          $flags['hasSmall'] = 1;
        }

        // Check for Numeric Character
        if (array_search($char, $charSet['number']) !== false) {
          $flags['hasNumber'] = 1;
        }
      }

      if ($flags['hasNumber'] == 0) {
        $this -> valid = 0;
        $this -> err_msg = "{$label} must consist atleast 1 number";
      }

      if ($flags['hasSmall'] == 0) {
        $this -> valid = 0;
        $this -> err_msg = "{$label} must consist atleast 1 small letter";
      }

      if ($flags['hasCapital'] == 0) {
        $this -> valid = 0;
        $this -> err_msg = "{$label} must consist atleast 1 capital letter";
      }
    }
  }

  function test_bool($data, $label) {
    $finalValue = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if ($finalValue != null) {
      $this -> valid = 1;
      $this -> value = $finalValue;
    } else {
      $this -> valid = 0;
      $this -> err_msg = 'Please enter a valid ' . $label;
    }
  }

  function test_email($data, $label) {
    if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
      $this -> valid = 1;
      $this -> value = filter_var($this -> filter_data($data), FILTER_SANITIZE_EMAIL);
    } else {
      $this -> valid = 0;
      $this -> err_msg = 'Please enter a valid ' . $label;
    }
  }

  function test_date($data, $label) {
    $date_exploded = explode('-', $data);
    if (count($date_exploded) === 3) {
      if (checkdate($date_exploded[1],$date_exploded[2],$date_exploded[0])) {
        $min_date = strtotime(date('Y-m-d',strtotime(date('Y') - 18 . '-' . date('m-d'))));
        $input_date = strtotime($data);
        if ($min_date < $input_date) {
          if ($label == 'Date of Birth') {
            $this -> valid = 0;
            $this -> err_msg = $label . ' must be atleast earlier than 18 years';
          } else {
            $this -> valid = 1;
            $this -> value = $this -> filter_data($data);
          }
        } else {
          $this -> valid = 1;
          $this -> value = $this -> filter_data($data);
        }
      } else {
        $this -> valid = 0;
        $this -> err_msg = 'Date dont exist';
      }
    } else {
      $this -> valid = 0;
      $this -> err_msg = 'Please enter a ' . $label . ' in a valid format';
    }
  }

  function test_time($data, $label) {
    $timeExploded = explode(':', $data);
    if (count($timeExploded) === 3 || count($timeExploded) === 2) {
      foreach ($timeExploded AS $timeSegment) {
        $this -> test_int($timeSegment, $label);
        if ($this -> valid == 0) {
          break;
        }
      }
      if ($this -> valid != 0) {
        $this -> valid = 1;
        $this -> value = $data;
      }
    } else {
      $this -> valid = 0;
      $this -> err_msg = 'Please enter a ' . $label . ' in a valid format';
    }
  }

  function test_mobile_number($data, $label) {
    $this -> test_int($data, $label);

    if ($this -> valid == 1) {
      if (strlen($data) != 12) {
        $this -> valid = 0;
        $this -> err_msg = $label . ' must be 12 digits';
      } else {
        if (!(substr($data, 0, 3) == '639')) {
          $this -> valid = 0;
          $this -> err_msg = 'Please Enter a valid ' . $label . ' Number';
        } else {
          $this -> valid = 1;
          $this -> value = $this -> filter_data($data);
        }
      }
    }
  }

  function test_telephone_number($data, $label) {
    $this -> test_int($data, $label);

    if ($this -> valid == 1) {
      if (strlen($data) != 10) {
        $this -> valid = 0;
        $this -> err_msg = $label . ' must be 10 digits';
      } else {
        $this -> valid = 1;
        $this -> value = $this -> filter_data($data);
      }
    }
  }

  function test_account_number($data, $label) {
    $this -> test_int($data, $label);

    if ($this -> valid == 1) {
      if (stristr($label, 'sss') != false) {
        if (strlen($this -> value) !== 10) {
          $this -> valid = 0;
          $this -> err_msg = 'Please enter a valid ' . $label;
        }
      } elseif (stristr($label, 'pagibig') != false || stristr($label, 'philhealth') != false || stristr($label, 'tin') != false || stristr($label, 'tax identification number') != false) {
        if (strlen($this -> value) !== 12) {
          $this -> valid = 0;
          $this -> err_msg = 'Please enter a valid ' . $label;
        }
      }
    }
  }

  function test_empty($data, $label, $type, $required) {
    if (substr($type, 0, 4) == 'file') {
      if (!empty($data['name'])) {
        return true;
      } else {
        if (!$required) {
          $this -> valid = 1;
          $this -> err_msg = '';
          return false;
        } else {
          $this -> valid = 0;
          $this -> err_msg = 'Please enter a ' . $label;
          return false;
        }
      }
    } else if ($type == 'array') {
      if (!empty($data)) {
        return true;
      } else {
        if (!$required) {
          $this -> valid = 1;
          $this -> err_msg = '';
          return false;
        } else {
          $this -> valid = 0;
          $this -> err_msg = 'Please enter a ' . $label;
          return false;
        }
      }
    } else {
      if ((!empty($data) && strlen($data) > 0) || ($data == 0 && strlen($data) > 0)) {
        return true;
      } else {
        if (!$required) {
          $this -> valid = 1;
          $this -> value = '';
          $this -> err_msg = '';
          return false;
        } else {
          $this -> valid = 0;
          $this -> err_msg = 'Please enter a ' . $label;
          return false;
        }

      }
    }
  }

  function filter_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
}
?>
