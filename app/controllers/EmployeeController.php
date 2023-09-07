<?php

namespace App\Controllers;

use App\Model\Employee;
use Exception;

class EmployeeController
{

    protected Employee $employeeModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();
    }

    /**
     * Employee Listing from model
     *
     * @return void
     * @throws Exception
     */
    public function list()
    {
        $list = $this->employeeModel->getList();

        return returnJsonResponse($list);
    }

    /**
     * Update email of employee
     *
     * @throws Exception
     */
    public function update($data): array
    {
        $employeeData = json_decode(file_get_contents('php://input'), true);
        try {
            $employee = $this->employeeModel->findOrFail($data['id']);
            $employeeUpdateResult = $employee->updateWhere([
                'Email_Address' => $employeeData['email']
            ], [
                ['id', '=', $data['id']]
            ]);
            if ($employeeUpdateResult) {
                return returnJsonResponse(['success' => true, 'message' => 'Employee email has been updated']);
            };
            return returnJsonResponse(['success' => false, 'message' => 'Employee email not updated. Please tra again']);
        } catch (Exception $exception) {
            return returnJsonResponse(['success' => false, 'message' => 'Employee not found']);
        }
    }

    /**
     * Get average salary by company
     *
     * @return null
     */
    public function salary()
    {
        $salaryData = $this->employeeModel->getAverageSalaryByCompany();
        return returnJsonResponse($salaryData);
    }
}