<?php

namespace App\Repository;

use App\Entity\Comentario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// Repositorio para gestionar consultas relacionadas con la entidad Comentario
class ComentarioRepository extends ServiceEntityRepository
{
    // Constructor donde se inyecta el ManagerRegistry y se indica la entidad asociada
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comentario::class);
    }

    // Método para obtener todos los comentarios de un curso concreto
    public function buscarPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('c') // Creamos el QueryBuilder con alias 'c' que es un comentario
            ->join('c.usuario', 'u') // Hacemos join con la entidad usuario 'u' que es un usuario
            ->addSelect('u') // Añadimos el usuario a la selección para evitar consultas extra
            ->where('c.curso = :curso') // Filtramos por el curso indicado
            ->setParameter('curso', $cursoId) // Asignamos el parámetro del curso
            ->orderBy('c.destacado', 'DESC') // Ordenamos primero por destacados (los destacados arriba)
            ->addOrderBy('c.id', 'DESC') // Luego por ID descendente (los más recientes primero)
            ->getQuery() // Generamos la consulta
            ->getResult(); // Ejecutamos y devolvemos los resultados
    }

    // Método para obtener solo los comentarios destacados de un curso
    public function buscarDestacados(int $cursoId): array
    {
        return $this->createQueryBuilder('c') 
            ->join('c.usuario', 'u')
            ->addSelect('u') 
            ->where('c.curso = :curso')
            ->andWhere('c.destacado = 1') // Solo comentarios marcados como destacados
            ->setParameter('curso', $cursoId) 
            ->orderBy('c.id', 'DESC') 
            ->getQuery()
            ->getResult();
    }
}