<?php
class Student
{

    private $std = 'uoj_student';
    private $conn;

    private $stdId;

    public function __construct($database)
    {
        $this->conn = $database;
    }

    

}
?>