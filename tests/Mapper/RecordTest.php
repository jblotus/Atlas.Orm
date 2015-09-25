<?php
namespace Atlas\Mapper;

use Atlas\Table\Row;

class RecordTest extends \PHPUnit_Framework_TestCase
{
    protected $row;
    protected $related;
    protected $record;

    protected function setUp()
    {
        $this->row = new Row(
            [
                'id' => '1',
                'foo' => 'bar',
                'baz' => 'dib',
            ],
            'id'
        );

        $this->related = [
            'zim' => 'gir',
            'irk' => 'doom',
        ];

        $this->record = new Record($this->row, $this->related);
    }

    public function testGetRow()
    {
        $this->assertSame($this->row, $this->record->getRow());
    }

    public function testGetRelated()
    {
        $this->assertSame($this->related, $this->record->getRelated());
    }

    public function test__get()
    {
        // row
        $this->assertSame('bar', $this->record->foo);

        // related
        $this->assertSame('gir', $this->record->zim);

        // missing
        $this->setExpectedException(
            'Atlas\Exception',
            'Atlas\Mapper\Record->noSuchField does not exist'
        );
        $this->record->noSuchField;
    }

    public function test__set()
    {
        // row
        $this->record->foo = 'barbar';
        $this->assertSame('barbar', $this->record->foo);
        $this->assertSame('barbar', $this->row->foo);

        // related
        $this->record->zim = 'girgir';
        $this->assertSame('girgir', $this->record->zim);

        // missing
        $this->setExpectedException(
            'Atlas\Exception',
            'Atlas\Mapper\Record->noSuchField does not exist'
        );
        $this->record->noSuchField = 'missing';
    }

    public function test__isset()
    {
        // row
        $this->assertTrue(isset($this->record->foo));

        // related
        $this->assertTrue(isset($this->record->zim));

        // missing
        $this->assertFalse(isset($this->record->noSuchField));
    }

    public function test__unset()
    {
        // row
        unset($this->record->foo);
        $this->assertNull($this->record->foo);
        $this->assertNull($this->row->foo);

        // related
        unset($this->record->zim);
        $this->assertNull($this->record->zim);

        // missing
        $this->setExpectedException(
            'Atlas\Exception',
            'Atlas\Mapper\Record->noSuchField does not exist'
        );
        unset($this->record->noSuchField);
    }
}