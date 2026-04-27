<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para gestionar consultas relacionadas con la entidad Curso */
class CursoRepository extends ServiceEntityRepository
{
    /* Constructor que vincula el repositorio con la entidad Curso */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Curso::class);
    }

    /* Método para obtener todos los cursos que están activos */
    public function buscarCursosActivos(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('c.estado = :estado')
            ->setParameter('estado', 'activo')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Método para obtener cursos activos filtrados por idioma del curso */
    public function buscarCursosActivosPorIdioma(string $idioma): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('c.estado = :estado')
            ->andWhere('c.idioma = :idioma')
            ->setParameter('estado', 'activo')
            ->setParameter('idioma', $idioma)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Buscar cursos activos con filtros opcionales */
    public function buscarCursosActivosFiltrados(?string $busqueda, ?string $nivel, ?string $modalidad, ?string $idioma = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('c.estado = :estado')
            ->setParameter('estado', 'activo');

        /* Filtro por idioma del curso */
        if ($idioma !== null && $idioma !== '') {
            $qb->andWhere('c.idioma = :idioma')
                ->setParameter('idioma', $idioma);
        }

        /* Filtro por texto en título, descripción o nombre del profesor */
        if ($busqueda !== null && $busqueda !== '') {
            $qb->andWhere('c.titulo LIKE :busqueda OR c.descripcion LIKE :busqueda OR p.nombre LIKE :busqueda')
                ->setParameter('busqueda', '%' . $busqueda . '%');
        }

        /* Filtro por nivel */
        if ($nivel !== null && $nivel !== '') {
            $qb->andWhere('c.nivel = :nivel')
                ->setParameter('nivel', $nivel);
        }

        /* Filtro por modalidad */
        if ($modalidad !== null && $modalidad !== '') {
            $qb->andWhere('c.modalidad = :modalidad')
                ->setParameter('modalidad', $modalidad);
        }

        return $qb
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Buscar cursos activos por texto */
    public function buscarPorTexto(string $texto): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('(c.titulo LIKE :texto OR c.descripcion LIKE :texto OR p.nombre LIKE :texto)')
            ->andWhere('c.estado = :estado')
            ->setParameter('texto', '%' . $texto . '%')
            ->setParameter('estado', 'activo')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Buscar cursos activos por texto y por idioma del curso */
    public function buscarPorTextoEIdioma(string $texto, string $idioma): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('(c.titulo LIKE :texto OR c.descripcion LIKE :texto OR p.nombre LIKE :texto)')
            ->andWhere('c.estado = :estado')
            ->andWhere('c.idioma = :idioma')
            ->setParameter('texto', '%' . $texto . '%')
            ->setParameter('estado', 'activo')
            ->setParameter('idioma', $idioma)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Obtener cursos de un profesor concreto */
    public function buscarCursosDeProfesor(User $profesor): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.profesor = :profesor')
            ->setParameter('profesor', $profesor)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Contar todos los cursos de un profesor */
    public function contarCursosDeProfesor(User $profesor): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.profesor = :profesor')
            ->setParameter('profesor', $profesor)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* Contar solo los cursos activos de un profesor */
    public function contarCursosActivosDeProfesor(User $profesor): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.profesor = :profesor')
            ->andWhere('c.estado = :estado')
            ->setParameter('profesor', $profesor)
            ->setParameter('estado', 'activo')
            ->getQuery()
            ->getSingleScalarResult();
    }
}