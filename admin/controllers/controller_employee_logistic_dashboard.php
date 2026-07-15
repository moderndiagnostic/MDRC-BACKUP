<?php
class _employee_logistic_dashboard extends controller
{
	function init() {
	}
	
	function onload()
	{
		$employeeId=$_SESSION['employeeId'];
		//$employeeId=1;

		$obj_table_tble= $this->app->load_model('employee');
		$employee= $obj_table_tble->execute("SELECT", false, "","id='".$employeeId."'");
		$subEmployee=$this->app->utility->getSubEmployeeAll($employee[0]['lms_employee_id']);
		$html=$this->getSubEmployeeHtml($subEmployee);

		$this->assign("subEmployee", $subEmployee);
		$this->assign("html", $html);
		$this->assign("employee", $employee[0]);
	}
	/* function getSubEmployeeHtml($subEmployee = [])
	{
		$html = '<ul class="list-unstyled media-list mg-b-15 -bottom-3 employeeList">';
		foreach ($subEmployee as $employee) {
			$html .= '<li class="align-items-center">
						<div class="media-body pd-l-15 mb-3">
							<h6 class="mg-b-2">
								<a href="javascript:void(0)" class="link-01" data-id="'.$employee['id'].'" data-name="'.$employee['name'].'">' . $employee['name'] . ' - ' . $employee['designation'] . '<br> '.$employee['lms_employee_code'].' - ' . $employee['mobile'] . '</a>
							</h6>
							<span class="tx-13 tx-color-03">' . count($employee['children']) . ' Members</span>
						</div>';

			if (!empty($employee['children'])) {
				$html .= $this->getSubEmployeeHtml($employee['children']);
			}

			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	} */


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
                if (!empty($employee['children'])) {
                    $html .= '<span class="toggle-btn ml-2" onclick="toggleSubEmployees(this)" style="cursor: pointer;">
                                <i class="fas fa-chevron-down"></i>
                            </span>';
                }
                $html .= '</h6>';
                $html .= '<span class="tx-13 tx-color-03">' . count($employee['children']) . ' Members</span>';
                if (!empty($employee['children'])) {
                    $html .= '<ul class="list-unstyled media-list mg-b-15 -bottom-3 mt-3 employeeList subEmployee" style="display: none;">';
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
                if (!empty($employee['children'])) {
                    $html .= '<span class="toggle-btn ml-2" onclick="toggleSubEmployees(this)" style="cursor: pointer;">
                                <i class="fas fa-chevron-down"></i>
                            </span>';
                }
                $html .= '</h6>';
                $html .= '<span class="tx-13 tx-color-03">' . count($employee['children']) . ' Members</span>';
                if (!empty($employee['children'])) {
                    $html .= '<ul class="list-unstyled media-list mg-b-15 -bottom-3 mt-3 employeeList subEmployee" style="display: none;">';
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
        
        $ExeclHeads=array("Employee Code","Employee Name","Designation","Client","Total Collect Sample","Total Collect Payment","Total Logistics Visits");

		$employeeId=$_SESSION['employeeId'];
        $obj_table_tble= $this->app->load_model('employee');
        $employee= $obj_table_tble->execute("SELECT", false, "","id='".$employeeId."'");
        
        $subEmployee=$this->app->utility->getSubEmployee($employee[0]['lms_employee_id']);
        array_merge($employee,$subEmployee);

        $searchQuery='';
        if($start_date!='')
        {
            $searchQuery.=" AND date(employee_sample_pickup.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
        }
		
		foreach(array_merge($employee,$subEmployee) as $employeeSub)
		{
            $taskDataSub=[];
            if($employeeSub)
            {
                $obj_table_tble=$this->app->load_model('employee_sample_pickup');
		        $visitCountDataSub= $obj_table_tble->execute("SELECT", false, "SELECT COUNT(*) AS visit_count,COUNT(CASE WHEN collect_sample = 'Yes' THEN 1 END) AS collect_sample_count,COUNT(CASE WHEN collect_payment = 'Yes' THEN 1 END) AS collect_payment_count  FROM employee_sample_pickup WHERE employee_id='".$employeeSub['id']."' ".$searchQuery."");

                $visitDataSub= $obj_table_tble->execute("SELECT", false, "SELECT 
                    employee_sample_pickup.client_id, 
                    c.company_name AS client_name, 
                    COUNT(*) AS visit_count 
                    FROM employee_sample_pickup 
                    LEFT JOIN client c ON employee_sample_pickup.client_id = c.id
                    WHERE employee_sample_pickup.employee_id='".$employeeSub['id']."' ".$searchQuery."
                    GROUP BY employee_sample_pickup.client_id");

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
                "Client"=>'',
                "Total Collect Sample" => $visitCountDataSub[0]['collect_sample_count'] ?? '0',
                "Total Collect Payment" => $visitCountDataSub[0]['collect_payment_count']?? '0',
                "Total Logistics Visits" =>$visitCountDataSub[0]['visit_count'] ?? '0',
            ];

            foreach ($visitDataSub as $item) 
            {
                $user_array[] = [
                    "Employee Code" => '',
                    "Employee Name" => '',
                    'Client'=>$item['client_name'],
                    "Total Collect Sample" => '',
                    "Total Collect Payment" =>'',
                    "Total Logistics Visits" =>$item['visit_count']?? '0',
                ];
            }
		}

		$array_field=array(
			"block_name"=>array("options"=>"","prompt_title"=>"","prompt"=>""),
			"flat_type"=>array("options"=>"","prompt_title"=>"","prompt"=>""),
			"resident_type"=>array("options"=>"","prompt_title"=>"","prompt"=>"")
		);
		$data_array=$user_array;
		$fields=array("Employee Code","Employee Name","Designation","Client","Total Collect Sample","Total Collect Payment","Total Logistics Visits");
		$filename="Task - ".date('d-m-Y');
		$this->app->utility->export_excel($ExeclHeads,$data_array,$fields,$filename,$array_field);			
	}
}
