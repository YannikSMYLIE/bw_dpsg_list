<?php
namespace BoergenerWebdesign\BwDpsgList\Controller;

class Controller extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view): void {
        $this->assignRequestData();
        parent::initializeView($view);
    }

    /**
     * Übergibt die Request-Daten an den View.
     */
    private function assignRequestData() : void {
        $this -> settings = array_merge(
            $this -> settings ?? [],
            [
                'request' => $this -> request -> getArguments()
            ]
        );
        $this -> view -> assign('settings', $this -> settings);
    }
}