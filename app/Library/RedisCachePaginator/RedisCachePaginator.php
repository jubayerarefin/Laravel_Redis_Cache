<?php

namespace App\Library\RedisCachePaginator;

use App\Library\RedisCachePaginator\Exceptions\InvalidKeyException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Predis;

class RedisCachePaginator
{
    protected int $perPage = 20;
    protected ?int $currentPage;
    protected ?string $key;
    protected bool $asc = true;
    private Predis\Client $redis;

    public function __construct($connection, $perPage = 20)
    {
        $this->perPage = $perPage;
        $this->redis = $connection;
    }

    /**
     * Load a Redis sorted set into a length aware paginator.
     *
     * @see https://redis.io/topics/data-types-intro#redis-sorted-sets
     *
     * @param string $key
     * @param string $pageName
     * @param int|null $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(string $key, string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $this->key($key);
        $this->validateArguments();

        $page = $page ?? $this->resolveCurrentPage($pageName);

        $total = $this->loadTotalItems();

        return new LengthAwarePaginator(
            $this->results($page),
            $total,
            $this->perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

    /**
     * Load paginated results from Redis.
     *
     * @param int $page
     *
     * @return Collection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function results(int $page)
    {
        // Calculate start and end positions
        $start = ($page - 1) * $this->perPage;
        $end = ($page * $this->perPage) - 1;

        return $this->loadFromRedis($start, $end);
    }

    /**
     * Resolve current page from request or user-called method.
     *
     * @param string $pageName
     *
     * @return int
     */
    private function resolveCurrentPage(string $pageName): int
    {
        return $this->currentPage ?? Paginator::resolveCurrentPage($pageName);
    }

    /**
     * Load results from Redis as a Collection.
     *
     * @param int $start
     * @param int $end
     *
     * @return Collection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function loadFromRedis(int $start, int $end)
    {
        $results = $this->asc
            ? $this->redis->zrange($this->key, $start, $end)
            : $this->redis->zrevrange($this->key, $start, $end);

        return $this->newCollection($results)->map(function ($item) {
            return json_decode($item);
        });
    }

    /**
     * Get count of items in a sorted set.
     *
     * @return int
     */
    protected function loadTotalItems(): int
    {
        return $this->redis->zcard($this->key);
    }

    /**
     * Create a new Collection.
     *
     * @param array|Collection $items
     *
     * @return Collection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function newCollection($items)
    {
        return new Collection($items);
    }

    private function validateArguments(): void
    {
        if (!isset($this->key)) {
            throw new InvalidKeyException();
        }
    }

    /**
     * @param int $perPage
     *
     * @return RedisCachePaginator
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return RedisCachePaginator
     */
    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param int $currentPage
     *
     * @return RedisCachePaginator
     */
    public function page(int $currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return RedisCachePaginator
     */
    public function sortAsc(): self
    {
        $this->asc = true;

        return $this;
    }

    /**
     * @return RedisCachePaginator
     */
    public function sortDesc(): self
    {
        $this->asc = false;

        return $this;
    }
}
