<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{
    function getSchool()
    {
        $sqlQuery = "SELECT DISTINCT SCHOOL FROM `account` WHERE STATE ='FINISH' ORDER BY SCHOOL";
        $result = $this->db->query($sqlQuery);
        return $result->result_array();
    }

    function getReportByGrade($school, $from_date, $to_date)
    {
        $this->db->select("
            A.SCHOOL,
            IFNULL(A.GRADE, 'Tidak Ada Kelas') AS GRADE,
            (SELECT COUNT(ID) FROM account AS a1 WHERE a1.SCHOOL = A.SCHOOL AND a1.STATE = 'FINISH' AND a1.GRADE = A.GRADE) AS COUNT_OF_STUDENT,
            SUM(CASE WHEN Q.TIPE = 'Critical Thinking' THEN R.VALUE ELSE 0 END) AS 'critical_thinking',
            SUM(CASE WHEN Q.TIPE = 'Cyber Security Management' THEN R.VALUE ELSE 0 END) AS 'cyber_security_management',
            SUM(CASE WHEN Q.TIPE = 'Cyberbullying' THEN R.VALUE ELSE 0 END) AS 'cyberbullying',
            SUM(CASE WHEN Q.TIPE = 'Digital Citizen Identity' THEN R.VALUE ELSE 0 END) AS 'digital_citizen_identity',
            SUM(CASE WHEN Q.TIPE = 'Digital Empathy' THEN R.VALUE ELSE 0 END) AS 'digital_empathy',
            SUM(CASE WHEN Q.TIPE = 'Digital Footprint' THEN R.VALUE ELSE 0 END) AS 'digital_footprint',
            SUM(CASE WHEN Q.TIPE = 'Privacy Management' THEN R.VALUE ELSE 0 END) AS 'privacy_management',
            SUM(CASE WHEN Q.TIPE = 'Screen Time' THEN R.VALUE ELSE 0 END) AS 'screen_time'
        ");
        $this->db->from("RESULT AS R");
        $this->db->join("QUESTION AS Q", "Q.ID = R.QUESTION", "LEFT");
        $this->db->join("CATEGORY AS C", "C.KATEGORI = Q.TIPE", "LEFT");
        $this->db->join("account AS A", "A.ID = R.USERID", "LEFT");

        $this->db->where("A.STATE", "FINISH");

        if ($school != "") {
            $this->db->where("A.SCHOOL", $school);
        }

        if ($from_date != "") {
            $from_date = date_format(date_create($from_date), "Y-m-d");
            $from_date = $from_date . " 00:00:00";
            $this->db->where("R.ACTIVITY_ON >=", $from_date);
        }

        if ($to_date != "") {
            $to_date = date_format(date_create($to_date), "Y-m-d");
            $to_date = $to_date . " 23:59:59";
            $this->db->where("R.ACTIVITY_ON <=", $to_date);
        }

        $this->db->group_by(array("A.SCHOOL", "A.GRADE"));
        $this->db->order_by('A.SCHOOL', 'ASC');
        $this->db->order_by('A.GRADE', 'ASC');
        
        return $this->db->get();
    }

    function getReportByPersonal($school, $from_date, $to_date)
    {
        $this->db->select("
            A.SCHOOL,
            IFNULL(A.GRADE, 'Tidak Ada Kelas') AS GRADE,
            A.ID,
            A.USERNAME,
            1 AS COUNT_OF_STUDENT,
            SUM(CASE WHEN Q.TIPE = 'Critical Thinking' THEN R.VALUE ELSE 0 END) AS 'critical_thinking',
            SUM(CASE WHEN Q.TIPE = 'Cyber Security Management' THEN R.VALUE ELSE 0 END) AS 'cyber_security_management',
            SUM(CASE WHEN Q.TIPE = 'Cyberbullying' THEN R.VALUE ELSE 0 END) AS 'cyberbullying',
            SUM(CASE WHEN Q.TIPE = 'Digital Citizen Identity' THEN R.VALUE ELSE 0 END) AS 'digital_citizen_identity',
            SUM(CASE WHEN Q.TIPE = 'Digital Empathy' THEN R.VALUE ELSE 0 END) AS 'digital_empathy',
            SUM(CASE WHEN Q.TIPE = 'Digital Footprint' THEN R.VALUE ELSE 0 END) AS 'digital_footprint',
            SUM(CASE WHEN Q.TIPE = 'Privacy Management' THEN R.VALUE ELSE 0 END) AS 'privacy_management',
            SUM(CASE WHEN Q.TIPE = 'Screen Time' THEN R.VALUE ELSE 0 END) AS 'screen_time'
        ");
        $this->db->from("RESULT AS R");
        $this->db->join("QUESTION AS Q", "Q.ID = R.QUESTION", "LEFT");
        $this->db->join("CATEGORY AS C", "C.KATEGORI = Q.TIPE", "LEFT");
        $this->db->join("account AS A", "A.ID = R.USERID", "LEFT");

        $this->db->where("A.STATE", "FINISH");

        if ($school != "") {
            $this->db->where("A.SCHOOL", $school);
        }

        if ($from_date != "") {
            $from_date = date_format(date_create($from_date), "Y-m-d");
            $from_date = $from_date . " 00:00:00";
            $this->db->where("R.ACTIVITY_ON >=", $from_date);
        }

        if ($to_date != "") {
            $to_date = date_format(date_create($to_date), "Y-m-d");
            $to_date = $to_date . " 23:59:59";
            $this->db->where("R.ACTIVITY_ON <=", $to_date);
        }

        $this->db->group_by(array("A.SCHOOL", "A.GRADE", "A.ID", "A.USERNAME"));
        $this->db->order_by('A.SCHOOL', 'ASC');
        $this->db->order_by('A.GRADE', 'ASC');
        $this->db->order_by('A.USERNAME', 'ASC');
        
        return $this->db->get();
    }
}