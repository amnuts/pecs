<?php

namespace Hamidrezaniazi\Pecs\Tests\Unit\Generator;

use Hamidrezaniazi\Pecs\Generator\Field;
use Hamidrezaniazi\Pecs\Generator\Property;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hamidrezaniazi\Pecs\Generator\Field
 */
class FieldTest extends TestCase
{
    public function testItCanParseFieldSchema(): void
    {
        $link = 'https://www.elastic.co/guide/en/elasticsearch/reference/current/field-caps.html';
        $class = 'FieldCaps';
        $key = 'field_caps';
        $first = [
            'fields' => [
                'types' => ['array'],
                'cast' => 'array',
            ]
        ];
        $second = [
            'indices' => [
                'types' => ['array'],
                'cast' => 'array',
            ]
        ];

        $field = Field::parse([
            'document_link' => $link,
            'class' => $class,
            'key' => $key,
            'properties' => [
                ...$first,
                ...$second,
            ],
        ]);

        $this->assertSame($link, $field->documentLink);
        $this->assertSame($class, $field->class);
        $this->assertSame($key, $field->key);
        $this->assertEquals([
            Property::parse($first['fields'], 'fields'),
            Property::parse($second['indices'], 'indices'),
        ], $field->properties);
    }

    public function testItCanParseFieldSchemaWhenKeyIsNotPresent(): void
    {
        $link = 'https://www.elastic.co/guide/en/elasticsearch/reference/current/field-caps.html';
        $class = 'FieldCaps';

        $field = Field::parse([
            'document_link' => $link,
            'class' => $class,
            'properties' => [],
        ]);

        $this->assertSame($link, $field->documentLink);
        $this->assertSame($class, $field->class);
        $this->assertNull($field->key);
    }
}
