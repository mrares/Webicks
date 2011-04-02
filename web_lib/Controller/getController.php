<?php
namespace Controller;

use Webicks\Controller\Front;

use Webicks\Document;

class GetController extends \Webicks\ControllerAbstract {
    public function init() {

    }

    public function getDataFromRedis() {

    }

    public function denyAction() {
        die('sorry, you are not allowed here');
    }

    public function indexAction() {
        $router = Front::getInstance()->getRouter();
        if( $content = Document::fetch($router->getDestination())) {
            header("Content-type: ".$content->getType());
            echo $content->getContent();
            exit;
        } else {
            $this->forward('notFound');
        }
    }

    public function notFound() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
        <title>Upload info</title>
        </head>
        <body>
        It sucks to be here...
        <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="publish" value="1" />
        <input type="text" name="dest" id="dest" />
        <select name="MIME">
        <option value="text/html">HTML</option>
        <option value="image/png">PNG Image</option>
        <option value="image/jpeg">JPEG Image</option>
        <option value="application/javascript">Javascript</option>
        <option value="text/css">CSS File</option>
        <option value="text/plain">Plain text</option>
        </select>
        <textarea rows="25" cols="80" name="content"></textarea>
        <input type="file" name="file" id="file" />
        <input type="submit" name="submit" value="send!">
        </form>
        </body>
        </html>
        <?php
    }
}