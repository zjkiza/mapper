<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\Embedded;

use Doctrine\ORM\EntityManager;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\Embedded\Dto\MediaDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\Embedded\Dto\UserDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class EmbeddedTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private MapperInterface $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->mapper = $this->getContainer()->get(MapperInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->entityManager,
            $this->mapper,
        );
    }

    public function testEmbeddedClassEdit(): void
    {
        $mediaId = '60b16643-d5e0-468a-8823-499fcf07684b';
        $userId = '87101abb-4e71-427a-a433-5ea7c253e56f';
        $userName = 'Update';

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);
        $user = $media->getUser();

        $this->assertSame($userId, $user->getIdentifier());
        $this->assertSame('User 2', $user->getName());

        $mediaDto = new MediaDto(
            $mediaId,
            new UserDto(
                id: $userId,
                name: $userName
            )
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, $media);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);
        $user = $media->getUser();

        $this->assertSame($userId, $user->getIdentifier());
        $this->assertSame($userName, $user->getName());
    }

    public function testEmbeddedClassChange(): void
    {
        $mediaId = '60b16643-d5e0-468a-8823-499fcf07684b';
        $userId = '87101abb-4e71-427a-a433-5ea7c253e56f';

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);
        $user = $media->getUser();

        $this->assertSame($userId, $user->getIdentifier());
        $this->assertSame('User 2', $user->getName());

        $changeUserWithId = 'd6dc85b9-58e1-407b-97d4-53af658e1e90';
        $changeUserWithName = 'User 1';

        $mediaDto = new MediaDto(
            $mediaId,
            new UserDto(
                id: $changeUserWithId,
                name: $changeUserWithName
            )
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, $media);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);
        $user = $media->getUser();

        $this->assertSame($changeUserWithId, $user->getIdentifier());
        $this->assertSame($changeUserWithName, $user->getName());
    }

    public function testEmbeddedClassChangeWithNew(): void
    {
        $mediaId = '60b16643-d5e0-468a-8823-499fcf07684b';
        $userId = '87101abb-4e71-427a-a433-5ea7c253e56f';

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);
        $user = $media->getUser();

        $this->assertSame($userId, $user->getIdentifier());
        $this->assertSame('User 2', $user->getName());

        $changeUserWithId = 'f7b34617-7e74-4511-8331-578cbe1dcd70';
        $changeUserWithName = 'User new';

        $mediaDto = new MediaDto(
            $mediaId,
            new UserDto(
                id: $changeUserWithId,
                name: $changeUserWithName
            )
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, $media);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);
        $user = $media->getUser();

        $this->assertSame($changeUserWithId, $user->getIdentifier());
        $this->assertSame($changeUserWithName, $user->getName());
    }
}
