<?php
require_once __DIR__ . '/../config/config.php';

class BaseDao
{
    protected $connection;
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;

        try {
            $host = Config::DB_HOST();
            $port = Config::DB_PORT();
            $name = Config::DB_NAME();
            $user = Config::DB_USER();
            $pass = Config::DB_PASSWORD();

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Filters input data so only valid table columns are used in SQL queries.
     */
    protected function only_columns($data)
    {
        $stmt = $this->connection->prepare("DESCRIBE " . $this->table);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $columns)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * Executes a SELECT query with prepared statement parameters.
     */
    public function query($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executes a SELECT query expecting a single record.
     */
    public function query_unique($query, $params = [])
    {
        $results = $this->query($query, $params);
        return reset($results) ?: null;
    }

    /**
     * Executes non-SELECT queries (INSERT, UPDATE, DELETE).
     */
    public function execute($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Retrieves all records from the table.
     */
    public function get_all()
    {
        return $this->query("SELECT * FROM " . $this->table);
    }

    /**
     * Retrieves a single record by primary key ID.
     */
    public function get_by_id($id)
    {
        return $this->query_unique("SELECT * FROM " . $this->table . " WHERE id = :id", ['id' => $id]);
    }

    /**
     * Inserts a new record into the table.
     */
    public function add($entity)
    {
        $entity = $this->only_columns($entity);
        $columns = implode(", ", array_keys($entity));
        $placeholders = ":" . implode(", :", array_keys($entity));

        $query = "INSERT INTO " . $this->table . " ({$columns}) VALUES ({$placeholders})";
        $this->execute($query, $entity);

        $entity['id'] = (int) $this->connection->lastInsertId();
        return $entity;
    }

    /**
     * Updates an existing record by ID.
     */
    public function update($id, $entity, $id_column = "id")
    {
        $entity = $this->only_columns($entity);
        $fields = "";
        foreach ($entity as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ", ");

        $entity[$id_column] = $id;
        $query = "UPDATE " . $this->table . " SET {$fields} WHERE {$id_column} = :{$id_column}";

        $this->execute($query, $entity);
        return $this->get_by_id($id);
    }

    /**
     * Deletes a record by ID.
     */
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        return $this->execute($query, ['id' => $id]);
    }
}