<?php

namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

// Autenticador para iniciar sesión con Google
class GoogleAuthenticator extends OAuth2Authenticator {
    use TargetPathTrait;

    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    // Solo se activa en la ruta de respuesta de Google
    public function supports(Request $request): ?bool {
        return $request->attributes->get('_route') === 'app_google_check';
    }

    // Autentica al usuario usando los datos recibidos desde Google
    public function authenticate(Request $request): SelfValidatingPassport {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($client, $accessToken) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                // Datos básicos del usuario de Google
                $email = $googleUser->getEmail();
                $nombre = $googleUser->getName() ?: $email;

                // Busca si el usuario ya existe en la base de datos
                $usuario = $this->entityManager
                    ->getRepository(User::class)
                    ->findOneBy(['email' => $email]);

                // Si existe, inicia sesión con ese usuario
                if ($usuario instanceof User) {
                    return $usuario;
                }

                // Si no existe, crea un usuario nuevo
                $usuario = new User();
                $usuario->setEmail($email);
                $usuario->setNombre($nombre);
                $usuario->setRoles(['ROLE_ESTUDIANTE']);
                $usuario->setActivo(true);

                // Se genera una contraseña aleatoria porque entra con Google
                $passwordAleatoria = bin2hex(random_bytes(16));
                $usuario->setPassword(
                    $this->passwordHasher->hashPassword($usuario, $passwordAleatoria)
                );

                // Guarda el nuevo usuario en la base de datos
                $this->entityManager->persist($usuario);
                $this->entityManager->flush();

                return $usuario;
            }),
            [
                // Activa la opción de recordar sesión
                new RememberMeBadge(),
            ]
        );
    }

    // Redirige cuando el login con Google es correcto
    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response {
        // Si venía de una página protegida, vuelve a esa página
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Si no, va al inicio
        return new RedirectResponse($this->urlGenerator->generate('app_inicio'));
    }

    // Si falla el login con Google, vuelve al login normal
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
