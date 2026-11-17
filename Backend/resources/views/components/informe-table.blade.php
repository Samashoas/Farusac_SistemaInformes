@props([
    'informes' => [],
    'rolePrefix' => 'docente'
])

<div class="table-responsive">
    <table class="courses-table" id="informesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Curso</th>
                <th>Código</th>
                <th>Sección</th>
                <th>Año</th>
                <th>Periodo</th>
                <th>Mes</th>
                <th style="text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody id="informesTableBody">
            @forelse ($informes as $index => $inf)
                @php
                    $cursoNombre = $inf->curso->nombre_curso ?? 'Sin asignar';
                    $cursoCodigo = $inf->curso->codigo_curso ?? '—';
                    $cursoSeccion = $inf->curso->seccion ?? '—';
                    $cursoCarrera = $inf->curso->carrera ?? '';
                    $anioVal = $inf->curso->anio ?? date('Y', strtotime($inf->created_at));
                    
                    $periodoClean = trim(preg_replace('/\d{4}/', '', $inf->periodo));
                    if (!$periodoClean) { $periodoClean = $inf->periodo; }
                @endphp
                <tr class="informe-row" 
                    id="row-informe-{{ $inf->id }}"
                    data-carrera="{{ strtolower($cursoCarrera) }}"
                    data-curso="{{ strtolower($cursoNombre) }}"
                    data-codigo="{{ strtolower($cursoCodigo) }}"
                    data-periodo="{{ strtolower($periodoClean) }}"
                    data-anio="{{ $anioVal }}"
                    data-mes="{{ strtolower($inf->mes) }}">
                    <td class="td-id">{{ $index + 1 }}.</td>
                    <td class="td-curso">{{ $cursoNombre }}</td>
                    <td class="td-codigo">{{ $cursoCodigo }}</td>
                    <td class="td-seccion">{{ $cursoSeccion }}</td>
                    <td>{{ $anioVal }}</td>
                    <td>{{ $periodoClean }}</td>
                    <td>{{ $inf->mes }}</td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; align-items: center; gap: 8px; justify-content: flex-end;">
                            <a href="{{ route($rolePrefix . '.informes.ver', $inf->id) }}" target="_blank" class="action-btn view-btn" title="Visualizar informe">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            <a href="{{ route($rolePrefix . '.informes.editar', $inf->id) }}" class="action-btn edit-btn" title="Editar informe">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>

                            <button type="button" class="action-btn delete-btn" onclick="openDeleteModal({{ $inf->id }}, '{{ addslashes($cursoNombre) }}')" title="Eliminar informe">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="noDataRow">
                    <td colspan="8" class="no-records-row">No se han registrado informes todavía.</td>
                </tr>
            @endforelse
            
            <tr id="noResultsRow" style="display: none;">
                <td colspan="8" class="no-records-row">No se encontraron informes coincidentes con los filtros seleccionados.</td>
            </tr>
        </tbody>
    </table>
</div>
