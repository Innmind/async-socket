<?php
declare(strict_types = 1);

namespace Innmind\Async\Socket\Server;

use Innmind\Socket\Server;
use Innmind\Stream\Stream\{
    Position,
    Position\Mode,
};
use Innmind\Mantle\Suspend;
use Innmind\Immutable\{
    Maybe,
    Either,
};

final class Async implements Server
{
    private Server $synchronous;
    private Suspend $suspend;

    private function __construct(
        Server $synchronous,
        Suspend $suspend,
    ) {
        $this->synchronous = $synchronous;
        $this->suspend = $suspend;
    }

    public static function of(
        Server $synchronous,
        Suspend $suspend,
    ): self {
        return new self($synchronous, $suspend);
    }

    /** @psalm-suppress InvalidReturnType */
    public function accept(): Maybe
    {
        /** @psalm-suppress InvalidReturnStatement */
        return $this
            ->synchronous
            ->accept()
            ->map(fn($connection) => Connection\Async::of(
                $connection,
                $this->suspend,
            ));
    }

    /**
     * @psalm-mutation-free
     */
    public function resource()
    {
        return $this->synchronous->resource();
    }

    public function read(int $length = null): Maybe
    {
        // as described in the Server interface this method does nothing so no
        // need to suspend
        return $this->synchronous->read($length);
    }

    public function readLine(): Maybe
    {
        // as described in the Server interface this method does nothing so no
        // need to suspend
        return $this->synchronous->readLine();
    }

    public function position(): Position
    {
        return $this->synchronous->position();
    }

    public function seek(Position $position, Mode $mode = null): Either
    {
        // as described in the Server interface this method does nothing so no
        // need to wrap the return
        return $this->synchronous->seek($position, $mode);
    }

    public function rewind(): Either
    {
        // as described in the Server interface this method does nothing so no
        // need to wrap the return
        return $this->synchronous->rewind();
    }

    /**
     * @psalm-mutation-free
     */
    public function end(): bool
    {
        return $this->synchronous->end();
    }

    /**
     * @psalm-mutation-free
     */
    public function size(): Maybe
    {
        return $this->synchronous->size();
    }

    public function close(): Either
    {
        return $this->synchronous->close();
    }

    /**
     * @psalm-mutation-free
     */
    public function closed(): bool
    {
        return $this->synchronous->closed();
    }

    public function toString(): Maybe
    {
        // as described in the Server interface this method does nothing so no
        // need to suspend
        return $this->synchronous->toString();
    }
}
