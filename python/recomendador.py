import json
import random
import sys

LIMITE_RECOMENDADOS = 3


def construir_recomendados(cursos, ids_cursos_clicados):
    ids_clicados = {int(x) for x in ids_cursos_clicados if str(x).isdigit()}

    no_clicados = [
        curso for curso in cursos
        if int(curso.get("id", 0)) not in ids_clicados
    ]

    clicados = [
        curso for curso in cursos
        if int(curso.get("id", 0)) in ids_clicados
    ]

    if no_clicados:
        random.shuffle(no_clicados)

    if clicados:
        random.shuffle(clicados)

    return (no_clicados + clicados)[:LIMITE_RECOMENDADOS]


def main():
    if len(sys.argv) < 2:
        print(json.dumps({
            "destacados": []
        }, ensure_ascii=False))
        return

    ruta_json = sys.argv[1]

    with open(ruta_json, "r", encoding="utf-8") as archivo:
        payload = json.load(archivo)

    cursos = payload.get("cursos", [])
    ids_cursos_clicados = payload.get("ids_cursos_clicados", [])

    destacados = construir_recomendados(cursos, ids_cursos_clicados)

    print(json.dumps({
        "destacados": destacados
    }, ensure_ascii=False))


if __name__ == "__main__":
    main()