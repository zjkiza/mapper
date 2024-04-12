<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\Attribute;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Zjk\DtoMapper\Attribute\Collection;
use Zjk\DtoMapper\Attribute\Dto;
use Zjk\DtoMapper\Attribute\Entity;
use Zjk\DtoMapper\Attribute\Getter;
use Zjk\DtoMapper\Attribute\Setter;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Exception\InvalidClassException;
use Zjk\DtoMapper\Exception\InvalidMethodException;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class AttributeExceptionTest extends KernelTestCase
{
    private ManagerRegistry $doctrine;

    /**
     * @var ObjectRepository<Media>
     */
    private ObjectRepository $mediaRepository;

    private MapperInterface $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        /* @phpstan-ignore-next-line */
        $this->doctrine = $this->getContainer()->get(ManagerRegistry::class);
        /* @phpstan-ignore-next-line */
        $this->mediaRepository = $this->doctrine->getRepository(Media::class);
        /* @phpstan-ignore-next-line */
        $this->mapper = $this->getContainer()->get(MapperInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->doctrine,
            $this->mediaRepository,
            $this->mapper,
        );
    }

    public function testExpectExceptionWhenInAttributeCollectionThePropertyClassNameWithThisNameIsNotExist(): void
    {
        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage(\sprintf('Class "%s" does not exist', Lorem::class)); // @phpstan-ignore-line

        $dto = new #[Entity(Media::class)] class {
            #[Collection(Lorem::class)] // @phpstan-ignore-line
            public ?array $expert = null; // @phpstan-ignore-line
        };

        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $this->mapper->fromObjectEntityToDto($media, $dto::class);
    }

    public function testExpectExceptionWhenInAttributeDtoThePropertyClassNameWithThisNameIsNotExist(): void
    {
        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage(\sprintf('Class "%s" does not exist', Lorem::class)); // @phpstan-ignore-line

        $dto = new #[Entity(Media::class)] class {
            #[Dto(Lorem::class)] // @phpstan-ignore-line
            public ?array $expert = null; // @phpstan-ignore-line
        };

        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $this->mapper->fromObjectEntityToDto($media, $dto::class);
    }

    public function testExpectExceptionWhenInAttributeGetterThePropertyMethodNameWithThisNameIsNotExist(): void
    {
        $this->expectException(InvalidMethodException::class);
        $this->expectExceptionMessage(\sprintf('In class "%s" method "%s" not exist. Check the Dto configuration from Attribute Getter.', Media::class, 'lorem'));

        $dto = new #[Entity(Media::class)] class {
            #[Getter('lorem')]
            public ?string $name = null;
        };

        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $this->mapper->fromObjectEntityToDto($media, $dto::class);
    }

    public function testExpectExceptionWhenInAttributeSetterThePropertyMethodNameWithThisNameIsNotExist(): void
    {
        $this->expectException(InvalidMethodException::class);
        $this->expectExceptionMessage(\sprintf('In class "%s" method "%s" not exist. Check the Dto configuration from Attribute Setter.', Media::class, 'lorem'));

        $dto = new #[Entity(Media::class)] class {
            #[Setter('lorem')]
            public ?string $name = null;
        };

        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $this->mapper->fromObjectEntityToDto($media, $dto::class);
    }

    public function testExpectExceptionWhenInAttributeEntityThePropertyNameClassWithThisNameIsNotExist(): void
    {
        $this->expectException(InvalidClassException::class);
        $this->expectExceptionMessage(\sprintf('In Attribute Entity in the property name="%s", class with this name is not exist.', Lorem::class)); // @phpstan-ignore-line

        $dto = new #[Entity(Lorem::class)] class { // @phpstan-ignore-line
            public ?string $name = null;
        };

        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $this->mapper->fromObjectEntityToDto($media, $dto::class);
    }
}
