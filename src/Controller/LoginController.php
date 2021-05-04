<?php

namespace App\Controller;

use App\Model\LoginManager;

use function Amp\Iterator\filter;

class LoginController extends AbstractController
{
    public function destroy()
    {
        $this->destroySession();
        $success = 'Vous êtes bien deconnecté';

        return $this->twig->render('Home/index.html.twig', [
            'session_destroy' => $success
        ]);
    }

    public function login(): string
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $data = array_map('trim', $_POST);
            $email = htmlentities($data['email']);
            $password = htmlentities($data['password']);
            if (empty($_POST['email'])) {
                $this->errors = "Veuillez indiquer votre email .";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors = "Adresse Email non valide";
            }
            if (empty($_POST['password'])) {
                $this->errors = "Veuillez indiquer votre mot de passe.";
            }

            if (empty($this->errors)) {
                $loginManager = new LoginManager();

                // $user = $verif
                $user = $loginManager->verifLog($email, $password);
                if ($user === -1) {
                    $this->errors = " Identifiants incorrects ! Veuillez-vous inscrire";
                } else {
                    $this->success = "Vous êtes connecté";

                    $_SESSION['current_user'] = $user;
                    header('Location : home/index');

                        // Default User
                        return $this->customRender('Home/index.html.twig', [
                            'success' => $this->success
                        ]);
                }
            }

            return $this->twig->render('Login/form.html.twig', [
                'errors' => $this->errors
            ]);
        }

        return $this->twig->render('Login/form.html.twig', [
            'errors' => $this->errors
        ]);
    }
}
