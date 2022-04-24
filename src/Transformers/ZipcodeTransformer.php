<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\ZipcodeColumn;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;

/**
 * Clean the zip code.
 */
class ZipcodeTransformer implements TransformerInterface
{
    protected Collection $columns;

    protected int $maxlength = 5;

    public function __construct()
    {
        $this->columns = new Collection();
    }

    /**
     * @param mixed $column name|index
     * @param string|null $pad padleft|padright
     * @param int|null $maxlength
     *
     * @return ZipcodeTransformer
     */
    public function tranformColumn($column, ?string $pad = null, ?int $maxlength = null): self
    {
        $this->columns->push(new ZipcodeColumn(
            $column,
            $maxlength ?? $this->maxlength,
            (is_null($pad) ? $pad : $this->setOption($pad)),
        ));

        return $this;
    }

    /**
     * @param string $option padleft|padright
     *
     * @return int
     *
     * @throws PipesInvalidArgumentException
     */
    protected function setOption(string $option): int
    {
        if (strtolower($option) === 'padleft') {
            return STR_PAD_LEFT;
        }

        if (strtolower($option) === 'padright') {
            return STR_PAD_RIGHT;
        }

        throw new PipesInvalidArgumentException('Invalid zipcode option!');
    }

    public function __invoke(Frame $frame): Frame
    {
        $frame->getData()->transform(function ($item, $key) {
            foreach ($this->columns as $column) {
                if ($column->column === $key) {
                    return $this->transformZipcode(
                        $item,
                        $column->option,
                        $column->maxlength
                    );
                }
            }

            return $item;
        });

        return $frame;
    }

    private function transformZipcode(string $zipcode, ?int $type, int $maxlength): string
    {
        $transformed = (string) \preg_replace('/\D+/', '', $zipcode);

        $zipLength = \strlen($transformed);

        if ($zipLength > $maxlength) {
            return \substr($transformed, 0, $maxlength);
        }

        if (! \is_null($type) && $zipLength < $maxlength) {
            return \str_pad($transformed, $maxlength, '0', $type);
        }

        return $transformed;
    }
}
