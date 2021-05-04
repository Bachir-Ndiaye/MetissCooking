<?php

/**
 * Created by MetisCooking Team.
 */

namespace App\Controller;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    /**
     * @var Environment
     */
    protected Environment $twig;
    public $errors = [];
    public $success = [];

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => !APP_DEV, // @phpstan-ignore-line
                'debug' => APP_DEV,
            ]
        );
        $this->twig->addExtension(new DebugExtension());
    }

    /**
     *  Destroy the current user session.
     */
    public function destroySession()
    {
        session_destroy();
    }

    /**
     * Custom the render of pages by giving them in param the array $_SESSION
     */
    public function customRender(string $template, array $params): string
    {
        if (isset($_SESSION['current_user'])) {
            $params['current_user'] = $_SESSION['current_user'];
        }
        if (isset($_SESSION['command'])) {
            $params['command'][] = $_SESSION['command'];
        }
        if (isset($_SESSION['command-status'])) {
            $params['command-status'] = $_SESSION['command-status'];
        } elseif (!isset($_SESSION['command-status'])) {
            $_SESSION['command-status'] = null;
        }
        return $this->twig->render($template, $params);
    }

    /**
     *  Check if a variable in an array is empty of not.
     */
    public function isEmpty(array $datas): bool
    {
        foreach ($datas as $var) {
            if (empty($var)) {
                return false;
            }
        }
        return true;
    }
}
