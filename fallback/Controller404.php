<?php
namespace SNTools\Framework\Fallback;
use SNTools\Framework\BackController;

/**
 * Description of Controller404
 *
 * @author Samy NAAMANI
 */
class Controller404 extends BackController {
    /**
     * Called by Controller404::execute(), inherited from BackController.
     */
    public function execute404() {
        $this->app->response['error'] = $this->app->request->getParameter('error');
        $this->app->response['title'] = 'SN Framework : an error occured';
        $this->app->response->addHeader('Content-type: text/html; charset=utf-8');
        $this->app->response->send($this->view);
    }
}
