import json
import re
import sys
import unicodedata
from collections import Counter

LIMITE_RECOMENDADOS = 3


def quitar_acentos(texto: str) -> str:
    texto = unicodedata.normalize("NFD", texto or "")
    return "".join(c for c in texto if unicodedata.category(c) != "Mn")


def limpiar_texto(texto: str) -> str:
    texto = (texto or "").lower().strip()
    texto = quitar_acentos(texto)
    texto = re.sub(r"[^a-z0-9\s]", " ", texto)
    texto = re.sub(r"\s+", " ", texto).strip()
    return texto


def tokenizar(texto: str) -> list[str]:
    texto = limpiar_texto(texto)
    return [token for token in texto.split() if token]


def construir_texto_curso(curso: dict) -> str:
    return " ".join([
        str(curso.get("titulo", "") or ""),
        str(curso.get("descripcion", "") or ""),
        str(curso.get("profesor", "") or ""),
        str(curso.get("nivel", "") or ""),
        str(curso.get("modalidad", "") or ""),
    ])


def obtener_tokens_cursos_clicados(cursos: list[dict], ids_clicados: list[int]) -> list[str]:
    textos = []

    for curso in cursos:
        if curso.get("id") in ids_clicados:
            textos.append(construir_texto_curso(curso))

    return tokenizar(" ".join(textos))


def score_recomendacion(curso: dict, tokens_interes: list[str], ids_clicados: list[int]) -> int:
    texto = tokenizar(construir_texto_curso(curso))
    contador = Counter(texto)

    puntuacion = 0

    for token in tokens_interes:
        puntuacion += contador.get(token, 0) * 3

        for palabra in texto:
            if token in palabra or palabra in token:
                puntuacion += 1

    if curso.get("id") in ids_clicados:
        puntuacion -= 3

    return puntuacion


def construir_recomendados(cursos: list[dict], tokens_interes: list[str], ids_clicados: list[int]) -> list[dict]:
    cursos_ordenados = sorted(
        cursos,
        key=lambda curso: score_recomendacion(curso, tokens_interes, ids_clicados),
        reverse=True
    )

    recomendados = []

    for curso in cursos_ordenados:
        if curso.get("id") in ids_clicados:
            continue

        if score_recomendacion(curso, tokens_interes, ids_clicados) > 0:
            recomendados.append(curso)

        if len(recomendados) == LIMITE_RECOMENDADOS:
            break

    if len(recomendados) < LIMITE_RECOMENDADOS:
        ids_metidos = {curso.get("id") for curso in recomendados}

        for curso in cursos:
            if curso.get("id") in ids_metidos:
                continue
            if curso.get("id") in ids_clicados:
                continue

            recomendados.append(curso)
            ids_metidos.add(curso.get("id"))

            if len(recomendados) == LIMITE_RECOMENDADOS:
                break

    if len(recomendados) < LIMITE_RECOMENDADOS:
        ids_metidos = {curso.get("id") for curso in recomendados}

        for curso in cursos:
            if curso.get("id") in ids_metidos:
                continue

            recomendados.append(curso)
            ids_metidos.add(curso.get("id"))

            if len(recomendados) == LIMITE_RECOMENDADOS:
                break

    return recomendados[:LIMITE_RECOMENDADOS]


def construir_tokens_interes(busquedas_recientes: list[str], cursos: list[dict], ids_clicados: list[int]) -> list[str]:
    tokens = []

    for busqueda in busquedas_recientes:
        tokens.extend(tokenizar(str(busqueda or "")))

    tokens.extend(obtener_tokens_cursos_clicados(cursos, ids_clicados))

    return tokens


def main():
    if len(sys.argv) < 2:
        print(json.dumps({
            "modo": "sin_datos",
            "mensaje": "",
            "cursos": [],
            "destacados": []
        }, ensure_ascii=False))
        return

    ruta_json = sys.argv[1]

    with open(ruta_json, "r", encoding="utf-8") as archivo:
        payload = json.load(archivo)

    cursos = payload.get("cursos", [])
    busqueda_actual = str(payload.get("busqueda_actual", "") or "").strip()
    busquedas_recientes = payload.get("busquedas_recientes", [])
    ids_cursos_clicados = payload.get("ids_cursos_clicados", [])

    tokens_interes = construir_tokens_interes(busquedas_recientes, cursos, ids_cursos_clicados)

    if busqueda_actual == "":
        recomendados = construir_recomendados(cursos, tokens_interes, ids_cursos_clicados)

        print(json.dumps({
            "modo": "recomendados",
            "mensaje": "",
            "cursos": [],
            "destacados": recomendados
        }, ensure_ascii=False))
        return

    recomendados = construir_recomendados(cursos, tokens_interes, ids_cursos_clicados)

    print(json.dumps({
        "modo": "sin_resultados",
        "mensaje": "No hay resultados para esta búsqueda.",
        "cursos": [],
        "destacados": recomendados
    }, ensure_ascii=False))


if __name__ == "__main__":
    main()