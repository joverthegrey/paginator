<?php

class paginator
{

    private $pageSize = 1;
    private $currentPage = 1;
    private $pageMethod = null;
    private $countMethod = null;
    private $numberOfPages = 0;
    private $numberOfEntries = 0;

    /**
     * paginator constructor.
     */
    public function __construct()
    {
        // nothing yet
    }

    /**
     * Set the size of the pages.
     *
     * @param $size
     * @return paginator
     * @throws \Exception
     */
    public function setPageSize($size): paginator
    {
        $this->pageSize = $size;

        // (re)calculate the number of pages
        $this->calculatePages();

        return $this;
    }

    /**
     * Set the method used to calculate the pages
     *
     * @param $f
     * @return paginator
     * @throws \Exception
     */
    public function setPageMethod($f): paginator
    {
        if (!is_callable($f))
            throw new \Exception('Not a valid method, please set it a method which returns the total size of the collection');
        $this->pageMethod = $f;

        // (re)calculate the number of pages
        $this->calculatePages();

        return $this;
    }

    /**
     * Set the method used to calculate the collection szie
     *
     * @param $f
     * @return paginator
     * @throws \Exception
     */
    public function setCountMethod($f): paginator
    {
        if (!is_callable($f))
            throw new \Exception('Not a valid method, please set it a method with offset and amount arguments');

        $this->countMethod = $f;

        return $this;
    }

    /**
     * Set the current page
     *
     * @param $page
     * @return bool
     */
    public function setCurrentPage($page): bool
    {
        if ($page > $this->numberOfPages || $page < 1) return false;
        $this->currentPage = $page;

        return true;
    }

    /**
     * Set currentpage to the next page
     *
     * @return bool
     */
    public function nextPage(): bool
    {
        if ($this->currentPage == $this->numberOfPages) return false;
        $this->currentPage++;

        return true;
    }


    /**
     * Set the currentpage to the previous one
     *
     * @return bool
     */
    public function prevPage(): bool
    {
        if ($this->currentPage == 1) return false;
        $this->currentPage--;

        return true;
    }

    /**
     * Return the number of pages
     *
     * @return int
     */
    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    /**
     * Get a slice of the collection using the pageMethod and returns it
     *
     * @return mixed
     * @throws \Exception
     */
    public function getPage()
    {
        if (is_null($this->pageMethod) || !is_callable($this->pageMethod))
            throw new \Exception('pageMethod is not set, or isn\'t a method');

        $offset = ($this->currentPage - 1) * $this->pageSize;
        $amount = $this->pageSize;

        $result = call_user_func($this->pageMethod, $offset, $amount);

        return $result;
    }

    /**
     * Returns if we are on the last page
     * @return bool
     */
    public function lastPage(): bool
    {
        return ($this->currentPage == $this->numberOfPages);
    }

    /**
     * Return the current page
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Calculate the number of pages.
     *
     * @throws \Exception
     */
    protected function calculatePages(): void
    {
        if (is_null($this->countMethod)) {
            throw new \Exception('No countMethod set, please set it first');
        }

        $totalEntries = call_user_func($this->countMethod);
        if (!is_int($totalEntries)) {
            throw new \Exception('Count method didn\'t returned an integer');
        }

        $this->numberOfEntries = $totalEntries;
        $this->numberOfPages = ceil($totalEntries / $this->pageSize);
    }
}