<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
 /**
 * REPORTE 1: Clientes por zona geográfica
 */
    public function clientesPorZona()
    {
        // 1. Consulta: parte de la tabla clients
        $zonas = DB::table('clients')
            ->select(
                'zona_geografica', // la columna por la que se agrupa
                DB::raw('COUNT(*) as total')// cuenta los clientes de cada zona
            )
            ->groupBy('zona_geografica') // una fila por zona
            ->orderByDesc('total')// la zona con más clientes primero
            ->get();  // ejecuta y devuelve una collection

        // 2. Suma la columna total de todas las zonas
        $totalGeneral = $zonas->sum('total');

        // 3. Recorre cada zona y le agrega su porcentaje
        $zonasConPorcentaje = $zonas->map(function ($zona) use ($totalGeneral) {
            $zona->porcentaje = $totalGeneral > 0
                ? round(($zona->total / $totalGeneral) * 100, 2) // regla de tres con 2 decimales
                : 0; // evita dividir entre cero
            return $zona;  //necesita devolver el elemento
        });

        // aqui uso el filter de la pista para el reto 1 del 15%
        $zonasConPorcentaje = $zonasConPorcentaje->filter(function ($zona) {
        return $zona->porcentaje > 15;
        });




        // 4. Extrae las columnas para el gráfico de Chart.js
        $labels = $zonasConPorcentaje->pluck('zona_geografica')->toArray(); // nombres de zonas
        $data   = $zonasConPorcentaje->pluck('total')->toArray(); // cantidades

        // 5. Envía las variables a la vista
        return view('reports.zonas', compact('zonasConPorcentaje', 'totalGeneral', 'labels', 'data'));
    }

    public function interaccionesPorAsesor()
    {
        $asesores = DB::table('users') // ← HUECO A
            ->select(
                'users.id', 'users.name',
                DB::raw("COUNT(CASE WHEN interactions.tipo_interaccion = 'Llamada' 
            THEN 1 END) AS llamadas"),   // ← HUECO B
                DB::raw("COUNT(CASE WHEN interactions.tipo_interaccion = 'Visita' 
            THEN 1 END) AS visitas"),   // ← HUECO C
                DB::raw("COUNT(CASE WHEN interactions.tipo_interaccion = 'WhatsApp' 
            THEN 1 END) AS whatsapp"),  // ← HUECO D
                DB::raw('COUNT(interactions.id) AS total')  // ← HUECO E
            )
            ->leftJoin('clients', 'clients.user_id', '=', 'users.id') //← HUECO F
            ->leftJoin('interactions', 'interactions.client_id', '=',
    'clients.id') // ← HUECO G
            ->groupBy('users.id', 'users.name') // ← HUECO H, I
            ->orderByDesc('total') // ← HUECO J
            ->get();
        
        $labels = $asesores->pluck('name')->toArray(); // ← HUECO K
        $llamadas = $asesores->pluck('llamadas')->toArray(); // ← HUECO L
        $visitas = $asesores->pluck('visitas')->toArray(); // ← HUECO M
        $whatsapp = $asesores->pluck('whatsapp')->toArray(); // ← HUECO N
    
        return view('reports.interacciones', compact(
            'asesores', 'labels', 'llamadas', 'visitas', 'whatsapp'
        ));
    }
}



