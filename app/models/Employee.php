<?php

namespace App\Model;

use App\core\database\Model;
use Exception;

class Employee extends Model
{
    protected static string $table = 'employee';

    /**
     * Get List of employees
     *
     * @throws Exception
     */
    public function getList()
    {
        $employeeData = parent::all();

        $returnArray = [];
        foreach ($employeeData as $employee) {
            $returnArray[] = [
                'Id' => $employee->id(),
                'Company_name' => $employee->Company_Name,
                'Email_Address' => $employee->Email_Address,
                'EmployeeName' => $employee->Employee_Name,
                'Salary' => $employee->Salary,
            ];
        }

        return $returnArray;
    }

    /**
     * Insert employee data
     *
     * @param array $columns
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function insertData(array $columns = [], array $data = [])
    {
        $employeeTableExists = parent::describe();
        if (empty($employeeTableExists)) {
            $createTableResult = $this->createTable($columns);
            if (empty($createTableResult)) {
                return ['success' => false, 'message' => 'Table doesn\'t created'];
            }
        }

        $count = 0;
        if (!empty($data)) {
            foreach ($data as $employee) {
                if (parent::add($employee)) {
                    $count++;
                };
            }
        }
        return [
            'success' => true, 'message' => 'Total ' . $count . ' records inserted'
        ];
    }

    /**
     * Get average salary by company
     *
     * @return array
     */
    public function getAverageSalaryByCompany()
    {
        $sql = "SELECT Company_Name, AVG(Salary) as AverageSalary FROM employee GROUP BY Company_Name;";
        $salaryData = parent::raw($sql);
        $finalSalaryData = [];
        foreach ($salaryData as $salary) {
            $finalSalaryData[] = [
                'Company_Name' => $salary->Company_Name,
                'Salary' => $salary->AverageSalary
            ];
        }
        return [
            'success' => true,
            'message' => $finalSalaryData
        ];
    }
}