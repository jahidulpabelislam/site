<?php

namespace JPI;

use DateTime;

trait MeTrait {

    protected $dateStarted;
    protected $yearStarted;

    public function getDateStarted(): DateTime {
        if (!$this->dateStarted) {
            $this->dateStarted = new DateTime(self::START_DATE);
        }

        return $this->dateStarted;
    }

    public function getYearStarted(): string {
        if (!$this->yearStarted) {
            $dateStartedDate = $this->getDateStarted();
            $this->yearStarted = $dateStartedDate->format("Y");
        }

        return $this->yearStarted;
    }

}
