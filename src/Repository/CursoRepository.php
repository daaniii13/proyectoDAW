<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// Repositorio para gestionar consultas relacionadas con la entidad Curso
class CursoRepository extends ServiceEntityRepository
{
    // Constructor que vincula el repositorio con la entidad Curso
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Curso::class);
    }

    // Método para obtener todos los cursos que están activos
    public function buscarCursosActivos(): array
    {
        return $this->createQueryBuilder('c') // Creamos el QueryBuilder con alias 'c' que es el curso
            ->leftJoin('c.profesor', 'p') // Hacemos un LEFT JOIN con el profesor del curso
            ->addSelect('p') // Añadimos el profesor a la selección para evitar consultas adicionales
            ->where('c.estado = :estado') // Filtramos solo los cursos activos
            ->setParameter('estado', 'activo') // Asignamos el valor del parámetro
            ->orderBy('c.id', 'DESC') // Ordenamos por ID descendente (más recientes primero)
            ->getQuery() // Generamos la consulta
            ->getResult(); // Ejecutamos y devolvemos los resultados
    }

    // Buscar cursos activos con filtros opcionales
    public function buscarCursosActivosFiltrados(?string $busqueda, ?string $nivel, ?string $modalidad): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('c.estado = :estado')
            ->setParameter('estado', 'activo');

        // Filtro por texto en título, descripción o nombre del profesor
        if ($busqueda !== null && $busqueda !== '') {
            $qb->andWhere('c.titulo LIKE :busqueda OR c.descripcion LIKE :busqueda OR p.nombre LIKE :busqueda')
                ->setParameter('busqueda', '%' . $busqueda . '%');
        }

        // Filtro por nivel
        if ($nivel !== null && $nivel !== '') {
            $qb->andWhere('c.nivel = :nivel')
                ->setParameter('nivel', $nivel);
        }

        // Filtro por modalidad
        if ($modalidad !== null && $modalidad !== '') {
            $qb->andWhere('c.modalidad = :modalidad')
                ->setParameter('modalidad', $modalidad);
        }

        return $qb
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Buscar cursos activos por texto
    public function buscarPorTexto(string $texto): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.profesor', 'p')
            ->addSelect('p')
            ->where('(c.titulo LIKE :texto OR c.descripcion LIKE :texto OR p.nombre LIKE :texto)')
            ->andWhere('c.estado = :estado') // Solo activos
            ->setParameter('texto', '%' . $texto . '%')
            ->setParameter('estado', 'activo')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Obtener cursos de un profesor concreto
    public function buscarCursosDeProfesor(User $profesor): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.profesor = :profesor')
            ->setParameter('profesor', $profesor)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Contar todos los cursos de un profesor
    public function contarCursosDeProfesor(User $profesor): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)') // Conteo de cursos
            ->where('c.profesor = :profesor')
            ->setParameter('profesor', $profesor)
            ->getQuery()
            ->getSingleScalarResult(); // Devuelve un único valor
    }

    // Contar solo los cursos activos de un profesor
    public function contarCursosActivosDeProfesor(User $profesor): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.profesor = :profesor')
            ->andWhere('c.estado = :estado') // Filtro adicional por estado
            ->setParameter('profesor', $profesor)
            ->setParameter('estado', 'activo')
            ->getQuery()
            ->getSingleScalarResult();
    }
}