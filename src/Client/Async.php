<?php
declare(strict_types = 1);

namespace Innmind\Async\Socket\Client;

use Innmind\Socket\Client;
use Innmind\Async\Stream\{
    Readable\Async as Readable,
    Writable\Async as Writable,
};
use Innmind\Stream\Stream\{
    Position,
    Position\Mode,
};
use Innmind\Mantle\Suspend;
use Innmind\Immutable\{
    Maybe,
    Either,
    Str,
};

final class Async implements Client
{
    private Readable $readable;
    private Writable $writable;

    private function __construct(
        Readable $readable,
        Writable $writable,
    ) {
        $this->readable = $readable;
        $this->writable = $writable;
    }

    /**
     * @internal
     */
    public static function of(
        Client $client,
        Suspend $suspend,
    ): self {
        return new self(
            Readable::of($client, $suspend),
            Writable::of($client, $suspend),
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function resource()
    {
        return $this->readable->resource();
    }

    public function read(int $length = null): Maybe
    {
        return $this->readable->read($length);
    }

    public function readLine(): Maybe
    {
        return $this->readable->readLine();
    }

    /** @psalm-suppress InvalidReturnType */
    public function write(Str $data): Either
    {
        /**
         * @psalm-suppress InvalidReturnStatement
         * @psalm-suppress ArgumentTypeCoercion
         */
        return $this
            ->writable
            ->write($data)
            ->map(fn($writable) => new self($this->readable, $writable));
    }

    public function position(): Position
    {
        return $this->readable->position();
    }

    /** @psalm-suppress InvalidReturnType */
    public function seek(Position $position, Mode $mode = null): Either
    {
        /**
         * @psalm-suppress InvalidReturnStatement
         * @psalm-suppress ArgumentTypeCoercion
         */
        return $this
            ->readable
            ->seek($position, $mode)
            ->map(fn($readable) => new self($readable, $this->writable));
    }

    /** @psalm-suppress InvalidReturnType */
    public function rewind(): Either
    {
        /**
         * @psalm-suppress InvalidReturnStatement
         * @psalm-suppress ArgumentTypeCoercion
         */
        return $this
            ->readable
            ->rewind()
            ->map(fn($readable) => new self($readable, $this->writable));
    }

    /**
     * @psalm-mutation-free
     */
    public function end(): bool
    {
        return $this->readable->end();
    }

    /**
     * @psalm-mutation-free
     */
    public function size(): Maybe
    {
        return $this->readable->size();
    }

    public function close(): Either
    {
        return $this->readable->close();
    }

    /**
     * @psalm-mutation-free
     */
    public function closed(): bool
    {
        return $this->readable->closed();
    }

    public function toString(): Maybe
    {
        return $this->readable->toString();
    }
}
