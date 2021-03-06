<?php

namespace JPI;

use DateTime;

trait MeTrait {

    protected $dateStarted = null;
    protected $yearStarted = null;

    public function getDateStarted(): DateTime {
        if (is_null($this->dateStarted)) {
            $this->dateStarted = new DateTime(self::START_DATE);
        }

        return $this->dateStarted;
    }

    public function getYearStarted(): string {
        if (is_null($this->yearStarted)) {
            $date = $this->getDateStarted();
            $this->yearStarted = $date->format("Y");
        }

        return $this->yearStarted;
    }

}
