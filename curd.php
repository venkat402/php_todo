<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Curd {

    public $conn;

    public function __construct() {
        $this->connect();
    }

    public function index() {
        
    }

    public function connect() {
        $this->conn = mysqli_connect("localhost", "root", "", "php_todo");

        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
    }

    public function register($data) {
        $name = $this->mysqli_escape($data['name']);
        $email = $this->mysqli_escape($data['email']);
        $password = md5($this->mysqli_escape($data['password']));
        if ($this->check_exist('email', 'users', $email)) {
            echo "$email already exist";
        } else {
            $sql = "INSERT INTO users (name, email, password)
        VALUES ('$name', '$email', '$password')";
            if ($this->conn->query($sql) === TRUE) {
                $this->alert("New record created successfully");
                header("Location:login.php");
            } else {
                echo "Error: " . $sql . "<br>" . $this->conn->error;
            }
        }
    }

    public function check_exist($column, $table, $value) {
        $sql = "SELECT $column FROM $table where $column = '$value'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function mysqli_escape($data) {
        $temp_data = mysqli_real_escape_string($this->conn, $data);
        return $temp_data;
    }

    public function login($data) {
        $email = $this->mysqli_escape($data['email']);
        $password = md5($this->mysqli_escape($data['password']));
        $sql = "SELECT * FROM users where email = '$email' and password = '$password'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            session_start();
            $_SESSION['login_user'] = $email;  // Initializing Session with value of PHP Variable
            $this->alert("loged in successfully");
            header("Location: home.php");
        } else {
            echo "User not found";
        }
    }

    public function get_tasks() {
        $sql = "SELECT * FROM tasks";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $getAllData[] = $row;
            }
        } else {
            $getAllData = "";
        }

        return $getAllData;
    }

    public function task_save($data) {
        $task = $this->mysqli_escape($data['task']);
        $sql = "INSERT INTO tasks (task_name,task_status)
        VALUES ('$task','incomplete')";

        if ($this->conn->query($sql) === TRUE) {
            $this->alert("New record created successfully");
            header("Location: home.php");
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    }

    public function task_delete($data) {

        if ($this->check_exist('id', 'tasks', $data)) {
            $sql = "DELETE FROM tasks WHERE id='$data'";
            if ($this->conn->query($sql) === TRUE) {
                $this->alert("Record deleted successfully");
                header("Location: home.php");
            } else {
                echo "Error deleting record: " . $this->conn->error;
            }
        }
    }

    public function task_update() {
        $sql = "UPDATE MyGuests SET lastname='Doe' WHERE id=2";

        if ($this->conn->query($sql) === TRUE) {
            $this->alert("Record updated successfully");
            header("Location: home.php");
        } else {
            echo "Error updating record: " . $this->conn->error;
        }
    }

    public function task_status_update($id, $status) {

        if ($status = 'complete') {

            $sql = "UPDATE tasks SET task_status='incomplete' WHERE id='$id'";
        }
        if ($status = 'incomplete') {
            $sql = "UPDATE tasks SET task_status='complete' WHERE id='$id'";
        }

        if ($this->conn->query($sql) === TRUE) {
            $this->alert("Record updated successfully");
            echo $sql;
            header("Location: home.php");
        } else {
            echo "Error updating record: " . $this->conn->error;
        }
    }

    function fillterData($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function alert($msg) {
        echo "<script>alert('$msg');</script>";
    }

}

function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}

if (@$_GET['controller'] == 'register') {

    require "vendor/wixel/gump/gump.class.php";

    $gump = new GUMP();

    $_POST = $gump->sanitize($_POST); // You don't have to sanitize, but it's safest to do so.

    $gump->validation_rules(array(
        'name' => 'required|alpha_numeric|max_len,255',
        'password' => 'required|max_len,255',
        'email' => 'required|valid_email',
    ));

    $gump->filter_rules(array(
        'name' => 'trim|sanitize_string',
        'password' => 'trim',
        'email' => 'trim|sanitize_email',
    ));

    $validated_data = $gump->run($_POST);

    if ($validated_data === false) {
        alert($gump->get_readable_errors(true));
        header("Location:register.php");
    } else {
        $curd = new Curd();


        $curd->login($validated_data);
    }
}
if (@$_GET['controller'] == 'login') {

    require "vendor/wixel/gump/gump.class.php";

    $gump = new GUMP();

    $_POST = $gump->sanitize($_POST); // You don't have to sanitize, but it's safest to do so.

    $gump->validation_rules(array(
        'password' => 'required|max_len,255',
        'email' => 'required|valid_email',
    ));

    $gump->filter_rules(array(
        'password' => 'trim',
        'email' => 'trim|sanitize_email',
    ));

    $validated_data = $gump->run($_POST);

    if ($validated_data === false) {
        alert($gump->get_readable_errors(true));
        header("Location:login.php");
    } else {
        $curd = new Curd();

        $curd->login($validated_data);
    }
}
if (@$_GET['controller'] == 'task') {

    require "vendor/wixel/gump/gump.class.php";

    $gump = new GUMP();

    $_POST = $gump->sanitize($_POST); // You don't have to sanitize, but it's safest to do so.

    $gump->validation_rules(array(
        'task' => 'required',
    ));

    $gump->filter_rules(array(
        'task' => 'trim',
    ));

    $validated_data = $gump->run($_POST);

    if ($validated_data === false) {
        alert($gump->get_readable_errors(true));
        header("Location:home.php");
    } else {
        $curd = new Curd();
        $curd->task_save($validated_data);
    }
}

if (@$_GET['controller'] == 'update') {
    $id = $_GET['id'];
    $curd = new Curd();
    $curd->task_update($id);
}

if (@$_GET['controller'] == 'delete') {

    $id = $_GET['id'];
    $curd = new Curd();
    $curd->task_delete($id);
}

if (@$_GET['controller'] == 'status') {

    $id = $_GET['id'];
    $state = $_GET['state'];
    $curd = new Curd();
    $curd->task_status_update($id, $state);
}


//
//if ($_GET['controller'] == 'register') {
//    $curd->register($_POST);
//}
//
//if ($_GET['controller'] == 'register') {
//    $curd->register($_POST);
//}
//
//if ($_GET['controller'] == 'register') {
//    $curd->register($_POST);
//}
//
//if ($_GET['controller'] == 'register') {
//    $curd->register($_POST);
//}
//
//if ($_GET['controller'] == 'register') {
//    $curd->register($_POST);
//}
//
//if ($_GET['controller'] == 'register') {
//    $curd->register($_POST);
//}

