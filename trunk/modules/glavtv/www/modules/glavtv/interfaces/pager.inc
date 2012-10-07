<?php
#############################################################################
# Paging information supplied to returned result.                           #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

class Pager {
    protected $current;
    protected $perPage;
    protected $pages;
    protected $total;

    public function __construct($current, $perPage, $pages, $total = null) {
        $this->current = $current;
        $this->perPage = $perPage;
        $this->pages   = $pages;
        $this->total   = $total;
        if (null == $this->total) {
            $this->recalcTotal();
        } else if (null == $this->pages || -1 == $this->pages) {
            $this->recalcPages();
        }
    }

    public function getCurrent() {
        return $this->current;
    }
    public function getPerPage() {
        return $this->perPage;
    }
    public function getPages() {
        return $this->pages;
    }
    public function getTotal() {
        return $this->total;
    }

    public function isFirstPage() {
        return $this->current == 1;
    }
    public function isLastPage() {
        return $this->current == $this->pages;
    }

    public function recalcPages() {
        $this->pages = (int) (($this->total - 1) / $this->perPage + 1);
    }
    public function recalcTotal($thisPageCount = null) {
        $this->total = $this->perPage * $this->pages;
        if (isset($thisPageCount) && $this->isLastPage()) {
            if ($this->pages > 0) {
                $this->total -= $this->perPage;
            }
            $this->total += $thisPageCount;
        }
    }

    public function __toString() {
        if ($this->total <= $this->perPage) {
            return '';
        }
        $start = ($this->current - 1) * $this->perPage + 1;
        $stop  = min($this->total, $start + $this->perPage - 1);
        return "$start - $stop / " . $this->total;
    }
}
?>
