<?php

class FormBuilderTest extends PHPUnit_Framework_TestCase
{
    private $sxe;

    public function setUp()
    {
        $record = new Csv\Record();
        $record->addField(new Csv\TextareaField('description', 'Description'));
        $record->addField(new Csv\SelectField('gender', 'Gender', ['male', 'female']));
        $record->addField(new Csv\HiddenField('secret'));
        $record->addField(new Csv\CheckboxField('archived', 'Archived'));
        $record->addField(new Csv\DateField('due-date', 'Due Date'));

        $data = [
            'description' => '1<2',
            'gender' => 'male',
            'secret' => '0815',
            'archived' => true,
            'due-date' => '2015-12-12'
        ];

        $subject = new Csv\FormBuilder();
        $this->sxe = $subject->buildFormFor($record, $data);
    }

    public function testHas4Labels()
    {
        $this->assertCount(4, $this->sxe->xpath('/form/p/label'));
    }

    public function testSelectHas2Options()
    {
        $this->assertCount(2, $this->sxe->xpath('/form/p/select/option'));
    }

    public function testFieldsHaveCorrectNames()
    {
        $this->assertNotEmpty($this->sxe->xpath('/form/p/textarea[@name="csv_description"]'));
        $this->assertNotEmpty($this->sxe->xpath('/form/p/select[@name="csv_gender"]'));
        $this->assertNotEmpty($this->sxe->xpath('/form/p/input[@name="csv_secret"]'));
        $this->assertNotEmpty($this->sxe->xpath('/form/p/input[@name="csv_archived"]'));
        $this->assertNotEmpty($this->sxe->xpath('/form/p/input[@name="csv_due-date"]'));
    }

    public function testHiddenHasName()
    {
    }
}
