
<?php

namespace Core\Database;

abstract class Migration
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    abstract public function up(): void;
    abstract public function down(): void;

    protected function createTable(string $name, callable $callback): void
    {
        $schema = new Schema($name);
        $callback($schema);
        
        $sql = $schema->toSql();
        $this->db->query($sql);
    }

    protected function dropTable(string $name): void
    {
        $this->db->query("DROP TABLE IF EXISTS `{$name}`");
    }

    protected function addColumn(string $table, string $column, string $type): void
    {
        $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$type}");
    }

    protected function dropColumn(string $table, string $column): void
    {
        $this->db->query("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
    }
}

class Schema
{
    private string $tableName;
    private array $columns = [];
    private array $indexes = [];
    private string $engine = 'InnoDB';
    private string $charset = 'utf8mb4';

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function id(string $name = 'id'): self
    {
        $this->columns[] = "`{$name}` INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "`{$name}` VARCHAR({$length})";
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "`{$name}` TEXT";
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "`{$name}` INT";
        return $this;
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): self
    {
        $this->columns[] = "`{$name}` DECIMAL({$precision},{$scale})";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "`{$name}` BOOLEAN DEFAULT FALSE";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function index(array $columns): self
    {
        $columnList = implode('`, `', $columns);
        $this->indexes[] = "INDEX (`{$columnList}`)";
        return $this;
    }

    public function unique(array $columns): self
    {
        $columnList = implode('`, `', $columns);
        $this->indexes[] = "UNIQUE INDEX (`{$columnList}`)";
        return $this;
    }

    public function toSql(): string
    {
        $columns = implode(",\n    ", $this->columns);
        $indexes = !empty($this->indexes) ? ",\n    " . implode(",\n    ", $this->indexes) : '';
        
        return "CREATE TABLE `{$this->tableName}` (\n    {$columns}{$indexes}\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset}";
    }
}
