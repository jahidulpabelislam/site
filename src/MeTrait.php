<?php

namespace JPI;

use DateTime;

trait MeTrait {

    protected $dateStarted = null;

    public function getDateStarted(): DateTime {
        if (is_null($this->dateStarted)) {
            $this->dateStarted = new DateTime(self::START_DATE);
        }

        return $this->dateStarted;
    }
}
