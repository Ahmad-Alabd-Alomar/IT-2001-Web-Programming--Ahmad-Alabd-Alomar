<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('categories');
    }

    /**
     * Finds a category by its name.
     */
    public function get_by_name($name)
    {
        return $this->query_unique(
            "SELECT * FROM categories WHERE name = :name",
            ['name' => $name]
        );
    }
}