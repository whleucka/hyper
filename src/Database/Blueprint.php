<?php

namespace Nebula\Database;

class Blueprint
{
  private array $definitions = [];

  public function last(): int
  {
    return count($this->definitions) - 1;
  }

  public function getDefinition(int $index)
  {
    return key_exists($index, $this->definitions)
      ? $this->definitions[$index]
      : null;
  }

  public function lastDefinition()
  {
    return $this->getDefinition($this->last());
  }

  public function appendDefinition(int $index, string $string): Blueprint
  {
    $definition = $this->getDefinition($index);
    $this->definitions[$index] = "$definition $string";
    return $this;
  }

  public function insertDefinition(int $index, int $pos, string $string): Blueprint
  {
    if (key_exists($index, $this->definitions)) {
      $definition = $this->definitions[$index];
      if ($pos <= strlen($definition)) {
        $this->definitions[$index] = substr_replace(
          $definition,
          $string,
          $pos,
          0
        );
      }
    }
    return $this;
  }

  /**
   * Add a default value to column
   * @param string $separator Definition separator
   */
  public function getDefinitions(string $separator = ", "): string
  {
    return implode($separator, $this->definitions);
  }

  /**
   * Add a default value to column
   * @param string $value Default value
   * @return Blueprint
   */
  public function default(string $value): Blueprint
  {
    $this->appendDefinition($this->last(), "DEFAULT $value");
    return $this;
  }

  /**
   * Specify a column as unique index
   * @param string $attribute Unique attribute
   * @return Blueprint
   */
  public function unique(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("UNIQUE KEY (%s)", $attribute);
    return $this;
  }

  /**
   * Specify a reference attribute for foreign key constraint
   * @param string $attribute Attribute of foreign key constraint
   * @return Blueprint
   */
  public function references(string $table_name, string $attribute): Blueprint
  {
    $this->appendDefinition(
      $this->last(),
      "REFERENCES $table_name($attribute)"
    );
    return $this;
  }

  /**
   * Specify a ON DELETE properites of the constraint
   * @param string $action Action to take on delete
   * @return Blueprint
   */
  public function onDelete(string $action): Blueprint
  {
    $this->appendDefinition($this->last(), "ON DELETE $action");
    return $this;
  }
  /**
   * @return Blueprint
   */
  public function autoIncrement(): Blueprint
  {
    $this->appendDefinition($this->last(), "AUTO_INCREMENT");
    return $this;
  }

  /**
   * Specify a ON UPDATE properites of the constraint
   * @param string $action Action to take on delete
   * @return Blueprint
   */
  public function onUpdate(string $action): Blueprint
  {
    $this->appendDefinition($this->last(), "ON UPDATE $action");
    return $this;
  }

  /**
   * Specify an index (compound or composite)
   * @param array $attributes Indexed attribute
   * @return Blueprint
   * @param array<int,mixed> $attribute
   */
  public function index(array $attribute): Blueprint
  {
    $this->definitions[] = sprintf("INDEX (%s)", $attribute);
    return $this;
  }

  /**
   * Allow NULL values to be inserted into the column
   * @return Blueprint
   */
  public function nullable(): Blueprint
  {
    $index = count($this->definitions) - 1;
    if (key_exists($index, $this->definitions)) {
      $definition = $this->definitions[$index];
      $this->definitions[$index] = str_replace(
        " NOT NULL",
        "",
        $definition
      );
    }
    return $this;
  }

  /**
   * Specify a character set for the column
   * @param string $character_set Character set
   * @return Blueprint
   */
  public function charset(string $character_set = "utf8mb4"): Blueprint
  {
    return $this;
  }

  /**
   * Specify a collation for the column
   * @param string $collation Collation
   * @return Blueprint
   */
  public function collation(string $collation = "utf8mb4_unicode_ci"): Blueprint
  {
    return $this;
  }

  /**
   * Creates an auto-incrementing UNSIGNED BIGINT (primary key) column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function bigIncrements(string $attribute): Blueprint
  {
    $this->unsignedBigInteger($attribute)->autoIncrement();
    return $this;
  }

  /**
   * Creates a BIGINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function bigInteger(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s BIGINT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a BLOB column
   * @param string $attribute Name of attribute
   * @param int $length Length of varchar
   * @return Blueprint
   */
  public function binary(string $attribute, int $length): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s BINARY(%s) NOT NULL",
      $attribute,
      $length
    );
    return $this;
  }

  /**
   * Creates a BOOLEAN column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function boolean(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s BOOLEAN NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a CHAR column
   * @param string $attribute Name of attribute
   * @param int $length Length of char
   * @return Blueprint
   */
  public function char(string $attribute, int $length): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s CHAR(%s) NOT NULL",
      $attribute,
      $length
    );
    return $this;
  }

  /**
   * Creates a DATETIME column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @return Blueprint
   */
  public function dateTime(string $attribute, int $precision = 0): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s DATETIME(%s) NOT NULL",
      $attribute,
      $precision
    );
    return $this;
  }

  /**
   * Creates a DATE column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function date(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s DATE NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a DECIMAL column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @param int $scale Decimal digits
   * @return Blueprint
   */
  public function decimal(string $attribute, $precision = 8, $scale = 2): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s DECIMAL(%s,%s)",
      $attribute,
      $precision,
      $scale
    );
    return $this;
  }

  /**
   * Creates a DOUBLE column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @param int $scale Decimal digits
   * @return Blueprint
   */
  public function double(string $attribute, $precision = 8, $scale = 2): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s DOUBLE(%s,%s)",
      $attribute,
      $precision,
      $scale
    );
    return $this;
  }

  /**
   * Creates an ENUM column
   * @param string $attribute Name of attribute
   * @param array $values Valid values of enum
   * @return Blueprint
   */
  public function enum(string $attribute, array $values): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s ENUM(%s)",
      $attribute,
      "'" . implode("','", $values) . "'"
    );
    return $this;
  }

  /**
   * Creates a FLOAT column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @param int $scale Decimal digits
   * @return Blueprint
   */
  public function float(string $attribute, int $precision = 8, int $scale = 2): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s FLOAT(%s,%s)",
      $attribute,
      $precision,
      $scale
    );
    return $this;
  }

  /**
   * Creates an primary key column
   * @param string $attribute Name of attribute(s)
   * @return Blueprint
   */
  public function primaryKey(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("PRIMARY KEY (%s)", $attribute);
    return $this;
  }

  /**
   * Creates an foreign key column
   * @param string $attribute Name of attribute(s)
   * @return Blueprint
   */
  public function foreignKey(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("FOREIGN KEY (%s)", $attribute);
    return $this;
  }

  /**
   * Creates a GEOMETRY column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function geometry(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s GEOMETRY", $attribute);
    return $this;
  }

  /**
   * Creates an auto-incrementing UNSIGNED BIGINT (primary key) column
   * Alias of bigIncrements
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function id(string $attribute = "id"): Blueprint
  {
    $this->bigIncrements($attribute);
    return $this;
  }

  /**
   * Creates an auto-incrementing UNSIGNED INTEGER column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function increments(string $attribute): Blueprint
  {
    $this->unsignedInteger($attribute)->autoIncrement();
    return $this;
  }

  /**
   * Add UNSIGNED attribute to column
   * @return Blueprint
   */
  public function unsigned(): Blueprint
  {
    $pos = strpos($this->lastDefinition(), "NOT NULL");
    if ($pos) {
      $this->insertDefinition($this->last(), $pos, "UNSIGNED ");
    }
    return $this;
  }

  /**
   * Creates an INTEGER column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function integer(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s INTEGER NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a JSON column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function json(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s JSON NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a JSONB column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function jsonb(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s JSONB NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates an auto-incrementing UNSIGNED MEDIUMINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function mediumIncrements(string $attribute): Blueprint
  {
    $this->unsignedMediumInteger($attribute)->autoIncrement();
    return $this;
  }

  /**
   * Creates a MEDIUMINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function mediumInteger(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s MEDIUMINT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a MEDIUMTEXT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function mediumText(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s MEDIUMTEXT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a MULTILINESTRING column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function multiLineString(string $attribute): Blueprint
  {
    return $this;
  }

  /**
   * Creates a MULTIPOINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function multipoint(string $attribute): Blueprint
  {
    return $this;
  }

  /**
   * Creates a MULTIPOLYGON column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function multiPolygon(string $attribute): Blueprint
  {
    return $this;
  }

  /**
   * Creates a POINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function point(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s POINT", $attribute);
    return $this;
  }

  /**
   * Creates a POLYGON column
   * @param string $attribute Name of attribute
   * @param array $values values of polygon
   * @return Blueprint
   */
  public function polygon(string $attribute, array $values): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s POLYGON(%s)",
      $attribute,
      implode(",", $values)
    );
    return $this;
  }

  /**
   * Creates an auto-incrementing UNSIGNED SMALLINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function smallIncrements(string $attribute): Blueprint
  {
    $this->unsignedSmallInteger($attribute)->autoIncrement();
    return $this;
  }

  /**
   * Creates a SMALLINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function smallInt(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s SMALLINT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a VARCHAR column
   * @param string $attribute Name of attribute
   * @param int $length Length of varchar
   * @return Blueprint
   */
  public function varchar(string $attribute, int $length = 255): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s VARCHAR(%s) NOT NULL",
      $attribute,
      $length
    );
    return $this;
  }

  /**
   * Creates a TEXT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function text(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s TEXT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a TIME column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @return Blueprint
   */
  public function time(string $attribute, int $precision): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s TIME(%s) NOT NULL",
      $attribute,
      $precision
    );
    return $this;
  }

  /**
   * Creates a TIMESTAMP column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @return Blueprint
   */
  public function timestamp(string $attribute, int $precision = 0): Blueprint
  {
    $this->definitions[] = sprintf(
      "%s TIMESTAMP(%s) NOT NULL",
      $attribute,
      $precision
    );
    return $this;
  }

  /**
   * Creates created_at and updated_at columns
   * @param int $precision Total digits
   * @return Blueprint
   */
  public function timestamps(int $precision = 0): Blueprint
  {
    $this->dateTime("created_at")->default("CURRENT_TIMESTAMP");
    $this->timestamp("updated_at")
      ->onUpdate("CURRENT_TIMESTAMP")
      ->default("'0000-00-00 00:00:00'");
    return $this;
  }

  /**
   * Creates an auto-incrementing UNSIGNED TINYINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function tinyIncrements(string $attribute): Blueprint
  {
    $this->unsignedTinyInteger($attribute)->autoIncrement();
    return $this;
  }

  /**
   * Creates a TINYINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function tinyInteger(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s TINYINT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates a TINYTEXT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function tinyText(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s TINYTEXT NOT NULL", $attribute);
    return $this;
  }

  /**
   * Creates an UNSIGNED BIGINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function unsignedBigInteger(string $attribute): Blueprint
  {
    $this->bigInteger($attribute)->unsigned();
    return $this;
  }

  /**
   * Creates an UNSIGNED DECIMAL column
   * @param string $attribute Name of attribute
   * @param int $precision Total digits
   * @param int $scale Decimal digits
   * @return Blueprint
   */
  public function unsignedDecimal(
    string $attribute,
    int $precision,
    int $scale
  ): Blueprint {
    return $this;
  }

  /**
   * Creates an UNSIGNED INTEGER column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function unsignedInteger(string $attribute): Blueprint
  {
    $this->integer($attribute)->unsigned();
    return $this;
  }

  /**
   * Creates an UNSIGNED MEDIUMINT column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function unsignedMediumInteger(string $attribute): Blueprint
  {
    $this->mediumInteger($attribute)->unsigned();
    return $this;
  }

  /**
   * Creates an UNSIGNED SMALLINT
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function unsignedSmallInteger(string $attribute): Blueprint
  {
    $this->smallInt($attribute)->unsigned();
    return $this;
  }

  /**
   * Creates an UNSIGNED TINYINT
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function unsignedTinyInteger(string $attribute): Blueprint
  {
    $this->tinyInteger($attribute)->unsigned();
    return $this;
  }

  /**
   * Creates a UUID column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function uuid(string $attribute): Blueprint
  {
    $this->char($attribute, 36);
    return $this;
  }

  /**
   * Creates a YEAR column
   * @param string $attribute Name of attribute
   * @return Blueprint
   */
  public function year(string $attribute): Blueprint
  {
    $this->definitions[] = sprintf("%s YEAR NOT NULL", $attribute);
    return $this;
  }
}
