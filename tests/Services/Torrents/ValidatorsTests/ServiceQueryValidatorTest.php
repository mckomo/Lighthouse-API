<?php

namespace Lighthouse\tests\Services\Torrents\ValidatorsTests;

use Lighthouse\Services\Torrents\Validation\Validators\ServiceQuery as Validator;
use Lighthouse\Tests\Support\EntitySampler;

class ServiceQueryValidatorTest extends \PHPUnit_Framework_TestCase
{
    const ABOVE_LIMIT = 101;

    /**
     * @var Validator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new Validator();
    }

    public function testSucceedsWithValidQuery()
    {
        $validQuery = $this->getValidQuery();

        $result = $this->validator->isValid($validQuery);

        $this->assertTrue($result);
    }

    public function testSucceedsWithoutCategory()
    {
        $validQuery = $this->getValidQuery();
        $validQuery->category = null;

        $result = $this->validator->isValid($validQuery);

        $this->assertTrue($result);
    }

    public function testSucceedsWithoutLimit()
    {
        $validQuery = $this->getValidQuery();
        $validQuery->size = null;

        $result = $this->validator->isValid($validQuery);

        $this->assertTrue($result);
    }

    public function testFailsWithNull()
    {
        $result = $this->validator->isValid(null);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getNullAndEmptyString
     */
    public function testFailsWithEmptyPhrase($invalidPharse)
    {
        $brokenQuery = $this->getValidQuery();
        $brokenQuery->phrase = $invalidPharse;

        $result = $this->validator->isValid($brokenQuery);

        $this->assertFalse($result);
    }

    public function testFailsWithUnsupportedCategory()
    {
        $unsuportedCategory = 'Unsupported category';
        $brokenQuery = $this->getValidQuery();
        $brokenQuery->category = $unsuportedCategory;

        $result = $this->validator->isValid($brokenQuery);

        $this->assertFalse($result);
    }

    public function testFailsWithInvalidSortField()
    {
        $invalidSortField = 'NonsortableField';
        $brokenQuery = $this->getValidQuery();
        $brokenQuery->sortBy = $invalidSortField;

        $result = $this->validator->isValid($brokenQuery);

        $this->assertFalse($result);
    }

    public function testFailsWithInvalidSortOrder()
    {
        $invalidSortOrder = 'random';
        $brokenQuery = $this->getValidQuery();
        $brokenQuery->sortOrder = $invalidSortOrder;

        $result = $this->validator->isValid($brokenQuery);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getZeroAndAboveLimit
     */
    public function testFailsWithOutOfRangeLimit($invalidLimit)
    {
        $brokenQuery = $this->getValidQuery();
        $brokenQuery->size = $invalidLimit;

        $result = $this->validator->isValid($brokenQuery);

        $this->assertFalse($result);
    }

    public function testReturnsValidatorError()
    {
        $brokenQuery = $this->getValidQuery();
        $brokenQuery->phrase = '';

        $this->validator->isValid($brokenQuery, $errors);
        $errorCount = count($errors);

        $this->assertEquals(1, $errorCount);
    }

    public function testAppendsValidatorErrors()
    {
        $emptyPharse = '';
        $aboveLimit = static::ABOVE_LIMIT;

        $brokenQuery = $this->getValidQuery();
        $brokenQuery->phrase = $emptyPharse;
        $brokenQuery->size = $aboveLimit;

        $this->validator->isValid($brokenQuery, $errors);
        $errorCount = count($errors);

        $this->assertEquals(2, $errorCount);
    }

    /**
     * @return array
     */
    public function getNullAndEmptyString()
    {
        return [[null], ['']];
    }

    /**
     * @return array
     */
    public function getZeroAndAboveLimit()
    {
        return [[0], [static::ABOVE_LIMIT]];
    }

    private function getValidQuery()
    {
        return EntitySampler::sampleQuery();
    }
}
