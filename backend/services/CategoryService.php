<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new CategoryDao());
    }

    public function create_category(array $data): array
    {
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new Exception("Category name is required.", 400);
        }

        if ($this->dao->get_by_name($name)) {
            throw new Exception("Category '{$name}' already exists.", 409);
        }

        return $this->dao->add([
            'name'        => $name,
            'description' => trim($data['description'] ?? '')
        ]);
    }

    public function update_category($id, array $data): array
    {
        $this->get_by_id($id);
        $payload = [];

        if (isset($data['name'])) {
            $name = trim($data['name']);
            if ($name === '') {
                throw new Exception("Category name cannot be empty.", 400);
            }
            $existing = $this->dao->get_by_name($name);
            if ($existing && (int)$existing['id'] !== (int)$id) {
                throw new Exception("Category '{$name}' already exists.", 409);
            }
            $payload['name'] = $name;
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = trim((string)$data['description']);
        }

        if (empty($payload)) {
            throw new Exception("No valid fields provided for update.", 400);
        }

        return $this->dao->update($id, $payload);
    }

    public function delete_category($id): array
    {
        $this->get_by_id($id);
        try {
            $this->dao->delete($id);
            return ['message' => 'Category deleted successfully.'];
        } catch (\PDOException $e) {
            // Foreign key constraint violation (SQLSTATE 23000) when recipes still reference this category
            if ($e->getCode() === '23000') {
                throw new Exception("Cannot delete category because recipes are currently assigned to it.", 400);
            }
            throw $e;
        }
    }
}