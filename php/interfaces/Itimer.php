<?php
interface Itimer
 {
    public function getTimer() :DateTime;
    public function startTimer(): bool;
 }
?>