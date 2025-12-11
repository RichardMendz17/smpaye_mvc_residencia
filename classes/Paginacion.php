<?php

namespace Classes;

class Paginacion 
{
    public $pagina_actual;
    public $registros_por_pagina;
    public $total_registros;
    public $parametros_extra;

    public function __construct($pagina_actual = 1, $registros_por_pagina = 3, $total_registros = 0, $parametros_extra = '')
    {
        $this->pagina_actual = (int) $pagina_actual;
        $this->registros_por_pagina = (int) $registros_por_pagina;
        $this->total_registros = (int) $total_registros;
        $this->parametros_extra = $parametros_extra ? '&' . ltrim($parametros_extra, '&') : '';
    }

    public function offset()
    {
        return $this->registros_por_pagina * ($this->pagina_actual - 1);
    }

    public function total_paginas() {
        $total = ceil($this->total_registros / $this->registros_por_pagina);
        $total == 0 ? $total = 1 : $total = $total;
        return $total;
    }

    public function pagina_anterior()
    {
        $anterior = $this->pagina_actual - 1;
        return ($anterior > 0) ? $anterior : false;
    }

    public function pagina_siguiente()
    {
        $siguiente = $this->pagina_actual + 1;
        return ($siguiente <= $this->total_paginas()) ? $siguiente : false;
    }

    public function enlace_anterior()
    {
        if ($this->pagina_anterior()) {
            $params = htmlspecialchars($this->parametros_extra);
            return "<a class=\"enlace enlace_paginacion\" href=\"?page={$this->pagina_anterior()}{$params}\">&laquo; Anterior</a>";
        }
        return '';
    }

    public function enlace_siguiente()
    {
        if ($this->pagina_siguiente()) {
            $params = htmlspecialchars($this->parametros_extra);
            return "<a class=\"enlace enlace_paginacion\" href=\"?page={$this->pagina_siguiente()}{$params}\">Siguiente &raquo;</a>";
        }
        return '';
    }

    public function numeros_paginas()
    {
        $html = '';
        $cantidadPorBloque = 3;

        $total = $this->total_paginas();
        $paginaActual = $this->pagina_actual;
        $params = htmlspecialchars($this->parametros_extra);

        $bloqueActual = (int) floor(($paginaActual - 1) / $cantidadPorBloque);
        $inicio = $bloqueActual * $cantidadPorBloque + 1;
        $fin = min($inicio + $cantidadPorBloque - 1, $total);

        for ($i = $inicio; $i <= $fin; $i++) {
            if ($i === $paginaActual) {
                $html .= "<span class=\"paginacion_numeracion actual\">{$i}</span>";
            } else {
                $html .= "<a class=\"paginacion_numeracion\" href=\"?page={$i}{$params}\">{$i}</a> ";
            }
        }

        return $html;
    }

    public function paginacion()
    {
        if ($this->total_registros >= 1) {
            return '<div class="paginacion">' .
                $this->enlace_anterior() .
                $this->numeros_paginas() .
                $this->enlace_siguiente() .
                '</div>';
        }
        return '';
    }
}