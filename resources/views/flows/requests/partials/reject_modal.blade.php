<!-- Modal para Rechazar Solicitud -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <form action="{{ route('flows.requests.reject', $materialRequest->id) }}" method="POST">
                @csrf
                
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle"></i> Rechazar Solicitud #{{ $materialRequest->request_code }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                a
                <div class="modal-body">
                    <p>Por favor, ingrese la razón por la cual esta solicitud será rechazada. Esta información será visible para el solicitante.</p>
                    
                    <div class="form-group">
                        <label for="rejection_reason">Razón del Rechazo:</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" placeholder="Ej: No hay stock suficiente para los productos X y Y, o el propósito de la solicitud no está justificado." required></textarea>
                        
                        {{-- Opcional: Mostrar error de validación si Laravel lo devuelve --}}
                        @error('rejection_reason')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                </div>
            </form>
            
        </div>
    </div>
</div>