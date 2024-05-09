<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create;

use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Exception\NotExistAttribute;
use Zjk\DtoMapper\Exception\TypeError;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithIgnoreDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithoutEntityDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithoutIdentifierDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithTransformerWithoutNewEntityDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithTransformerDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithoutNewEntityDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithSetterDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class CreateNewEntityTest extends KernelTestCase
{
    private MapperInterface $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->getContainer()->get(MapperInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->mapper,
        );
    }

    public function testCreateNewEntityDefault(): void
    {
        $name = 'Input name';
        $title = 'Input title';

        $expertDto = new ExpertNewDto(
            name: $name,
            title: $title
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
    }

    public function testCreateNewEntityWithAttributeIgnore(): void
    {
        $name = 'Input name';
        $title = 'Input title';

        $expertDto = new ExpertNewWithIgnoreDto(
            name: $name,
            title: $title,
            lorem: 'Lorem ipsum'
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
    }

    public function testCreateNewEntityWithSetterForProperty(): void
    {
        $name = 'Input name';
        $title = 'Input title';

        $expertDto = new ExpertNewWithSetterDto(
            isNotName: $name,
            title: $title
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
    }

    public function testCreateNewEntityWithAttributeTransformerWhenDefineIdForEntity(): void
    {
        $name = 'Input name';
        $title = 'Input title';
        $id = '51ca1e01-a791-4277-afa2-cc2bed7c6939';

        $expertDto = new ExpertNewWithTransformerDto(
            name: $name,
            title: $title,
            id: $id
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
        $this->assertSame($id, $entityExpert->getIdentifier());
    }

    public function testCreateNewEntityWithoutAttributeNewEntity(): void
    {
        $name = 'Input name';
        $title = 'Input title';

        $expertDto = new ExpertNewWithoutNewEntityDto(
            name: $name,
            title: $title
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
        $this->assertIsString($entityExpert->getIdentifier());
    }

    public function testCreateNewEntityWithoutAttributeNewEntityWithAttributeTransformerWhenDefineIdForEntity(): void
    {
        $name = 'Input name';
        $title = 'Input title';
        $id = '51ca1e01-a791-4277-afa2-cc2bed7c6939';

        $expertDto = new ExpertNewWithTransformerWithoutNewEntityDto(
            name: $name,
            title: $title,
            id: $id
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
        $this->assertSame($id, $entityExpert->getIdentifier());
    }

    public function testExpectExceptionCreateNewEntityWithoutAttributeNewAndDefineIdWithoutTransformerEntity(): void
    {
        $name = 'Input name';
        $title = 'Input title';
        $id = '51ca1e01-a791-4277-afa2-cc2bed7c6939';

        $expertDto = new ExpertNewWithoutNewEntityDto(
            name: $name,
            title: $title,
            id: $id
        );

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            \sprintf(
                'A new entity cannot be created. Id argument in dto "%s" must by same type with entity. Use the transformer on the ID property to convert it to a type like in entity "%s".',
                $expertDto::class,
                Expert::class
            )
        );

        $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);
    }

    public function testExpectExceptionIfIdentifierIsNotDefinedWhenCreateNewEntity(): void
    {
        $name = 'Input name';
        $title = 'Input title';

        $expertDto = new ExpertNewWithoutIdentifierDto(
            name: $name,
            title: $title
        );

        $this->expectException(NotExistAttribute::class);
        $this->expectExceptionMessage(\sprintf('IdentifierStrategy attribute is must be define (example: #[Identifier]) on property ID in object %s', ExpertNewWithoutIdentifierDto::class));

        $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);
    }

    public function testExpectExceptionIfEntityIsNotDefinedWhenCreateNewEntity(): void
    {
        $name = 'Input name';
        $title = 'Input title';

        $expertDto = new ExpertNewWithoutEntityDto(
            name: $name,
            title: $title
        );

        $this->expectException(NotExistAttribute::class);
        $this->expectExceptionMessage(\sprintf('Entity attribute is must be define on dto class "%s"', ExpertNewWithoutEntityDto::class));

        $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);
    }
}
