<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Transformers;

use Illuminate\Support\Collection;
use function is_null;
use Jwhulette\Pipes\Contracts\TransformerInterface;
use Jwhulette\Pipes\DataTransferObjects\ZipcodeDto;
use Jwhulette\Pipes\Exceptions\PipesInvalidArgumentException;
use Jwhulette\Pipes\Frame;
use function preg_replace;
use function str_pad;
use function strlen;
use function substr;

/**
 * Clean the zip code.
 */
final class ZipcodeTransformer implements TransformerInterface
{
    /** @var Collection<int,ZipcodeDto> */
    protected Collection $columns;

    protected int $maxlength = 5;

    public function __construct()
    {
        $this->columns = new Collection();
    }

    public function __invoke(Frame $frame): Frame
    {
        /* @phpstan-ignore-next-line */
        $frame->getData()->transform(function (string|int $item, string|int $key) {
            foreach ($this->columns as $column) {
                if ($column->column === $key) {
                    return $this->transformZipcode(
                        (string) $item,
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
        $transformed = (string) preg_replace('/\D+/', '', $zipcode);

        $zipLength = strlen($transformed);

        if ($zipLength > $maxlength) {
            return substr($transformed, 0, $maxlength);
        }

        if (! is_null($type) && $zipLength < $maxlength) {
            return str_pad($transformed, $maxlength, '0', $type);
        }

        return $transformed;
    }

    /**
     * Set the columns and transformation.
     *
     * @param  string|null  $pad     [Options: padleft, padright]
     * @param  int|null  $maxlength  [Default: 5]
     */
    public function tranformColumn(int|string $column, ?string $pad = null, ?int $maxlength = null): self
    {
        $this->columns->push(new ZipcodeDto(
            $column,
            $maxlength ?? $this->maxlength,
            (is_null($pad) ? $pad : $this->setOption($pad)),
        ));

        return $this;
    }

    /**
     * @param  string  $option  padleft|padright
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
}
