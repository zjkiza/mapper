<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Mapping;

use Doctrine\ORM\EntityManager;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Mapping\CollectionMapper;
use Zjk\DtoMapper\Mapping\DtoToEntityBatchItem;
use Zjk\DtoMapper\Mapping\EntityFactory;
use Zjk\DtoMapper\Mapping\IdentifierResolver;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Create\Dto\ExpertNewWithTransformerDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\WeakObject\Dto\ImageDto;
use Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto\UserResponseDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Image;
use Zjk\DtoMapper\Tests\Resources\App\Entities\User;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class MappingServicesTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private MapperInterface $mapper;

    private CollectionMapper $collectionMapper;

    private EntityFactory $entityFactory;

    private IdentifierResolver $identifierResolver;

    protected function setUp(): void
    {
        parent::setUp();

        /** @psalm-suppress PropertyTypeCoercion */
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->mapper = $this->getContainer()->get(MapperInterface::class);
        $this->collectionMapper = $this->getContainer()->get(CollectionMapper::class);
        $this->entityFactory = $this->getContainer()->get(EntityFactory::class);
        $this->identifierResolver = $this->getContainer()->get(IdentifierResolver::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->entityManager,
            $this->mapper,
            $this->collectionMapper,
            $this->entityFactory,
            $this->identifierResolver,
        );
    }

    public function testMapperDelegatesToServices(): void
    {
        $user = $this->entityManager->find(User::class, 'd6dc85b9-58e1-407b-97d4-53af658e1e90');

        $this->assertNotNull($user);

        $dto = $this->mapper->fromObjectEntityToDto($user, UserResponseDto::class);

        $this->assertInstanceOf(UserResponseDto::class, $dto);
    }

    public function testCollectionMapperEntityToDto(): void
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $dtos = $this->collectionMapper->entityToDto(
            $users,
            UserResponseDto::class
        );

        $this->assertCount(3, $dtos);
    }

    public function testCollectionMapperEntityToDtoLazy(): void
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $generator = $this->collectionMapper->entityToDtoLazy(
            $users,
            UserResponseDto::class
        );

        $count = 0;
        foreach ($generator as $dto) {
            $this->assertInstanceOf(UserResponseDto::class, $dto);
            ++$count;
        }

        $this->assertSame(3, $count);
    }

    public function testMapperEntityToDtoLazyDelegation(): void
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $generator = $this->mapper->fromCollectionEntityToDtoLazy(
            $users,
            UserResponseDto::class
        );

        foreach ($generator as $dto) {
            $this->assertInstanceOf(UserResponseDto::class, $dto);
        }
    }

    public function testMapperDtoToEntityLazyDelegation(): void
    {
        $dtos = [
            new ImageDto(
                id: 'a0b51430-c935-41ee-9877-af39637ab24a',
                name: 'Image 1'
            ),
        ];

        $generator = $this->mapper->fromCollectionDtoToEntityLazy($dtos, Image::class);

        foreach ($generator as $entity) {
            $this->assertInstanceOf(Image::class, $entity); // @phpstan-ignore-line
        }
    }

    public function testIdentifierResolverResolvesIdentifier(): void
    {
        $dto = new ExpertNewWithTransformerDto(
            name: 'Test',
            title: 'Doctor',
            id: 'c0b51430-c935-41ee-9877-af39637ab24a'
        );

        $identifier = $this->identifierResolver->resolve($dto);

        $this->assertNotNull($identifier->getValue());
    }

    public function testEntityFactoryCreatesEntity(): void
    {
        $dto = new ExpertNewWithTransformerDto(
            name: 'Test',
            title: 'Doctor',
            id: 'c0b51430-c935-41ee-9877-af39637ab24a'
        );

        $identifier = $this->identifierResolver->resolve($dto);

        $entity = $this->entityFactory->create($identifier, Expert::class);

        $this->assertInstanceOf(Expert::class, $entity);
    }

    public function testEntityFactoryBuildLookupMap(): void
    {
        $dto1 = new ImageDto(
            id: 'a0b51430-c935-41ee-9877-af39637ab24a',
            name: 'Image 1'
        );

        $dto2 = new ImageDto(
            id: 'a0b51430-c935-41ee-9877-af39637ab24b',
            name: 'Image 2'
        );

        $identifier1 = $this->identifierResolver->resolve($dto1);
        $identifier2 = $this->identifierResolver->resolve($dto2);

        $batchItems = [
            0 => new DtoToEntityBatchItem($dto1, $identifier1, Image::class),
            1 => new DtoToEntityBatchItem($dto2, $identifier2, Image::class),
        ];

        $lookupMap = $this->entityFactory->buildLookupMap(
            ['a0b51430-c935-41ee-9877-af39637ab24a', 'a0b51430-c935-41ee-9877-af39637ab24b'],
            $batchItems
        );

        $this->assertArrayHasKey(Image::class.':a0b51430-c935-41ee-9877-af39637ab24a', $lookupMap);
        $this->assertArrayHasKey(Image::class.':a0b51430-c935-41ee-9877-af39637ab24b', $lookupMap);
    }
}
