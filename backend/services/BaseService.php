<?php

class BaseService
{
    protected $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function get_all()
    {
        return $this->dao->get_all();
    }

    public function get_by_id($id)
    {
        $record = $this->dao->get_by_id($id);
        if (!$record) {
            throw new Exception("Record not found.", 404);
        }
        return $record;
    }

    public function add($data)
    {
        if (empty($data) || !is_array($data)) {
            throw new Exception("Request payload cannot be empty.", 400);
        }
        return $this->dao->add($data);
    }

    public function update($id, $data)
    {
        $this->get_by_id($id);
        if (empty($data) || !is_array($data)) {
            throw new Exception("No valid fields provided for update.", 400);
        }
        return $this->dao->update($id, $data);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $this->dao->delete($id);
        return ['message' => 'Record deleted successfully.'];
    }
}