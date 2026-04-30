import json
import random
import sys

# Número máximo de cursos recomendados que se van a devolver
LIMITE_RECOMENDADOS = 3

def construir_recomendados(cursos, ids_cursos_clicados):
    """
    Construye una lista de cursos recomendados.
    La idea es dar prioridad a cursos que el usuario todavía no ha visitado.
    Si no hay suficientes cursos nuevos, se completan las recomendaciones
    con cursos que ya fueron clicados.
    """

    # Se convierten los ids clicados a números enteros.
    # Se comprueba con isdigit() para evitar errores si llega algún dato raro.
    ids_clicados = {int(x) for x in ids_cursos_clicados if str(x).isdigit()}

    # Cursos que el usuario todavía no ha visitado.
    no_clicados = [
        curso for curso in cursos
        if int(curso.get("id", 0)) not in ids_clicados
    ]

    # Cursos que el usuario ya ha visitado anteriormente
    clicados = [
        curso for curso in cursos
        if int(curso.get("id", 0)) in ids_clicados
    ]

    # Mezcla los cursos no clicados para que las recomendaciones varíen
    if no_clicados:
        random.shuffle(no_clicados)

    # También mezcla los cursos clicados por si hay que usarlos como relleno
    if clicados:
        random.shuffle(clicados)

    # Primero se devuelven cursos no clicados
    # Si faltan cursos para llegar al límite, se añaden cursos ya clicados
    return (no_clicados + clicados)[:LIMITE_RECOMENDADOS]

def main():
    """
    Función principal del script.
    Este script recibe como argumento la ruta de un archivo JSON.
    Ese JSON contiene los cursos disponibles y los ids de cursos clicados.
    """

    # Si no se recibe la ruta del archivo JSON, devolvemos una lista vacía.
    if len(sys.argv) < 2:
        print(json.dumps({
            "destacados": []
        }, ensure_ascii=False))
        return

    # Ruta del archivo JSON recibido desde Symfony
    ruta_json = sys.argv[1]

    # Abrimos el archivo JSON y cargamos su contenido en un diccionario de Python
    with open(ruta_json, "r", encoding="utf-8") as archivo:
        payload = json.load(archivo)

    # Se obtienen los cursos enviados desde Symfony
    # Si no existen, se usa una lista vacía para evitar errores
    cursos = payload.get("cursos", [])

    # Se obtienen los ids de cursos que el usuario ya ha visitado
    ids_cursos_clicados = payload.get("ids_cursos_clicados", [])

    # Se genera la lista final de cursos recomendados
    destacados = construir_recomendados(cursos, ids_cursos_clicados)

    # Por último, devuelve el resultado en formato JSON para que Symfony pueda leerlo
    print(json.dumps({
        "destacados": destacados
    }, ensure_ascii=False))

# Esto hace que main() solo se ejecute cuando se lanza este archivo directamente
if __name__ == "__main__":
    main()
