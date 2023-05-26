<?php

namespace Hamidrezaniazi\Pecs;

use Hamidrezaniazi\Pecs\Fields\AbstractEcsField;
use Hamidrezaniazi\Pecs\Fields\Base;
use Hamidrezaniazi\Pecs\Fields\Log;
use Hamidrezaniazi\Pecs\Monolog\LogRecord;
use Illuminate\Support\Collection;

/** @extends Collection<int, AbstractEcsField> */
class EcsFieldsCollection extends Collection
{
    protected function getArrayableItems(mixed $items): array
    {
        $items = parent::getArrayableItems($items);

        return array_filter($items, fn ($item) => $item instanceof AbstractEcsField && $item->rootable);
    }

    public function loadInitialFields(LogRecord $records): self
    {
        $this
            ->prepend(
                new Base(
                    timestamp: $records->datetime,
                    message: $records->message,
                ),
            )
            ->prepend(
                new Log(
                    level: $records->level,
                    logger: $records->channel,
                ),
            );

        return $this;
    }

    public function loadWrappers(): self
    {
        $this
            ->reduce(function (?EcsFieldsCollection $carry, AbstractEcsField $item) {
                return is_null($carry) ? $item->wrapper() : $carry->merge($item->wrapper());
            })
            ->each(function (AbstractEcsField $item) {
                $this->push($item);
            });

        return $this;
    }

    public function render(): AbstractEcsField
    {
        return $this->reverse()->reduce(function (?AbstractEcsField $carry, AbstractEcsField $item) {
            return is_null($carry) ? $item : $carry->append($item);
        });
    }
}
