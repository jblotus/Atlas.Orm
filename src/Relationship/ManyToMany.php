<?php
namespace Atlas\Relationship;

use Atlas\Mapper\Mapper;
use Atlas\Mapper\MapperLocator;
use Atlas\Table\Row;
use Atlas\Table\RowSet;

class ManyToMany extends AbstractRelationship
{
    public function throughNativeCol($throughNativeCol)
    {
        $this->throughNativeCol = $throughNativeCol;
        return $this;
    }

    public function throughForeignCol($throughForeignCol)
    {
        $this->throughForeignCol = $throughForeignCol;
        return $this;
    }

    protected function fixThroughNativeCol()
    {
        if ($this->throughNativeCol) {
            return;
        }

        $nativeMapper = $this->mapperLocator->get($this->nativeMapperClass);
        $this->throughNativeCol = $nativeMapper->getTable()->getPrimary();
    }

    protected function fixThroughForeignCol()
    {
        if ($this->throughForeignCol) {
            return;
        }

        $foreignMapper = $this->mapperLocator->get($this->foreignMapperClass);
        $this->throughForeignCol = $foreignMapper->getTable()->getPrimary();
    }

    protected function fixForeignCol()
    {
        if ($this->foreignCol) {
            return;
        }

        $foreignMapper = $this->mapperLocator->get($this->foreignMapperClass);
        $this->foreignCol = $foreignMapper->getTable()->getPrimary();
    }

    public function fetchForRow(
        Row $row,
        array &$related,
        callable $custom = null
    ) {
        $this->fix();
        $throughRecordSet = $related[$this->throughName];
        $foreignVals = $this->getUniqueVals($throughRecordSet, $this->throughForeignCol);
        $foreign = $this->fetchForeignRecordSet($foreignVals, $custom);
        $related[$this->name] = $foreign;
    }

    public function fetchForRowSet(
        RowSet $rowSet,
        array &$relatedSet,
        callable $custom = null
    ) {
        $this->fix();

        $foreignColVals = [];
        foreach ($rowSet as $row) {
            $primaryVal = $row->getPrimaryVal();
            $throughRecordSet = $relatedSet[$primaryVal][$this->throughName];
            $foreignColVals = array_merge(
                $foreignColVals,
                $this->getUniqueVals($throughRecordSet, $this->throughForeignCol)
            );
        }
        $foreignColVals = array_unique($foreignColVals);

        $foreignRecordSet = $this->fetchForeignRecordSet($foreignColVals, $custom);

        foreach ($rowSet as $row) {
            $primaryVal = $row->getPrimaryVal();
            $throughRecordSet = $relatedSet[$primaryVal][$this->throughName];
            $vals = $this->getUniqueVals($throughRecordSet, $this->throughForeignCol);
            $relatedSet[$primaryVal][$this->name] = $foreignRecordSet->newRecordSetBy(
                $this->foreignCol,
                $vals
            );
        }
    }
}
