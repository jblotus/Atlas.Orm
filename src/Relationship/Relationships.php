<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Atlas\Orm\Relationship;

use Atlas\Orm\Exception;
use Atlas\Orm\Mapper\MapperLocator;
use Atlas\Orm\Mapper\RecordInterface;

/**
 *
 * __________
 *
 * @package atlas/orm
 *
 */
class Relationships
{
    protected $mapperLocator;

    protected $defs = [];

    protected $fields = [];

    public function __construct(MapperLocator $mapperLocator)
    {
        $this->mapperLocator = $mapperLocator;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function oneToOne(
        $name,
        $nativeMapperClass,
        $foreignMapperClass
    ) {
        return $this->set(
            $name,
            $nativeMapperClass,
            OneToOne::CLASS,
            $foreignMapperClass
        );
    }

    public function oneToMany(
        $name,
        $nativeMapperClass,
        $foreignMapperClass
    ) {
        return $this->set(
            $name,
            $nativeMapperClass,
            OneToMany::CLASS,
            $foreignMapperClass
        );
    }

    public function manyToOne(
        $name,
        $nativeMapperClass,
        $foreignMapperClass
    ) {
        return $this->set(
            $name,
            $nativeMapperClass,
            ManyToOne::CLASS,
            $foreignMapperClass
        );
    }

    public function manyToMany(
        $name,
        $nativeMapperClass,
        $foreignMapperClass,
        $throughName
    ) {
        return $this->set(
            $name,
            $nativeMapperClass,
            ManyToMany::CLASS,
            $foreignMapperClass,
            $throughName
        );
    }

    protected function set(
        $name,
        $nativeMapperClass,
        $relationClass,
        $foreignMapperClass,
        $throughName = null
    ) {
        if (! class_exists($foreignMapperClass)) {
            throw Exception::classDoesNotExist($foreignMapperClass);
        }

        if ($throughName && ! isset($this->defs[$throughName])) {
            throw Exception::relationDoesNotExist($throughName);
        }

        $this->fields[$name] = null;
        $this->defs[$name] = $this->newRelation(
            $nativeMapperClass,
            $name,
            $relationClass,
            $foreignMapperClass,
            $throughName
        );

        return $this->defs[$name];
    }

    protected function newRelation(
        $nativeMapperClass,
        $name,
        $relationClass,
        $foreignMapperClass,
        $throughName = null
    ) {
        return new $relationClass(
            $this->mapperLocator,
            $nativeMapperClass,
            $name,
            $foreignMapperClass,
            $throughName
        );
    }

    protected function fixWith($spec)
    {
        $with = [];
        foreach ($spec as $key => $val) {
            if (is_int($key)) {
                $with[$val] = null;
            } else {
                $with[$key] = $val;
            }
        }
        return $with;
    }

    public function stitchIntoRecords(
        array $records,
        array $with = []
    ) {
        foreach ($this->fixWith($with) as $name => $custom) {
            $this->defs[$name]->stitchIntoRecords(
                $records,
                $custom
            );
        }
    }
}
