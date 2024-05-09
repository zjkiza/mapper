<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\OnlyRelation;

use Doctrine\ORM\EntityManager;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\OnlyRelation\Dto\ExpertDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\OnlyRelation\Dto\MediaDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class RelationOnlyRelationTest extends KernelTestCase
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

    public function testAddEntityToRelation(): void
    {
        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, '60b16643-d5e0-468a-8823-499fcf07684b');

        /**
         * @phpstan-ignore-next-line
         */
        $initialStateExpertIds = \array_map(static fn (Expert $expert): string => $expert->getIdentifier(), $media->getExpert()->toArray());

        $this->assertEquals(
            [
                'c0b51430-c935-41ee-9877-af39637ab24a',
            ],
            $initialStateExpertIds
        );

        $mediaDto = new MediaDto(
            id: '60b16643-d5e0-468a-8823-499fcf07684b',
            expert: [
                new ExpertDto('c0b51430-c935-41ee-9877-af39637ab24a'),
                new ExpertDto('c0b51430-c935-41ee-9877-af39637ab24b'),
            ],
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, Media::class);

        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, '60b16643-d5e0-468a-8823-499fcf07684b');

        /**
         * @phpstan-ignore-next-line
         */
        $addedExpertIds = \array_map(static fn (Expert $expert): string => $expert->getIdentifier(), $media->getExpert()->toArray());

        $this->assertEquals(
            [
                'c0b51430-c935-41ee-9877-af39637ab24a',
                'c0b51430-c935-41ee-9877-af39637ab24b',
            ],
            $addedExpertIds
        );
    }

    public function testRemoveEntityFromRelation(): void
    {
        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, '60b16643-d5e0-468a-8823-499fcf07684b');

        /**
         * @phpstan-ignore-next-line
         */
        $initialStateExpertIds = \array_map(static fn (Expert $expert): string => $expert->getIdentifier(), $media->getExpert()->toArray());

        $this->assertEquals(
            [
                'c0b51430-c935-41ee-9877-af39637ab24a',
            ],
            $initialStateExpertIds
        );

        $mediaDto = new MediaDto(
            id: '60b16643-d5e0-468a-8823-499fcf07684b',
            expert: [],
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, Media::class);

        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, '60b16643-d5e0-468a-8823-499fcf07684b');

        /**
         * @phpstan-ignore-next-line
         */
        $addedExpertIds = \array_map(static fn (Expert $expert): string => $expert->getIdentifier(), $media->getExpert()->toArray());

        $this->assertEquals(
            [],
            $addedExpertIds
        );
    }
}
