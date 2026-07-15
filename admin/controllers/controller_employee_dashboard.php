<?php
class _employee_dashboard extends controller
{
    function init() {}

    function onload()
    {
        $employeeId = $_SESSION['employeeId'];
        //$employeeId=1;

        $obj_table_tble = $this->app->load_model('employee');
        $employee = $obj_table_tble->execute("SELECT", false, "", "id='" . $employeeId . "'");
        $subEmployee = $this->app->utility->getSubEmployeeAll($employee[0]['lms_employee_id']);
        $html = $this->getSubEmployeeHtml($subEmployee);

        $this->assign("subEmployee", $subEmployee);
        $this->assign("html", $html);
        $this->assign("employee", $employee[0]);
    }
    
    function getSubEmployeeHtml($subEmployee = [], $sub = null)
    {
        if (!empty($sub)) {
            $html = '';
            foreach ($subEmployee as $employee) {
                $html .= '<li class="align-items-center">
                    <div class="media-body pd-l-15 mb-3 cat-search">
                        <h6 class="mg-b-2 d-flex align-items-top justify-content-between">
                            <a href="javascript:void(0)" class="link-01" data-id="' . $employee['id'] . '" data-name="' . $employee['name'] . '">
                                ' . $employee['name'] . ' - ' . $employee['designation'] . '<br> ' . $employee['lms_employee_code'] . ' - ' . $employee['mobile'] . '
                            </a>';
                if (!empty($employee['children']) && count($employee['children'])>0) {
                    $html .= '<span class="toggle-btn ml-2" onclick="toggleSubEmployees(this)" style="cursor: pointer;">
                                <i class="fas fa-chevron-down"></i>
                            </span>';
                }
                $html .= '</h6>';
                $html .= '<span class="tx-13 tx-color-03">' . count($employee['children']) . ' Members</span>';
                if (!empty($employee['children']) && count($employee['children'])>0) {
                    $html .= '<ul class="list-unstyled media-list mg-b-15 -bottom-3 employeeList subEmployee mt-3" style="display: none;">';
                    $html .= $this->getSubEmployeeHtml($employee['children'], 'sub');
                    $html .= '</ul>';
                }
                $html .= '</div></li>';
            }
            return $html;
        } else {
            $html = '<ul class="list-unstyled media-list mg-b-15 -bottom-3 employeeList">';
            foreach ($subEmployee as $employee) {
                $html .= '<li class="align-items-center">
                    <div class="media-body mb-3 cat-search">
                        <h6 class="mg-b-2 d-flex align-items-top justify-content-between">
                            <a href="javascript:void(0)" class="link-01" data-id="' . $employee['id'] . '" data-name="' . $employee['name'] . '">
                                ' . $employee['name'] . ' - ' . $employee['designation'] . '<br> ' . $employee['lms_employee_code'] . ' - ' . $employee['mobile'] . '
                            </a>';
                if (!empty($employee['children']) && count($employee['children'])>0) {
                    $html .= '<span class="toggle-btn ml-2" onclick="toggleSubEmployees(this)" style="cursor: pointer;">
                                <i class="fas fa-chevron-down"></i>
                            </span>';
                }
                $html .= '</h6>';
                $html .= '<span class="tx-13 tx-color-03">' . count($employee['children']) . ' Members</span>';
                if (!empty($employee['children']) && count($employee['children'])>0) {
                    $html .= '<ul class="list-unstyled media-list mg-b-15 -bottom-3 employeeList subEmployee mt-3" style="display: none;">';
                    $html .= $this->getSubEmployeeHtml($employee['children'], 'sub');
                    $html .= '</ul>';
                }
                $html .= '</div></li>';
            }
            $html .= '</ul>';
            return $html;
        }
    }

    function export_data()
	{
		
		$start_date=$this->app->getGetVar("start_date");
        $end_date=$this->app->getGetVar("end_date");

		$this->app->no_html=true;
		$obj_excel = $this->app->load_module("PHPExcel");
        
        $ExeclHeads=array("Employee Code","Employee Name","Designation","Billing Discussion","Test Marketing","CME Related","Potential Client/Business","Feedback Visit","Literature","Other","Total Visit");

		$employeeId=$_SESSION['employeeId'];
        $obj_table_tble= $this->app->load_model('employee');
        $employee= $obj_table_tble->execute("SELECT", false, "","id='".$employeeId."'");
        
        $subEmployee=$this->app->utility->getSubEmployee($employee[0]['lms_employee_id']);
        array_merge($employee,$subEmployee);

        $searchQuery='';
        if($start_date!='')
        {
            $searchQuery.=" AND date(employee_task_master.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
        }
		
		foreach(array_merge($employee,$subEmployee) as $employeeSub)
		{
            $taskDataSub=[];
            if($employeeSub)
            {
                $obj_table_tble= $this->app->load_model('employee_task_master');
                $taskDataSub= $obj_table_tble->execute("SELECT", false, "SELECT purpose, COUNT(*) AS task_count FROM employee_task_master WHERE employee_primary_id='".$employeeSub['id']."' ".$searchQuery." GROUP BY purpose");
            }
            $result = [];
            if (!empty($taskDataSub)) {
                foreach ($taskDataSub as $row) {
                    $key = !empty($row['purpose']) ? $row['purpose'] : 'Other';
                    if (!isset($result[$key])) {
                        $result[$key] = 0;
                    }
                    $result[$key] += (int)$row['task_count'];
                }
            }
			$user_array[] = [
                "Employee Code" => $employeeSub['lms_employee_code'],
                "Employee Name" => $employeeSub['name'],
                "Designation" => $employeeSub['designation'],
                "Billing Discussion" =>$result['Billing Discussion'] ?? '0',
                "Test Marketing" => $result['Test Marketing'] ?? '0',
                "CME Related" => $result['CME Related'] ?? '0',
                "Potential Client/Business" =>$result['Potential Client/Business'] ?? '0',
                "Feedback Visit" =>$result['Feedback Visit'] ?? '0',
                "Literature" => $result['Literature'] ?? '0',
                "Other" =>$result['Other'] ?? '0',
                "Total Visit" =>count($result)>0?array_sum($result):'0',
            ];
		}

		$array_field=array(
			"block_name"=>array("options"=>"","prompt_title"=>"","prompt"=>""),
			"flat_type"=>array("options"=>"","prompt_title"=>"","prompt"=>""),
			"resident_type"=>array("options"=>"","prompt_title"=>"","prompt"=>"")
		);
		$data_array=$user_array;
		$fields=array("Employee Code","Employee Name","Designation","Billing Discussion","Test Marketing","CME Related","Potential Client/Business","Feedback Visit","Literature","Other","Total Visit");
		$filename="Task - ".date('d-m-Y');
		$this->app->utility->export_excel($ExeclHeads,$data_array,$fields,$filename,$array_field);			
	}

}
