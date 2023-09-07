<?php

namespace App\Controllers;

use App\Model\Employee;

class ImportController
{
    protected Employee $employee;

    /**
     * Constructor function
     */
    public function __construct()
    {
        $this->employee = new Employee();
    }

    /**
     * Import function from csv to database
     *
     * @param array $data
     * @return null
     */
    public function import(array $data = [])
    {
        $importJsonData = json_decode(file_get_contents('php://input'), true);
        $columns = !empty($importJsonData['columns']) ? $importJsonData['columns'] : [];
        $data = !empty($importJsonData['data']) ? $importJsonData['data'] : [];

        if (empty($columns)) {
            return returnJsonResponse(['success' => false, 'message' => 'Columns not found']);
        }

        if (empty($data)) {
            return returnJsonResponse(['success' => false, 'message' => 'Data not found']);
        }

        $result = $this->employee->insertData($columns, $data);
        return returnJsonResponse(['success' => true, 'message' => $result]);
    }
}