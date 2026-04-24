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
    return [t for t in limpiar_texto(texto).split() if len(t) >= 2]


def construir_texto_curso(curso: dict) -> str:
    return " ".join([
        str(curso.get("titulo", "") or ""),
        str(curso.get("descripcion", "") or ""),
        str(curso.get("profesor", "") or ""),
        str(curso.get("nivel", "") or ""),
        str(curso.get("modalidad", "") or ""),
    ])


def construir_mapa_cursos(cursos: list[dict]) -> dict:
    return {curso.get("id"): curso for curso in cursos}


def construir_tokens_busquedas(busquedas_recientes: list[str]) -> Counter:
    pesos = Counter()

    for indice, busqueda in enumerate(busquedas_recientes[:5]):
        tokens = tokenizar(str(busqueda or ""))
        if not tokens:
            continue

        if indice == 0:
            peso = 120
        elif indice == 1:
            peso = 80
        elif indice == 2:
            peso = 50
        else:
            peso = 25

        for token in tokens:
            pesos[token] += peso

    return pesos


def obtener_clicks_existentes(ids_cursos_clicados: list[int], mapa_cursos: dict) -> list[int]:
    return [curso_id for curso_id in ids_cursos_clicados if curso_id in mapa_cursos]


def construir_tokens_clicks(ids_cursos_clicados: list[int], mapa_cursos: dict) -> Counter:
    pesos = Counter()
    clicks_existentes = obtener_clicks_existentes(ids_cursos_clicados, mapa_cursos)

    for indice, curso_id in enumerate(clicks_existentes[:3]):
        curso = mapa_cursos[curso_id]
        tokens = tokenizar(construir_texto_curso(curso))

        if indice == 0:
            peso = 140
        elif indice == 1:
            peso = 90
        else:
            peso = 60

        for token in tokens:
            pesos[token] += peso

    return pesos


def obtener_perfil_ultimo_click(ids_cursos_clicados: list[int], mapa_cursos: dict) -> dict:
    clicks_existentes = obtener_clicks_existentes(ids_cursos_clicados, mapa_cursos)
    if not clicks_existentes:
        return {}

    ultimo = mapa_cursos[clicks_existentes[0]]

    return {
        "nivel": limpiar_texto(str(ultimo.get("nivel", "") or "")),
        "modalidad": limpiar_texto(str(ultimo.get("modalidad", "") or "")),
        "profesor": limpiar_texto(str(ultimo.get("profesor", "") or "")),
    }


def score_por_tokens(curso: dict, tokens_interes: Counter) -> float:
    if not tokens_interes:
        return 0.0

    titulo_tokens = tokenizar(str(curso.get("titulo", "") or ""))
    descripcion_tokens = tokenizar(str(curso.get("descripcion", "") or ""))
    profesor_tokens = tokenizar(str(curso.get("profesor", "") or ""))
    nivel_tokens = tokenizar(str(curso.get("nivel", "") or ""))
    modalidad_tokens = tokenizar(str(curso.get("modalidad", "") or ""))

    score = 0.0

    for token, peso in tokens_interes.items():
        score += titulo_tokens.count(token) * peso * 14
        score += descripcion_tokens.count(token) * peso * 5
        score += profesor_tokens.count(token) * peso * 3
        score += nivel_tokens.count(token) * peso * 4
        score += modalidad_tokens.count(token) * peso * 4

        for palabra in titulo_tokens:
            if palabra != token and (token in palabra or palabra in token):
                score += peso * 8

        for palabra in descripcion_tokens:
            if palabra != token and (token in palabra or palabra in token):
                score += peso * 2

    return score


def score_por_perfil(curso: dict, perfil_ultimo_click: dict) -> float:
    if not perfil_ultimo_click:
        return 0.0

    score = 0.0
    nivel = limpiar_texto(str(curso.get("nivel", "") or ""))
    modalidad = limpiar_texto(str(curso.get("modalidad", "") or ""))
    profesor = limpiar_texto(str(curso.get("profesor", "") or ""))

    if perfil_ultimo_click.get("nivel") and nivel == perfil_ultimo_click["nivel"]:
        score += 40

    if perfil_ultimo_click.get("modalidad") and modalidad == perfil_ultimo_click["modalidad"]:
        score += 30

    if perfil_ultimo_click.get("profesor") and profesor == perfil_ultimo_click["profesor"]:
        score += 20

    return score


def ordenar_candidatos(cursos: list[dict], tokens_interes: Counter, perfil_ultimo_click: dict, ids_cursos_clicados: list[int]) -> list[tuple[float, dict]]:
    candidatos = []

    for curso in cursos:
        curso_id = curso.get("id")
        score = 0.0
        score += score_por_tokens(curso, tokens_interes)
        score += score_por_perfil(curso, perfil_ultimo_click)

        # Penalización fuerte a lo ya clicado para forzar variación
        if curso_id in ids_cursos_clicados:
            score -= 500

        candidatos.append((score, curso))

    candidatos.sort(key=lambda item: (item[0], item[1].get("id", 0)), reverse=True)
    return candidatos


def construir_recomendados(cursos: list[dict], busquedas_recientes: list[str], ids_cursos_clicados: list[int]) -> list[dict]:
    if not cursos:
        return []

    mapa_cursos = construir_mapa_cursos(cursos)
    tokens_busquedas = construir_tokens_busquedas(busquedas_recientes)
    tokens_clicks = construir_tokens_clicks(ids_cursos_clicados, mapa_cursos)
    tokens_interes = tokens_busquedas + tokens_clicks
    perfil_ultimo_click = obtener_perfil_ultimo_click(ids_cursos_clicados, mapa_cursos)

    # Si no hay señales, fallback simple
    if not tokens_interes and not perfil_ultimo_click:
        return cursos[:LIMITE_RECOMENDADOS]

    candidatos = ordenar_candidatos(cursos, tokens_interes, perfil_ultimo_click, ids_cursos_clicados)

    recomendados = []
    ids_metidos = set()

    # 1) primero no clicados con score útil
    for score, curso in candidatos:
        curso_id = curso.get("id")
        if curso_id in ids_metidos or curso_id in ids_cursos_clicados:
            continue
        if score <= 0:
            continue

        recomendados.append(curso)
        ids_metidos.add(curso_id)

        if len(recomendados) >= LIMITE_RECOMENDADOS:
            return recomendados

    # 2) si faltan, completa con no clicados aunque tengan score bajo
    for score, curso in candidatos:
        curso_id = curso.get("id")
        if curso_id in ids_metidos or curso_id in ids_cursos_clicados:
            continue

        recomendados.append(curso)
        ids_metidos.add(curso_id)

        if len(recomendados) >= LIMITE_RECOMENDADOS:
            return recomendados

    # 3) último recurso: rellena con cualquiera
    for score, curso in candidatos:
        curso_id = curso.get("id")
        if curso_id in ids_metidos:
            continue

        recomendados.append(curso)
        ids_metidos.add(curso_id)

        if len(recomendados) >= LIMITE_RECOMENDADOS:
            return recomendados

    return recomendados[:LIMITE_RECOMENDADOS]


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
    idioma_web = str(payload.get("idioma_web", "") or "").strip()

    if idioma_web:
        cursos = [curso for curso in cursos if str(curso.get("idioma", "") or "") == idioma_web]

    recomendados = construir_recomendados(cursos, busquedas_recientes, ids_cursos_clicados)

    if busqueda_actual == "":
        print(json.dumps({
            "modo": "recomendados",
            "mensaje": "",
            "cursos": [],
            "destacados": recomendados
        }, ensure_ascii=False))
        return

    print(json.dumps({
        "modo": "sin_resultados",
        "mensaje": "No hay resultados para esta búsqueda.",
        "cursos": [],
        "destacados": recomendados
    }, ensure_ascii=False))


if __name__ == "__main__":
    main()