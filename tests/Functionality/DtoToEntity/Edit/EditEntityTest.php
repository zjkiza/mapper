<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Edit;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Exception\TypeError;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Edit\Dto\ExpertEditDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Edit\Dto\ExpertEditWithSetterDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class EditEntityTest extends KernelTestCase
{
    private MapperInterface $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        /* @phpstan-ignore-next-line */
        $this->mapper = $this->getContainer()->get(MapperInterface::class);
    }

    public function testEditEntityWithTheSamePropertyName(): void
    {
        $name = 'UPDATE';
        $title = 'Doctor 1';
        $id = 'c0b51430-c935-41ee-9877-af39637ab24a';

        $expertDto = new ExpertEditDto(
            name: $name,
            id: $id,
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
        $this->assertSame($id, $entityExpert->getIdentifier());
    }

    public function testEditEntityWithTheDifferentPropertyNameViaPropertySetter(): void
    {
        $name = 'Update';
        $title = 'Doctor 1';
        $id = 'c0b51430-c935-41ee-9877-af39637ab24a';

        $expertDto = new ExpertEditWithSetterDto(
            foo: $name,
            id: $id,
        );

        /** @var Expert $entityExpert */
        $entityExpert = $this->mapper->fromObjectDtoToEntity($expertDto, Expert::class);

        $this->assertSame($name, $entityExpert->getName());
        $this->assertSame($title, $entityExpert->getTitle());
        $this->assertSame($id, $entityExpert->getIdentifier());
    }

    public function testExpectExceptionEditEntityWithNotExistId(): void
    {
        $expertDto = new ExpertEditDto(
            name: 'Update',
            id: 'bef74a38-a6b8-4240-9878-7cd93dad7351',
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
}
