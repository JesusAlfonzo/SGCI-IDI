{{-- En resources/views/flows/requests/show.blade.php --}}

@if ($materialRequest->status === 'Pending' && Auth::user()->hasAnyRole(['Administrador', 'Super Administrador']))
    <div class="card-footer">
        <h4 class="text-bold text-danger">Acciones de Aprobación</h4>
        
        <form action="{{ route('flows.requests.approve', $materialRequest->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Confirmar APROBACIÓN de esta solicitud?');">
            @csrf
            <button type="submit" class="btn btn-lg btn-success">
                <i class="fas fa-thumbs-up"></i> Aprobar Solicitud
            </button>
        </form>

        <button type="button" class="btn btn-lg btn-danger" data-toggle="modal" data-target="#rejectModal">
            <i class="fas fa-thumbs-down"></i> Rechazar Solicitud
        </button>
        
    </div>

    {{-- MODAL para solicitar la razón de rechazo --}}
    @include('flows.requests.partials.reject_modal', ['materialRequest' => $materialRequest])
@endif