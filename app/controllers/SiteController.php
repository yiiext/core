<?php


class SiteController extends CController
{
    public $layout = '//layouts/website';

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionFaq()
    {
        $this->render('faq');
    }

    public function actionContact()
    {
        $this->render('contact');
    }
}