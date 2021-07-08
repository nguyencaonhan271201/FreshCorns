<?php
    class Info{
        public $content = '';
        public $start_year = new DateTime();
        public $end_year = new DateTime();
        public $conn;
    
        public function __construct($conn, $content = null, $start_year = null, $end_year = null)
        {
          $this->conn - $conn;
          $this->content = $content;
          $this->start_year = $start_year;
          $this->end_year = $end_year;
        }
        
        public function addInfo()
        {
            $sql = "INSERT INTO job_education_info (user_profile, info, start_year, end_year) VALUES (?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("isss",$_SESSION['user_id'], $this->content, $this->start_year, $this->end_year);
            $stmt->execute();
            if ($stmt->affected_rows == 1){
                header("Location:profile.php?user_id=".$_SESSION['user_id']);
            }
        }

        public function deleteInfo(){

        }

        public function editInfo()
        {
            # code...
        }
    }
?>