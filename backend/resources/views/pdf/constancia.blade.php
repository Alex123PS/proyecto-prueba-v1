<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Docente - CEPRE</title>
    <style>
        @page { margin: 100px 60px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 12px;
        }

        /* Encabezado y pie de página */
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 70px;
            text-align: center;
            border-bottom: 2px solid #003366;
        }

        header img {
            position: absolute;
            top: 0;
            left: 60px;
            height: 60px;
        }

        header h1 {
            margin: 0;
            font-size: 16px;
            color: #003366;
            text-transform: uppercase;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #ccc;
            color: #777;
        }

        /* Contenido principal */
        main {
            margin-top: 20px;
            text-align: justify;
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            color: #003366;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .datos {
            margin: 15px 0;
            line-height: 1.6;
        }

        .firma {
            text-align: center;
            margin-top: 80px;
        }

        .firma .linea {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }

        .firma p {
            margin: 2px 0;
        }

    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/logo_cepre.png') }}" alt="Logo CEPRE">
    <h1>Universidad Nacional del Altiplano Puno - CENTRO PREUNIVERSITARIO - CENTRO DE PRODUCCION DE BIENES Y SERVICIOS - CEPREUNA</h1>
</header>

<footer>
    DIRECCION: JR. ACORA N° 235 - WWWW.CEPREUNA.EDU.PE • {{ now()->format('d/m/Y H:i') }}
</footer>

<main>
    <p class="titulo">Constancia</p>

    <p>LA QUE SUSCRIBE, PRESIDENTE DEL CENTRO PREUNIVERSITARIO DE LA UNIVERSIDAD NACIONAL DEL ALTIPLANO - PUNO</p>

    <p>Que el(la) Sr(a) <strong> {{ $constancia->nombres }} {{ $constancia->ap_paterno }} {{ $constancia->ap_materno }} </strong>, identificado(a) con DNI N° <strong> {{ $constancia->dni }} </strong>, ha laborado como DOCENTE en el Centro Preuniversitario de la Universidad Nacional del Altiplano durante el ciclo académico <strong> {{ $constancia->ciclo }} </strong>, según el siguiente detalle:</p>

    <div class="datos">
        <strong>SEDE(S):</strong> {{ $constancia->sede }} <br>
        <strong>CURSO(S):</strong> {{ $constancia->curso }} <br>
        <strong>ÁREA(S):</strong> {{ $constancia->area }} <br>
        <strong>TOTAL HORA(S):</strong> {{ $constancia->cantidad_horas }} horas <br>
    </div>

    <p>Durante este periodo, el/la mencionado/a docente ha cumplido satisfactoriamente con sus funciones académicas, demostrando responsabilidad y compromiso con la formación preuniversitaria de nuestros estrudiantes.</p>

    <p>Se expide la presente constancia a solicitud escrita del interesado(a) para los fines que estime conveniente.</p>

    <div class="firma">
        <div class="linea"></div>
        <p><strong>{{ $constancia->emitido_por ?? 'Dirección Académica del CEPRE' }}</strong></p>
        <p><em>Centro Preuniversitario - UNA Puno</em></p>
    </div>
</main>

</body>
</html>
