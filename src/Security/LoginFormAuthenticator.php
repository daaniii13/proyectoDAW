<?php

namespace App\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

// Autenticador del login con formulario
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator {
    use TargetPathTrait;

    // Nombre de la ruta del login.
    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator) {
    }

    // Prepara los datos del formulario para que Symfony intente iniciar sesión
    public function authenticate(Request $request): Passport {
        $email = (string) $request->request->get('_username', '');

        // Guarda el último email escrito para reutilizarlo si el login falla
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Valida el token CSRF del formulario.
        $badges = [
            new CsrfTokenBadge('authenticate', (string) $request->request->get('_csrf_token')),
        ];

        // Si el usuario marca "recordarme", se añade esa opción
        if ($request->request->getBoolean('_remember_me')) {
            $badges[] = new RememberMeBadge();
        }

        // Symfony usa el email, la contraseña y los badges para autenticar
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials((string) $request->request->get('_password')),
            $badges
        );
    }

    // Decide a dónde se redirige al usuario cuando el login es correcto
    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?RedirectResponse {
        // Si intentaba entrar a una página protegida, vuelve a esa página
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Si no había página previa, va al inicio
        return new RedirectResponse($this->urlGenerator->generate('app_inicio'));
    }

    // Devuelve la URL del formulario de login
    protected function getLoginUrl(Request $request): string {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
