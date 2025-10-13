<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("report_model");
	}

	public function grade()
	{
        $data['title'] = "Report by Grade";
		$data['from_date'] = date("d-m-Y", strtotime("-6 months"));
		$data['to_date'] = date("d-m-Y");
		$data['school'] = $this->report_model->getSchool();

		$this->layout->view('report/report_by_grade', $data);
	}

	public function grade_ajax()
	{
		$school = $this->input->post('school');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');

		$reportPerGrade = $this->report_model->getReportByGrade($school, $from_date, $to_date);

		$tHtml = '<table class="table mt-4 table-report table-striped table-bordered table-sm">';
		$tHtml .= '<thead>';
		$tHtml .= '<tr>';
		$tHtml .= '<th>School</th>';
		$tHtml .= '<th>Grade</th>';
		$tHtml .= '<th>Student</th>';
		$tHtml .= '<th class="small">Critical Thinking</th>';
		$tHtml .= '<th class="small">Cyber Security Management</th>';
		$tHtml .= '<th class="small">Cyberbullying</th>';
		$tHtml .= '<th class="small">Digital Citizen Identity</th>';
		$tHtml .= '<th class="small">Digital Empathy</th>';
		$tHtml .= '<th class="small">Digital Footprint</th>';
		$tHtml .= '<th class="small">Privacy Management</th>';
		$tHtml .= '<th class="small">Screen Time</th>';
		$tHtml .= '</tr>';
		$tHtml .= '</thead>';
		$tHtml .= '<tbody>';

		foreach ($reportPerGrade->result_array() as $row) {
			$critical_thinking = $row['critical_thinking'] / $row['COUNT_OF_STUDENT'];
			$cyber_security_management = $row['cyber_security_management'] / $row['COUNT_OF_STUDENT'];
			$cyberbullying = $row['cyberbullying'] / $row['COUNT_OF_STUDENT'];
			$digital_citizen_identity = $row['digital_citizen_identity'] / $row['COUNT_OF_STUDENT'];
			$digital_empathy = $row['digital_empathy'] / $row['COUNT_OF_STUDENT'];
			$digital_footprint = $row['digital_footprint'] / $row['COUNT_OF_STUDENT'];
			$privacy_management = $row['privacy_management'] / $row['COUNT_OF_STUDENT'];
			$screen_time = $row['screen_time'] / $row['COUNT_OF_STUDENT'];

			$critical_thinking = round($critical_thinking, 0) * 1;
			$cyber_security_management = round($cyber_security_management, 0) * 1;
			$cyberbullying = round($cyberbullying, 0) * 1;
			$digital_citizen_identity = round($digital_citizen_identity, 0) * 1;
			$digital_empathy = round($digital_empathy, 0) * 1;
			$digital_footprint = round($digital_footprint, 0) * 1;
			$privacy_management = round($privacy_management, 0) * 1;
			$screen_time = round($screen_time, 0) * 1;

			$critical_thinking_color = getBgColor($critical_thinking);
			$cyber_security_management_color = getBgColor($cyber_security_management);
			$cyberbullying_color = getBgColor($cyberbullying);
			$digital_citizen_identity_color = getBgColor($digital_citizen_identity);
			$digital_empathy_color = getBgColor($digital_empathy);
			$digital_footprint_color = getBgColor($digital_footprint);
			$privacy_management_color = getBgColor($privacy_management);
			$screen_time_color = getBgColor($screen_time);

			$tHtml .= '<tr>';
			$tHtml .= '<td>'.$row['SCHOOL'].'</td>';
			$tHtml .= '<td>'.$row['GRADE'].'</td>';
			$tHtml .= '<td>'.$row['COUNT_OF_STUDENT'].'</td>';
			$tHtml .= '<td class="text-end '.$critical_thinking_color.'">'.$critical_thinking.'</td>';
			$tHtml .= '<td class="text-end '.$cyber_security_management_color.'">'.$cyber_security_management.'</td>';
			$tHtml .= '<td class="text-end '.$cyberbullying_color.'">'.$cyberbullying.'</td>';
			$tHtml .= '<td class="text-end '.$digital_citizen_identity_color.'">'.$digital_citizen_identity.'</td>';
			$tHtml .= '<td class="text-end '.$digital_empathy_color.'">'.$digital_empathy.'</td>';
			$tHtml .= '<td class="text-end '.$digital_footprint_color.'">'.$digital_footprint.'</td>';
			$tHtml .= '<td class="text-end '.$privacy_management_color.'">'.$privacy_management.'</td>';
			$tHtml .= '<td class="text-end '.$screen_time_color.'">'.$screen_time.'</td>';
			$tHtml .= '</tr>';
		}

		$tHtml .= '</tbody>';
		$tHtml .= '<table>';

		$data['report'] = $tHtml;

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function grade_export_pdf()
	{
		$this->load->library('Pdf');
		error_reporting(0);

		$school = $this->input->get('school');
		$from_date = $this->input->get('from_date');
		$to_date = $this->input->get('to_date');
		$reportPerGrade = $this->report_model->getReportByGrade($school, $from_date, $to_date);

		$from_date = date_format(date_create($from_date), "d M Y");
		$to_date = date_format(date_create($to_date), "d M Y");
		$range_date = $from_date . ' - ' . $to_date;

		$pdf = new FPDF('L', 'mm','Letter');
		$pdf->SetTitle('Report by Grade');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, $school, 0, 1, 'C');
		$pdf->Cell(0, 6, $range_date, 0, 1, 'C');

		$pdf->Cell(0, 6, "", 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);

		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->MultiCell(28, 14, 'Grade', 1, 'C', 0);

		$x = $x + 28;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 7, 'Critical Thinking', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 7, 'Cyber Security Management', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 14, 'Cyberbullying', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 7, 'Digital Citizen Identity', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 14, 'Digital Empathy', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 7, 'Digital Footprint', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 7, 'Privacy Management', 1, 'C', 0);

		$x = $x + 29;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(29, 14, 'Screen Time', 1, 'C', 0);
		
		$pdf->SetFont('Arial', '', 10);
		foreach ($reportPerGrade->result_array() as $row) {
			$critical_thinking = round($row['critical_thinking'] / $row['COUNT_OF_STUDENT'], 0);
			$cyber_security_management = round($row['cyber_security_management'] / $row['COUNT_OF_STUDENT'], 0);
			$cyberbullying = round($row['cyberbullying'] / $row['COUNT_OF_STUDENT'], 0);
			$digital_citizen_identity = round($row['digital_citizen_identity'] / $row['COUNT_OF_STUDENT'], 0);
			$digital_empathy = round($row['digital_empathy'] / $row['COUNT_OF_STUDENT'], 0);
			$digital_footprint = round($row['digital_footprint'] / $row['COUNT_OF_STUDENT'], 0);
			$privacy_management = round($row['privacy_management'] / $row['COUNT_OF_STUDENT'], 0);
			$screen_time = round($row['screen_time'] / $row['COUNT_OF_STUDENT'], 0);

			$pdf->Cell(28, 10, $row['GRADE'], 1, 0, 'L');
			$pdf->Cell(29, 10, $critical_thinking, 1, 0, 'R');
			$pdf->Cell(29, 10, $cyber_security_management, 1, 0, 'R');
			$pdf->Cell(29, 10, $cyberbullying, 1, 0, 'R');
			$pdf->Cell(29, 10, $digital_citizen_identity, 1, 0, 'R');
			$pdf->Cell(29, 10, $digital_empathy, 1, 0, 'R');
			$pdf->Cell(29, 10, $digital_footprint, 1, 0, 'R');
			$pdf->Cell(29, 10, $privacy_management, 1, 0, 'R');
			$pdf->Cell(29, 10, $screen_time, 1, 1, 'R');

		}
        
        $pdf->Output('result.pdf', 'I');
	}

	public function personal()
	{
        $data['title'] = "Report by Personal";
		$data['from_date'] = date("d-m-Y", strtotime("-6 months"));
		$data['to_date'] = date("d-m-Y");
		$data['school'] = $this->report_model->getSchool();

		$this->layout->view('report/report_by_personal', $data);
	}

	public function personal_ajax()
	{
		$school = $this->input->post('school');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');

		$reportPerGrade = $this->report_model->getReportByPersonal($school, $from_date, $to_date);

		$tHtml = '<table class="table mt-4 table-report table-striped table-bordered table-sm">';
		$tHtml .= '<thead>';
		$tHtml .= '<tr>';
		$tHtml .= '<th>School</th>';
		$tHtml .= '<th>Grade</th>';
		$tHtml .= '<th>Student</th>';
		$tHtml .= '<th class="small">Critical Thinking</th>';
		$tHtml .= '<th class="small">Cyber Security Management</th>';
		$tHtml .= '<th class="small">Cyberbullying</th>';
		$tHtml .= '<th class="small">Digital Citizen Identity</th>';
		$tHtml .= '<th class="small">Digital Empathy</th>';
		$tHtml .= '<th class="small">Digital Footprint</th>';
		$tHtml .= '<th class="small">Privacy Management</th>';
		$tHtml .= '<th class="small">Screen Time</th>';
		$tHtml .= '</tr>';
		$tHtml .= '</thead>';
		$tHtml .= '<tbody>';

		foreach ($reportPerGrade->result_array() as $row) {
			$critical_thinking = $row['critical_thinking'] / $row['COUNT_OF_STUDENT'];
			$cyber_security_management = $row['cyber_security_management'] / $row['COUNT_OF_STUDENT'];
			$cyberbullying = $row['cyberbullying'] / $row['COUNT_OF_STUDENT'];
			$digital_citizen_identity = $row['digital_citizen_identity'] / $row['COUNT_OF_STUDENT'];
			$digital_empathy = $row['digital_empathy'] / $row['COUNT_OF_STUDENT'];
			$digital_footprint = $row['digital_footprint'] / $row['COUNT_OF_STUDENT'];
			$privacy_management = $row['privacy_management'] / $row['COUNT_OF_STUDENT'];
			$screen_time = $row['screen_time'] / $row['COUNT_OF_STUDENT'];

			$critical_thinking = round($critical_thinking, 0) * 1;
			$cyber_security_management = round($cyber_security_management, 0) * 1;
			$cyberbullying = round($cyberbullying, 0) * 1;
			$digital_citizen_identity = round($digital_citizen_identity, 0) * 1;
			$digital_empathy = round($digital_empathy, 0) * 1;
			$digital_footprint = round($digital_footprint, 0) * 1;
			$privacy_management = round($privacy_management, 0) * 1;
			$screen_time = round($screen_time, 0) * 1;

			$critical_thinking_color = getBgColor($critical_thinking);
			$cyber_security_management_color = getBgColor($cyber_security_management);
			$cyberbullying_color = getBgColor($cyberbullying);
			$digital_citizen_identity_color = getBgColor($digital_citizen_identity);
			$digital_empathy_color = getBgColor($digital_empathy);
			$digital_footprint_color = getBgColor($digital_footprint);
			$privacy_management_color = getBgColor($privacy_management);
			$screen_time_color = getBgColor($screen_time);

			$tHtml .= '<tr>';
			$tHtml .= '<td>'.$row['SCHOOL'].'</td>';
			$tHtml .= '<td>'.$row['GRADE'].'</td>';
			$tHtml .= '<td>'.$row['USERNAME'].'</td>';
			$tHtml .= '<td class="text-end '.$critical_thinking_color.'">'.$critical_thinking.'</td>';
			$tHtml .= '<td class="text-end '.$cyber_security_management_color.'">'.$cyber_security_management.'</td>';
			$tHtml .= '<td class="text-end '.$cyberbullying_color.'">'.$cyberbullying.'</td>';
			$tHtml .= '<td class="text-end '.$digital_citizen_identity_color.'">'.$digital_citizen_identity.'</td>';
			$tHtml .= '<td class="text-end '.$digital_empathy_color.'">'.$digital_empathy.'</td>';
			$tHtml .= '<td class="text-end '.$digital_footprint_color.'">'.$digital_footprint.'</td>';
			$tHtml .= '<td class="text-end '.$privacy_management_color.'">'.$privacy_management.'</td>';
			$tHtml .= '<td class="text-end '.$screen_time_color.'">'.$screen_time.'</td>';
			$tHtml .= '</tr>';
		}

		$tHtml .= '</tbody>';
		$tHtml .= '<table>';

		$data['report'] = $tHtml;

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function personal_export_pdf()
	{
		$this->load->library('Pdf');
		error_reporting(0);

		$school = $this->input->get('school');
		$from_date = $this->input->get('from_date');
		$to_date = $this->input->get('to_date');
		$reportPerPersonal = $this->report_model->getReportByPersonal($school, $from_date, $to_date);

		$from_date = date_format(date_create($from_date), "d M Y");
		$to_date = date_format(date_create($to_date), "d M Y");
		$range_date = $from_date . ' - ' . $to_date;

		$pdf = new FPDF('L', 'mm','Letter');
		$pdf->SetTitle('Report by Personal');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, $school, 0, 1, 'C');
		$pdf->Cell(0, 6, $range_date, 0, 1, 'C');

		$pdf->Cell(0, 6, "", 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);

		$width = 22;

		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->MultiCell(25, 24, 'Grade', 1, 'C', 0);

		$x = $x + 25;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell(60, 24, 'Name', 1, 'C', 0);

		$x = $x + 60;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 12, 'Critical Thinking', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 6, 'Cyber Security Management', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 12, 'Cyberbullying', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 8, 'Digital Citizen Identity', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 12, 'Digital Empathy', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 12, 'Digital Footprint', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 8, 'Privacy Management', 1, 'C', 0);

		$x = $x + $width;
		$pdf->SetXY($x, $y);
		$pdf->MultiCell($width, 12, 'Screen Time', 1, 'C', 0);
		
		$pdf->SetFont('Arial', '', 10);

		$height = 8;
		foreach ($reportPerPersonal->result_array() as $row) {
			$critical_thinking = round($row['critical_thinking'] / $row['COUNT_OF_STUDENT'], 0);
			$cyber_security_management = round($row['cyber_security_management'] / $row['COUNT_OF_STUDENT'], 0);
			$cyberbullying = round($row['cyberbullying'] / $row['COUNT_OF_STUDENT'], 0);
			$digital_citizen_identity = round($row['digital_citizen_identity'] / $row['COUNT_OF_STUDENT'], 0);
			$digital_empathy = round($row['digital_empathy'] / $row['COUNT_OF_STUDENT'], 0);
			$digital_footprint = round($row['digital_footprint'] / $row['COUNT_OF_STUDENT'], 0);
			$privacy_management = round($row['privacy_management'] / $row['COUNT_OF_STUDENT'], 0);
			$screen_time = round($row['screen_time'] / $row['COUNT_OF_STUDENT'], 0);

			$pdf->Cell(25, $height, $row['GRADE'], 1, 0, 'L');
			$pdf->Cell(60, $height, $row['USERNAME'], 1, 0, 'L');			
			$pdf->Cell($width, $height, $critical_thinking, 1, 0, 'R');
			$pdf->Cell($width, $height, $cyber_security_management, 1, 0, 'R');
			$pdf->Cell($width, $height, $cyberbullying, 1, 0, 'R');
			$pdf->Cell($width, $height, $digital_citizen_identity, 1, 0, 'R');
			$pdf->Cell($width, $height, $digital_empathy, 1, 0, 'R');
			$pdf->Cell($width, $height, $digital_footprint, 1, 0, 'R');
			$pdf->Cell($width, $height, $privacy_management, 1, 0, 'R');
			$pdf->Cell($width, $height, $screen_time, 1, 1, 'R');

		}
        
        $pdf->Output('result.pdf', 'I');
	}
}
